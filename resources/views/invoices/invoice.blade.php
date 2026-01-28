<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .company {
            font-size: 16px;
            font-weight: bold;
        }

        .muted {
            color: #555;
        }

        .invoice-title {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
        }

        .right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            font-size: 11px;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <table width="100%">
            <tr>
                <td width="60%">
                    <div class="company">ALGOSPACE CYBER & TECH SERVICES</div>
                    <div class="muted">Ground Floor Villa Nova Building, Kapsokwony, Bungoma County</div>
                    <div class="muted">Phone: 0112 514 440</div>
                </td>
                <td width="40%" class="invoice-title">
                    <h2>{{ $title ?? 'INVOICE' }}</h2>
                    <div><strong>No:</strong> {{ $invoice_no ?? '-' }}</div>
                    <div><strong>Date:</strong> {{ $date ?? now()->format('Y-m-d') }}</div>
                    <div><strong>Due:</strong> {{ $due_date ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- CUSTOMER INFO --}}
    <table width="100%">
        <tr>
            <td width="50%">
                <strong>Billed To</strong><br>
                {{ $customer['name'] ?? 'Walk-in Customer' }}<br>
                {{ $customer['email'] ?? '' }}<br>
                {{ $customer['phone'] ?? '' }}
            </td>
            <td width="50%">
                <strong>Status</strong><br>
                {{ $status ?? 'Preview' }}
            </td>
        </tr>
    </table>

    {{-- ITEMS --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th class="right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ number_format($item['price'], 2) }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td class="right">{{ number_format($item['line_total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="right">TOTAL</td>
                <td class="right">KES {{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        Thank you for your business.<br>
        Payments via Cash | M-Pesa | Bank Transfer
    </div>

</body>
</html>
