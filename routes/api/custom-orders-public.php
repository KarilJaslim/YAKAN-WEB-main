<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomOrderController;

// Custom Orders - Public endpoints
Route::get('/catalog', [CustomOrderController::class, 'getCatalog'])->name('catalog');
Route::post('/pricing-estimate', [CustomOrderController::class, 'getPricingEstimate'])->middleware('throttle:uploads')->name('pricing-estimate');
Route::post('/upload-design', [CustomOrderController::class, 'uploadDesign'])->middleware('throttle:uploads')->name('upload-design');
