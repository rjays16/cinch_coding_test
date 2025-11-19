<?php

use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\Auth\BuyerAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProductController;
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