<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Students Module
            ['name' => 'View Students', 'slug' => 'view_students', 'module' => 'Students'],
            ['name' => 'Create Student', 'slug' => 'create_student', 'module' => 'Students'],
            ['name' => 'Edit Student', 'slug' => 'edit_student', 'module' => 'Students'],
            ['name' => 'Delete Student', 'slug' => 'delete_student', 'module' => 'Students'],

            // Fees Module
            ['name' => 'View Fees', 'slug' => 'view_fees', 'module' => 'Fees'],
            ['name' => 'Create Fee', 'slug' => 'create_fee', 'module' => 'Fees'],
            ['name' => 'Collect Fee', 'slug' => 'collect_fee', 'module' => 'Fees'],

            // Results Module
            ['name' => 'View Results', 'slug' => 'view_results', 'module' => 'Results'],
            ['name' => 'Upload Results', 'slug' => 'upload_results', 'module' => 'Results'],
            ['name' => 'Edit Results', 'slug' => 'edit_results', 'module' => 'Results'],

            // Wallet Module
            ['name' => 'View Wallet', 'slug' => 'view_wallet', 'module' => 'Wallet'],
            ['name' => 'Fund Wallet', 'slug' => 'fund_wallet', 'module' => 'Wallet'],
            ['name' => 'Debit Wallet', 'slug' => 'debit_wallet', 'module' => 'Wallet'],

            // Admin Panel
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'module' => 'Admin Panel'],
            ['name' => 'Assign Roles', 'slug' => 'assign_roles', 'module' => 'Admin Panel'],
            ['name' => 'View Reports', 'slug' => 'view_reports', 'module' => 'Admin Panel'],
            
            // Website Settings
            ['name' => 'Manage Website', 'slug' => 'manage_website', 'module' => 'Website'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
