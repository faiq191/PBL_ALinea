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
use Stichoza\GoogleTranslate\GoogleTranslate;

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
            ->with(['book', 'owner'])
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

        $response = Http::withoutVerifying()->get('https://www.googleapis.com/books/v1/volumes', [
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

            try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

            return redirect('/koleksi');
        }

        if ($request->source_mode === 'google') {
            $request->validate([
                'google_volume_id' => 'required'
            ]);

            $response = Http::withoutVerifying()->get("https://www.googleapis.com/books/v1/volumes/{$request->google_volume_id}", [
                'key' => env('GOOGLE_BOOKS_API_KEY')
            ]);

            $bookData = $response->json()['volumeInfo'] ?? null;

            if (!$bookData) {
                return back()->withErrors(['google_volume_id' => 'Gagal mengambil data dari Google Books.']);
            }

            $imagePath = 'books/default.png';
            $imageLinks = $bookData['imageLinks'] ?? [];

            if (!empty($imageLinks)) {
                $volumeId = $request->google_volume_id;
                // Simpan langsung URL High-resolution fife agar permanen di CDN dan anti-hilang!
                $imagePath = "https://books.google.com/books/publisher/content/images/frontcover/{$volumeId}?fife=w400-h600&source=gbs_api";
            }

            $publishedYear = '2026';
            if (!empty($bookData['publishedDate'])) {
                $publishedYear = substr($bookData['publishedDate'], 0, 4);
            }
            $yearRecord = Year::firstOrCreate(['year' => $publishedYear]);

            $typeName = 'Novel';
            $genreNames = [];
            $translator = new GoogleTranslate('id', 'en'); // Inisialisasi Translator

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
                            try {
                                $translatedStr = ucwords(strtolower($translator->translate($cleanStr)));
                            } catch (\Exception $e) {
                                $translatedStr = $cleanStr; // Fallback jika gagal translate
                            }

                            if (!in_array($translatedStr, $genreNames)) {
                                $genreNames[] = $translatedStr;
                            }
                        }
                    }
                }
            }

            $typeRecord = Type::firstOrCreate(['name' => $typeName]);

            $description = $bookData['description'] ?? null;
            if ($description) {
                try {
                    $description = $translator->translate($description);
                } catch (\Exception $e) {
                    // Fallback to original description if translation fails
                }
            }

            $newBook = Book::create([
                'title'          => $bookData['title'] ?? 'Unknown Title',
                'author'         => isset($bookData['authors']) ? implode(', ', $bookData['authors']) : 'Unknown Author',
                'image'          => $imagePath,
                'user_id'        => auth()->id(),
                'type_id'        => $request->type_id ?? $typeRecord->id,
                'year_id'        => $yearRecord->id,
                'demographic_id' => $request->demographic_id ?? 1,
                'description'    => $description,
            ]);

            foreach ($genreNames as $gName) {
                $genreRecord = Genre::firstOrCreate(['name' => $gName]);
                $newBook->genres()->attach($genreRecord->id);
            }

            try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

            return redirect('/koleksi');
        }

        $request->validate([
            'title'  => 'required',
            'author' => 'required',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_url' => 'nullable|url'
        ]);

        if (!$request->hasFile('image') && !$request->image_url) {
            return back()->withInput()->withErrors(['image' => 'Anda harus mengunggah file gambar atau memasukkan URL gambar.']);
        }

        $imagePath = 'books/default.png';

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('books', 'public');
        } elseif ($request->image_url) {
            $imagePath = $request->image_url;
        }

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

        try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

        return redirect('/koleksi');
    }

    public function home(Request $request = null)
    {
        $query = Book::with('genres')->latest();

        if ($request && $request->genre_id) {
            $query->whereHas('genres', fn($q) => $q->where('genres.id', $request->genre_id));
        }

        $books            = $query->take(4)->get();
        $hotDiscussions   = Discussion::withCount('comments')
            ->with('user')
            ->orderByDesc('comments_count')
            ->orderByDesc('created_at')
            ->take(4)
            ->get();
        $runningEvents    = \App\Models\Event::where('status', 'running')
            ->orderBy('start_date', 'asc')
            ->get();
        
        // Stats
        $totalBorrowed    = auth()->check() ? Loan::where('borrower_id', auth()->id())->where('status', 'dipinjam')->count() : 0;
        $totalDiscussions = Discussion::count();
        $totalBooks       = Book::count();
        $myBooks          = auth()->check() ? Book::where('user_id', auth()->id())->count() : 0;
        
        $genres = \App\Models\Genre::all();

        return view('home', compact('books', 'hotDiscussions', 'runningEvents', 'totalBorrowed', 'totalDiscussions', 'totalBooks', 'myBooks', 'genres'));
    }

    public function show($id)
    {
        // ---------------------------------------------------------
        // BLOK 1: Jika ID bukan angka (Data dari Google Books API)
        // ---------------------------------------------------------
        if (!is_numeric($id)) {
            $response = Http::withoutVerifying()->get("https://www.googleapis.com/books/v1/volumes/{$id}", [
                'key' => env('GOOGLE_BOOKS_API_KEY')
            ]);

            if (!$response->successful()) {
                abort(404);
            }

            $volume = $response->json()['volumeInfo'];

            $translator = new GoogleTranslate('id', 'en'); // Inisialisasi Translator

            $description = $volume['description'] ?? 'Deskripsi belum tersedia.';
            if ($description && $description !== 'Deskripsi belum tersedia.') {
                try {
                    $description = $translator->translate($description);
                } catch (\Exception $e) {
                    // Fallback to original if translation fails
                }
            }

            $book = new Book([
                'title'       => $volume['title'] ?? 'Unknown Title',
                'author'      => isset($volume['authors']) ? implode(', ', $volume['authors']) : 'Unknown Author',
                'description' => $description,
            ]);

            $imageLinks = $volume['imageLinks'] ?? [];
            if (!empty($imageLinks)) {
                // High-resolution fife URL for preview/show (dynamic scaling, flat edges)
                $thumbnail = "https://books.google.com/books/publisher/content/images/frontcover/{$id}?fife=w400-h600&source=gbs_api";
            } else {
                $thumbnail = 'books/default.png';
            }
            $book->image = $thumbnail;

            // Tandai properti khusus untuk Blade agar tombol berfungsi
            $book->is_google_api = true;
            $book->google_id = $id;

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
                            try {
                                $translatedStr = ucwords(strtolower($translator->translate($cleanStr)));
                            } catch (\Exception $e) {
                                $translatedStr = $cleanStr; // Fallback jika gagal translate
                            }

                            if (!in_array($translatedStr, $genreNames)) {
                                $genreNames[] = $translatedStr;
                            }
                        }
                    }
                }
            }

            $book->setRelation('type', (object)['name' => $typeName]);
            $book->setRelation('year', (object)['year' => substr($volume['publishedDate'] ?? '', 0, 4) ?: '-']);
            $book->setRelation('demographic', (object)['name' => '-']);
            $book->setRelation('genres', collect(array_map(fn($name) => (object)['name' => $name], $genreNames)));

            // Cari pengguna lain yang punya buku dengan judul & penulis yang sama
            $otherOwners = Book::where('title', $book->title)
                ->where('author', $book->author)
                ->where('user_id', '!=', auth()->id())
                ->with('user')
                ->get();

            return view('books.show', compact('book', 'otherOwners'));
        }

        // ---------------------------------------------------------
        // BLOK 2: Jika ID adalah angka (Data dari Database Lokal)
        // ---------------------------------------------------------
        $book = Book::with(['genres', 'type', 'year', 'demographic', 'user'])->findOrFail($id);
        
        // Tandai bahwa ini bukan buku API
        $book->is_google_api = false;
        
        $books = Book::with(['genres'])->latest()->take(4)->get();

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
            'image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_url' => 'nullable|url'
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
            if ($book->image && !\Illuminate\Support\Str::startsWith($book->image, 'http') && file_exists(public_path('storage/' . $book->image))) {
                unlink(public_path('storage/' . $book->image));
            }
            $data['image'] = $request->file('image')->store('books', 'public');
        } elseif ($request->image_url) {
            if ($book->image && !\Illuminate\Support\Str::startsWith($book->image, 'http') && file_exists(public_path('storage/' . $book->image))) {
                unlink(public_path('storage/' . $book->image));
            }
            $data['image'] = $request->image_url;
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

        if ($book->image && !\Illuminate\Support\Str::startsWith($book->image, 'http') && file_exists(public_path('storage/' . $book->image))) {
            unlink(public_path('storage/' . $book->image));
        }

        $book->genres()->detach();
        $book->delete();

        try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

        return back();
    }
}