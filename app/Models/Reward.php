<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Reward extends Model
{
    protected $fillable = [
        'customer_id',
        'reward_type',
        'value',
        'visits',
        'redeemed',
    ];

    /**
     * The customer who earned the reward
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
        
}
