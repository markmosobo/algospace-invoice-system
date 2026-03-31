<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiInsight extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'content',
        'confidence_score',
        'sources',
        'relevant_from',
        'relevant_to'
    ];

    protected $casts = [
        'sources' => 'array',
    ];    
}
