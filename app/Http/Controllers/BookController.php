<?php

namespace App\Http\Controllers;

use App\Models\Book;
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

        /*
        |--------------------------------------------------------------------------
        | Upload Cover
        |--------------------------------------------------------------------------
        */

        $coverPath = null;

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')
                ->store('book_covers', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Ebook
        |--------------------------------------------------------------------------
        */

        $ebookPath = null;
        $fileSize = null;

        if ($request->hasFile('ebook_file')) {

            $ebook = $request->file('ebook_file');

            $ebookPath = $ebook->store('ebooks', 'public');

            $fileSize = $ebook->getSize();
        }

        /*
        |--------------------------------------------------------------------------
        | Save Book
        |--------------------------------------------------------------------------
        */

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

        return response()->json([
            'message' => 'Book added successfully.',
            'book' => $book
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

        /*
        |--------------------------------------------------------------------------
        | Replace Cover Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('cover_image')) {

            if (
                $book->cover_image &&
                Storage::disk('public')->exists($book->cover_image)
            ) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $data['cover_image'] = $request
                ->file('cover_image')
                ->store('book_covers', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Replace Ebook
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('ebook_file')) {

            if (
                $book->ebook_file &&
                Storage::disk('public')->exists($book->ebook_file)
            ) {
                Storage::disk('public')->delete($book->ebook_file);
            }

            $ebook = $request->file('ebook_file');

            $data['ebook_file'] = $ebook->store('ebooks', 'public');

            $data['file_size'] = $ebook->getSize();
        }

        $book->update($data);

        return response()->json([
            'message' => 'Book updated successfully.',
            'book' => $book->fresh()
        ]);
    }

    /**
     * Delete book.
     */
    public function destroy(Book $book)
    {
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
            'message' => 'Book deleted successfully.'
        ]);
    }

    /**
     * Download Ebook.
     */
    public function download(Book $book)
    {
        if ($book->book_type !== 'ebook') {
            return response()->json([
                'message' => 'This is not an e-book.'
            ], 404);
        }

        if (!$book->ebook_file) {
            return response()->json([
                'message' => 'E-book file not found.'
            ], 404);
        }

        $book->increment('download_count');

        return Storage::disk('public')->download($book->ebook_file);
    }

    public function read(Book $book)
    {
        if ($book->book_type !== 'ebook' || !$book->ebook_file) {
            abort(404);
        }

        $path = storage_path('app/public/' . $book->ebook_file);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }    
}