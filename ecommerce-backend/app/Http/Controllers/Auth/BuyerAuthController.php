<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class BuyerAuthController extends Controller
{
    /**
     * Register a new buyer.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(6)],
        ]);

        // Split name into first and last name
        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        // Create new buyer
        $buyer = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => 'buyer',
            'email' => $request->email,
            'password' => $request->password,
            'terms_accepted' => true,
        ]);

        // Generate authentication token
        $token = $buyer->createToken('buyer-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully!',
            'token' => $token,
            'user' => [
                'id' => $buyer->id,
                'name' => $buyer->full_name,
                'email' => $buyer->email,
                'role' => $buyer->role,
            ],
        ], 201);
    }

    /**
     * Login buyer.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address.',
            ], 404);
        }

        // Check if password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password.',
            ], 401);
        }

        // Check if user is a buyer
        if (!$user->isBuyer()) {
            return response()->json([
                'success' => false,
                'message' => 'This account is not registered as a buyer account.',
            ], 403);
        }

        // Generate authentication token
        $token = $user->createToken('buyer-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
    }

    /**
     * Logout buyer.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully!',
        ], 200);
    }

    /**
     * Get authenticated buyer profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isBuyer()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }
}