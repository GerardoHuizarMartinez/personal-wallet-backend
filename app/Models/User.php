<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'first_last_name',
        'second_last_name',
        'birthday',
        'gender',
        'email',
        'password',
        'telephone',
        'cellphone',
        'country',
        'colony_id',
        'street',
        'no_ext',
        'no_int',
        'status',
        'url_image',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function colony()
    {
        return $this->belongsTo(Colony::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Mapa módulo => acciones permitidas. Un super admin recibe todas las
     * combinaciones existentes, para que el frontend y el middleware no
     * tengan que ramificar el caso "es super admin" por separado.
     */
    public function effectivePermissions(): array
    {
        $role = $this->role ?? $this->role()->with('permissions')->first();

        if (!$role) {
            return [];
        }

        $permissions = $role->is_super_admin
            ? Permission::all()
            : $role->permissions;

        return $permissions
            ->groupBy('module')
            ->map(fn ($modulePermissions) => $modulePermissions->pluck('action')->values()->all())
            ->all();
    }
}
