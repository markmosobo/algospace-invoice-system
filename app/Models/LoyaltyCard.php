<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class LoyaltyCard extends Model
{
    protected $fillable = [
        'customer_id',
        'serial',
        'visits',
        'status',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Increment visits
    public function addVisit()
    {
        $this->visits++;
        if ($this->visits >= 10) {
            $this->status = 'completed';
        }
        $this->save();
    }    
}
