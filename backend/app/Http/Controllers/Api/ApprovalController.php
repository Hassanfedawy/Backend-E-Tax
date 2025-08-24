<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class ApprovalController extends Controller
{
    public function approve($id)
    {

        $user = User::find($id);
        $setting=Setting::first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (is_null($user->email_verified_at)) {
            return response()->json([
                'message' => 'User email is not verified. Cannot approve.'
            ], 400);
        }

        $user->is_approved = true;
        $user->available_posts=setting->available_posts;
        $user->save();

        return response()->json([
            'message' => 'User approved successfully',
            'user' => $user
        ]);
    }

    public function reject($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_approved = false;
        $user->save();

        return response()->json([
            'message' => 'User rejected successfully',
            'user' => $user
        ]);
    }
}
