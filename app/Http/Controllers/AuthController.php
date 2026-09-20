<?php

namespace App\Http\Controllers;

use App\Mappers\UserMapper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credential = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credential['email'])->first();

        if (!$user || !Hash::check($credential['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciales invalidas',
                'success' => false
            ], 401);
        }

        $token = $user->createToken('auth_token_jers')->plainTextToken;

        return response()->json([
            'message' => 'Bienvenido ' . $user->name,
            'user' => UserMapper::toArray($user->load('role.permissions')),
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesion cerrada correctamente',
            'success' => true
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json(UserMapper::toArray($request->user()->load('role.permissions')));
    }
}
