<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Invoice;
use App\Models\FootTraffic;
use App\Models\LoyaltyCard;
use App\Models\CustomerNote;
use App\Models\CustomerHistory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'gender',
        'image'
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

    public function notes()
    {
        return $this->hasMany(CustomerNote::class);
    }

    public function history()
    {
        return $this->hasMany(CustomerHistory::class)
            ->latest();
    }    

    // Convenience method to get the active card
    public function activeCard()
    {
        return $this->hasOne(LoyaltyCard::class)->where('status', 'active');
    }
}
