<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomOrderController;

// Custom Orders - Protected endpoints
Route::get('/', [CustomOrderController::class, 'index'])->name('index');
Route::post('/', [CustomOrderController::class, 'store'])->name('store');
Route::get('/{customOrder}', [CustomOrderController::class, 'show'])->name('show');
Route::put('/{customOrder}', [CustomOrderController::class, 'update'])->name('update');
Route::post('/{customOrder}/cancel', [CustomOrderController::class, 'cancel'])->name('cancel');
Route::get('/statistics', [CustomOrderController::class, 'getStatistics'])->name('statistics');
