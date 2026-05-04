<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class PerpustakaanController extends Controller
{
    public function index(Request $request)
    {
        $genres = ['Novel', 'Komik', 'Edukasi', 'Sejarah', 'Teknologi'];

        $query = Book::with('user');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->genre) {
            $query->where('genre', $request->genre);
        }

        $books        = $query->get();
        $booksByGenre = $books->groupBy('genre');

        return view('perpustakaan', compact('books', 'booksByGenre', 'genres'));
    }
}
