<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../routes.php');
    }
}
