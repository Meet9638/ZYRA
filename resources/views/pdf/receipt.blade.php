<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #d4af37; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #121212; letter-spacing: 2px; }
        .logo span { color: #d4af37; }
        .subtitle { color: #666; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .details-wrapper { width: 100%; margin-bottom: 30px; }
        .details-wrapper td { vertical-align: top; }
        h3 { color: #121212; font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-box { font-size: 13px; color: #555; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items th { background: #121212; color: #fff; text-align: left; padding: 12px; font-size: 13px; text-transform: uppercase; }
        table.items td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .totals-table { width: 40%; float: right; border-collapse: collapse; }
        .totals-table td { padding: 8px 12px; text-align: right; font-size: 14px; }
        .totals-table tr.total { font-weight: bold; font-size: 16px; border-top: 2px solid #121212; }
        .totals-table .label { color: #666; text-align: left; }
        .footer { text-align: center; color: #888; font-size: 12px; margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">NOIR <span>AI</span></div>
        <div class="subtitle">Official Order Receipt</div>
    </div>

    <table class="details-wrapper">
        <tr>
            <td style="width: 50%;">
                <h3>Order Information</h3>
                <div class="info-box">
                    <strong>Order Number:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                    <strong>Status:</strong> {{ ucfirst($order->status) }}
                </div>
            </td>
            <td style="width: 50%;">
                <h3>Billing & Shipping</h3>
                <div class="info-box">
                    <strong>Name:</strong> {{ $order->user->name ?? 'Customer' }}<br>
                    <strong>Contact:</strong> {{ $order->phone }}<br>
                    <strong>Address:</strong><br>
                    {!! nl2br(e($order->shipping_address)) !!}
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th>Size</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->size }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">₹{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: right;">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="label">Subtotal:</td>
            <td>₹{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Tax (10%):</td>
            <td>₹{{ number_format($order->tax, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Shipping:</td>
            <td>₹{{ number_format($order->shipping_cost, 2) }}</td>
        </tr>
        <tr class="total">
            <td class="label" style="color:#000;">Total Amount:</td>
            <td>₹{{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for shopping with ZYRA!</p>
        <p>If you have any questions about this receipt, please contact support@noirr.ai13@gmailcom</p>
    </div>
</body>
</html>

