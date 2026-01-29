<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaryEntry extends Model
{
    protected $fillable = [
        'title',
        'type',
        'amount',
        'category',
        'description',
        'attachment',
        'tags',
        'entry_date',
        'remind_at',
        'status',
    ];

    // Ensure dates are Carbon instances
    protected $casts = [
        'entry_date' => 'datetime',
        'remind_at' => 'datetime',
    ];
}
