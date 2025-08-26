<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Api\SubscriptionController as AdminSubscriptionController;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ProfileController;

// Auth Routes (زي ما هي)
// Route::prefix('auth')->group(function () {
//     Route::post('/register', [AuthController::class, 'register']);
//     Route::post('/login',    [AuthController::class, 'login']);
//     Route::post('/logout',   [AuthController::class, 'logout'])->middleware('auth:api');
//     Route::post('/refresh',  [AuthController::class, 'refresh'])->middleware('auth:api');
//     Route::get('/me',        [AuthController::class, 'me'])->middleware('auth:api');
// });

// Post Routes (بدون middleware عشان تقدر تختبرهم دلوقتي)
Route::post('/posts',      [PostController::class, 'store']);
Route::get('/posts/mine',  [PostController::class, 'myPosts']);
Route::get('/posts',       [PostController::class, 'index']);
Route::get('/posts/{id}',  [PostController::class, 'show']);


// APIs are tested by Abdalrahman and Done


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



//Route::apiResource('subscriptions', AdminSubscriptionController::class);

// Get all subscription plans
Route::get('/subscriptions', [SubscriptionController::class, 'active']);

// Initialize Paymob payment for a subscription
Route::post('/subscriptions/pay', [PaymentController::class, 'checkout']);

// Optional: Webhook / Callback from Paymob
Route::post('/payment/callback', [PaymentController::class, 'paymentCallback']);

Route::prefix('posts')->group(function () {
    // Get all comments for a post
    Route::get('{postId}/comments', [CommentController::class, 'index']); 

    // Actions on comments under a post
    Route::prefix('{postId}/comments')->group(function () {
        Route::post('/', [CommentController::class, 'store']); // ✅ POST for new comment
        Route::get('{id}', [CommentController::class, 'show']);
        Route::put('{id}', [CommentController::class, 'update']);
        Route::delete('{id}', [CommentController::class, 'destroy']);
    });
});

Route::post('/reaction', [ReactionController::class, 'react']);
Route::delete('/reaction', [ReactionController::class, 'remove']);
Route::get('/reaction', [ReactionController::class, 'getReactions']);
    

Route::post('/users/{id}/approve', [ApprovalController::class, 'approve']);
Route::post('/users/{id}/reject', [ApprovalController::class, 'reject']);

Route::apiResource('users', UserController::class);
Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);
Route::post('users/{user}/remove-role', [UserController::class, 'removeRole']);



Route::get('/revenue/subscriptions', [SettingsController::class, 'getRevenueBySubscription']);


Route::get('/stats/users-this-month', [StatsController::class, 'getUsersThisMonth']);
Route::get('/stats/posts-today', [StatsController::class, 'getPostsToday']);
Route::post('/change-password', [AuthController::class, 'changePassword']);


Route::post('/settings', [SettingsController::class, 'store']);
Route::put('/settings/{key}', [SettingsController::class, 'update']);
Route::get('/profile/{id}', [ProfileController::class, 'show']);
Route::put('/profile/{id}', [ProfileController::class, 'update']);
Route::get('/settings', [SettingsController::class, 'index']);   
Route::delete('/settings/{id}', [SettingsController::class, 'destroy']); 

