<?php
use App\Http\Controllers\Api\ApprovalController;

Route::post('/users/{id}/approve', [ApprovalController::class, 'approve']);
Route::post('/users/{id}/reject', [ApprovalController::class, 'reject']);
