<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoleController
{
    public function __construct(
        private readonly RoleService $roleService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->roleService->list());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->roleService->find($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validated($request);

        $role = $this->roleService->create($validated);

        return response()->json($role, 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $validated = $this->validated($request);

        $role = $this->roleService->update($id, $validated);

        return response()->json($role);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->roleService->delete($id);

        return response()->json([
            'message' => 'Rol eliminado correctamente',
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'                     => 'required|string|max:50',
            'is_super_admin'           => 'boolean',
            'permissions'              => 'array',
            'permissions.*'            => 'array',
            'permissions.*.*'          => 'string|in:view,create,edit,delete',
        ]);
    }
}
