<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Subscription;
use App\Http\Requests\SettingStoreRequest;

class SettingsController extends Controller
{
    public function store(SettingStoreRequest $request)
    {
        
        $setting = Setting::create([
            'key'   => $request->key,
            'value' => $request->value,
            'label' => $request->label,
        ]);

        return response()->json([
            'message' => 'Setting created successfully',
            'data'    => $setting
        ], 201);
    }

    /**
     * Update an existing setting by key.
     */
    public function update(Request $request, $key)
    {
        
        $request->validate([
            'value' => 'required|string',
            'label' => 'sometimes|string', 
        ]);

        // Find setting by key
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return response()->json(['message' => 'Setting not found'], 404);
        }

        
        $setting->value = $request->value;
        if ($request->has('label')) {
            $setting->label = $request->label;
        }
        $setting->save();

        return response()->json([
            'message' => 'Setting updated successfully',
            'data'    => $setting
        ]);
    }
    
    /**
     * Get revenue per subscription plan.
     */
    public function getRevenueBySubscription()
    {
        // Fetch all subscriptions with user count
        $subscriptions = Subscription::withCount('users')->get();

        // Calculate revenue for each plan
        $revenues = $subscriptions->map(function ($subscription) {
            return [
                'plan'     => $subscription->name,
                'users'    => $subscription->users_count,
                'cost'     => $subscription->cost,
                'revenue'  => $subscription->users_count * $subscription->cost,
            ];
        });

        return response()->json([
            'message' => 'Revenue calculated successfully',
            'data'    => $revenues
        ]);
    }

}
