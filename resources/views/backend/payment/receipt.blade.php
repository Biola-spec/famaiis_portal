<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; color: #222; }
        .title { font-size: 20px; margin-bottom: 10px; }
        .meta { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="title">School Fee Payment Receipt</div>
    <div class="meta">
        <strong>Reference:</strong> {{ $payment->reference }}<br>
        <strong>Date:</strong> {{ optional($payment->paid_at)->format('Y-m-d H:i:s') }}<br>
        <strong>Status:</strong> {{ strtoupper($payment->status) }}
    </div>

    <table>
        <tr>
            <th>Student</th>
            <td>{{ optional($payment->student)->name }}</td>
        </tr>
        <tr>
            <th>Paid By</th>
            <td>{{ optional($payment->payer)->name ?? optional($payment->student)->name }}</td>
        </tr>
        <tr>
            <th>Class</th>
            <td>{{ optional($payment->fee->studentClass)->name }}</td>
        </tr>
        <tr>
            <th>Term</th>
            <td>{{ optional($payment->fee)->term }}</td>
        </tr>
        <tr>
            <th>Session</th>
            <td>{{ optional($payment->fee)->session }}</td>
        </tr>
        <tr>
            <th>Fee Type</th>
            <td>{{ optional($payment->fee)->title }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>₦{{ number_format((float) $payment->amount, 2) }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ strtoupper($payment->payment_method ?? 'ONLINE') }}</td>
        </tr>
        <tr>
            <th>Transaction ID</th>
            <td>{{ $payment->transaction_id }}</td>
        </tr>
    </table>
</body>
</html>
