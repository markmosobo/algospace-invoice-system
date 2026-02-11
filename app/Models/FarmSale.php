<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FarmVenture;

class FarmSale extends Model
{

    protected $table = 'farm_sales';

    // Fillable fields for mass assignment
    protected $fillable = [
        'venture_id',
        'product_name',
        'quantity',
        'unit',
        'price_per_unit',
        'buyer',
        'sale_date',
        'total_amount',
    ];

    // Cast types
    protected $casts = [
        'quantity' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sale_date' => 'date',
    ];

    // Relationships
    public function venture()
    {
        return $this->belongsTo(FarmVenture::class, 'venture_id');
    }

    // Automatically calculate total_amount if not provided
    protected static function booted()
    {
        static::creating(function ($sale) {
            if (empty($sale->total_amount)) {
                $sale->total_amount = $sale->quantity * $sale->price_per_unit;
            }
        });
    }    
}
