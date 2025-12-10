<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;

// Cart endpoints
Route::get('/', [CartController::class, 'index'])->name('index');
Route::post('/add', [CartController::class, 'add'])->name('add');
Route::post('/', [CartController::class, 'store'])->name('store');
Route::post('/items', [CartController::class, 'store'])->name('items.store');
Route::put('/{id}', [CartController::class, 'update'])->name('update');
Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
Route::delete('/', [CartController::class, 'clear'])->name('clear');
