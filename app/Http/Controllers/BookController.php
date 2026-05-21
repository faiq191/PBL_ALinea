<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Type;
use App\Models\Year;
use App\Models\Demographic;
use App\Models\Discussion;
use App\Models\Loan;

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

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        $myLoans = Loan::where('borrower_id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam'])
            ->with('book')
            ->get();

        $incomingRequests = Loan::where('owner_id', auth()->id())
            ->where('status', 'pending')
            ->with(['book', 'borrower'])
            ->get();

        $lentBooks = Loan::where('owner_id', auth()->id())
            ->where('status', 'dipinjam')
            ->with(['book', 'borrower'])
            ->get();

        return view('koleksi', [
            'books'            => $query->get(),
            'genres'           => Genre::all(),
            'types'            => Type::all(),
            'years'            => Year::all(),
            'demographics'     => Demographic::all(),
            'myLoans'          => $myLoans,
            'incomingRequests' => $incomingRequests,
            'lentBooks'        => $lentBooks,
        ]);
    }

    public function create()
    {
        $allLibraryBooks = Book::all()->unique(function ($book) {
            return $book->title . $book->author;
        });

        return view('books.create', [
            'genres'           => Genre::all(),
            'types'            => Type::all(),
            'years'            => Year::all(),
            'demographics'     => Demographic::all(),
            'allLibraryBooks'  => $allLibraryBooks,
        ]);
    }

    public function searchGoogleBooks(Request $request)
    {
        $query = $request->query('q');
        if (!$query) {
            return response()->json([]);
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q'          => $query,
            'key'        => env('GOOGLE_BOOKS_API_KEY'),
            'maxResults' => 5
        ]);

        return response()->json($response->json()['items'] ?? []);
    }

    public function store(Request $request)
    {
        if ($request->source_mode === 'existing') {
            $existingBook = Book::with('genres')->findOrFail($request->existing_book_id);
            
            $newBook = $existingBook->replicate();
            $newBook->user_id = auth()->id();
            $newBook->save();
            
            $newBook->genres()->sync($existingBook->genres->pluck('id'));
            
            return redirect('/koleksi');
        }

        if ($request->source_mode === 'google') {
            $request->validate([
                'google_volume_id' => 'required'
            ]);

            $response = Http::get("https://www.googleapis.com/books/v1/volumes/{$request->google_volume_id}", [
                'key' => env('GOOGLE_BOOKS_API_KEY')
            ]);

            $bookData = $response->json()['volumeInfo'] ?? null;

            if (!$bookData) {
                return back()->withErrors(['google_volume_id' => 'Gagal mengambil data dari Google Books.']);
            }

            $imagePath = 'books/default.png';
            $thumbnailUrl = $bookData['imageLinks']['thumbnail'] ?? null;

            if ($thumbnailUrl) {
                $imageContent = Http::get($thumbnailUrl)->body();
                $imageName = 'books/' . Str::random(40) . '.jpg';
                Storage::disk('public')->put($imageName, $imageContent);
                $imagePath = $imageName;
            }

            $publishedYear = '2026';
            if (!empty($bookData['publishedDate'])) {
                $publishedYear = substr($bookData['publishedDate'], 0, 4);
            }
            $yearRecord = Year::firstOrCreate(['year' => $publishedYear]);

            $typeName = 'Novel';
            $genreNames = [];

            if (!empty($bookData['categories'])) {
                foreach ($bookData['categories'] as $categoryStr) {
                    $parts = explode('/', $categoryStr);
                    foreach ($parts as $part) {
                        $cleanStr = trim($part);
                        
                        if (stripos($cleanStr, 'manga') !== false) {
                            $typeName = 'Manga';
                        } elseif (stripos($cleanStr, 'comic') !== false || stripos($cleanStr, 'graphic novel') !== false) {
                            if ($typeName !== 'Manga') $typeName = 'Comic';
                        }

                        if (!in_array($cleanStr, ['General', 'Comics & Graphic Novels'])) {
                            if (!in_array($cleanStr, $genreNames)) {
                                $genreNames[] = $cleanStr;
                            }
                        }
                    }
                }
            }

            $typeRecord = Type::firstOrCreate(['name' => $typeName]);

            $newBook = Book::create([
                'title'          => $bookData['title'] ?? 'Unknown Title',
                'author'         => isset($bookData['authors']) ? implode(', ', $bookData['authors']) : 'Unknown Author',
                'image'          => $imagePath,
                'user_id'        => auth()->id(),
                'type_id'        => $request->type_id ?? $typeRecord->id,
                'year_id'        => $yearRecord->id,
                'demographic_id' => $request->demographic_id ?? 1,
                'description'    => $bookData['description'] ?? null,
            ]);

            foreach ($genreNames as $gName) {
                $genreRecord = Genre::firstOrCreate(['name' => $gName]);
                $newBook->genres()->attach($genreRecord->id);
            }

            return redirect('/koleksi');
        }

        $request->validate([
            'title'  => 'required',
            'author' => 'required',
            'image'  => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $imagePath = $request->file('image')->store('books', 'public');

        Book::create([
            'title'          => $request->title,
            'author'         => $request->author,
            'image'          => $imagePath,
            'user_id'        => auth()->id(),
            'type_id'        => $request->type_id,
            'year_id'        => $request->year_id,
            'demographic_id' => $request->demographic_id,
            'description'    => $request->description,
        ])->genres()->attach($request->genre_ids);

        return redirect('/koleksi');
    }

    public function home(Request $request = null)
    {
        $query = auth()->check()
            ? Book::where('user_id', auth()->id())->with('genres')
            : Book::with('genres');

        if ($request && $request->genre_id) {
            $query->whereHas('genres', fn($q) => $q->where('genres.id', $request->genre_id));
        }

        $books       = $query->take(4)->get();
        $discussions = Discussion::latest()->take(5)->get();
        $totalBooks  = Book::count();
        $myBooks     = auth()->check() ? Book::where('user_id', auth()->id())->count() : 0;

        return view('home', compact('books', 'discussions', 'totalBooks', 'myBooks'));
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            $response = Http::get("https://www.googleapis.com/books/v1/volumes/{$id}", [
                'key' => env('GOOGLE_BOOKS_API_KEY')
            ]);

            if (!$response->successful()) {
                abort(404);
            }

            $volume = $response->json()['volumeInfo'];
            
            $book = new Book([
                'id'          => $id,
                'title'       => $volume['title'] ?? 'Unknown Title',
                'author'      => isset($volume['authors']) ? implode(', ', $volume['authors']) : 'Unknown Author',
                'description' => $volume['description'] ?? 'Deskripsi belum tersedia.',
            ]);
            
            $thumbnail = $volume['imageLinks']['thumbnail'] ?? 'books/default.png';
            $book->image = str_replace('http://', 'https://', $thumbnail);

            $typeName = 'Novel';
            $genreNames = [];

            if (!empty($volume['categories'])) {
                foreach ($volume['categories'] as $categoryStr) {
                    $parts = explode('/', $categoryStr);
                    foreach ($parts as $part) {
                        $cleanStr = trim($part);
                        
                        if (stripos($cleanStr, 'manga') !== false) {
                            $typeName = 'Manga';
                        } elseif (stripos($cleanStr, 'comic') !== false || stripos($cleanStr, 'graphic novel') !== false) {
                            if ($typeName !== 'Manga') $typeName = 'Comic';
                        }

                        if (!in_array($cleanStr, ['General', 'Comics & Graphic Novels'])) {
                            if (!in_array($cleanStr, $genreNames)) {
                                $genreNames[] = $cleanStr;
                            }
                        }
                    }
                }
            }

            $book->setRelation('type', (object)['name' => $typeName]);
            $book->setRelation('year', (object)['year' => substr($volume['publishedDate'] ?? '', 0, 4) ?: '-']);
            $book->setRelation('demographic', (object)['name' => '-']);
            $book->setRelation('genres', collect(array_map(fn($name) => (object)['name' => $name], $genreNames)));

            $otherOwners = Book::where('title', $book->title)
                ->where('author', $book->author)
                ->where('user_id', '!=', auth()->id())
                ->with('user')
                ->get();

            return view('books.show', compact('book', 'otherOwners'));
        }

        $book = Book::with(['genres', 'type', 'year', 'demographic', 'user'])->findOrFail($id);

        $otherOwners = Book::where('title', $book->title)
            ->where('author', $book->author)
            ->where('user_id', '!=', auth()->id())
            ->with('user')
            ->get();

        return view('books.show', compact('book', 'otherOwners'));
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);

        if ($book->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        return view('books.edit', [
            'book'         => $book,
            'genres'       => Genre::all(),
            'types'        => Type::all(),
            'years'        => Year::all(),
            'demographics' => Demographic::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'  => 'required',
            'author' => 'required',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $book = Book::findOrFail($id);

        if ($book->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $data = [
            'title'          => $request->title,
            'author'         => $request->author,
            'type_id'        => $request->type_id,
            'year_id'        => $request->year_id,
            'demographic_id' => $request->demographic_id,
            'description'    => $request->description,
        ];

        if ($request->hasFile('image')) {
            if ($book->image && file_exists(public_path('storage/' . $book->image))) {
                unlink(public_path('storage/' . $book->image));
            }
            $data['image'] = $request->file('image')->store('books', 'public');
        }

        $book->update($data);
        $book->genres()->sync($request->genre_ids ?? []);

        return redirect('/koleksi');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        if ($book->image && file_exists(public_path('storage/' . $book->image))) {
            unlink(public_path('storage/' . $book->image));
        }

        $book->genres()->detach();
        $book->delete();

        return back();
    }
}