<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    /**
     * Display a listing of invoice items.
     */
    public function index()
    {
        $invoiceitems = InvoiceItem::get();

        return response()->json($invoiceitems);
    }

    /**
     * Store a newly created invoice item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $total = $validated['quantity'] * $validated['unit_price'];

        $invoiceItem = InvoiceItem::create([
            'invoice_id' => $validated['invoice_id'],
            'service_id' => $validated['service_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total' => $total,
        ]);

        app(AuditLogger::class)->record(
            'invoice_item.created',
            "Invoice item created (ID: {$invoiceItem->id})",
            $invoiceItem,
            [
                'invoice_item_id' => $invoiceItem->id,
                'invoice_id' => $invoiceItem->invoice_id,
                'service_id' => $invoiceItem->service_id,
                'quantity' => $invoiceItem->quantity,
                'unit_price' => $invoiceItem->unit_price,
                'total' => $invoiceItem->total,
            ],
            $request,
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Invoice item created successfully',
            'invoice_item' => $invoiceItem,
        ]);
    }

    /**
     * Display the specified invoice item.
     */
    public function show(string $id)
    {
        $invoiceitem = InvoiceItem::find($id);

        if (!$invoiceitem) {
            return response()->json([
                'message' => 'Invoice item not found',
            ], 404);
        }

        return response()->json($invoiceitem);
    }

    /**
     * Update the specified invoice item.
     */
    public function update(Request $request, string $id)
    {
        $invoiceItem = InvoiceItem::findOrFail($id);

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $fields = [
            'invoice_id',
            'service_id',
            'quantity',
            'unit_price',
            'total',
        ];

        $before = $invoiceItem->only($fields);

        $total = $validated['quantity'] * $validated['unit_price'];

        $invoiceItem->update([
            'invoice_id' => $validated['invoice_id'],
            'service_id' => $validated['service_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total' => $total,
        ]);

        $invoiceItem->refresh();

        $changes = [];

        foreach ($fields as $field) {
            $oldValue = $before[$field] ?? null;
            $newValue = $invoiceItem->getAttribute($field);

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'invoice_item.updated',
                "Invoice item updated (ID: {$invoiceItem->id})",
                $invoiceItem,
                [
                    'invoice_item_id' => $invoiceItem->id,
                    'changes' => $changes,
                ],
                $request,
                auth('api')->id()
            );
        }

        return response()->json([
            'message' => 'Invoice item updated successfully',
            'invoice_item' => $invoiceItem,
        ]);
    }

    /**
     * Delete the specified invoice item.
     */
    public function destroy(string $id)
    {
        $invoiceItem = InvoiceItem::find($id);

        if (!$invoiceItem) {
            return response()->json([
                'message' => 'Invoice item not found',
            ], 404);
        }

        $itemId = $invoiceItem->id;
        $invoiceId = $invoiceItem->invoice_id;
        $serviceId = $invoiceItem->service_id;
        $quantity = $invoiceItem->quantity;
        $total = $invoiceItem->total;

        $invoiceItem->delete();

        app(AuditLogger::class)->record(
            'invoice_item.deleted',
            "Invoice item deleted (ID: {$itemId})",
            $invoiceItem,
            [
                'invoice_item_id' => $itemId,
                'invoice_id' => $invoiceId,
                'service_id' => $serviceId,
                'quantity' => $quantity,
                'total' => $total,
            ],
            request(),
            auth('api')->id()
        );

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}