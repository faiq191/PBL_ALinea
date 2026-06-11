<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Type;
use App\Models\Year;
use App\Models\Demographic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Stichoza\GoogleTranslate\GoogleTranslate;

class PerpustakaanController extends Controller
{
    public function index(Request $request)
    {
        $hasFilters = $request->hasAny(['search', 'genre_ids', 'type_ids', 'demo_ids', 'year_from', 'year_to']);
        
        $books = collect();
        $localBooksMapped = collect();
        $googleBooksMapped = collect();
        $booksByGenre = [];

        if ($hasFilters) {
            $items = [];
            
            if ($request->get('local_only') !== 'true') {
                $googleQuery = $request->search;
                if (!$googleQuery) {
                    if ($request->genre_ids) {
                        $firstGenre = Genre::find($request->genre_ids[0]);
                        $googleQuery = $firstGenre ? $firstGenre->name : 'best seller';
                    } else {
                        $googleQuery = 'best seller';
                    }
                }

                $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                    'q'          => $googleQuery,
                    'key'        => env('GOOGLE_BOOKS_API_KEY'),
                    'maxResults' => 10
                ]);

                $items = $response->json()['items'] ?? [];
            }

            $localQuery = Book::with(['user', 'genres', 'type', 'year', 'demographic']);

            if ($request->search) {
                $localQuery->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('author', 'like', "%{$request->search}%");
                });
            }

            if ($request->genre_ids) {
                $localQuery->whereHas('genres', fn($q) => $q->whereIn('genres.id', $request->genre_ids));
            }
            if ($request->type_ids) {
                $localQuery->whereIn('type_id', $request->type_ids);
            }
            if ($request->year_from) {
                $localQuery->whereHas('year', fn($q) => $q->where('year', '>=', $request->year_from));
            }
            if ($request->year_to) {
                $localQuery->whereHas('year', fn($q) => $q->where('year', '<=', $request->year_to));
            }
            if ($request->demo_ids) {
                $localQuery->whereIn('demographic_id', $request->demo_ids);
            }

            $localBooks = $localQuery->get()->unique(function ($book) {
                return strtolower($book->title) . strtolower($book->author);
            })->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE);

            foreach ($localBooks as $localBook) {
                $localBooksMapped->push((object)[
                    'id'            => $localBook->id,
                    'title'         => $localBook->title,
                    'author'        => $localBook->author,
                    'image'         => $localBook->image,
                    'genres'        => $localBook->genres,
                    'user_id'       => $localBook->user_id,
                    'is_google_api' => false,
                    'google_url'    => null
                ]);
            }
            $localBooksMapped = $localBooksMapped->values();

            $translator = new GoogleTranslate('id', 'en'); // Inisialisasi Translator

            foreach ($items as $item) {
                $volume = $item['volumeInfo'] ?? null;
                if (!$volume) continue;

                $title = $volume['title'] ?? 'Unknown Title';
                $author = isset($volume['authors']) ? implode(', ', $volume['authors']) : 'Unknown Author';

                $alreadyExists = $localBooks->contains(function ($local) use ($title, $author) {
                    return strtolower($local->title) === strtolower($title) && strtolower($local->author) === strtolower($author);
                });

                if (!$alreadyExists) {
                    // 1. Filter: Tahun Rilis
                    $publishedYear = null;
                    if (!empty($volume['publishedDate'])) {
                        $publishedYear = (int) substr($volume['publishedDate'], 0, 4);
                    }
                    if ($request->year_from || $request->year_to) {
                        if (!$publishedYear) {
                            continue;
                        }
                        if ($request->year_from && $publishedYear < (int)$request->year_from) {
                            continue;
                        }
                        if ($request->year_to && $publishedYear > (int)$request->year_to) {
                            continue;
                        }
                    }

                    $thumbnail = $volume['imageLinks']['thumbnail'] ?? 'books/default.png';
                    $thumbnail = str_replace('http://', 'https://', $thumbnail);

                    $genreNames = [];
                    if (!empty($volume['categories'])) {
                        foreach ($volume['categories'] as $categoryStr) {
                            $parts = explode('/', $categoryStr);
                            foreach ($parts as $part) {
                                $cleanStr = trim($part);
                                if (!in_array($cleanStr, ['General', 'Comics & Graphic Novels'])) {
                                    
                                    $lowerClean = strtolower($cleanStr);
                                    if (in_array($lowerClean, ['self-help', 'self-improvement'])) {
                                        $translatedStr = 'Pengembangan Diri';
                                    } elseif ($lowerClean === 'light novel') {
                                        $translatedStr = 'Novel Ringan';
                                    } else {
                                        try {
                                            $translatedStr = ucwords(strtolower($translator->translate($cleanStr)));
                                        } catch (\Exception $e) {
                                            $translatedStr = $cleanStr; // Fallback jika gagal translate
                                        }
                                    }

                                    if (!in_array($translatedStr, $genreNames)) {
                                        $genreNames[] = $translatedStr;
                                    }
                                }
                            }
                        }
                    }

                    // 2. Filter: Genre
                    if ($request->genre_ids) {
                        $selectedGenreNames = Genre::whereIn('id', $request->genre_ids)->pluck('name')->map(fn($n) => strtolower($n))->toArray();
                        $hasMatchingGenre = false;
                        foreach ($genreNames as $gName) {
                            if (in_array(strtolower($gName), $selectedGenreNames)) {
                                $hasMatchingGenre = true;
                                break;
                            }
                        }
                        if (!$hasMatchingGenre) {
                            continue;
                        }
                    }

                    // 3. Filter: Tipe
                    $typeName = 'Novel';
                    foreach ($genreNames as $gName) {
                        $lowerClean = strtolower($gName);
                        if (stripos($lowerClean, 'manga') !== false) {
                            $typeName = 'Manga';
                        } elseif (stripos($lowerClean, 'komik') !== false || stripos($lowerClean, 'comic') !== false || stripos($lowerClean, 'graphic novel') !== false) {
                            if ($typeName !== 'Manga') $typeName = 'Comic';
                        }
                    }
                    if ($request->type_ids) {
                        $selectedTypeNames = Type::whereIn('id', $request->type_ids)->pluck('name')->map(fn($n) => strtolower($n))->toArray();
                        if (!in_array(strtolower($typeName), $selectedTypeNames)) {
                            continue;
                        }
                    }

                    $googleBooksMapped->push((object)[
                        'id'            => $item['id'],
                        'title'         => $title,
                        'author'        => $author,
                        'image'         => $thumbnail,
                        'genres'        => collect(array_map(fn($name) => (object)['name' => $name], $genreNames)),
                        'user_id'       => null,
                        'is_google_api' => true,
                        'google_url'    => $volume['infoLink'] ?? '#'
                    ]);
                }
            }

            $googleBooksMapped = $googleBooksMapped->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)->values();
            $books = $localBooksMapped->concat($googleBooksMapped)->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)->values();
        } else {
            $genresPaginated = Genre::has('books')
                ->with(['books.user', 'books.genres'])
                ->orderBy('name', 'asc')
                ->paginate(15)
                ->withQueryString();

            foreach ($genresPaginated as $genre) {
                $uniqueBooks = $genre->books->unique(function ($book) {
                    return strtolower($book->title) . strtolower($book->author);
                })->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE);

                if ($uniqueBooks->isNotEmpty()) {
                    $booksByGenre[$genre->name] = $uniqueBooks->map(function ($localBook) {
                        return (object)[
                            'id'            => $localBook->id,
                            'title'         => $localBook->title,
                            'author'        => $localBook->author,
                            'image'         => $localBook->image,
                            'genres'        => $localBook->genres,
                            'user_id'       => $localBook->user_id,
                            'is_google_api' => false,
                            'google_url'    => null
                        ];
                    })->values();
                }
            }
        }

        return view('perpustakaan', [
            'books'        => $books,
            'localBooks'   => $localBooksMapped,
            'googleBooks'  => $googleBooksMapped,
            'booksByGenre' => $booksByGenre,
            'genres'       => Genre::orderBy('name', 'asc')->get(),
            'types'        => Type::orderBy('name', 'asc')->get(),
            'years'        => Year::orderBy('year', 'desc')->get(),
            'demographics' => Demographic::orderBy('name', 'asc')->get(),
            'hasFilters'   => $hasFilters,
            'genresPaginated' => $genresPaginated ?? null
        ]);
    }
}