<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .title { color: #2563eb; font-size: 32px; font-weight: bold; }
        .info { margin-bottom: 40px; width: 100%; border-collapse: collapse; }
        .info td { vertical-align: top; width: 50%; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .table th { background: #f9fafb; text-align: left; padding: 12px; border-bottom: 2px solid #eee; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; }
        .totals { float: right; width: 250px; }
        .totals div { display: flex; justify-content: space-between; padding: 8px 0; }
        .grand-total { border-top: 2px solid #2563eb; font-size: 20px; font-weight: bold; color: #2563eb; }
        .status { padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .pending { background: #fef3c7; color: #92400e; }
        .approved { background: #dbeafe; color: #1e40af; }
        .completed { background: #dcfce7; color: #166534; }
        .rejected { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="title">SCHOOL SHOP</div>
            <div style="text-align: right;">
                <strong>Invoice #{{ $order->id }}</strong><br>
                Date: {{ $order->created_at->format('M d, Y') }}<br>
                Status: <span class="status {{ $order->status }}">{{ $order->status }}</span>
            </div>
        </div>

        <table class="info">
            <tr>
                <td>
                    <strong>Bill To:</strong><br>
                    {{ $order->user->name }}<br>
                    {{ $order->user->email }}<br>
                    Role: {{ ucfirst($order->role_type) }}
                </td>
                <td style="text-align: right;">
                    <strong>School Name</strong><br>
                    123 Education Ave<br>
                    City, Country<br>
                    support@school.com
                </td>
            </tr>
        </table>

        <table class="info">
            <tr>
                <td>
                    <strong>Payment Method:</strong><br>
                    {{ ucwords(str_replace('_', ' ', $order->payment_method ?? $order->payment_provider ?? 'bank_transfer')) }}
                </td>
                <td style="text-align: right;">
                    <strong>Payment Status:</strong><br>
                    {{ ucwords(str_replace('_', ' ', $order->payment_status ?? 'pending')) }}<br>
                    @if($order->transfer_reference)
                        Reference: {{ $order->transfer_reference }}
                    @endif
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>₦{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₦{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div>
                <span>Subtotal:</span>
                <span>₦{{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="grand-total">
                <span>Total:</span>
                <span>₦{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <div style="margin-top: 100px; text-align: center; font-size: 12px; color: #999;">
            Thank you for your purchase from the School Shop!
        </div>
    </div>
</body>
</html>
