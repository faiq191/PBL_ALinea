<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Discussion;
use App\Models\Comment;
use App\Models\Book;
use App\Models\Genre;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $query = Discussion::with('user')->latest();

        // Pencarian Teks
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Multi-Genre
        if ($request->has('genres') && is_array($request->genres)) {
            $query->whereIn('genre', $request->genres);
        }

        $discussions = $query->get();
        $genres = Genre::pluck('name');

        return view('komunitas', compact('discussions', 'genres'));
    }

    public function create()
    {
        $allLibraryBooks = Book::all()->unique(function ($book) {
            return $book->title . $book->author;
        });
        
        $genres = Genre::all();

        return view('discussions.create', compact('allLibraryBooks', 'genres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'content' => 'required',
        ]);

        $imagePath = 'books/default.png';
        $genreName = 'Umum';

        if ($request->source_mode === 'existing') {
            $request->validate(['existing_book_id' => 'required']);
            $book = Book::with('genres')->findOrFail($request->existing_book_id);
            $imagePath = $book->image;
            $genreName = $book->genres->first()->name ?? 'Umum';
        } elseif ($request->source_mode === 'google') {
            $request->validate(['google_volume_id' => 'required']);
            $response = Http::get("https://www.googleapis.com/books/v1/volumes/{$request->google_volume_id}", [
                'key' => env('GOOGLE_BOOKS_API_KEY')
            ]);
            $bookData = $response->json()['volumeInfo'] ?? null;

            if ($bookData) {
                if (!empty($bookData['imageLinks']['thumbnail'])) {
                    $thumbnailUrl = $bookData['imageLinks']['thumbnail'];
                    // Ubah http:// menjadi https:// agar aman dari mixed content
                    $thumbnailUrl = Str::replaceFirst('http://', 'https://', $thumbnailUrl);
                    $imagePath = $thumbnailUrl;
                }
                
                if (!empty($bookData['categories'])) {
                    $parts = explode('/', $bookData['categories'][0]);
                    foreach ($parts as $part) {
                        $cleanStr = trim($part);
                        if (!in_array($cleanStr, ['General', 'Comics & Graphic Novels', 'Manga', 'Comic'])) {
                            $genreName = $cleanStr;
                            break;
                        }
                    }
                }
            }
        } elseif ($request->source_mode === 'manual') {
            $request->validate([
                'image' => 'nullable|image|max:2048',
                'image_url' => 'nullable|url',
                'genre' => 'required'
            ]);

            if (!$request->hasFile('image') && !$request->image_url) {
                return back()->withInput()->withErrors(['image' => 'Anda harus mengunggah file gambar atau memasukkan URL gambar.']);
            }

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('discussions', 'public');
            } elseif ($request->image_url) {
                $imagePath = $request->image_url;
            }

            $genreName = $request->genre;
        }

        Discussion::create([
            'title'    => $request->title,
            'content'  => $request->content,
            'genre'    => $genreName,
            'category' => 'Buku',
            'image'    => $imagePath,
            'user_id'  => auth()->id(),
        ]);

        return redirect('/komunitas');
    }

    public function show($id)
    {
        $discussion = Discussion::with(['user', 'comments' => function ($query) {
            $query->whereNull('parent_id')->with(['user', 'replies.user']);
        }])->findOrFail($id);
        
        return view('discussions.show', compact('discussion'));
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
            'parent_id' => 'nullable|exists:comments,id'
        ]);
        
        // 1. Simpan hasil komentar ke dalam variabel $comment
        $comment = Comment::create([
            'discussion_id' => $id,
            'user_id'       => auth()->id(),
            'parent_id'     => $request->parent_id,
            'content'       => $request->content,
        ]);
        
        // 2. BROADCAST SECARA REAL-TIME (.toOthers() biar pengirim sendiri tidak duplikat chat di layarnya)
        broadcast(new \App\Events\CommentSent($comment))->toOthers();
        
        return back();
    }


    public function edit($id)
    {
        $discussion = Discussion::findOrFail($id);
        if ($discussion->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        $genres = Genre::all();
        return view('discussions.edit', compact('discussion', 'genres'));
    }

    public function update(Request $request, $id)
    {
        $discussion = Discussion::findOrFail($id);
        if ($discussion->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'title'   => 'required',
            'content' => 'required',
            'image'   => 'nullable|image|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $data = [
            'title'   => $request->title,
            'content' => $request->content,
        ];

        if ($request->genre) {
            $data['genre'] = $request->genre;
        }

        if ($request->hasFile('image')) {
            if ($discussion->image && !Str::startsWith($discussion->image, 'books/default.png') && !Str::startsWith($discussion->image, 'http')) {
                Storage::disk('public')->delete($discussion->image);
            }
            $data['image'] = $request->file('image')->store('discussions', 'public');
        } elseif ($request->image_url) {
            if ($discussion->image && !Str::startsWith($discussion->image, 'books/default.png') && !Str::startsWith($discussion->image, 'http')) {
                Storage::disk('public')->delete($discussion->image);
            }
            $data['image'] = $request->image_url;
        }

        $discussion->update($data);
        return redirect('/diskusi/' . $id);
    }

    public function destroy($id)
    {
        $discussion = Discussion::findOrFail($id);
        if ($discussion->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        
        if ($discussion->image && !Str::startsWith($discussion->image, 'books/default.png') && !Str::startsWith($discussion->image, 'books/') && !Str::startsWith($discussion->image, 'http')) {
            Storage::disk('public')->delete($discussion->image);
        }

        $discussion->delete();
        return redirect('/komunitas');
    }

    public function updateComment(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        
        $request->validate(['content' => 'required']);
        $comment->update(['content' => $request->content]);
        return back();
    }

    public function destroyComment($id)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        $comment->delete();
        return back();
    }
}