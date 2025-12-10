<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

Route::prefix('v1')->group(function () {
    // Mobile app orders - no authentication required
    Route::post('/orders', [OrderController::class, 'store']);
});
