<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Inventory Module API Routes
|--------------------------------------------------------------------------
|
| Define inventory module routes here.
| All routes are prefixed with /api/inventory
|
*/

Route::get('/', function () {
    return response()->json([
        'module' => 'Inventory',
        'status' => 'ready',
    ]);
});
