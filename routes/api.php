<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    // aut
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // profile
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'changePassword']);
    });

    // addresses
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('addresses', AddressController::class);
        // ->only([
        //     'index',
        //     'store',
        //     'show',
        //     'update',
        //     'destroy',
        // ])

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('brands', BrandController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource( 'products.variants', ProductVariantController::class);

        Route::get('/variants/{variant}/inventory',[InventoryController::class, 'show']);
        Route::put('/variants/{variant}/inventory',[InventoryController::class, 'update']);

        Route::get('/products/{product}/images',[ProductImageController::class, 'index']);

        Route::post('/products/{product}/images',[ProductImageController::class, 'store']);
        Route::put('/products/{product}/images/{image}',[ProductImageController::class, 'update']);
        Route::delete('/products/{product}/images/{image}',[ProductImageController::class, 'destroy']);
    });
});
