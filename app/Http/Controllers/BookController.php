<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
        public function index()
    {
        $books = Book::all();
        return view('koleksi', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $imagePath = $request->file('image')->store('books', 'public');

        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'image' => $imagePath,
        ]);

        return redirect('/koleksi');
    }
}
