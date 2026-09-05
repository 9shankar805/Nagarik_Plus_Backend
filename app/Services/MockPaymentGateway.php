<?php

namespace App\Services;

use Illuminate\Support\Str;

class MockPaymentGateway
{
    /**
     * Simulate initiating a payment and returning a payment URL.
     */
    public function initiatePayment($amount, $transactionId, $gateway)
    {
        // In a real scenario, this would call eSewa or Khalti API
        // and return the redirect URL.
        
        $mockPaymentUrl = url("/api/v1/payments/mock-gateway?transaction_id={$transactionId}&gateway={$gateway}&amount={$amount}");
        
        return [
            'success' => true,
            'payment_url' => $mockPaymentUrl,
            'transaction_code' => 'MOCK-' . strtoupper(Str::random(10)),
        ];
    }

    /**
     * Simulate verifying a payment callback.
     */
    public function verifyPayment($gateway, $transactionCode, $amount)
    {
        // In a real scenario, we'd hit the eSewa/Khalti verification endpoint here.
        // We simulate a 90% success rate for testing purposes.
        $isSuccess = rand(1, 10) <= 9;

        return [
            'success' => $isSuccess,
            'gateway_response' => [
                'status' => $isSuccess ? 'COMPLETED' : 'FAILED',
                'amount' => $amount,
                'transaction_code' => $transactionCode,
                'timestamp' => now()->toIso8601String(),
            ]
        ];
    }
}
