<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\VerificationController;

// Public routes (no authentication needed)
Route::post('/register', [AuthController::class, 'register']);  // an identifier when a post requests happens go to the register method in authcontroller to implement it
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

// ===================
// Email Verification
// ===================

// Verify email (user clicks link from email)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // mark email as verified
        return response()->json(['message' => 'Email verified successfully!']);
    })->middleware(['signed'])->name('verification.verify');


    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');



// Protected routes (JWT authentication required)
Route::middleware('auth:api')->group(function () {          //will check for a valid JWT token in the request header. If the token is missing or invalid, it will return an "Unauthorized" error.
    
    Route::post('/user', [AuthController::class, 'logout']); 
    Route::post('/logout', [AuthController::class, 'logout']);     //in protected routes because user must be authenticated (have a valid token) to log out. 



    // Resend verification link
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }
        
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    })->middleware('throttle:6,1')->name('verification.send');

});
