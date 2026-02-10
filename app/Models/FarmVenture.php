<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Farm;

class FarmVenture extends Model
{
    protected $fillable = [
        'farm_id',
        'venture_name',
        'venture_type',
        'start_date',
        'status',
        'notes'
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }    
}
