<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions
        $permissions = [
            ['name' => 'Manage Users', 'slug' => 'manage-users'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles'],
            ['name' => 'View Results', 'slug' => 'view-results'],
            ['name' => 'Manage Fees', 'slug' => 'manage-fees'],
            ['name' => 'Manage Library', 'slug' => 'manage-library'],
            ['name' => 'Manage Books', 'slug' => 'manage-books'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['slug' => $perm['slug']], $perm);
        }

        // Roles
        $adminRole = Role::updateOrCreate(['name' => 'Admin'], ['description' => 'Super Administrator']);
        $teacherRole = Role::updateOrCreate(['name' => 'Teacher'], ['description' => 'School Teacher']);
        $studentRole = Role::updateOrCreate(['name' => 'Student'], ['description' => 'School Student']);
        $parentRole = Role::updateOrCreate(['name' => 'Parent'], ['description' => 'Student Parent']);
        $accountantRole = Role::updateOrCreate(['name' => 'Accountant'], ['description' => 'School Accountant']);
        $librarianRole = Role::updateOrCreate(['name' => 'Librarian'], ['description' => 'School Librarian']);
        $facilityRole = Role::updateOrCreate(['name' => 'Facility'], ['description' => 'Facility Manager']);

        // Assign all permissions to Admin
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Assign specific permissions to others
        $teacherRole->permissions()->sync(Permission::whereIn('slug', ['view-results'])->pluck('id'));
        $parentRole->permissions()->sync(Permission::whereIn('slug', ['view-results'])->pluck('id'));
        $accountantRole->permissions()->sync(Permission::whereIn('slug', ['manage-fees'])->pluck('id'));
        $librarianRole->permissions()->sync(Permission::whereIn('slug', ['manage-library', 'manage-books'])->pluck('id'));

        // Assign Admin role to existing admin users
        $admins = User::where('usertype', 'Admin')->get();
        foreach ($admins as $admin) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
