<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

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