<?php

namespace App\Http\Controllers;

use App\Models\CyberRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    return response()->json([
        "draft" => [
            "request_id" => $request->id,
            "client_name" => $request->name,

            // 👇 THIS IS WHAT YOU WERE MISSING IN UI
            "system_pages" => $totalPages,

            // hybrid confidence
            "confidence" => $request->files->count() ? 0.85 : 0.5,

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
    public function confirm(Request $request, $id)
    {
        $cyberRequest = CyberRequest::findOrFail($id);

        $items = $request->input('items', []);
        $notes = $request->input('notes');

        $total = collect($items)->sum(function ($item) {
            return $item['pages'] * $item['unit_price'];
        });

        // CREATE INVOICE (assumes you have Invoice model)
        $invoice = $cyberRequest->invoice()->create([
            "total" => $total,
            "notes" => $notes,
            "status" => "pending"
        ]);

        // optional: store invoice items
        foreach ($items as $item) {
            $invoice->items()->create([
                "description" => $item['description'],
                "pages" => $item['pages'],
                "unit_price" => $item['unit_price'],
                "total" => $item['pages'] * $item['unit_price'],
            ]);
        }

        // link request
        $cyberRequest->update([
            "invoice_id" => $invoice->id
        ]);

        return response()->json([
            "message" => "Invoice created successfully",
            "invoice_id" => $invoice->id
        ]);
    }


}