<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;


class PerpustakaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['user', 'genres', 'type', 'year', 'demographic']);

        if ($request->search) {
            $query->where(fn($q) => $q->where('title', 'like', "%{$request->search}%")
                ->orWhere('author', 'like', "%{$request->search}%"));
        }
        if ($request->genre_id) {
            $query->whereHas('genres', fn($q) => $q->where('genres.id', $request->genre_id));
        }
        if ($request->type_id) $query->where('type_id', $request->type_id);
        if ($request->year_id) $query->where('year_id', $request->year_id);
        if ($request->demographic_id) $query->where('demographic_id', $request->demographic_id);

        $books = $query->get();

        $booksByGenre = [];
        foreach (\App\Models\Genre::with('books.genres', 'books.user')->get() as $genre) {
            if ($genre->books->isNotEmpty()) {
                $booksByGenre[$genre->name] = $genre->books;
            }
        }

        return view('perpustakaan', [
            'books' => $books,
            'booksByGenre' => $booksByGenre,
            'genres' => \App\Models\Genre::all(),
            'types' => \App\Models\Type::all(),
            'years' => \App\Models\Year::all(),
            'demographics' => \App\Models\Demographic::all()
        ]);
    }
}
