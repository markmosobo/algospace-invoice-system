<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AiChatSession;

class AiChatMessage extends Model
{
    protected $fillable = [
        'session_id',
        'role',
        'content',
        'confidence_score',
        'meta'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'session_id');
    }    
}
