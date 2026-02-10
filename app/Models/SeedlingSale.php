<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Seedling;

class SeedlingSale extends Model
{
    protected $fillable = [
        'seedling_id',
        'buyer_name',
        'quantity_sold',
        'price_per_unit',
        'sale_date',
        'total_amount'
    ];

    // Relationships
    public function seedling()
    {
        return $this->belongsTo(Seedling::class);
    }    
}
