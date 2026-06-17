<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceItem;
use App\Models\Enrollment;

class Service extends Model
{
    protected $fillable = [
        'id',
        'name',
        'category',
        'price',
        'payment_type',
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
    
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }    
}
