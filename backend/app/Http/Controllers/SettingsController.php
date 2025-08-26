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
 * Update an existing setting by ID.
 */
public function update(Request $request, $id)
{   
    $request->validate([
        'key'   => 'sometimes|string|unique:settings,key,' . $id,
        'value' => 'required|string',
        'label' => 'sometimes|string',
    ]);

    // Find setting by ID
    $setting = Setting::find($id);

    if (!$setting) {
        return response()->json(['message' => 'Setting not found'], 404);
    }

    // Prevent changing the key if it's "free_posts"
    if ($setting->key === 'free_posts' && $request->has('key') && $request->key !== $setting->key) {
        return response()->json(['message' => 'You cannot change the key of free_posts record'], 403);
    }

    // Update fields if allowed
    if ($request->has('key') && $setting->key !== 'free_posts') {
        $setting->key = $request->key;
    }

    $setting->value = $request->value; // always required

    if ($request->has('label')) {
        $setting->label = $request->label;
    }

    $setting->save();

    return response()->json([
        'message' => 'Setting updated successfully',
        'data'    => $setting
    ]);
}


    
public function getRevenueBySubscription()
{
    // Fetch all subscriptions with user count
    $subscriptions = \App\Models\Subscription::withCount('users')->get();

    // Calculate revenue for each plan
    $revenues = $subscriptions->map(function ($subscription) {
        return [
            'id'       => $subscription->id,  
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

    public function index()
    {
        $settings = Setting::all();

        return response()->json([
            'status' => true,
            'data'   => $settings
        ], 200);
    }

    /**
     * Delete a setting by id
     */
    public function destroy($id)
    {   
        $setting = Setting::find($id);

        if (!$setting) {
            return response()->json([
                'status'  => false,
                'message' => 'Setting not found'
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Setting deleted successfully'
        ], 200);
    }




}
