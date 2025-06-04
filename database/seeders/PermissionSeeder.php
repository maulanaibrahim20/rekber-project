<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionName = [
            'add user',
            'edit user',
            'delete user',
            'add permission',
            'edit permission',
            'delete permission',
            'menu permission',
            'add administrator',
            'edit administrator',
            'delete administrator',
        ];

        $guardName = 'admin';
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        foreach ($permissionName as $name) {
            Permission::create([
                'name' => $name,
                'guard_name' => $guardName
            ]);
        }
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('id', 1)->first();
        $role->givePermissionTo($permissionName);
    }
}
