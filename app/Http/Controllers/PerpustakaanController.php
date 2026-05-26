<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Type;
use App\Models\Year;
use App\Models\Demographic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PerpustakaanController extends Controller
{
    public function index(Request $request)
    {
        $hasFilters = $request->hasAny(['search', 'genre_ids', 'type_ids', 'demo_ids', 'year_ids', 'author']);
        
        $books = collect();
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
            if ($request->year_ids) {
                $localQuery->whereIn('year_id', $request->year_ids);
            }
            if ($request->demo_ids) {
                $localQuery->whereIn('demographic_id', $request->demo_ids);
            }

            $localBooks = $localQuery->get()->unique(function ($book) {
                return strtolower($book->title) . strtolower($book->author);
            });

            foreach ($localBooks as $localBook) {
                $books->push((object)[
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

            foreach ($items as $item) {
                $volume = $item['volumeInfo'] ?? null;
                if (!$volume) continue;

                $title = $volume['title'] ?? 'Unknown Title';
                $author = isset($volume['authors']) ? implode(', ', $volume['authors']) : 'Unknown Author';

                $alreadyExists = $localBooks->contains(function ($local) use ($title, $author) {
                    return strtolower($local->title) === strtolower($title) || strtolower($local->author) === strtolower($author);
                });

                if (!$alreadyExists) {
                    $thumbnail = $volume['imageLinks']['thumbnail'] ?? 'books/default.png';
                    $thumbnail = str_replace('http://', 'https://', $thumbnail);

                    $genreNames = [];
                    if (!empty($volume['categories'])) {
                        foreach ($volume['categories'] as $categoryStr) {
                            $parts = explode('/', $categoryStr);
                            foreach ($parts as $part) {
                                $cleanStr = trim($part);
                                if (!in_array($cleanStr, ['General', 'Comics & Graphic Novels'])) {
                                    if (!in_array($cleanStr, $genreNames)) {
                                        $genreNames[] = $cleanStr;
                                    }
                                }
                            }
                        }
                    }

                    $books->push((object)[
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
        } else {
            foreach (Genre::with(['books.user', 'books.genres'])->get() as $genre) {
                $uniqueBooks = $genre->books->unique(function ($book) {
                    return strtolower($book->title) . strtolower($book->author);
                });

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
                    });
                }
            }
        }

        return view('perpustakaan', [
            'books'        => $books,
            'booksByGenre' => $booksByGenre,
            'genres'       => Genre::all(),
            'types'        => Type::all(),
            'years'        => Year::all(),
            'demographics' => Demographic::all(),
            'hasFilters'   => $hasFilters
        ]);
    }
}