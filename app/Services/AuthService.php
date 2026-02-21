<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        // Get the default 'user' role from database
        $defaultRole = Role::where('name', 'user')->first();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => $defaultRole?->id, // automatically assign 'user' role
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])
                    ->with('role')
                    ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Delete old tokens - stateless approach
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return compact('user', 'token');
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}