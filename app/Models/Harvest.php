<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Crop;

class Harvest extends Model
{
    protected $fillable = [
        'crop_id',
        'harvest_date',
        'quantity',
        'unit',
        'quality_grade',
        'remarks'
    ];

    // Relationships
    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }    
}
