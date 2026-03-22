<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Purchasing Module API Routes
|--------------------------------------------------------------------------
|
| Define purchasing module routes here.
| All routes are prefixed with /api/purchasing
|
*/

Route::get('/', function () {
    return response()->json([
        'module' => 'Purchasing',
        'status' => 'ready',
    ]);
});
