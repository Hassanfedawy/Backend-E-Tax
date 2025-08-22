<?php
use App\Http\Controllers\Api\UserController;

Route::apiResource('users', UserController::class);
Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);
Route::post('users/{user}/remove-role', [UserController::class, 'removeRole']);
