<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

/**
 * ModuleServiceProvider
 *
 * Auto-discovers and registers per-module service providers.
 * Each module under app/Modules/{ModuleName} can have a
 * {ModuleName}ServiceProvider.php that will be auto-registered.
 */
class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $modulesPath = app_path('Modules');

        if (!File::isDirectory($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $providerClass = "App\\Modules\\{$moduleName}\\{$moduleName}ServiceProvider";

            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }

    public function boot(): void
    {
        //
    }
}
