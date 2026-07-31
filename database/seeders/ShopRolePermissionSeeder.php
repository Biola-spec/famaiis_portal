<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class ShopRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            ['name' => 'Manage Shop', 'slug' => 'manage-shop'],
            ['name' => 'View Shop', 'slug' => 'view-shop'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['slug' => $perm['slug']], $perm);
        }

        // 2. Assign to Roles
        $adminRole = Role::where('name', 'Admin')->first();
        $accountantRole = Role::where('name', 'Accountant')->first();

        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching(Permission::all()->pluck('id'));
        }

        if ($accountantRole) {
            $accountantRole->permissions()->syncWithoutDetaching(Permission::where('slug', 'manage-shop')->pluck('id'));
        }
        
        // 3. Ensure any 'Admin' usertype user has the Admin role in pivot table
        $admins = User::where('usertype', 'Admin')->get();
        if ($adminRole) {
            foreach ($admins as $admin) {
                $admin->roles()->syncWithoutDetaching([$adminRole->id]);
            }
        }
    }
}
