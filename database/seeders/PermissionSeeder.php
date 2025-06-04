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
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // Permission::create(['name' => 'edit user', 'guard_name' => 'admin']);
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('id', 1)->first();
        $role->givePermissionTo(['edit user', 'add user']);
    }
}
