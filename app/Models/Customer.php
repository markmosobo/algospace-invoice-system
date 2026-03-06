<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Invoice;
use App\Models\FootTraffic;
use App\Models\LoyaltyCard;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
    ];

    // Relationships
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }  
    
    public function visits()
    {
        return $this->hasMany(FootTraffic::class);
    }

    // Relationship to loyalty cards
    public function loyaltyCards()
    {
        return $this->hasMany(LoyaltyCard::class);
    }

    // Convenience method to get the active card
    public function activeCard()
    {
        return $this->loyaltyCards()->where('status', 'active')->first();
    }
}
