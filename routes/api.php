<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware('salespro.api')
    ->group(function (): void {
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{sku}', [ProductController::class, 'show']);
    });
