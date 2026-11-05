<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #000; font-size: 24px; margin-bottom: 20px;">New Order Placed</h1>
    
    <p>A new order has been placed on your INARAH website.</p>
    
    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 8px;"><strong>Order #:</strong></td>
            <td style="padding: 8px;">{{ $order->id }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;"><strong>Customer:</strong></td>
            <td style="padding: 8px;">{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;"><strong>Email:</strong></td>
            <td style="padding: 8px;">{{ $order->customer_email }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;"><strong>Phone:</strong></td>
            <td style="padding: 8px;">{{ $order->customer_phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;"><strong>Total:</strong></td>
            <td style="padding: 8px;"><strong>£{{ number_format($order->total, 2) }}</strong></td>
        </tr>
    </table>

    <h3>Items:</h3>
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

    <p><strong>Shipping Address:</strong><br>{{ $order->address }}<br>{{ $order->city }}, {{ $order->country }}</p>
    
    <p><strong>Payment method:</strong> {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
    <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
    <p><strong>Order Status:</strong> {{ ucfirst($order->status) }}</p>

    <p style="margin-top: 30px;">Log in to your admin panel to process this order.</p>
</body>
</html>
