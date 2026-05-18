<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    /**
     * Create invoice with items for ANY source (walk-in or cyber request)
     *
     * @param array $data
     * @param Customer $customer
     * @param string $source (walkin, cyber_request, api, etc)
     * @param int|null $sourceId
     * @return Invoice
     */
    public function create(array $data, Customer $customer, string $source = 'walkin', ?int $sourceId = null): Invoice
    {
        return DB::transaction(function () use ($data, $customer, $source, $sourceId) {

            // ----------------------------
            // 1. Validate required fields
            // ----------------------------
            if (!isset($data['items']) || !is_array($data['items'])) {
                throw ValidationException::withMessages([
                    'items' => 'Invoice items are required and must be an array.'
                ]);
            }

            // ----------------------------
            // 2. Create Invoice Header
            // ----------------------------
            $invoice = Invoice::create([
                'customer_id'    => $customer->id,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date'   => $data['invoice_date'] ?? now(),
                'due_date'       => $data['due_date'] ?? null,
                'status'         => $data['status'] ?? 'pending',
                'total_amount'   => $data['total_amount'] ?? 0,

                // 🔥 unified source tracking
                'source'         => $source,
                'source_id'      => $sourceId,
            ]);

            $total = 0;

            // ----------------------------
            // 3. Create Invoice Items
            // ----------------------------
            foreach ($data['items'] as $item) {

                $quantity = $item['quantity'] ?? 1;
                $unitPrice = (float) ($item['price'] ?? $item['unit_price'] ?? 0);

                $lineTotal = $quantity * $unitPrice;
                $total += $lineTotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,

                    'service_id' => $item['service_id'] 
                        ?? ($item['service']['id'] ?? null),

                    'service_name' => $item['service_name']
                        ?? ($item['service']['name'] ?? null)
                        ?? $item['name']
                        ?? $item['description']
                        ?? null,

                    'provider_service_id' => $item['provider_service_id'] ?? null,
                    'provider_service_name' => $item['provider_service_name'] ?? null,

                    'expense_name' => $item['expense_name'] ?? null,

                    'item_type' => $item['item_type'] ?? 'cyber_service',

                    'unit_price' => $unitPrice,
                    'quantity'   => $quantity,
                    'line_total' => $lineTotal,
                ]);
            }

            // ----------------------------
            // 4. Update invoice total (safe recalculation)
            // ----------------------------
            $invoice->update([
                'total_amount' => $total
            ]);

            return $invoice;
        });
    }
}