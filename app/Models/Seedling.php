<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FarmVenture;

class Seedling extends Model
{
    protected $fillable = [
        'venture_id',
        'seedling_type',
        'species_name',
        'date_planted',
        'quantity',
        'expected_ready_date',
        'survival_rate'
    ];

    // Relationships
    public function venture()
    {
        return $this->belongsTo(FarmVenture::class);
    }    
}
