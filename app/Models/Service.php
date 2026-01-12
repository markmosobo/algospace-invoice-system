<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceItem;

class Service extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'unit',
    ];

    // Relationships
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }    
}
