<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CyberRequest extends Model
{
    protected $fillable = [
        'service',
        'message',
        'delivery_method',
        'urgency',
        'name',
        'email',
        'phone',
        'status',
    ];
}
