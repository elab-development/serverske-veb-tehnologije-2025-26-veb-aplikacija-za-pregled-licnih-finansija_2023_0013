<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $token = Str::random(80);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => User::ROLE_USER,
            'is_active' => true,
            'api_token' => $token,
        ]);

        return response()->json([
            'data'    => new UserResource($user),
            'token'   => $token,
            'message' => 'Registracija uspješna.',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Pogrešan email ili lozinka.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Nalog je deaktiviran.'], 403);
        }

        $token = Str::random(80);
        $user->update(['api_token' => $token]);

        return response()->json([
            'data'    => new UserResource($user),
            'token'   => $token,
            'message' => 'Prijava uspješna.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->update(['api_token' => null]);

        return response()->json(['message' => 'Odjava uspješna.']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'data'    => new UserResource($request->user()),
            'message' => 'OK',
        ]);
    }
}
