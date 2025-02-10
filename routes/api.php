<?php

use App\Http\Controllers\Api\V1\CarBrandController;
use App\Http\Controllers\Api\V1\CarModelController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('car-brands', CarBrandController::class);

    Route::post('car-models/{brand}', [CarModelController::class, 'store']);
    Route::apiResource('car-models', CarModelController::class)->except(['index', 'show', 'store']);
});
