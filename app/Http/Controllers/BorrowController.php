<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BorrowController extends Controller
{
    /**
     * Borrow a book.
     */
    public function borrow(Request $request)
    {
        $data = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'expected_return_date' => 'nullable|date|after_or_equal:today',
        ]);

        $borrow = DB::transaction(function () use ($data, $request) {
            // Lock the book row to prevent simultaneous borrowing.
            $book = Book::whereKey($data['book_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($book->status === 'borrowed') {
                return null;
            }

            $borrow = BorrowRecord::create([
                'book_id' => $book->id,
                'user_id' => $data['user_id'],
                'borrow_date' => Carbon::today()->toDateString(),
                'expected_return_date' => $data['expected_return_date'] ?? null,
                'status' => 'borrowed',
            ]);

            $book->update([
                'status' => 'borrowed',
            ]);

            // Audit successful borrowing.
            app(AuditLogger::class)->record(
                'book.borrowed',
                "Book borrowed: {$book->title}",
                $borrow,
                [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'borrower_id' => $borrow->user_id,
                    'borrow_record_id' => $borrow->id,
                    'borrow_date' => $borrow->borrow_date,
                    'expected_return_date' => $borrow->expected_return_date,
                ],
                $request
            );

            return $borrow;
        });

        if (!$borrow) {
            return response()->json([
                'error' => 'Book already borrowed',
            ], 400);
        }

        return response()->json($borrow, 201);
    }

    /**
     * Return a borrowed book.
     */
    public function return(Request $request, BorrowRecord $borrow)
    {
        $result = DB::transaction(function () use ($request, $borrow) {
            // Lock the borrowing record.
            $borrow = BorrowRecord::whereKey($borrow->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($borrow->status !== 'borrowed') {
                return null;
            }

            // Lock the associated book.
            $book = Book::whereKey($borrow->book_id)
                ->lockForUpdate()
                ->firstOrFail();

            $now = Carbon::now();
            $today = $now->toDateString();

            $daysLate = 0;
            $lateFee = 0;

            if (
                $borrow->expected_return_date &&
                $now->gt(
                    Carbon::parse($borrow->expected_return_date)->endOfDay()
                )
            ) {
                $daysLate = (int) Carbon::parse(
                    $borrow->expected_return_date
                )->startOfDay()->diffInDays($now->startOfDay());

                $lateFee = $daysLate * 10;
            }

            $borrow->update([
                'return_date' => $today,
                'returned_at' => $now,
                'status' => 'returned',
                'late_fee' => $lateFee,
            ]);

            $book->update([
                'status' => 'available',
            ]);

            // Audit successful return.
            app(AuditLogger::class)->record(
                'book.returned',
                "Book returned: {$book->title}",
                $borrow,
                [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'borrower_id' => $borrow->user_id,
                    'borrow_record_id' => $borrow->id,
                    'borrow_date' => $borrow->borrow_date,
                    'expected_return_date' => $borrow->expected_return_date,
                    'return_date' => $today,
                    'days_late' => $daysLate,
                    'late_fee' => $lateFee,
                    'currency' => 'KES',
                ],
                $request
            );

            return [
                'borrow' => $borrow->fresh(),
                'days_late' => $daysLate,
                'late_fee' => $lateFee,
            ];
        });

        if (!$result) {
            return response()->json([
                'error' => 'This book is not currently borrowed',
            ], 400);
        }

        return response()->json($result['borrow']);
    }

    /**
     * List borrowing records.
     */
    public function index()
    {
        return response()->json(
            BorrowRecord::with('book', 'user')->get()
        );
    }
}