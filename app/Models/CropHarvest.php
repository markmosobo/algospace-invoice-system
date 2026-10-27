<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Crop;
use Carbon\Carbon;

class CropHarvest extends Model
{
    protected $fillable = [
        'crop_id',
        'harvest_date',
        'season',
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


    /**
     * Automatically determine season from harvest_date.
     * Season runs from July to June.
     */
    protected static function booted(): void
    {
        static::saving(function (CropHarvest $harvest) {
            if ($harvest->harvest_date) {
                $date = Carbon::parse($harvest->harvest_date);

                $startYear = $date->month >= 7
                    ? $date->year
                    : $date->year - 1;

                $harvest->season = $startYear . '/' .
                    substr((string) ($startYear + 1), -2);
            }
        });
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