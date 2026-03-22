<?php

namespace App\Modules\Inventory;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routesFile = __DIR__ . '/routes.php';

        if (file_exists($routesFile)) {
            Route::middleware('api')
                ->prefix('api/inventory')
                ->group($routesFile);
        }
    }
}
