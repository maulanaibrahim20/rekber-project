<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi route ke permission table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = Route::getRoutes();
        $count = 0;

        $admin = Admin::find(1);
        if (!$admin) {
            $this->error('Admin dengan ID 1 tidak ditemukan.');
            return;
        }

        foreach ($routes as $route) {
            $uri = $route->uri();
            $name = $route->getName();

            if (
                !Str::startsWith($uri, '~admin') ||
                !$name ||
                Str::contains($uri, ['getData', 'login', 'logout', 'store', 'update'])
            ) {
                continue;
            }

            $permissionName = str_replace('.', ' ', $name);

            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'admin'],
                ['name' => $permissionName, 'guard_name' => 'admin']
            );

            if ($permission->wasRecentlyCreated) {
                $this->info("Added permission: {$permissionName}");
                $count++;
            }

            if (!$admin->hasPermissionTo($permission)) {
                $admin->givePermissionTo($permission);
                $this->info("Granted '{$permissionName}' to Admin ID 1");
            }
        }

        $this->info("Total new permissions added: $count");
    }
}
