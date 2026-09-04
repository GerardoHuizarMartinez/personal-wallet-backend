<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->userService->list());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->userService->find($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:50',
            'first_last_name'  => 'required|string|max:50',
            'second_last_name' => 'nullable|string|max:50',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:6',
            'telephone'        => 'nullable|string|max:20',
            'cellphone'        => 'nullable|string|max:20',
            'birthday'         => 'nullable|date',
            'gender'           => 'nullable|in:Male,Female',
            'country'          => 'nullable|string|max:100',
            'colony_id'        => 'nullable|exists:colonies,id',
            'street'           => 'nullable|string|max:150',
            'no_ext'           => 'nullable|string|max:20',
            'no_int'           => 'nullable|string|max:20',
            'status'           => 'required|in:Active,Inactive',
            'url_image'        => 'nullable|string|max:2048',
        ]);

        $user = $this->userService->create($validated);

        return response()->json($user, 201);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:50',
            'first_last_name'  => 'required|string|max:50',
            'second_last_name' => 'nullable|string|max:50',
            'email'            => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'current_password' => 'nullable|string|required_with:password',
            'password'         => 'nullable|string|min:6|confirmed',
            'telephone'        => 'nullable|string|max:20',
            'cellphone'        => 'nullable|string|max:20',
            'birthday'         => 'nullable|date',
            'gender'           => 'nullable|in:Male,Female',
            'country'          => 'nullable|string|max:100',
            'colony_id'        => 'nullable|exists:colonies,id',
            'street'           => 'nullable|string|max:150',
            'no_ext'           => 'nullable|string|max:20',
            'no_int'           => 'nullable|string|max:20',
            'status'           => 'required|in:Active,Inactive',
            'url_image'        => 'nullable|string|max:2048',
        ]);

        $user = $this->userService->update($id, $validated);

        return response()->json($user);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->userService->delete($id);

        return response()->json([
            'message' => 'Usuario eliminado correctamente',
        ]);
    }

    public function uploadPhoto(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $user = $this->userService->uploadPhoto($id, $request->file('photo'));

        return response()->json($user);
    }

    public function deletePhoto(int $id): JsonResponse
    {
        $user = $this->userService->deletePhoto($id);

        return response()->json($user);
    }
}