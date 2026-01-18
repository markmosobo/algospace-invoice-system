<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supply;
use App\Models\Supplier;
use App\Models\User;

class Restock extends Model
{
    // Allow mass assignment
    protected $fillable = [
        'supply_id',
        'supplier_id',
        'user_id',
        'quantity',
        'buying_price',
        'status'
    ];

    // Relationships (optional but recommended)

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
