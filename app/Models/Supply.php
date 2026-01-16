<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;

class Supply extends Model
{
    protected $fillable = [
        'supplier_id',
        'item',
        'quantity',
        'unit_price',
        'total',
        'payment_date',
        'payment_method',
        'status'
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }    
}
