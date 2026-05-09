<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Type;
use App\Models\Year;
use App\Models\Demographic;
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

        if ($request->genre_ids) {
            $query->whereHas('genres', fn($q) => $q->whereIn('genres.id', $request->genre_ids));
        } elseif ($request->genre_id) {
            $query->whereHas('genres', fn($q) => $q->where('genres.id', $request->genre_id));
        }

        if ($request->type_ids) {
            $query->whereIn('type_id', $request->type_ids);
        } elseif ($request->type_id) {
            $query->where('type_id', $request->type_id);
        }

        if ($request->demographic_id) {
            $query->where('demographic_id', $request->demographic_id);
        }

        if ($request->year_id) {
            $query->where('year_id', $request->year_id);
        }

        $books = $query->get()->unique(function ($book) {
            return $book->title . $book->author;
        });

        $booksByGenre = [];

        foreach (Genre::with(['books.genres', 'books.user', 'books.type', 'books.year', 'books.demographic'])->get() as $genre) {
            $uniqueGenreBooks = $genre->books->unique(function ($book) {
                return $book->title . $book->author;
            });

            if ($uniqueGenreBooks->isNotEmpty()) {
                $booksByGenre[$genre->name] = $uniqueGenreBooks;
            }
        }

        return view('perpustakaan', [
            'books'        => $books,
            'booksByGenre' => $booksByGenre,
            'genres'       => Genre::all(),
            'types'        => Type::all(),
            'years'        => Year::all(),
            'demographics' => Demographic::all()
        ]);
    }
}