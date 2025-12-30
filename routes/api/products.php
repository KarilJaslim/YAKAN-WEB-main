<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

// Products endpoints
Route::get('/', [ProductController::class, 'index'])->name('index');
Route::get('/{product}', [ProductController::class, 'show'])->name('show');
Route::get('/category/{category}', [ProductController::class, 'byCategory'])->name('byCategory');
