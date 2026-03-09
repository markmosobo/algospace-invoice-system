<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // List all books
    public function index()
    {
        return response()->json(Book::all());
    }

    // Get a single book
    public function show(Book $book)
    {
        return response()->json($book);
    }

    // Add a new book
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'shelf_location' => 'nullable|string|max:255',
            'condition' => 'nullable|string',
            'barcode' => 'nullable|string|unique:books,barcode',
            'partner_id' => 'nullable|exists:users,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $coverPath = null;

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')
                ->store('book_covers', 'public');
        }

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'genre' => $request->genre,
            'shelf_location' => $request->shelf_location,
            'condition' => $request->condition,
            'barcode' => $request->isbn,
            'partner_id' => $request->partner_id,
            'added_by' => auth()->id(),
            'cover_image' => $coverPath,
            'status' => 'available',
        ]);

        return response()->json([
            'message' => 'Book added successfully',
            'book' => $book
        ], 201);
    }

    // Update book
    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'shelf_location' => 'nullable|string|max:255',
            'condition' => 'nullable|string',
            'barcode' => 'nullable|string|unique:books,barcode,' . $book->id,
            'partner_id' => 'nullable|exists:users,id',
            'status' => 'sometimes|in:available,borrowed',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // 🔄 Handle cover image replacement
        if ($request->hasFile('cover_image')) {

            // Delete old image if exists
            if ($book->cover_image && Storage::disk('public/book_covers')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }

            // Store new image
            $data['cover_image'] = $request->file('cover_image')
                ->store('book_covers', 'public');
        }

        $book->update($data);

        return response()->json([
            'message' => 'Book updated successfully',
            'book' => $book
        ]);
    }

    // Delete book
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Book deleted']);
    }
}
