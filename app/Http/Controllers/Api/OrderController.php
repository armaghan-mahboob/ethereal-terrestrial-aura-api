<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use App\Mail\AdminOrderNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required_without:email', 'nullable', 'email'],
            'email' => ['required_without:customer_email', 'nullable', 'email'],            
            'customer_phone' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'subtotal' => ['required', 'numeric'],
            'shipping' => ['nullable', 'numeric'],
            'total' => ['required', 'numeric'],
            'payment_method' => ['nullable', 'string', 'in:cod,stripe,apple_pay'],
        ]);

        $order = Order::create([
            'id' => 'ORD-' . Str::upper(Str::random(8)),
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? $validated['email'],
            'customer_phone' => $validated['customer_phone'] ?? $validated['phone'] ?? null,
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'items' => $validated['items'],
            'subtotal' => $validated['subtotal'],
            'shipping' => $validated['shipping'] ?? 0,
            'total' => $validated['total'],
            'payment_method' => $validated['payment_method'] ?? 'cod',
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        try {
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            Mail::to(env('ADMIN_EMAIL'))->send(new AdminOrderNotificationMail($order));
        } catch (\Exception $e) {
            Log::error('Order email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'payment_method' => $order->payment_method,
        ], 201);
    }

    public function index(Request $request)
    {
        $orders = Order::query()
            ->when($request->email, fn ($q) => $q->where('customer_email', $request->email))
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function show(Order $order)
    {
        return response()->json($order);
    }
}
