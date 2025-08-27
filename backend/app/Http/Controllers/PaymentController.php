<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    /**
     * Handle the checkout process using Paymob.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        // ✅ Ensure user is authenticated
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // ✅ Validate subscription
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id'
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);

        // ✅ Fetch user data dynamically
        $userId    = $user->id;
        $userEmail = $user->email;
        $userName  = $user->name;
        $userPhone = $user->phone ?? '01000000000'; // fallback if phone not stored

        // ✅ Step 1: Get Paymob Auth Token
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
            'amount_cents' => intval($subscription->cost * 100),
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
            'amount_cents' => intval($subscription->cost * 100),
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

        // ✅ Step 4: Save Transaction with real user_id
        Transaction::create([
            'user_id' => $userId,
            'subscription_id' => $subscription->id,
            'paymob_order_id' => $orderId,
            'amount' => $subscription->cost,
            'status' => 'pending'
        ]);

        return response()->json([
            'payment_url' => "https://accept.paymob.com/api/acceptance/iframes/" . env('PAYMOB_IFRAME_ID') . "?payment_token=" . $paymentToken
        ]);
    }

    /**
     * Handle the callback after payment completion.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
                if ($user && $transaction->subscription) {
                    $user->available_posts += $transaction->subscription->no_of_posts;
                    $user->save();
                }
            } else {
                $transaction->status = 'failed';
                $transaction->save();
            }
        }

        return response()->json(['message' => 'Callback processed']);
    }
}
