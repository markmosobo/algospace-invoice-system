<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Farm extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'location',
        'size',
        'description'
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class);
    }    
}
