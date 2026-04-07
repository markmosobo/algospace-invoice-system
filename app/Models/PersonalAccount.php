<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalAccount extends Model
{
    protected $fillable = [
        'name',
        'account_number',
        'sub_type',
        'balance',
        'currency'
    ];  
    
    public function applyAdjustment(float $amount, string $type): void
    {
        if ($type === 'debit') {
            $this->balance += $amount;
        } elseif ($type === 'credit') {
            $this->balance -= $amount;
        }

        $this->save();
    }    
}
