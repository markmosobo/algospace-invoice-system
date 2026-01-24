<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderService extends Model
{
    protected $fillable = [
        'id',
        'name',
        'category',
        'price',
        'created_at',
        'updated_at',
    ];    
}
