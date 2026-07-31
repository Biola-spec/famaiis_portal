<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .receipt-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .receipt-header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .receipt-title { font-size: 24px; font-weight: bold; color: #333; }
        .school-info { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; }
        .total-row td { font-weight: bold; background: #f9f9f9; }
        .text-right { text-align: right; }
        .footer { margin-top: 40px; text-align: center; color: #777; font-size: 14px; }
    </style>
</head>
<body onload="window.print()">

<div class="receipt-box">
    <div class="receipt-header">
        <div>
            <div class="receipt-title">FEE RECEIPT</div>
            <p>Receipt No: <strong>{{ $payment->receipt_no }}</strong><br>
               Date: {{ date('d M Y', strtotime($payment->payment_date)) }}<br>
               Payment Method: {{ $payment->payment_method }}
            </p>
        </div>
        <div class="school-info">
            <h2>School Management System</h2>
            <p>123 School Avenue, Education City</p>
            <p>Phone: (123) 456-7890</p>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <p><strong>Student Name:</strong> {{ $payment->student->name }} ({{ $payment->student->id_no }})</p>
        <p><strong>Section:</strong> {{ $payment->section->name ?? 'N/A' }}</p>
        <p><strong>Term / Year:</strong> {{ $payment->feeStructure->term->name ?? 'Annual' }} / {{ $payment->feeStructure->year->name ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Fee Payment for Structure ID #{{ $payment->feeStructure->id }}</td>
                <td class="text-right">₦{{ number_format($payment->amount_paid, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right">Total Paid:</td>
                <td class="text-right">₦{{ number_format($payment->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right">Remaining Balance:</td>
                <td class="text-right">₦{{ number_format($payment->balance, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated receipt and requires no physical signature.</p>
        <p>Thank you for your payment!</p>
    </div>
</div>

</body>
</html>
