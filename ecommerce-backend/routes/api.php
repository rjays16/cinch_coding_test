<?php

use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\Auth\BuyerAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Seller routes
Route::prefix('seller')->group(function () {
    Route::post('/register', [SellerAuthController::class, 'register']);
    Route::post('/login', [SellerAuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'sendSellerResetLink']);
});

Route::middleware('auth:sanctum')->prefix('seller')->group(function () {
    Route::post('/logout', [SellerAuthController::class, 'logout']);
    Route::get('/profile', [SellerAuthController::class, 'profile']);
});

// Buyer routes
Route::prefix('buyer')->group(function () {
    Route::post('/register', [BuyerAuthController::class, 'register']);
    Route::post('/login', [BuyerAuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'sendBuyerResetLink']);
});

Route::middleware('auth:sanctum')->prefix('buyer')->group(function () {
    Route::post('/logout', [BuyerAuthController::class, 'logout']);
    Route::get('/profile', [BuyerAuthController::class, 'profile']);
});

// Password Reset (shared endpoint for both)
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);