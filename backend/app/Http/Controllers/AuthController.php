<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Registered;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
     public function register(RegisterRequest $request): JsonResponse
    {
    
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'national_id' => $request->national_id,
            'is_admin' => false, // default is regular user
            'is_approved' => false,
        ]);

        // Store files
    if ($request->hasFile('profile_picture')) {
        $profilePath = $request->file('profile_picture')->store('uploads/profile-pictures', 'public');
        $user->attachments()->create([
            'path' => $profilePath,
            'attachable_id' => $user->id,
            'attachable_type' => 'User'  ,
            'category' => 'profile_image'  ,
        ]);
    }

    if ($request->hasFile('national_id_image')) {
        $nationalIdPath = $request->file('national_id_image')->store('uploads/national_ids', 'public');
        $user->attachments()->create([
            'path' => $nationalIdPath,
            'attachable_id' => $user->id,
            'attachable_type' => 'User'  ,
            'category' => 'national_id'  ,
        ]);
         // ✅ Add this line to include full URL in response
        $user->national_id_url = asset('storage/' . $nationalIdPath);
    }
        // send email verification link
        $user->sendEmailVerificationNotification();

        // Create JWT token for the user
        $token = auth('api')->login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully, Please verify your email',
            'user' => $user,
            'is_admin' => $user->is_admin,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 201);
    }
    //Login:
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Try to create a token with given credentials
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }
        
        
    // Get the authenticated user
    $user = auth('api')->user();

    // Check if email is verified
    if (is_null($user->email_verified_at) && !$user->is_admin) {
        // logout immediately so token is not usable
        return response()->json([
            'error' => 'Email not verified. Please check your inbox for the verification link.'
        ], 403);
    }
    // Check if approved by admin
    if (! $user->is_approved) {
        auth('api')->logout();

    return response()->json([
        'error' => 'Your account is pending approval by admin.'], 403);
    }

        // If successful, return token and user info
        return response()->json([
            'message'=>'Registered Successfully.',
                'status' => 'success',
    'user' => [
        'id' => auth('api')->user()->id,
        'name' => auth('api')->user()->name,
        'email' => auth('api')->user()->email,
        'is_admin' => auth('api')->user()->is_admin,
    ],
    'access_token' => $token,
    'token_type' => 'bearer',
    'expires_in' => auth('api')->factory()->getTTL() * 60,
            
        ]);
    }

    //Logout:

     /**
     * Log the user out (Invalidate the token).
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Invalidate the current token
        auth('api')->logout();

        // Return a success message
        return response()->json(['message' => 'Successfully logged out']);
    }

     //Forgot Password
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset email sent successfully.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Could not send password reset email.'
        ], 500);
    }


    //Reset Password
     /**
     * Reset the user's password using the token sent via email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function resetPassword(Request $request)
    {
        // 1. Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Reset the password using Laravel's built-in functionality
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        // 3. Return a response based on the status
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset successfully.']);
        }

        // Handle errors (e.g., invalid or expired token)
        return response()->json(['message' => 'The password reset token is invalid or has expired.'], 400);
    }




}
