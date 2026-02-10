<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FarmVenture;

class Crop extends Model
{
    protected $fillable = [
        'venture_id',
        'crop_name',
        'variety',
        'planting_date',
        'expected_harvest_date',
        'acreage',
        'status'
    ];

    // Relationships
    public function venture()
    {
        return $this->belongsTo(FarmVenture::class);
    }    
}
