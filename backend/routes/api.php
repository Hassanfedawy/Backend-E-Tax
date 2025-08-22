<?php
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;


Route::get('/checkout/{subscriptionId}', [PaymentController::class, 'checkout'])->name('checkout');

Route::post('/paymob/callback', [PaymentController::class, 'callback']);

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
    
