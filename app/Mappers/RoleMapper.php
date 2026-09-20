<?php

namespace App\Mappers;

use App\Models\Role;

class RoleMapper
{
    public static function toArray(Role $role): array
    {
        return [
            'id'             => $role->id,
            'name'           => $role->name,
            'slug'           => $role->slug,
            'is_super_admin' => $role->is_super_admin,
            'users_count'    => $role->users_count ?? $role->users()->count(),
            'permissions'    => self::normalizePermissions(
                $role->is_super_admin
                    ? []
                    : $role->permissions
                        ->groupBy('module')
                        ->map(fn ($modulePermissions) => $modulePermissions->pluck('action')->values()->all())
                        ->all()
            ),
        ];
    }

    public static function collection(iterable $roles): array
    {
        return collect($roles)
            ->map(fn (Role $role) => self::toArray($role))
            ->values()
            ->all();
    }

    /**
     * json_encode serializa un array PHP vacío como `[]`, no `{}`. Sin este
     * cast, un rol sin ningún permiso le llega al frontend como un arreglo en
     * vez de un objeto, y `JSON.stringify` descarta silenciosamente las claves
     * que el frontend le agregue después (ej. al marcar checkboxes).
     */
    private static function normalizePermissions(array $permissions): array|\stdClass
    {
        return empty($permissions) ? new \stdClass() : $permissions;
    }
}
