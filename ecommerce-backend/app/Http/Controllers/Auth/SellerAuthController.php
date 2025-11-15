<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellerRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SellerAuthController extends Controller
{
    /**
     * Register a new seller.
     *
     * @param SellerRegisterRequest $request
     * @return JsonResponse
     */
    public function register(SellerRegisterRequest $request): JsonResponse
    {
        // Create new seller
        $seller = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => 'seller',
            'store_name' => $request->store_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password, 
            'terms_accepted' => $request->terms_accepted ?? true,
        ]);

        // Generate authentication token
        $token = $seller->createToken('seller-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Seller account created successfully!',
            'token' => $token,
            'seller' => $seller->only([
                'id',
                'first_name',
                'last_name',
                'full_name',
                'role',
                'store_name',
                'email',
                'phone'
            ]),
        ], 201);
    }

    /**
     * Login seller.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Check if user exists with this email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No seller account found with this email address. Please register as a seller first.',
            ], 404);
        }

        // Check if password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password. Please check your credentials and try again.',
            ], 401);
        }

        // Check if user has seller role
        if (!$user->isSeller()) {
            return response()->json([
                'success' => false,
                'message' => 'This account is not registered as a seller account.',
            ], 403);
        }

        // Generate authentication token
        $token = $user->createToken('seller-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'token' => $token,
            'seller' => $user->only([
                'id',
                'first_name',
                'last_name',
                'full_name',
                'role',
                'store_name',
                'email',
                'phone'
            ]),
        ], 200);
    }

    /**
     * Logout seller (revoke current access token).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully!',
        ], 200);
    }

    /**
     * Get authenticated seller profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if authenticated user is a seller
        if (!$user->isSeller()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Only sellers can access this resource.',
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
        ], 200);
    }
}