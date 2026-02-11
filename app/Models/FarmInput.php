<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FarmVenture;

class FarmInput extends Model
{
    protected $fillable = [
        'venture_id', 'input_name', 'input_type', 'quantity', 'unit', 'date_applied'
    ]; 
    
    // Relationships
    public function venture()
    {
        return $this->belongsTo(FarmVenture::class);
    }    
}
