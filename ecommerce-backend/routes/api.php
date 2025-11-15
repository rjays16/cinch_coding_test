<?php

use App\Http\Controllers\Auth\SellerAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('seller')->group(function () {
    Route::post('/register', [SellerAuthController::class, 'register']);
    Route::post('/login', [SellerAuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->prefix('seller')->group(function () {
    Route::post('/logout', [SellerAuthController::class, 'logout']);
    Route::get('/profile', [SellerAuthController::class, 'profile']);
});
