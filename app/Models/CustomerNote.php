<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class CustomerNote extends Model
{
    protected $fillable = [
        'customer_id',
        'note'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
