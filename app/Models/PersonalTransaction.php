<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PersonalAccount;
use App\Models\PersonalCategory;

class PersonalTransaction extends Model
{
    protected $fillable = [
        'account_id',
        'category_id',
        'type',
        'amount',
        'payment_method',
        'description',
        'transaction_date'
    ];  
    
    // Relationships
    public function account()
    {
        return $this->belongsTo(PersonalAccount::class);
    }
    
    // Relationships
    public function category()
    {
        return $this->belongsTo(PersonalCategory::class);
    }    
}
