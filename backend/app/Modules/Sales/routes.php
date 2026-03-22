<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sales Module API Routes
|--------------------------------------------------------------------------
|
| Define sales module routes here.
| All routes are prefixed with /api/sales
|
*/

Route::get('/', function () {
    return response()->json([
        'module' => 'Sales',
        'status' => 'ready',
    ]);
});
