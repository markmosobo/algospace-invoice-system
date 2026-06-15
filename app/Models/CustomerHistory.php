<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class CustomerHistory extends Model
{
    protected $fillable = [
        'customer_id',
        'action',
        'description'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
