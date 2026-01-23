<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceItem;

class Service extends Model
{
    protected $fillable = [
        'id',
        'name',
        'category',
        'price',
        'unit',        // page, document, hour, service, bundle
        'is_bundle',   // boolean
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }    
}
