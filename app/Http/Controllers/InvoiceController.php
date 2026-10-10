<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceSend;
use App\Services\AuditLogger;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display invoices and customers.
     */
    public function index()
    {
        $pendinginvoices = Invoice::with('customer')
            ->where('status', 'pending')
            ->get()
            ->map(function ($invoice) {
                $invoice->is_overdue = $invoice->due_date < now();

                return $invoice;
            });

        $invoices = Invoice::with('customer')
            ->where('status', 'paid')
            ->get();

        $customers = Customer::get();

        return response()->json([
            'pendinginvoices' => $pendinginvoices,
            'invoices' => $invoices,
            'customers' => $customers,
        ]);
    }

    /**
     * Create a walk-in invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pending,paid,overdue',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        $data = [
            'items' => $validated['items'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'] ?? null,
            'total_amount' => $validated['total_amount'],
        ];

        $invoice = $this->invoiceService->create(
            $data,
            $customer,
            'walkin',
            null
        );

        app(AuditLogger::class)->record(
            'invoice.created',
            "Walk-in invoice created (ID: {$invoice->id})",
            $invoice,
            [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'customer_id' => $invoice->customer_id,
                'total_amount' => $invoice->total_amount,
                'status' => $invoice->status,
                'source' => 'walkin',
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with(['customer', 'items'])
            ->findOrFail($id);

        return response()->json([
            'invoice' => $invoice,
        ]);
    }

    /**
     * Update an invoice.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'invoice_type' => 'required|in:sales,expense',
            'customer_id' => 'nullable|exists:customers,id',
            'vendor_name' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,paid,overdue',
        ]);

        $invoice = Invoice::findOrFail($id);

        $fields = [
            'invoice_type',
            'customer_id',
            'vendor_name',
            'invoice_date',
            'due_date',
            'total_amount',
            'status',
        ];

        $before = $invoice->only($fields);

        $invoice->update([
            'invoice_type' => $validated['invoice_type'],
            'customer_id' => $validated['invoice_type'] === 'sales'
                ? ($validated['customer_id'] ?? null)
                : null,
            'vendor_name' => $validated['invoice_type'] === 'expense'
                ? ($validated['vendor_name'] ?? null)
                : null,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'] ?? null,
            'total_amount' => $validated['total_amount'],
            'status' => $validated['status'] ?? null,
        ]);

        $invoice->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $invoice->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'invoice.updated',
                "Invoice updated (ID: {$invoice->id})",
                $invoice,
                [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'changes' => $changes,
                ],
                $request,
                auth('api')->id()
            );
        }

        return response()->json([
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Delete an invoice.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found',
            ], 404);
        }

        $invoiceId = $invoice->id;
        $invoiceNumber = $invoice->invoice_number;
        $customerId = $invoice->customer_id;
        $totalAmount = $invoice->total_amount;
        $status = $invoice->status;

        $invoice->delete();

        app(AuditLogger::class)->record(
            'invoice.deleted',
            "Invoice deleted (ID: {$invoiceId})",
            $invoice,
            [
                'invoice_id' => $invoiceId,
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
                'status' => $status,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }

    /**
     * Download the final invoice PDF.
     */
    public function download($id)
    {
        $invoice = Invoice::findOrFail($id);

        $path = storage_path('app/public/' . $invoice->pdf_path);

        if (!$invoice->pdf_path || !is_file($path)) {
            return response()->json([
                'message' => 'Invoice PDF not found',
            ], 404);
        }

        app(AuditLogger::class)->record(
            'invoice.downloaded',
            "Invoice PDF downloaded (ID: {$invoice->id})",
            $invoice,
            [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ],
            request(),
            auth('api')->id()
        );

        return response()->file($path);
    }

    /**
     * Print the final invoice PDF.
     */
    public function print($id)
    {
        $invoice = Invoice::findOrFail($id);

        $path = storage_path('app/public/' . $invoice->pdf_path);

        if (!$invoice->pdf_path || !is_file($path)) {
            return response()->json([
                'message' => 'Invoice PDF not found',
            ], 404);
        }

        app(AuditLogger::class)->record(
            'invoice.printed',
            "Invoice PDF opened for printing (ID: {$invoice->id})",
            $invoice,
            [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ],
            request(),
            auth('api')->id()
        );

        return response()->file($path);
    }

    /**
     * Close an unpaid invoice and flag the customer as risky.
     */
    public function closeUnpaid(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Paid invoices cannot be closed as unpaid.',
            ], 422);
        }

        $validated = $request->validate([
            'note' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use (
            $invoice,
            $validated,
            $request
        ) {
            $invoice->update([
                'status' => 'closed_unpaid',
                'closed_note' => $validated['note'] ?? null,
                'closed_at' => now(),
            ]);

            if ($invoice->customer_id) {
                $customer = Customer::whereKey($invoice->customer_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $customer->increment('unpaid_invoices_count');

                $customer->last_unpaid_invoice_at = now();
                $customer->is_risky = true;
                $customer->save();
            }

            app(AuditLogger::class)->record(
                'invoice.closed_unpaid',
                "Invoice closed as unpaid (ID: {$invoice->id})",
                $invoice,
                [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_id' => $invoice->customer_id,
                    'total_amount' => $invoice->total_amount,
                    'previous_status' => 'unpaid',
                    'new_status' => 'closed_unpaid',
                    'customer_flagged_risky' => (bool) $invoice->customer_id,
                ],
                $request,
                auth('api')->id()
            );
        });

        $invoice->refresh();

        return response()->json([
            'message' => 'Invoice closed as unpaid and customer flagged as risky.',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Generate a PDF and send the invoice by email.
     */
    public function sendPdf(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        $invoice = Invoice::with(['customer', 'items'])
            ->findOrFail($validated['invoice_id']);

        try {
            if (!$invoice->customer || !$invoice->customer->email) {
                return response()->json([
                    'message' => 'The invoice customer does not have an email address.',
                ], 422);
            }

            $pdf = Pdf::loadView('invoices.invoice', [
                'title' => 'INVOICE',
                'invoice_number' => $invoice->invoice_number,
                'date' => $invoice->created_at->format('Y-m-d'),
                'due_date' => optional($invoice->due_date)->format('Y-m-d'),
                'status' => $invoice->status,

                'customer' => [
                    'name' => $invoice->customer->name ?? 'Walk-in Customer',
                    'email' => $invoice->customer->email ?? '',
                    'phone' => $invoice->customer->phone ?? '',
                ],

                'items' => $invoice->items->map(function ($item) {
                    return [
                        'name' => $item->description,
                        'price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'line_total' => $item->line_total,
                    ];
                })->toArray(),

                'total' => $invoice->total_amount,
            ]);

            $pdfContent = $pdf->output();

            Mail::to($invoice->customer->email)
                ->send(new InvoiceMail($invoice, $pdfContent));

            InvoiceSend::create([
                'invoice_id' => $invoice->id,
                'channel' => 'email',
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            app(AuditLogger::class)->record(
                'invoice.email_sent',
                "Invoice email sent (ID: {$invoice->id})",
                $invoice,
                [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'channel' => 'email',
                    'status' => 'sent',
                ],
                $request,
                auth('api')->id()
            );

            return response()->json([
                'message' => 'Invoice sent successfully',
            ]);
        } catch (\Throwable $e) {
            Log::error('Invoice send failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            InvoiceSend::create([
                'invoice_id' => $invoice->id,
                'channel' => 'email',
                'status' => 'failed',
            ]);

            app(AuditLogger::class)->record(
                'invoice.email_failed',
                "Invoice email sending failed (ID: {$invoice->id})",
                $invoice,
                [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'channel' => 'email',
                    'status' => 'failed',
                    'error_type' => class_basename($e),
                ],
                $request,
                auth('api')->id()
            );

            return response()->json([
                'message' => 'Failed to send invoice',
            ], 500);
        }
    }

    /**
     * Return invoice delivery tracking.
     */
    public function tracking($id)
    {
        $invoice = Invoice::with('sends')->findOrFail($id);

        return response()->json([
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,

            'summary' => [
                'total_sends' => $invoice->sends->count(),
                'email_sent' => $invoice->sends
                    ->where('channel', 'email')->count(),
                'whatsapp_sent' => $invoice->sends
                    ->where('channel', 'whatsapp')->count(),
                'failed' => $invoice->sends
                    ->where('status', 'failed')->count(),
            ],

            'logs' => $invoice->sends->map(function ($send) {
                return [
                    'id' => $send->id,
                    'channel' => $send->channel,
                    'status' => $send->status,
                    'sent_at' => $send->sent_at,
                    'created_at' => $send->created_at,
                ];
            }),
        ]);
    }
}