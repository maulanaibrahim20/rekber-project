<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roles = ['Super Admin', 'Admin'];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'admin',
            ]);
        }

        $superAdmin = Admin::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mailinator.com',
            'username' => 'superadmin',
            'is_super_admin' => true,
        ]);

        $superAdmin->assignRole('Super Admin');

        $admin = Admin::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mailinator.com',
            'username' => 'admin',
            'is_super_admin' => false,
        ]);

        $admin->assignRole('Admin');

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@mailinator.com',
        ]);
    }
}
