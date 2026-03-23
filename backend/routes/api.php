<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Module-specific API routes are registered by each module's ServiceProvider.
| This file is for global/cross-module API routes.
|
*/

Route::get('/health', function () {
    return response()->json([
        'status'  => 'ok',
        'app'     => 'Naventra ERP',
        'version' => '1.0.0',
    ]);
});

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/login', [\App\Modules\Auth\Controllers\AuthController::class, 'login']);
    Route::post('/refresh', [\App\Modules\Auth\Controllers\AuthController::class, 'refresh']);
});

Route::middleware([\App\Http\Middleware\JwtAuthMiddleware::class])->group(function () {
    Route::post('/logout', [\App\Modules\Auth\Controllers\AuthController::class, 'logout']);
    Route::get('/me', [\App\Modules\Auth\Controllers\AuthController::class, 'me']);
    
    // Core ERP Data Routes
    Route::get('/menu', [\App\Modules\System\Controllers\MenuController::class, 'index']);
    Route::get('/dashboard', [\App\Modules\Dashboard\Controllers\DashboardController::class, 'index']);
    
    // Example Module
    Route::get('/setup/master/examples', [\App\Modules\Setup\Master\Example\Controllers\ExampleController::class, 'index']);
    Route::post('/setup/master/examples', [\App\Modules\Setup\Master\Example\Controllers\ExampleController::class, 'store']);
    Route::delete('/setup/master/examples/{id}', [\App\Modules\Setup\Master\Example\Controllers\ExampleController::class, 'destroy']);
});
