<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .invoice-box {
            background: #fff;
            padding: 25px;
            max-width: 800px;
            margin: auto;
            border-radius: 8px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .brand {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .brand img {
            height: 50px;
        }

        .text-right {
            text-align: right;
        }

        .section {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border-bottom: 1px solid #eee;
            padding: 8px;
        }

        .total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
            color: green;
        }

        .payment {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>

<body>

<div class="invoice-box">

    <!-- HEADER -->
    <div class="header">
        <div class="brand">
            <img src="{{ public_path('assets/algospacelogo.png') }}">
            <div>
                <strong>AlgoSpace Cyber</strong><br>
                <small>Professional Digital Services</small>
            </div>
        </div>

        <div class="text-right">
            <strong>Invoice</strong><br>
            #{{ $invoice_number }}
        </div>
    </div>

    <!-- CUSTOMER -->
    <div class="section">
        <strong>Bill To</strong><br>
        {{ $customer['name'] }}<br>
        {{ $customer['email'] }}<br>
        {{ $customer['phone'] }}
    </div>

    <!-- ITEMS -->
    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price'], 2) }}</td>
                        <td>{{ number_format($item['line_total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            TOTAL: KES {{ number_format($total, 2) }}
        </div>
    </div>

    <!-- PAYMENT -->
    <div class="payment">
        <strong>Payment Details</strong><br><br>

        Paybill: 542542<br>
        Account: 608755<br>
        Amount: KES {{ number_format($total, 2) }}
    </div>

</div>

</body>
</html>