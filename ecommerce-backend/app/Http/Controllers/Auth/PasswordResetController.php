<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link for BUYERS.
     */
    public function sendBuyerResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'We could not find an account with this email address.',
        ]);

        $email = $request->email;

        // Check if user is a buyer
        $user = User::where('email', $email)->first();
        
        if ($user->role !== 'buyer') {
            return response()->json([
                'success' => false,
                'message' => 'This email is not registered as a buyer account.',
            ], 403);
        }

        return $this->sendResetLink($email, 'buyer');
    }

    /**
     * Send password reset link for SELLERS.
     */
    public function sendSellerResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'We could not find an account with this email address.',
        ]);

        $email = $request->email;

        // Check if user is a seller
        $user = User::where('email', $email)->first();
        
        if ($user->role !== 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'This email is not registered as a seller account.',
            ], 403);
        }

        return $this->sendResetLink($email, 'seller');
    }

    /**
     * Private method to send reset link.
     */
    private function sendResetLink(string $email, string $role): JsonResponse
    {
        // Generate reset token
        $token = Str::random(64);

        // Delete old tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Create new token
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Get frontend URL from config based on role
        $frontendUrl = $role === 'buyer' 
            ? config('frontend.buyer_url') 
            : config('frontend.seller_url');

        // Send email
        $resetLink = $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($email);

        try {
            Mail::send('emails.password-reset', ['resetLink' => $resetLink, 'role' => $role], function($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Your Password');
            });

            Log::info('Password reset link sent', [
                'email' => $email,
                'role' => $role,
                'frontend_url' => $frontendUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Password reset email error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password (works for both buyers and sellers).
     */
    public function resetPassword(Request $request): JsonResponse
    {
        // Log the incoming request for debugging
        Log::info('Password reset request', [
            'email' => $request->email,
            'token_length' => strlen($request->token ?? ''),
            'has_password' => !empty($request->password),
            'has_confirmation' => !empty($request->password_confirmation)
        ]);

        // Validate request
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
        ], [
            'token.required' => 'Reset token is required.',
            'email.required' => 'Email address is required.',
            'email.exists' => 'We could not find an account with this email address.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        $email = $request->email;
        $token = $request->token;
        $password = $request->password;

        // Find token in database
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetToken) {
            Log::warning('Reset token not found', ['email' => $email]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 400);
        }

        // Check if token matches
        if (!Hash::check($token, $resetToken->token)) {
            Log::warning('Token mismatch', ['email' => $email]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token.',
            ], 400);
        }

        // Check if token is expired (24 hours)
        $tokenCreated = Carbon::parse($resetToken->created_at);
        if ($tokenCreated->addHours(24)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            
            Log::warning('Token expired', ['email' => $email]);
            
            return response()->json([
                'success' => false,
                'message' => 'Reset token has expired. Please request a new one.',
            ], 400);
        }

        try {
            // Find user and update password
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                Log::error('User not found', ['email' => $email]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Update password
            $user->password = Hash::make($password);
            $user->save();

            // Delete used token
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            Log::info('Password reset successful', [
                'email' => $email,
                'role' => $user->role
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully!',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage(), [
                'email' => $email,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}