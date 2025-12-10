<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// User profile endpoints
Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::put('/profile', [UserController::class, 'update'])->name('update');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
