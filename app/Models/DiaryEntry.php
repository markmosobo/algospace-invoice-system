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
        'category',
        'tags',
        'entry_date',
        'remind_at'
    ];     
}
