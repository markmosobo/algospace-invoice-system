<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CropHarvestFunding extends Model
{
    protected $fillable = [
        'harvest_id',
        'funding_source',
        'transaction_type',
        'amount',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date:Y-m-d',
    ];

    public function harvest(): BelongsTo
    {
        return $this->belongsTo(
            CropHarvest::class,
            'harvest_id'
        );
    }
}