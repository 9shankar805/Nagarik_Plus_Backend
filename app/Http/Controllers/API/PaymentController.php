<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MockPaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $gatewayService;

    public function __construct(MockPaymentGateway $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'payable_type' => 'required|string', // e.g., App\Models\FormSubmission
            'payable_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'gateway' => 'required|in:esewa,khalti,connectips',
        ]);

        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'payable_type' => $request->payable_type,
            'payable_id' => $request->payable_id,
            'amount' => $request->amount,
            'payment_method' => $request->gateway,
            'status' => 'pending',
            'transaction_code' => 'MOCK-' . strtoupper(Str::random(10)),
        ]);

        $response = $this->gatewayService->initiatePayment($transaction->amount, $transaction->id, $request->gateway);

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated.',
            'data' => [
                'transaction_id' => $transaction->id,
                'payment_url' => $response['payment_url']
            ]
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'gateway_transaction_code' => 'required|string', // The token/code returned by gateway
        ]);

        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($transaction->status === 'completed') {
            return response()->json(['success' => true, 'message' => 'Already verified.']);
        }

        $verification = $this->gatewayService->verifyPayment(
            $transaction->payment_method, 
            $request->gateway_transaction_code, 
            $transaction->amount
        );

        if ($verification['success']) {
            $transaction->update([
                'status' => 'completed',
                'transaction_code' => $request->gateway_transaction_code,
                'gateway_response' => $verification['gateway_response']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment completed successfully.',
                'transaction' => $transaction
            ]);
        } else {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => $verification['gateway_response']
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed.',
            ], 400);
        }
    }

    // A mock page that simulates the external eSewa/Khalti login page
    public function mockGatewayView(Request $request)
    {
        $transactionId = $request->transaction_id;
        $gateway = $request->gateway;
        $amount = $request->amount;
        $mockToken = 'TOKEN-' . strtoupper(Str::random(15));
        
        // Return a very simple HTML page to simulate payment success
        return response("
            <html>
            <body style='font-family: sans-serif; text-align: center; padding-top: 50px;'>
                <h2>Mock {$gateway} Payment Gateway</h2>
                <p>Amount: Rs. {$amount}</p>
                <button onclick='alert(\"Return this token to the app to verify: {$mockToken}\")' style='padding:10px 20px; background: #4CAF50; color: white; border: none; cursor:pointer;'>Pay Now (Simulate Success)</button>
            </body>
            </html>
        ");
    }
}
