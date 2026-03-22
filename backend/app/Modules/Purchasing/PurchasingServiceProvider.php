<?php

namespace App\Modules\Purchasing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PurchasingServiceProvider extends ServiceProvider
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
                ->prefix('api/purchasing')
                ->group($routesFile);
        }
    }
}
