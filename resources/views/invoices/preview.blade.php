@include('invoices.invoice', [
    'title'      => 'INVOICE PREVIEW',
    'status'     => 'Preview',
    'invoice_no' => $invoice_no ?? null,
    'date'       => $date ?? now()->format('Y-m-d'),
    'due_date'   => $due_date ?? null,
    'customer'   => $customer,
    'items'      => $items,
    'total'      => $total
])
