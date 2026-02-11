<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FarmVenture;

class FarmExpense extends Model
{
    protected $fillable = [
        'venture_id', 'expense_category', 'description', 'amount', 'expense_date', 'paid_by'
    ];

    protected static function booted()
    {
        static::creating(function ($expense) {
            $venture = $expense->venture; // assumes venture relationship defined
            $date = now()->format('Ymd');

            // Count how many expenses already exist today for this venture
            $count = self::where('venture_id', $expense->venture_id)
                        ->whereDate('created_at', today())
                        ->count() + 1;

            // Example: COFFEE-20260211-0001
            $code = strtoupper($venture->venture_name); // convert venture name to uppercase
            $expense->receipt_no = $code . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }  
    
    // Relationships
    public function venture()
    {
        return $this->belongsTo(FarmVenture::class);
    }    
}
