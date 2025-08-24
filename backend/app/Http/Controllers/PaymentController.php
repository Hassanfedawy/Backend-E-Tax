<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function checkout(Request $request)
{

        // ✅ Dummy user data for testing
    $userId     = 1; // Hardcoded dummy user ID
    $userEmail  = 'testuser@example.com';
    $userName   = 'Test User';
    $userPhone  = '01000000000';
    
    // ✅ Validate subscription
    $request->validate([
        'subscription_id' => 'required|exists:subscriptions,id'
    ]);

    $subscription = Subscription::find($request->subscription_id);



    // ✅ Step 1: Get Paymob Auth Token (disable SSL for testing)
    $authResponse = Http::withOptions(['verify' => false])->post('https://accept.paymob.com/api/auth/tokens', [
        'api_key' => env('PAYMOB_API_KEY'),
    ]);

    if (!$authResponse->successful()) {
        \Log::error('Paymob Auth Token Request Failed', ['response' => $authResponse->json()]);
        return response()->json(['error' => 'Failed to authenticate with Paymob'], 500);
    }

    $authToken = $authResponse['token'];

    // ✅ Step 2: Create Order on Paymob
    $orderResponse = Http::withOptions(['verify' => false])->post('https://accept.paymob.com/api/ecommerce/orders', [
        'auth_token' => $authToken,
        'delivery_needed' => false,
        'amount_cents' => $subscription->cost * 100,
        'currency' => 'EGP',
        'items' => [],
    ]);

    if (!$orderResponse->successful()) {
        \Log::error('Paymob Order Creation Failed', ['response' => $orderResponse->json()]);
        return response()->json(['error' => 'Failed to create order on Paymob'], 500);
    }

    $orderId = $orderResponse['id'];

    // ✅ Step 3: Generate Payment Key
    $paymentKeyResponse = Http::withOptions(['verify' => false])->post('https://accept.paymob.com/api/acceptance/payment_keys', [
        'auth_token' => $authToken,
        'amount_cents' => $subscription->cost * 100,
        'currency' => 'EGP',
        'order_id' => $orderId,
        'billing_data' => [
            "apartment" => "NA",
            "email" => $userEmail,
            "floor" => "NA",
            "first_name" => $userName,
            "street" => "NA",
            "building" => "NA",
            "phone_number" => $userPhone,
            "shipping_method" => "NA",
            "postal_code" => "NA",
            "city" => "Cairo",
            "country" => "EG",
            "last_name" => $userName,
            "state" => "NA"
        ],
        'integration_id' => env('PAYMOB_INTEGRATION_ID'),
    ]);

    if (!$paymentKeyResponse->successful()) {
        \Log::error('Paymob Payment Key Request Failed', ['response' => $paymentKeyResponse->json()]);
        return response()->json(['error' => 'Failed to generate payment key'], 500);
    }

    $paymentToken = $paymentKeyResponse['token'];

    // ✅ Step 4: Save Transaction with dummy user_id
    Transaction::create([
        'user_id' => $userId, // Dummy ID for testing
        'subscription_id' => $subscription->id,
        'paymob_order_id' => $orderId,
        'amount' => $subscription->cost,
        'status' => 'pending'
    ]);

return response()->json([
    'payment_url' => "https://accept.paymob.com/api/acceptance/iframes/" . env('PAYMOB_IFRAME_ID') . "?payment_token=" . $paymentToken
]);

}


    public function paymentCallback(Request $request)
    {
        $orderId = $request->input('order');
        $success = $request->input('success') == "true";

        $transaction = Transaction::where('paymob_order_id', $orderId)->first();

        if ($transaction) {
            if ($success) {
                $transaction->status = 'paid';
                $transaction->save();

                $user = $transaction->user;
                $user->available_posts += $transaction->subscription->no_of_posts;
                $user->save();
            } else {
                $transaction->status = 'failed';
                $transaction->save();
            }
        }

        return response()->json(['message' => 'Callback processed']);
    }
}
