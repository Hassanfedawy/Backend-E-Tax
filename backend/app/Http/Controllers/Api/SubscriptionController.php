<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;



class SubscriptionController extends Controller
{
    public function index()
    {
        // dd("1");
        // Show all plans (with active/inactive status)
        return response()->json(Subscription::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
            'features' => 'nullable|string',
            'is_active'=> 'boolean'
        ]);

        $subscription = Subscription::create($request->all());

        return response()->json(['message' => 'Subscription created', 'data' => $subscription]);
    }

    public function show(int $id)
    {
     $subscription = Subscription::findOrFail($id);
    //  where('id', $id)
    //  where('name', $name)

        return response()->json($subscription);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'cost'    => 'sometimes|required|numeric|min:0',
            'features' => 'nullable|string',
            'is_active'=> 'boolean'
        ]);

        $subscription->update($request->all());

        return response()->json(['message' => 'Subscription updated', 'data' => $subscription]);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'Subscription deleted']);
    }
}


