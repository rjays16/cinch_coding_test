<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellerRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SellerAuthController extends Controller
{
    /**
     * Register a new seller.
     */
    public function register(SellerRegisterRequest $request): JsonResponse
    {
        $seller = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => 'seller',
            'store_name' => $request->store_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'terms_accepted' => $request->terms_accepted,
        ]);

        $token = $seller->createToken('seller-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Seller account created successfully!',
            'data' => [
                'user' => $seller->only(['id', 'first_name', 'last_name', 'full_name', 'role', 'store_name', 'email', 'phone']),
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login seller.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->isSeller()) {
            return response()->json([
                'success' => false,
                'message' => 'This account is not a seller account.',
            ], 403);
        }

        $token = $user->createToken('seller-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'data' => [
                'user' => $user->only(['id', 'first_name', 'last_name', 'full_name', 'role', 'store_name', 'email', 'phone']),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout seller.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully!',
        ]);
    }

    /**
     * Get authenticated seller profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isSeller()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $user->only([
                'id', 
                'first_name', 
                'last_name', 
                'full_name', 
                'role', 
                'store_name', 
                'email', 
                'phone', 
                'email_verified_at', 
                'created_at', 
                'updated_at'
            ]),
        ]);
    }
}