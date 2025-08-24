<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    private $apiKey;
    private $integrationId;
    private $iframeId;

    public function __construct()
    {
        $this->apiKey = config('paymob.api_key');
        $this->integrationId = config('paymob.integration_id');
        $this->iframeId = config('paymob.iframe_id'); // Optional: use for iframe
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
    $response = Http::withOptions(['verify' => false])
        ->withHeaders(['Authorization' => "Bearer $authToken"])
        ->post("https://accept.paymob.com/api/ecommerce/orders", [
            "delivery_needed" => false,
            "amount_cents" => $amount * 100,
            "currency" => "EGP",
            "merchant_order_id" => "order_" . time(),
            "items" => [],
            "shipping_data" => $billingData
        ])->throw(false);

    $data = $response->json();
    Log::info('Paymob Order Response', $data);

    return $data ?? [];
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

    $response = Http::withOptions(['verify' => false])
        ->withHeaders(['Authorization' => "Bearer $authToken"])
        ->post("https://accept.paymob.com/api/acceptance/payment_keys", [
            "amount_cents" => $amount * 100,
            "expiration" => 3600,
            "order_id" => $orderId,
            "billing_data" => $billingData,
            "currency" => "EGP",
            "integration_id" => $this->integrationId
        ])->throw(false);

    $data = $response->json();
    Log::info('Paymob Payment Key Response', $data);

    return $data['token'] ?? null;
}

    // Step 4: Get Payment URL (iframe)
    public function getPaymentUrl($paymentToken)
    {
        return "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";
    }
}
