<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = Route::getRoutes();
        $admin = Admin::find(1);

        foreach ($routes as $route) {
            $uri = $route->uri();
            $name = $route->getName();

            if (Str::startsWith($uri, '~admin') && $name) {
                if (Str::contains($uri, ['getData', 'login', 'logout', 'store', 'update', 'destroy'])) {
                    continue;
                }

                $permissionName = str_replace('.', ' ', $name);

                $permission = Permission::where('name', $permissionName)->where('guard_name', 'admin')->first();

                if (!$permission) {
                    $permission = Permission::create([
                        'name' => $permissionName,
                        'guard_name' => 'admin',
                    ]);
                }

                if ($admin && !$admin->hasPermissionTo($permission->name)) {
                    $admin->givePermissionTo($permission->name);
                }
            }
        }
    }
}
