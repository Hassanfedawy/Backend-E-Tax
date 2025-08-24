<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Return all active subscriptions.
     */
    public function active()
    {
        // Fetch all subscriptions where is_active is true
        $activeSubscriptions = Subscription::where('is_active', true)->get();

        // Return as JSON
        return response()->json([
            'status' => 'success',
            'data' => $activeSubscriptions
        ]);
    }
}
