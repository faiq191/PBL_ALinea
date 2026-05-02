<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Discussion;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::where('user_id', auth()->id())->get();
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
            'user_id' => auth()->id()
        ]);

        return redirect('/koleksi');
    }

    public function home()
    {
        $books = auth()->check()
            ? Book::where('user_id', auth()->id())->take(4)->get()
            : collect();

        $discussions = \App\Models\Discussion::latest()->take(5)->get();

        return view('home', compact('books', 'discussions'));
    }

    public function show($id)
    {
        $discussion = Discussion::with('user')->findOrFail($id);
        return view('discussions.show', compact('discussion'));
    }
}
