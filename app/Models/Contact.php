<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'is_read',
        'replied_at',
        'replied_by',
        'type',
        'tier',
        'duration_days',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
    ];

    /*
    |-----------------------------------------
    | RELATIONSHIPS
    |-----------------------------------------
    */

    public function replier()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /*
    |-----------------------------------------
    | HELPERS (clean business logic)
    |-----------------------------------------
    */

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
        ]);
    }

    public function markAsReplied($userId)
    {
        $this->update([
            'is_read' => true,
            'replied_at' => now(),
            'replied_by' => $userId,
        ]);
    }

    public function isReplied(): bool
    {
        return !is_null($this->replied_at);
    }
}
