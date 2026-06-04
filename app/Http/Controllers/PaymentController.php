<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            env('RAZORPAY_KEY_ID'),
            env('RAZORPAY_KEY_SECRET')
        );
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        try {
            $amountInPaise = $validated['amount'] * 100;

            $razorpayOrder = $this->razorpay->order->create([
                'receipt' => 'order_' . uniqid(),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'notes' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ],
            ]);

            $payment = Payment::create([
                'user_id' => auth()->id(),
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $validated['amount'],
                'currency' => 'INR',
                'status' => 'pending',
                'metadata' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ],
            ]);

            return response()->json([
                'order_id' => $razorpayOrder['id'],
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'key' => env('RAZORPAY_KEY_ID'),
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create payment order',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        try {
            $attributes = [
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            $payment = Payment::where('razorpay_order_id', $validated['razorpay_order_id'])
                ->firstOrFail();

            $payment->update([
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
                'status' => 'success',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment successful',
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());

            $payment = Payment::where('razorpay_order_id', $validated['razorpay_order_id'])
                ->first();

            if ($payment) {
                $payment->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function success()
    {
        return view('payment.success');
    }

    public function failure()
    {
        return view('payment.failure');
    }

    public function webhook(Request $request)
    {
        try {
            $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
            $razorpaySignature = $request->header('X-Razorpay-Signature');
            
            $this->razorpay->utility->verifyWebhookSignature(
                $request->getContent(),
                $razorpaySignature,
                $webhookSecret
            );

            $event = $request->get('event');
            
            if ($event === 'payment.captured') {
                $paymentData = $request->get('payload')['payment']['entity'];
                
                $payment = Payment::where('razorpay_payment_id', $paymentData['id'])
                    ->first();

                if ($payment) {
                    $payment->update(['status' => 'success']);
                }
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Webhook verification failed: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 400);
        }
    }
}
