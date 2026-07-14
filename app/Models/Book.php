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
        'cover_image',
        // E-Book fields
        'book_type',
        'ebook_file',
        'pages',
        'language',
        'download_count',
        'file_size',
        'description',
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

    public function getEbookUrlAttribute()
    {
        return $this->ebook_file
            ? asset('storage/' . $this->ebook_file)
            : null;
    }

    public function getFileSizeHumanAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['Bytes', 'KB', 'MB', 'GB'];

        $bytes = $this->file_size;

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}