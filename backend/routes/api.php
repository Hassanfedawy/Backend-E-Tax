<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ProfileController;

Route::get('/revenue/subscriptions', [SettingsController::class, 'getRevenueBySubscription']);


Route::get('/stats/users-this-month', [StatsController::class, 'getUsersThisMonth']);
Route::get('/stats/posts-today', [StatsController::class, 'getPostsToday']);


Route::post('/settings', [SettingsController::class, 'store']);
Route::put('/settings/{key}', [SettingsController::class, 'update']);
Route::get('/profile/{id}', [ProfileController::class, 'show']);
Route::put('/profile/{id}', [ProfileController::class, 'update']);

