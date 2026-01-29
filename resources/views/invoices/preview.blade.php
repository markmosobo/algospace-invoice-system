<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Invoice Preview' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; padding: 0; background-color: #f4f4f4;
        }

        .invoice-wrapper {
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .invoice-header h1 {
            color: #006400;
            font-size: 28px;
        }

        .invoice-meta p {
            margin: 2px 0;
            font-size: 14px;
        }

        /* Status */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .status-preview { background-color: #ff9800; }
        .status-paid { background-color: #4caf50; }
        .status-pending { background-color: #f44336; }

        /* Customer info */
        .customer-info {
            margin-bottom: 30px;
        }

        .customer-info h3 { font-size: 18px; color: #333; margin-bottom: 10px; }
        .customer-info p { margin: 3px 0; font-size: 14px; }

        /* Items Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table th, table td {
            padding: 12px 10px;
            text-align: left;
            font-size: 14px;
        }

        table th {
            background-color: #006400;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Totals */
        .totals {
            display: flex;
            justify-content: flex-end;
        }

        .totals table {
            width: 300px;
            border-collapse: collapse;
        }

        .totals td {
            padding: 8px;
            font-size: 14px;
        }

        .totals tr td:first-child {
            text-align: left;
        }

        .totals tr td:last-child {
            text-align: right;
            font-weight: bold;
        }

        /* Footer */
        .invoice-footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        @media(max-width: 600px) {
            .invoice-header { flex-direction: column; align-items: flex-start; }
            .invoice-meta { margin-top: 15px; }
        }
    </style>
</head>
<body>

<div class="invoice-wrapper">

    <!-- Header -->
    <div class="invoice-header">
        <h1>AlgoSpace Cyber & Tech Services</h1>
        <div class="invoice-meta">
            <p><strong>Invoice No:</strong> {{ $invoice_no }}</p>
            <p><strong>Date:</strong> {{ $date }}</p>
            <p><strong>Due Date:</strong> {{ $due_date }}</p>
        </div>
    </div>

    <!-- Status -->


    <!-- Customer Info -->
    <div class="customer-info">
        <h3>Bill To:</h3>
        <p><strong>Name:</strong> {{ $customer['name'] ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $customer['email'] ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $customer['phone'] ?? 'N/A' }}</p>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>Unit Price (KES)</th>
                <th>Qty</th>
                <th>Total (KES)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ number_format($item['price'], 2) }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['line_total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr>
                <td>Total</td>
                <td>KES {{ number_format($total, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="invoice-footer">
        Thank you for choosing AlgoSpace Cyber & Tech Services.<br>
        Contact: 0112514440 | algo.space@yahoo.com | Kapsokwony, Bungoma County
    </div>

</div>

</body>
</html>
