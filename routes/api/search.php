<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;

// Search endpoints
Route::get('/search', [SearchController::class, 'products'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
