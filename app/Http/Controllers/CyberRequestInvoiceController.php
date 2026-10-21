<?php

namespace App\Http\Controllers;

use App\Models\CyberRequest;
use App\Models\Customer;
use App\Services\AuditLogger;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CyberRequestInvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * 1. BUILD INVOICE DRAFT
     */
    public function draft($id)
    {
        $cyberRequest = CyberRequest::with('files', 'service')
            ->findOrFail($id);

        $items = [];
        $totalPages = 0;

        foreach ($cyberRequest->files as $file) {
            $pages = $this->estimatePages($file);

            $totalPages += $pages;

            $items[] = [
                'file_id' => $file->id,
                'description' => $file->file_name,
                'pages' => $pages,
                'unit_price' => $cyberRequest->service->price ?? 10,
            ];
        }

        // Service-based fallback when there are no uploaded files.
        if ($cyberRequest->files->isEmpty()) {
            $servicePrice = $cyberRequest->service->price ?? 10;

            $items[] = [
                'file_id' => null,
                'description' => $cyberRequest->service->name ?? 'Service',
                'pages' => 1,
                'unit_price' => $servicePrice,
            ];

            $totalPages = 1;
        }

        app(AuditLogger::class)->record(
            'cyber_request.invoice_draft_generated',
            "Invoice draft generated for cyber request #{$cyberRequest->id}",
            $cyberRequest,
            [
                'request_id' => $cyberRequest->id,
                'service_id' => $cyberRequest->service_id,
                'files_count' => $cyberRequest->files->count(),
                'estimated_pages' => $totalPages,
                'draft_items_count' => count($items),
                'confidence' => $cyberRequest->files->count() ? 0.85 : 0.5,
            ],
            request()
        );

        return response()->json([
            'draft' => [
                'request_id' => $cyberRequest->id,
                'client_name' => $cyberRequest->name,
                'service_name' => $cyberRequest->service->name ?? 'Unknown Service',
                'service_id' => $cyberRequest->service_id,
                'system_pages' => $totalPages,
                'confidence' => $cyberRequest->files->count() ? 0.85 : 0.5,

                'files' => $cyberRequest->files->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'path' => $file->file_path,
                        'page_count' => $file->page_count,
                    ];
                }),

                'items' => $items,
            ],
        ]);
    }

    /**
     * HYBRID PAGE ESTIMATION
     */
    private function estimatePages($file)
    {
        $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));

        if ($ext === 'pdf' && $file->page_count) {
            return $file->page_count;
        }

        if ($ext === 'pdf') {
            return 1;
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $file->page_count ?? 1;
        }

        return $file->page_count ?? 1;
    }

    /**
     * 2. CONFIRM INVOICE
     */
    public function confirm(Request $request, $id)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        $cyberRequest = CyberRequest::findOrFail($id);

        // Prevent accidental duplicate invoices for an already billed request.
        // Remove this check if your workflow intentionally permits re-invoicing.
        if ($cyberRequest->invoice_id) {
            return response()->json([
                'message' => 'This cyber request already has an invoice.',
                'invoice_id' => $cyberRequest->invoice_id,
            ], 422);
        }

        $result = DB::transaction(function () use (
            $validated,
            $cyberRequest
        ) {
            // Resolve or create the customer.
            $customer = Customer::firstOrCreate(
                ['phone' => $cyberRequest->phone],
                [
                    'name' => $cyberRequest->name ?? 'Cyber Client',
                    'email' => $cyberRequest->email ?? null,
                    'phone' => $cyberRequest->phone,
                ]
            );

            $data = [
                'items' => $validated['items'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => collect($validated['items'])->sum(
                    function ($item) {
                        $quantity = $item['quantity'] ?? $item['pages'] ?? 1;

                        return $quantity * ($item['unit_price'] ?? 0);
                    }
                ),
            ];

            $invoice = $this->invoiceService->create(
                $data,
                $customer,
                'cyber_request',
                $cyberRequest->id
            );

            $previousStatus = $cyberRequest->status;

            $cyberRequest->update([
                'status' => 'billed',
                'invoice_id' => $invoice->id,
                'billed_at' => now(),
            ]);

            return [
                'invoice' => $invoice,
                'customer' => $customer,
                'previous_status' => $previousStatus,
                'total_amount' => $data['total_amount'],
                'items_count' => count($validated['items']),
            ];
        });

        // Audit only after the transaction commits successfully.
        app(AuditLogger::class)->record(
            'cyber_request.invoice_confirmed',
            "Invoice #{$result['invoice']->id} created for cyber request #{$cyberRequest->id}",
            $cyberRequest,
            [
                'request_id' => $cyberRequest->id,
                'invoice_id' => $result['invoice']->id,
                'customer_id' => $result['customer']->id,
                'previous_status' => $result['previous_status'],
                'new_status' => 'billed',
                'total_amount' => $result['total_amount'],
                'items_count' => $result['items_count'],
            ],
            $request
        );

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $result['invoice'],
        ]);
    }
}