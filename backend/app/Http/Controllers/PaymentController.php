<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymobService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymob;

    public function __construct(PaymobService $paymob)
    {
        $this->paymob = $paymob;
    }

    // Checkout for non-auth users with hardcoded values
    public function checkout()
    {
        try {
            $amount = 100; // Hardcoded amount in EGP

            $billingData = [
                "first_name" => "Guest",
                "last_name" => "User",
                "email" => "guest@example.com",
                "phone_number" => "+201000000000",
                "city" => "Cairo",
                "country" => "EG"
            ];

            // Step 1: Get Auth Token
            $authToken = $this->paymob->getAuthToken();
            if (!$authToken) {
                Log::error('Paymob auth token missing');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment checkout error',
                    'error' => 'Auth token not returned'
                ]);
            }

            // Step 2: Create Order
            $orderResponse = $this->paymob->createOrder($authToken, $amount, $billingData);
            $orderId = $orderResponse['id'] ?? 1;

            if (!$orderId) {
                Log::error('Paymob order creation failed', ['response' => $orderResponse]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment checkout error',
                    'error' => 'Order ID missing'
                ]);
            }

            // Step 3: Get Payment Key
            $paymentKey = $this->paymob->getPaymentKey($authToken, $orderId, $billingData, $amount);
            if (!$paymentKey) {
                Log::error('Paymob payment key missing', ['orderId' => $orderId]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment checkout error',
                    'error' => 'Payment token not returned'
                ]);
            }

            // Step 4: Return Payment URL
            $orderUrl = $orderResponse['order_url'] ?? null;

            return response()->json([
                'status' => 'success',
                'payment_url' => $orderUrl,
                'payment_token' => $paymentKey
            ]);

        } catch (\Exception $e) {
            Log::error('Payment checkout error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Payment checkout error',
                'error' => $e->getMessage()
            ]);
        }
    }
}
