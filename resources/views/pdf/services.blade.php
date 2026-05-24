<!DOCTYPE html>
<html>
<head>
    <title>Services PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 5px; }
        h4 { background: #eee; padding: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    </style>
</head>
<body>

<h2>ALGOSPACE CYBER</h2>
<p style="text-align:center;">SERVICES & PRICE LIST</p>
<p style="text-align:center;">As of: {{ $printDate }}</p>

@foreach($grouped as $category => $items)
    <h4>{{ strtoupper($category) }}</h4>

    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>Price</th>
                <th>Unit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->price }}</td>
                    <td>{{ $service->unit }}</td>
                    <td>
                        {{ $service->is_active ? 'ONLINE + IN-SHOP' : 'IN-SHOP ONLY' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

</body>
</html>