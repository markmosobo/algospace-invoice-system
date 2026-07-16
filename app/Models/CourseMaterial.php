<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\Book;

class CourseMaterial extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'description',
        'type',
        'source',
        'file',
        'book_id',
        'url',
        'sort_order',
        'is_downloadable',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getMaterialUrlAttribute()
    {
        return match ($this->source) {

            'upload' => $this->file
                ? asset('storage/'.$this->file)
                : null,

            'library' => $this->book?->ebook_url,

            'external' => $this->url,

            default => null,
        };
    }
}