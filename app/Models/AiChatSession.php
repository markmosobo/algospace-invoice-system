<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AiChatMessage;

class AiChatSession extends Model
{
    protected $fillable = ['user_id', 'title'];

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class, 'session_id');
    }    
}
