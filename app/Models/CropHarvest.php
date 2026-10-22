<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Crop;

class CropHarvest extends Model
{
    protected $fillable = [
        'crop_id',
        'harvest_date',
        'quantity_kg',
        'labour_cost',
        'transport_cost',
        'other_cost',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'harvest_date' => 'date:Y-m-d',
            'quantity_kg' => 'decimal:2',
            'labour_cost' => 'decimal:2',
            'transport_cost' => 'decimal:2',
            'other_cost' => 'decimal:2',
        ];
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function getTotalCostAttribute(): float
    {
        return round(
            (float) $this->labour_cost
            + (float) $this->transport_cost
            + (float) $this->other_cost,
            2
        );
    }

    public function getCostPerKgAttribute(): ?float
    {
        $quantity = (float) $this->quantity_kg;

        if ($quantity <= 0) {
            return null;
        }

        return round($this->total_cost / $quantity, 2);
    }
}