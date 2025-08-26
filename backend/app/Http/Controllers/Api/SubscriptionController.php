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
       $subscription= Subscription::all();

        return response()->json(['data' => $subscription]);
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'cost'    => 'required|numeric|min:0',
            'no_of_posts' => 'nullable|string',
            'is_active'=> 'boolean'
        ]);

        $subscription = Subscription::create($request->all());

        return response()->json(['message' => 'Subscription created', 'data' => $subscription]);
    }

    public function show(int $id)
    {
     $subscription = Subscription::find($id); //  NULL if not found
     if( $subscription === null ){
         return response()->json(['message' => 'Subscription not found'], 404);
     }
        return response()->json($subscription);
    }

    public function update(Request $request, string $id)
    {
        $subscription = Subscription::find($id);
        if( $subscription === null ){
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $validatedData =  $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'cost'    => 'sometimes|required|numeric|min:0',
            'no_of_posts' => 'nullable|integer',
            'is_active'=> 'boolean'
        ]);

        $subscription->update($validatedData);

        return response()->json(['message' => 'Subscription updated', 'data' => $subscription]);
    }

    public function destroy(string $id)
    {
        $subscription = Subscription::find($id);
        if( $subscription === null ){
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $subscription->delete();
        return response()->json(['message' => 'Subscription deleted']);
    }
}


