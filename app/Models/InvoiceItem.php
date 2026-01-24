<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ProviderService;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_type',

        // Cyber service
        'service_id',
        'service_name',

        // Provider service
        'provider_service_id',
        'provider_service_name',

        // Expense
        'expense_name',

        // Pricing
        'quantity',
        'unit_price',
        'line_total',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Cyber services (what YOU sell)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Provider services (what YOU pay for)
    public function providerService()
    {
        return $this->belongsTo(ProviderService::class);
    }
}
