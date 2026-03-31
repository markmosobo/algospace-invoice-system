<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiMemory extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'key',
        'value',
        'importance',
        'last_observed_at'
    ];    
}
