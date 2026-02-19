<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole   = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $userRole    = Role::where('name', 'user')->first();

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@taskapi.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole->id,
            ]
        );

        // Manager user
        User::firstOrCreate(
            ['email' => 'manager@taskapi.com'],
            [
                'name'     => 'Manager User',
                'password' => Hash::make('password'),
                'role_id'  => $managerRole->id,
            ]
        );

        // Regular user
        User::firstOrCreate(
            ['email' => 'user@taskapi.com'],
            [
                'name'     => 'Regular User',
                'password' => Hash::make('password'),
                'role_id'  => $userRole->id,
            ]
        );

        // Generate 50 more random users
        User::factory()->count(50)->create([
            'role_id' => $userRole->id,
        ]);
    }
}