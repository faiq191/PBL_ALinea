<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Discussion;
use App\Models\Genre;
use App\Models\Type;
use App\Models\Demographic;
use App\Models\Year;

class BookController extends Controller
{
        public function index(Request $request)
    {
        $query = Book::where('user_id', auth()->id())->with(['genres', 'type', 'year']);

        if ($request->genre_id) {
            $query->whereHas('genres', fn($q) => $q->where('genres.id', $request->genre_id));
        }

        if ($request->type_id) {
            $query->where('type_id', $request->type_id);
        }

        $myLoans = \App\Models\Loan::where('borrower_id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam'])
            ->with('book')
            ->get();

        return view('koleksi', [
            'books'      => $query->get(),
            'genres'     => Genre::all(),
            'types'      => Type::all(),
            'years'      => Year::all(),
            'demographics' => Demographic::all(),
            'myLoans'    => $myLoans,
        ]);
    }

    public function create()
    {
        $genres = \App\Models\Genre::all();
        $types = \App\Models\Type::all();
        $demographics = \App\Models\Demographic::all();
        $years = \App\Models\Year::all();

        return view('books.create', compact('genres', 'types', 'demographics', 'years'));
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
            'type_id' => $request->type_id,
            'year_id' => $request->year_id,
            'demographic_id' => $request->demographic_id,
        ])->genres()->attach($request->genre_ids); // For many-to-many genres

        return redirect('/koleksi');
    }

        public function home(Request $request = null)
    {
        $query = auth()->check()
            ? Book::where('user_id', auth()->id())
            : Book::query();

        if ($request && $request->genre_id) {
            $query->whereHas('genres', fn($q) => $q->where('genres.id', $request->genre_id));
        }

        $books = $query->take(4)->get();
        $discussions = Discussion::latest()->take(5)->get();
        $totalBooks = Book::count();

        return view('home', compact('books', 'discussions', 'totalBooks'));
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

        $genres = Genre::all();
        $types = Type::all();
        $demographics = Demographic::all();
        $years = Year::all();

        return view('books.edit', compact('book', 'genres', 'types', 'demographics', 'years'));
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
            'type_id' => $request->type_id,
            'year_id' => $request->year_id,
            'demographic_id' => $request->demographic_id,
        ];

        if ($request->hasFile('image')) {
            if ($book->image && file_exists(public_path('storage/' . $book->image))) {
                unlink(public_path('storage/' . $book->image));
            }
            $data['image'] = $request->file('image')->store('books', 'public');
        }

        $book->update($data);
        $book->genres()->sync($request->genre_ids);
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
