<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CyberRequestFile;

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

    public function files()
    {
        return $this->hasMany(CyberRequestFile::class);
    }    
}
