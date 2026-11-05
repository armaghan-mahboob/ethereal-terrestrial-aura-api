<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function publishableKey()
    {
        return response()->json([
            'publishableKey' => config('services.stripe.key'),
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'string'],
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
        ]);

        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => $item['name'] ?? 'Product',
                    ],
                    'unit_amount' => (int) round(($item['price'] ?? 0) * 100),
                ],
                'quantity' => $item['qty'] ?? 1,
            ];
        }

        if ($order->shipping > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => 'Shipping',
                    ],
                    'unit_amount' => (int) round($order->shipping * 100),
                ],
                'quantity' => 1,
            ];
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $request->success_url . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $request->cancel_url,
                'customer_email' => $order->customer_email,
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

            $order->update([
                'stripe_checkout_session_id' => $session->id,
            ]);

            return response()->json([
                'checkoutUrl' => $session->url,
                'sessionId' => $session->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            return response()->json(['error' => 'Checkout failed'], 500);
        }
    }

    public function intentCreate(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
        ]);

        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        try {
            $intent = PaymentIntent::create([
                'amount' => (int) round($request->amount * 100),
                'currency' => 'gbp',
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

            $order->update([
                'stripe_payment_intent_id' => $intent->id,
            ]);

            return response()->json([
                'clientSecret' => $intent->client_secret,
                'paymentIntentId' => $intent->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe intent error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment intent failed'], 500);
        }
    }

    public function intentConfirm(Request $request)
    {
        $request->validate([
            'payment_intent_id' => ['required', 'string'],
        ]);

        try {
            $intent = PaymentIntent::retrieve($request->payment_intent_id);

            return response()->json([
                'status' => $intent->status,
                'paymentIntentId' => $intent->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe confirm error: ' . $e->getMessage());
            return response()->json(['error' => 'Confirmation failed'], 500);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        try {
            $session = Session::retrieve($request->session_id);

            return response()->json([
                'status' => $session->status,
                'payment_status' => $session->payment_status,
                'amount_total' => $session->amount_total / 100,
                'currency' => $session->currency,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe verify error: ' . $e->getMessage());
            return response()->json(['error' => 'Verification failed'], 500);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $orderId = $session->metadata->order_id ?? null;
                if ($orderId) {
                    $order = Order::where('id', $orderId)->first();
                    if ($order) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'confirmed',
                        ]);
                    }
                }
                break;

            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                $orderId = $intent->metadata->order_id ?? null;
                if ($orderId) {
                    $order = Order::where('id', $orderId)->first();
                    if ($order) {
                        $order->update([
                            'payment_status' => 'failed',
                        ]);
                    }
                }
                break;

            default:
                break;
        }

        return response()->json(['received' => true]);
    }
}
