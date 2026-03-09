<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BorrowRecord;
use App\Models\User;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'genre',
        'shelf_location',
        'condition',
        'barcode',
        'added_by',
        'partner_id',
        'status',
        'cover_image'
    ];

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    protected $appends = ['cover_url'];

    public function getCoverUrlAttribute()
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : null;
    }
}