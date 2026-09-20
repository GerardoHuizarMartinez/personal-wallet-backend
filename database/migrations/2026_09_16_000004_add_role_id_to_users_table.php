<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MODULES_WITH_CRUD = ['expenses', 'income', 'categories', 'users', 'roles'];
    private const MODULES_VIEW_ONLY = ['dashboard', 'reports'];
    private const CRUD_ACTIONS = ['view', 'create', 'edit', 'delete'];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('colony_id')->constrained('roles');
        });

        $now = now();

        $permissions = [];
        foreach (self::MODULES_WITH_CRUD as $module) {
            foreach (self::CRUD_ACTIONS as $action) {
                $permissions[] = ['module' => $module, 'action' => $action, 'created_at' => $now, 'updated_at' => $now];
            }
        }
        foreach (self::MODULES_VIEW_ONLY as $module) {
            $permissions[] = ['module' => $module, 'action' => 'view', 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('permissions')->insert($permissions);
        $allPermissionIds = DB::table('permissions')->pluck('id');

        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Administrador',
            'slug' => 'administrador',
            'is_super_admin' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('roles')->insert([
            'name' => 'Usuario',
            'slug' => 'usuario',
            'is_super_admin' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('permission_role')->insert(
            $allPermissionIds->map(fn ($permissionId) => [
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId,
            ])->all()
        );

        // La(s) cuenta(s) que ya existían antes de este cambio conservan acceso total.
        DB::table('users')->update(['role_id' => $adminRoleId]);

        // No doctrine/dbal instalado para ->nullable(false)->change(), igual que en
        // make_colony_id_nullable_in_users_table.php: se aplica con SQL crudo.
        DB::statement('ALTER TABLE users MODIFY role_id BIGINT UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        $roleIds = DB::table('roles')->whereIn('slug', ['administrador', 'usuario'])->pluck('id');
        DB::table('permission_role')->whereIn('role_id', $roleIds)->delete();
        DB::table('roles')->whereIn('id', $roleIds)->delete();

        DB::table('permissions')
            ->whereIn('module', array_merge(self::MODULES_WITH_CRUD, self::MODULES_VIEW_ONLY))
            ->delete();
    }
};
