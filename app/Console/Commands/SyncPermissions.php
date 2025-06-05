<?php

namespace App\Console\Commands;

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

        foreach ($routes as $route) {
            $uri = $route->uri();
            $name = $route->getName();

            if (Str::startsWith($uri, '~admin') && $name) {
                if (Str::contains($uri, ['getData', 'login', 'logout', 'store', 'update', 'destroy'])) {
                    continue;
                }

                $permissionName = str_replace('.', ' ', $name);

                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create([
                        'name' => $permissionName,
                        'guard_name' => 'admin',
                    ]);
                    $this->info("Added permission: {$permissionName}");
                    $count++;
                }
            }
        }

        $this->info("Total permissions added: $count");
    }
}
