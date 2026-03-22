<?php

namespace App\Modules\Accounting;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Modules\Accounting\Events\TransactionCreated;
use App\Modules\Accounting\Listeners\WriteAuditLog;

class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadMigrations();
        $this->registerEvents();
    }

    protected function loadRoutes(): void
    {
        $routesFile = __DIR__ . '/routes.php';

        if (file_exists($routesFile)) {
            Route::middleware('api')
                ->prefix('api/accounting')
                ->group($routesFile);
        }
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(database_path('migrations'));
    }

    protected function registerEvents(): void
    {
        $this->app['events']->listen(
            TransactionCreated::class,
            WriteAuditLog::class,
        );
    }
}
