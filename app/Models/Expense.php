<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceItem;
use App\Models\ServiceProvider;
use App\Models\ProviderService;
use App\Models\Invoice;

class Expense extends Model
{
    protected $fillable = [
        'type',
        'service_provider_id',
        'provider_service_id',
        'description',
        'amount',
        'expense_date',
        'invoice_id'
    ];

    /**
     * Relationships
     */
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function providerService()
    {
        return $this->belongsTo(ProviderService::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoiceItem()
    {
        return $this->hasOne(InvoiceItem::class, 'expense_id');
    }

    /**
     * Create an InvoiceItem when expense is created
     * (Optional but recommended)
     */
    protected static function booted()
    {
        static::created(function ($expense) {
            InvoiceItem::create([
                'invoice_id' => $expense->invoice_id,
                'item_type' => $expense->type,
                'provider_service_id' => $expense->provider_service_id,
                'provider_service_name' => optional($expense->providerService)->name,
                'expense_name' => $expense->description,
                'quantity' => 1,
                'unit_price' => $expense->amount,
                'line_total' => $expense->amount,
            ]);
        });
    }
}
