<?php

use App\Modules\Accounting\Master\MCoa\Controllers\MCoaController;
use App\Modules\Accounting\Report\RNeraca\Controllers\RNeracaController;
use App\Modules\Accounting\Transaction\TKbk\Controllers\TKbkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Accounting Module API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api/accounting
| (configured in AccountingServiceProvider)
|
*/

// Master - Chart of Accounts
Route::prefix('master')->group(function () {
    Route::get('mcoa/tree', [MCoaController::class, 'tree']);
    Route::apiResource('mcoa', MCoaController::class);
});

// Transaction - Bank/Cash (KBK)
Route::prefix('transaction')->group(function () {
    Route::apiResource('tkbk', TKbkController::class)->except(['update']);
});

// Reports
Route::prefix('report')->group(function () {
    Route::get('neraca', RNeracaController::class);
});
