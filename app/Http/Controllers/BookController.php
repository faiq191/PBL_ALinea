<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Discussion;

class BookController extends Controller
{
    private $genres = ['Novel', 'Komik', 'Edukasi', 'Sejarah', 'Teknologi', 'Bisnis', 'Kesehatan', 'Sains', 'Biografi', 'Makanan'];

    public function index(Request $request)
    {
        $query = Book::where('user_id', auth()->id());

        if ($request->genre) {
            $query->where('genre', $request->genre);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        $books = $query->get();
        $genres = $this->genres;

        return view('koleksi', compact('books', 'genres'));
    }

    public function create()
    {
        $genres = $this->genres;
        return view('books.create', compact('genres'));
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
            'user_id' => auth()->id(),
            'genre' => $request->genre
        ]);

        return redirect('/koleksi');
    }

    public function home(Request $request = null)
    {
        $query = auth()->check()
            ? Book::where('user_id', auth()->id())
            : Book::query();

        if ($request && $request->genre) {
            $query->where('genre', $request->genre);
        }

        $books = $query->take(4)->get();
        $discussions = Discussion::latest()->take(5)->get();

        return view('home', compact('books', 'discussions'));
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);
        return view('books.show', compact('book'));
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);

        if ($book->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $genres = $this->genres;
        return view('books.edit', compact('book', 'genres'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $book = Book::findOrFail($id);

        if ($book->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $data = [
            'title' => $request->title,
            'author' => $request->author,
            'genre' => $request->genre
        ];

        if ($request->hasFile('image')) {
            if ($book->image && file_exists(public_path('storage/' . $book->image))) {
                unlink(public_path('storage/' . $book->image));
            }
            $data['image'] = $request->file('image')->store('books', 'public');
        }

        $book->update($data);
        return redirect('/koleksi');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->image && file_exists(public_path('storage/' . $book->image))) {
            unlink(public_path('storage/' . $book->image));
        }

        $book->delete();
        return back();
    }
}
