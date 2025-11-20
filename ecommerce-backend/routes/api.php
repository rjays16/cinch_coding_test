<?php

use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\Auth\BuyerAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Seller routes (Public)
Route::prefix('seller')->group(function () {
    Route::post('/register', [SellerAuthController::class, 'register']);
    Route::post('/login', [SellerAuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'sendSellerResetLink']);
});

// Seller routes (Protected - Requires Authentication)
Route::middleware('auth:sanctum')->prefix('seller')->group(function () {
    Route::post('/logout', [SellerAuthController::class, 'logout']);
    Route::get('/profile', [SellerAuthController::class, 'profile']);
    
    // Product routes
    Route::apiResource('products', ProductController::class);
    Route::get('products-stats', [ProductController::class, 'stats']);
});

// Buyer routes (Public)
Route::prefix('buyer')->group(function () {
    Route::post('/register', [BuyerAuthController::class, 'register']);
    Route::post('/login', [BuyerAuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'sendBuyerResetLink']);
});

// Buyer routes (Protected - Requires Authentication)
Route::middleware('auth:sanctum')->prefix('buyer')->group(function () {
    Route::post('/logout', [BuyerAuthController::class, 'logout']);
    Route::get('/profile', [BuyerAuthController::class, 'profile']);

    // Cart routes
        Route::get('/cart', [CartController::class, 'index']);
        Route::get('/cart/count', [CartController::class, 'count']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{id}', [CartController::class, 'update']);
        Route::delete('/cart/{id}', [CartController::class, 'destroy']);
        Route::delete('/cart', [CartController::class, 'clear']);
});

// Public product routes (no authentication required)
Route::prefix('products')->group(function () {
    Route::get('/', [PublicProductController::class, 'index']);
    Route::get('/categories', [PublicProductController::class, 'categories']);
    Route::get('/{id}', [PublicProductController::class, 'show']);
});

// Password Reset (shared endpoint for both)
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'E-Commerce API is running',
        'timestamp' => now()
    ]);
});