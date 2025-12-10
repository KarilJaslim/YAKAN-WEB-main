<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

// Orders endpoints
Route::get('/', [OrderController::class, 'index'])->name('index');
Route::post('/', [OrderController::class, 'create'])->name('create');
Route::get('/{order}', [OrderController::class, 'show'])->name('show');
