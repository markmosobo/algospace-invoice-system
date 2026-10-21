<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display all books.
     */
    public function index()
    {
        $books = Book::with([
            'addedBy:id,name',
            'partner:id,name'
        ])->latest()->get();

        return response()->json($books);
    }

    /**
     * Display a single book.
     */
    public function show(Book $book)
    {
        return response()->json(
            $book->load([
                'addedBy:id,name',
                'partner:id,name'
            ])
        );
    }

    /**
     * Store a new book.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',

            'book_type' => 'required|in:physical,ebook',

            'pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:100',

            'shelf_location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',

            'barcode' => 'nullable|string|unique:books,barcode',

            'partner_id' => 'nullable|exists:users,id',

            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'ebook_file' => 'nullable|mimes:pdf,epub|max:51200',
        ]);

        // Upload cover image.
        $coverPath = null;

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')
                ->store('book_covers', 'public');
        }

        // Upload ebook.
        $ebookPath = null;
        $fileSize = null;

        if ($request->hasFile('ebook_file')) {
            $ebook = $request->file('ebook_file');

            $ebookPath = $ebook->store('ebooks', 'public');

            $fileSize = $ebook->getSize();
        }

        // Save book.
        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'genre' => $request->genre,
            'book_type' => $request->book_type,
            'pages' => $request->pages,
            'description' => $request->description,
            'language' => $request->language ?? 'English',
            'shelf_location' => $request->shelf_location,
            'condition' => $request->condition,
            'barcode' => $request->barcode,
            'partner_id' => $request->partner_id,
            'added_by' => auth()->id(),
            'cover_image' => $coverPath,
            'ebook_file' => $ebookPath,
            'file_size' => $fileSize,
            'status' => 'available',
            'download_count' => 0,
        ]);

        // Audit: book created.
        app(AuditLogger::class)->record(
            'book.created',
            "Book added: {$book->title}",
            $book,
            [
                'book_type' => $book->book_type,
                'partner_id' => $book->partner_id,
                'barcode' => $book->barcode,
                'status' => $book->status,
                'has_cover' => !empty($book->cover_image),
                'has_ebook' => !empty($book->ebook_file),
            ],
            $request
        );

        return response()->json([
            'message' => 'Book added successfully.',
            'book' => $book,
        ], 201);
    }

    /**
     * Update an existing book.
     */
    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',

            'book_type' => 'sometimes|in:physical,ebook',

            'pages' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:100',
            'shelf_location' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',

            'barcode' => 'nullable|string|unique:books,barcode,' . $book->id,

            'partner_id' => 'nullable|exists:users,id',

            'status' => 'sometimes|in:available,borrowed',

            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'ebook_file' => 'nullable|mimes:pdf,epub|max:51200',
        ]);

        // Capture original values before making changes.
        $original = $book->only([
            'title',
            'author',
            'genre',
            'book_type',
            'pages',
            'language',
            'shelf_location',
            'condition',
            'barcode',
            'partner_id',
            'status',
            'cover_image',
            'ebook_file',
            'file_size',
        ]);

        $oldCoverPath = $book->cover_image;
        $oldEbookPath = $book->ebook_file;

        $newCoverPath = null;
        $newEbookPath = null;

        // Upload replacement cover without deleting the old one yet.
        if ($request->hasFile('cover_image')) {
            $newCoverPath = $request->file('cover_image')
                ->store('book_covers', 'public');

            if (!$newCoverPath) {
                return response()->json([
                    'message' => 'Failed to upload the book cover.',
                ], 500);
            }

            $data['cover_image'] = $newCoverPath;
        }

        // Upload replacement ebook without deleting the old one yet.
        if ($request->hasFile('ebook_file')) {
            $ebook = $request->file('ebook_file');

            $newEbookPath = $ebook->store('ebooks', 'public');

            if (!$newEbookPath) {
                if ($newCoverPath) {
                    Storage::disk('public')->delete($newCoverPath);
                }

                return response()->json([
                    'message' => 'Failed to upload the ebook.',
                ], 500);
            }

            $data['ebook_file'] = $newEbookPath;
            $data['file_size'] = $ebook->getSize();
        }

        try {
            $book->update($data);
            $book->refresh();
        } catch (\Throwable $e) {
            // Remove replacement uploads if the database update fails.
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            if ($newEbookPath) {
                Storage::disk('public')->delete($newEbookPath);
            }

            throw $e;
        }

        // Remove old files only after the database update succeeds.
        if (
            $newCoverPath &&
            $oldCoverPath &&
            $oldCoverPath !== $newCoverPath
        ) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        if (
            $newEbookPath &&
            $oldEbookPath &&
            $oldEbookPath !== $newEbookPath
        ) {
            Storage::disk('public')->delete($oldEbookPath);
        }

        // Compare original and updated values.
        $changes = [];

        foreach ($original as $field => $oldValue) {
            $newValue = $book->{$field};

            if ($oldValue != $newValue) {
                // Record file replacement without exposing storage paths.
                if (in_array($field, ['cover_image', 'ebook_file'])) {
                    $changes[$field] = [
                        'changed' => true,
                    ];
                } else {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        // Audit only if something actually changed.
        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'book.updated',
                "Book updated: {$book->title}",
                $book,
                [
                    'changes' => $changes,
                ],
                $request
            );
        }

        return response()->json([
            'message' => 'Book updated successfully.',
            'book' => $book,
        ]);
    }

    /**
     * Delete a book.
     */
    public function destroy(Book $book)
    {
        // Capture details before deleting the book.
        $bookDetails = [
            'title' => $book->title,
            'author' => $book->author,
            'book_type' => $book->book_type,
            'barcode' => $book->barcode,
            'partner_id' => $book->partner_id,
        ];

        // Audit before deletion so the book still exists for the
        // polymorphic relationship when the audit entry is created.
        app(AuditLogger::class)->record(
            'book.deleted',
            "Book deleted: {$book->title}",
            $book,
            $bookDetails,
            request()
        );

        // Delete associated files.
        if (
            $book->cover_image &&
            Storage::disk('public')->exists($book->cover_image)
        ) {
            Storage::disk('public')->delete($book->cover_image);
        }

        if (
            $book->ebook_file &&
            Storage::disk('public')->exists($book->ebook_file)
        ) {
            Storage::disk('public')->delete($book->ebook_file);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully.',
        ]);
    }

    /**
     * Download an ebook.
     */
    public function download(Book $book)
    {
        if ($book->book_type !== 'ebook') {
            return response()->json([
                'message' => 'This is not an e-book.',
            ], 404);
        }

        if (
            !$book->ebook_file ||
            !Storage::disk('public')->exists($book->ebook_file)
        ) {
            return response()->json([
                'message' => 'E-book file not found.',
            ], 404);
        }

        $book->increment('download_count');
        $book->refresh();

        // Audit successful download request.
        app(AuditLogger::class)->record(
            'book.downloaded',
            "E-book download requested: {$book->title}",
            $book,
            [
                'download_count' => $book->download_count,
            ],
            request()
        );

        return Storage::disk('public')->download($book->ebook_file);
    }

    /**
     * Read an ebook in the browser.
     */
    public function read(Book $book)
    {
        if ($book->book_type !== 'ebook' || !$book->ebook_file) {
            abort(404);
        }

        $path = storage_path('app/public/' . $book->ebook_file);

        if (!file_exists($path)) {
            abort(404);
        }

        // Audit successful read request.
        app(AuditLogger::class)->record(
            'book.read',
            "E-book opened for reading: {$book->title}",
            $book,
            [],
            request()
        );

        return response()->file($path);
    }
}