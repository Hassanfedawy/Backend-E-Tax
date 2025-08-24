<?php
use App\Http\Controllers\Api\SubscriptionController as AdminSubscriptionController;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Api\ApprovalController;
use Illuminate\Support\Facades\Route;

Route::apiResource('subscriptions', AdminSubscriptionController::class);

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
