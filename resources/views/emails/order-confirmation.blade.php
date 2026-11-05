<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #000; font-size: 24px; margin-bottom: 20px;">Thank You for Your Order!</h1>
    
    <p>Dear {{ $order->customer_name }},</p>
    
    <p>Your order has been received and is being processed.</p>
    
    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <thead>
            <tr style="border-bottom: 2px solid #000;">
                <th style="text-align: left; padding: 8px;">Item</th>
                <th style="text-align: right; padding: 8px;">Qty</th>
                <th style="text-align: right; padding: 8px;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 8px;">{{ $item['name'] ?? 'Product' }}</td>
                    <td style="padding: 8px; text-align: right;">{{ $item['qty'] ?? 1 }}</td>
                    <td style="padding: 8px; text-align: right;">£{{ number_format($item['price'] ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="text-align: right; padding: 8px;"><strong>Subtotal:</strong></td>
            <td style="text-align: right; padding: 8px;">£{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="text-align: right; padding: 8px;"><strong>Shipping:</strong></td>
            <td style="text-align: right; padding: 8px;">£{{ number_format($order->shipping, 2) }}</td>
        </tr>
        <tr style="border-top: 2px solid #000;">
            <td style="text-align: right; padding: 8px;"><strong>Total:</strong></td>
            <td style="text-align: right; padding: 8px;"><strong>£{{ number_format($order->total, 2) }}</strong></td>
        </tr>
    </table>

    <p><strong>Order Number:</strong> {{ $order->id }}</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Payment method:</strong> {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>

    <p>Shipping to:<br>{{ $order->address }}<br>{{ $order->city }}, {{ $order->country }}</p>

    <p style="margin-top: 30px;">Thank you for shopping with INARAH.</p>
</body>
</html>
