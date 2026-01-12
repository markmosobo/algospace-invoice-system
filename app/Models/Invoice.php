<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\Payment;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'total_amount',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helper function: get total paid
    public function totalPaid()
    {
        return $this->payments()->sum('amount');
    }

    // Helper function: get balance
    public function balance()
    {
        return $this->total_amount - $this->totalPaid();
    }    
}
