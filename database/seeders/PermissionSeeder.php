<?php
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'tasks.view_all',   'display_name' => 'View All Tasks'],
            ['name' => 'tasks.create',     'display_name' => 'Create Tasks'],
            ['name' => 'tasks.manage_all', 'display_name' => 'Manage All Tasks'],
            ['name' => 'users.manage',     'display_name' => 'Manage Users'],
            ['name' => 'roles.manage',     'display_name' => 'Manage Roles'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        // Assign permissions to roles
        $admin   = Role::where('name', 'admin')->first();
        $manager = Role::where('name', 'manager')->first();
        $user    = Role::where('name', 'user')->first();

        // Admin gets all permissions
        $admin->permissions()->sync(Permission::pluck('id'));

        // Manager gets task permissions
        $manager->permissions()->sync(
            Permission::whereIn('name', [
                'tasks.view_all',
                'tasks.create',
                'tasks.manage_all',
            ])->pluck('id')
        );

        // User gets only create
        $user->permissions()->sync(
            Permission::whereIn('name', [
                'tasks.create',
            ])->pluck('id')
        );
    }
}