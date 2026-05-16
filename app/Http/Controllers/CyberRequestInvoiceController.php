<?php

namespace App\Http\Controllers;

use App\Models\CyberRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;

class CyberRequestInvoiceController extends Controller
{
    /**
     * 1. BUILD INVOICE DRAFT
     */
    public function draft($id)
    {
        $request = CyberRequest::with('files', 'service')->findOrFail($id);

        $items = [];
        $totalPages = 0;

        foreach ($request->files as $file) {

            $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));

            $pages = $this->estimatePages($file);

            $totalPages += $pages;

            $items[] = [
                "file_id" => $file->id,
                "description" => $file->file_name,
                "pages" => $pages,
                "unit_price" => $request->service->price ?? 10,
            ];
        }

        // SERVICE-BASED FALLBACK (NO FILES CASE)
        if ($request->files->isEmpty()) {

            $servicePrice = $request->service->price ?? 10;

            $items[] = [
                "file_id" => null,
                "description" => $request->service->name ?? "Service",
                "pages" => 1,
                "unit_price" => $servicePrice,
            ];

            $totalPages = 1;
        }

        return response()->json([
            "draft" => [
                "request_id" => $request->id,
                "client_name" => $request->name,

                "service_name" => $request->service->name ?? "Unknown Service",
                "service_id" => $request->service_id,            

                // 👇 THIS IS WHAT YOU WERE MISSING IN UI
                "system_pages" => $totalPages,

                // hybrid confidence
                "confidence" => $request->files->count() ? 0.85 : 0.5,

                "files" => $request->files->map(function ($file) {
                    return [
                        "id" => $file->id,
                        "name" => $file->file_name,
                        "path" => $file->file_path,
                        "page_count" => $file->page_count
                    ];
                }),            

                "items" => $items
            ]
        ]);
    }

    /**
     * HYBRID PAGE ESTIMATION
     */
    private function estimatePages($file)
    {
        $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));

        // REAL PDF PAGE COUNT (BEST CASE)
        if ($ext === "pdf" && $file->page_count) {
            return $file->page_count;
        }

        if ($ext === "pdf") {
            return 1; // fallback
        }

        // IMAGE (OCR already done later)
        if (in_array($ext, ["jpg", "jpeg", "png"])) {
            return $file->page_count ?? 1;
        }

        return $file->page_count ?? 1;
    }

    /**
     * 2. CONFIRM INVOICE
     */
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Confirm invoice from cyber request
     */
    public function confirm(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $cyberRequest = CyberRequest::findOrFail($id);

        return DB::transaction(function () use ($request, $cyberRequest) {

            // -------------------------
            // 1. Resolve Customer (KEY FIX)
            // -------------------------
            $customer = Customer::firstOrCreate(
                ['phone' => $cyberRequest->phone],
                [
                    'name'  => $cyberRequest->name ?? 'Cyber Client',
                    'email' => $cyberRequest->email ?? null,
                    'phone' => $cyberRequest->phone
                ]
            );

            // -------------------------
            // 2. Build invoice payload
            // -------------------------
            $data = [
                'items' => $request->items,
                'notes' => $request->notes,
                'total_amount' => collect($request->items)->sum(function ($i) {
                    $qty = $i['quantity'] ?? $i['pages'] ?? 1;
                    return $qty * ($i['unit_price'] ?? 0);
                })
            ];

            // -------------------------
            // 3. Call unified service
            // -------------------------
            $invoice = $this->invoiceService->create(
                $data,
                $customer,
                'cyber_request',
                $cyberRequest->id
            );

            // -------------------------
            // 4. Mark request as billed (optional but recommended)
            // -------------------------
            $cyberRequest->update([
                'status' => 'billed',
                'invoice_id' => $invoice->id,
                'billed_at' => now()
            ]);

            return response()->json([
                'message' => 'Invoice created successfully',
                'invoice' => $invoice
            ]);
        });
    }


}