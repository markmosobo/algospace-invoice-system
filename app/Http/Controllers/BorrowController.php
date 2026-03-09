<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowController extends Controller
{
    // Borrow a book
    public function borrow(Request $request)
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'expected_return_date' => 'nullable|date|after_or_equal:today'
        ]);

        $book = Book::findOrFail($data['book_id']);

        if($book->status == 'borrowed'){
            return response()->json(['error' => 'Book already borrowed'], 400);
        }

        $borrow = BorrowRecord::create([
            'book_id' => $data['book_id'],
            'user_id' => $data['user_id'],
            'borrow_date' => Carbon::now()->toDateString(),
            'expected_return_date' => $data['expected_return_date'],
            'status' => 'borrowed'
        ]);

        $book->update(['status' => 'borrowed']);

        return response()->json($borrow, 201);
    }

    // Return a book
    public function return(Request $request, BorrowRecord $borrow)
    {
        if($borrow->status != 'borrowed'){
            return response()->json(['error' => 'This book is not currently borrowed'], 400);
        }

        $borrow->update([
            'return_date' => Carbon::now()->toDateString(),
            'returned_at' => Carbon::now(),
            'status' => 'returned'
        ]);

        $book = $borrow->book;
        $book->update(['status' => 'available']);

        // calculate late fee if needed
        if($borrow->expected_return_date && Carbon::now()->gt(Carbon::parse($borrow->expected_return_date))){
            $daysLate = Carbon::parse($borrow->expected_return_date)->diffInDays(Carbon::now());
            $borrow->update(['late_fee' => $daysLate * 10]); // example Ksh 10/day
        }

        return response()->json($borrow);
    }

    // List borrowed books
    public function index()
    {
        return response()->json(BorrowRecord::with('book', 'user')->get());
    }
}
