@include('invoices.invoice', [
    'title'      => 'INVOICE',
    'status'     => ucfirst($invoice->status),
    'invoice_no' => $invoice->invoice_no,
    'date'       => $invoice->created_at->format('Y-m-d'),
    'due_date'   => $invoice->due_date,
    'customer'   => [
        'name'  => $invoice->customer->name ?? 'Walk-in',
        'email' => $invoice->customer->email ?? '',
        'phone' => $invoice->customer->phone ?? '',
    ],
    'items' => $invoice->items->map(fn ($i) => [
        'name'       => $i->service_name,
        'price'      => $i->unit_price,
        'quantity'   => $i->quantity,
        'line_total' => $i->line_total,
    ])->toArray(),
    'total' => $invoice->total_amount
])
