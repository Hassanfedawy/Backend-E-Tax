<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    private $apiKey;
    private $integrationId;

    public function __construct()
    {
        // Get credentials from config/paymob.php
        $this->apiKey = config('paymob.api_key');
        $this->integrationId = config('paymob.integration_id');
    }

    // Step 1: Get Auth Token
    public function getAuthToken()
    {
        $response = Http::withOptions(['verify' => false])
            ->post("https://accept.paymob.com/api/auth/tokens", [
                "api_key" => $this->apiKey
            ])->throw(false);

        $data = $response->json();
        Log::info('Paymob Auth Token', $data);

        return $data['token'] ?? null;
    }

    // Step 2: Create Order
    public function createOrder($authToken, $amount, $billingData)
    {
        $response = Http::withOptions(['verify' => false])->post("https://accept.paymob.com/api/ecommerce/orders", [
            "auth_token" => $authToken,
            "delivery_needed" => false,
            "amount_cents" => $amount * 100,
            "currency" => "EGP",
            "merchant_order_id" => "test_order_" . time(),
            "items" => [],
            "shipping_data" => $billingData
        ])->throw(false);

        $data = $response->json();
        Log::info('Paymob Order Response', $data);

        return $data['response'] ?? [];
    }

    // Step 3: Get Payment Key
    public function getPaymentKey($authToken, $orderId, $billingData, $amount)
    {
        if (!$orderId) {
            Log::error('getPaymentKey called with empty orderId', [
                'billingData' => $billingData,
                'amount' => $amount
            ]);
            return null;
        }

        $response = Http::withOptions(['verify' => false])->post("https://accept.paymob.com/api/acceptance/payment_keys", [
            "auth_token" => $authToken,
            "amount_cents" => $amount * 100,
            "expiration" => 3600,
            "order_id" => $orderId,
            "billing_data" => $billingData,
            "currency" => "EGP",
            "integration_id" => $this->integrationId
        ])->throw(false);

        $data = $response->json();
        Log::info('Paymob Payment Key Raw Response', $data);

        return $data['token'] ?? $data['response']['token'] ?? null;
    }
}
