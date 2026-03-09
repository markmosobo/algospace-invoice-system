<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Book;
use App\Models\User;

class BorrowRecord extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'borrow_date',
        'expected_return_date',
        'return_date',
        'returned_at',
        'late_fee',
        'status'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'expected_return_date' => 'date',
        'return_date' => 'date',
        'returned_at' => 'datetime',
        'late_fee' => 'decimal:2'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}