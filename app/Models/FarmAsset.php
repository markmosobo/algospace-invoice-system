<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmAsset extends Model
{
    protected $table = 'farm_assets';

    protected $fillable = [
        'asset_name',
        'asset_type',
        'purchase_date',
        'cost',
        'condition',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * Optional helper: age of the asset in years
     */
    public function getAssetAgeAttribute()
    {
        return $this->purchase_date ? now()->diffInYears($this->purchase_date) : null;
    }    
}
