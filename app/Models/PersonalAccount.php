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
}
