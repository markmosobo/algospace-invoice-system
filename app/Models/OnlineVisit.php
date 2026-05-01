<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineVisit extends Model
{
    protected $fillable = [
        'visitor_id',
        'user_id',
        'ip',
        'url',
        'user_agent',
        'method',
        'referer',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * If the visitor is logged in, this links to the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}