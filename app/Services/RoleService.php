<?php

namespace App\Services;

use App\Mappers\RoleMapper;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function list(): array
    {
        return RoleMapper::collection(
            Role::withCount('users')->with('permissions')->orderBy('name')->get()
        );
    }

    public function find(int $id): array
    {
        return RoleMapper::toArray(
            Role::withCount('users')->with('permissions')->findOrFail($id)
        );
    }

    public function create(array $data): array
    {
        $role = Role::create([
            'name'           => $data['name'],
            'slug'           => str($data['name'])->slug(),
            'is_super_admin' => $data['is_super_admin'] ?? false,
        ]);

        $role->permissions()->sync($this->resolvePermissionIds($data));

        return RoleMapper::toArray($role->load('permissions')->loadCount('users'));
    }

    public function update(int $id, array $data): array
    {
        $role = Role::findOrFail($id);

        if ($role->is_super_admin && !($data['is_super_admin'] ?? false)) {
            throw ValidationException::withMessages([
                'is_super_admin' => ['No puedes quitarle el acceso total a este rol.'],
            ]);
        }

        $role->update([
            'name'           => $data['name'],
            'is_super_admin' => $data['is_super_admin'] ?? false,
        ]);

        $role->permissions()->sync($this->resolvePermissionIds($data));

        return RoleMapper::toArray($role->load('permissions')->loadCount('users'));
    }

    public function delete(int $id): void
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->is_super_admin) {
            throw ValidationException::withMessages([
                'role' => ['No puedes eliminar un rol con acceso total.'],
            ]);
        }

        if ($role->users_count > 0) {
            throw ValidationException::withMessages([
                'role' => ['No puedes eliminar un rol que tiene usuarios asignados.'],
            ]);
        }

        $role->delete();
    }

    /**
     * $data['permissions'] llega como ['expenses' => ['view','edit'], ...] desde el frontend.
     */
    private function resolvePermissionIds(array $data): array
    {
        if ($data['is_super_admin'] ?? false) {
            return [];
        }

        $permissions = $data['permissions'] ?? [];
        $ids = [];

        foreach ($permissions as $module => $actions) {
            $ids = array_merge(
                $ids,
                Permission::where('module', $module)->whereIn('action', $actions)->pluck('id')->all()
            );
        }

        return $ids;
    }
}
