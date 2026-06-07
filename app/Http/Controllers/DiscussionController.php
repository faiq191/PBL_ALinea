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
            'content' => 'nullable|required_without_all:attachment,attachment_url',
            'parent_id' => 'nullable|exists:comments,id',
            'attachment' => 'nullable|image|max:10240',
            'attachment_type' => 'nullable|string',
            'attachment_url' => 'nullable|string',
        ]);
        
        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentType = 'image';
            $attachmentPath = $file->store('comments', 'public');
        } elseif ($request->attachment_type && $request->attachment_url) {
            $attachmentType = $request->attachment_type;
            $attachmentPath = $request->attachment_url;
            if ($attachmentType === 'gmaps') {
                $parts = explode('|', $request->attachment_url);
                if (count($parts) > 1) {
                    $attachmentPath = $parts[0]; // coordinates: lat,lng
                    $attachmentName = $parts[1]; // location name
                }
            }
        }

        // 1. Simpan hasil komentar ke dalam variabel $comment
        $comment = Comment::create([
            'discussion_id' => $id,
            'user_id'       => auth()->id(),
            'parent_id'     => $request->parent_id,
            'content'       => $request->content ?? '',
            'attachment_path'=> $attachmentPath,
            'attachment_name'=> $attachmentName,
            'attachment_type'=> $attachmentType,
        ]);
        
        // Kirim Notifikasi ke Pemilik Diskusi / Pemilik Komentar Utama / Orang yang Di-tag
        try {
            $discussion = Discussion::findOrFail($id);
            $notifiedUserIds = [];

            // Build content preview with attachment placeholders
            $previewText = strip_tags($comment->content) ?: '';
            $previewText = trim($previewText);
            
            if ($comment->attachment_type === 'image') {
                $previewText = $previewText ? $previewText . ' [Gambar]' : '[Gambar]';
            } elseif ($comment->attachment_type === 'tenor') {
                $previewText = $previewText ? $previewText . ' [GIF]' : '[GIF]';
            } elseif ($comment->attachment_type === 'gmaps') {
                $previewText = $previewText ? $previewText . ' [Peta Lokasi]' : '[Peta Lokasi]';
            }

            $cleanedContent = \Illuminate\Support\Str::limit($previewText, 60);

            if ($request->parent_id) {
                $parentComment = Comment::findOrFail($request->parent_id);
                $targetUserId = $parentComment->user_id;
                
                // Smart Mention Detection: check if reply starts with @Username to route the notification to the actual recipient
                if (str_starts_with($comment->content, '@')) {
                    $contentWithoutAt = substr($comment->content, 1);
                    $allUsers = \App\Models\User::all()->sortByDesc(function($u) {
                        return strlen($u->name);
                    });
                    
                    foreach ($allUsers as $u) {
                        if (str_starts_with($contentWithoutAt, $u->name)) {
                            $targetUserId = $u->id;
                            break;
                        }
                    }
                }

                // Only send notification if the target recipient is NOT the sender itself!
                if ($targetUserId !== auth()->id()) {
                    \App\Models\CustomNotification::send(
                        $targetUserId,
                        'Balasan Komentar Baru',
                        auth()->user()->name . ' membalas: "' . $cleanedContent . '" di diskusi: "' . $discussion->title . '".',
                        '/diskusi/' . $id . '#comment-' . $comment->id,
                        auth()->id()
                    );
                    $notifiedUserIds[] = $targetUserId;
                }
            } else {
                if ($discussion->user_id !== auth()->id()) {
                    \App\Models\CustomNotification::send(
                        $discussion->user_id,
                        'Komentar Baru di Diskusi Anda',
                        auth()->user()->name . ' menulis: "' . $cleanedContent . '" di diskusi Anda: "' . $discussion->title . '".',
                        '/diskusi/' . $id . '#comment-' . $comment->id,
                        auth()->id()
                    );
                    $notifiedUserIds[] = $discussion->user_id;
                }
            }

            // Handle Mentions (@Name)
            preg_match_all('/@([a-zA-Z0-9\s]{2,30})/i', $comment->content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $trimmedName = trim(rtrim($match, '.,!?;:'));
                    
                    // Cari user yang namanya cocok dengan teks mention (bisa exact atau prefix untuk nama panjang)
                    $mentionedUser = \App\Models\User::where(function($query) use ($trimmedName) {
                            $query->where('name', '=', $trimmedName)
                                  ->orWhere('name', 'like', $trimmedName . '%');
                        })
                        ->where('id', '!=', auth()->id())
                        ->whereNotIn('id', $notifiedUserIds)
                        ->first();

                    if ($mentionedUser) {
                        \App\Models\CustomNotification::send(
                            $mentionedUser->id,
                            'Anda Disebut dalam Diskusi',
                            auth()->user()->name . ' menyebut Anda: "' . $cleanedContent . '" di diskusi: "' . $discussion->title . '".',
                            '/diskusi/' . $id . '#comment-' . $comment->id,
                            auth()->id()
                        );
                        // Hindari pengiriman notifikasi ganda ke user yang sama dalam 1 request
                        $notifiedUserIds[] = $mentionedUser->id;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Notification sending failed: ' . $e->getMessage());
        }
        
        // 2. BROADCAST SECARA REAL-TIME (.toOthers() biar pengirim sendiri tidak duplikat chat di layarnya)
        try {
            broadcast(new \App\Events\CommentSent($comment))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Reverb broadcast failed: ' . $e->getMessage());
        }
        
        return redirect('/diskusi/' . $id . '#comment-' . $comment->id);
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
        
        $request->validate([
            'content' => 'required',
            'attachment_path' => 'nullable|string',
            'attachment_name' => 'nullable|string',
        ]);
        
        $updateData = ['content' => $request->content];
        if ($request->has('attachment_path')) {
            $updateData['attachment_path'] = $request->attachment_path;
        }
        if ($request->has('attachment_name')) {
            $updateData['attachment_name'] = $request->attachment_name;
        }
        
        $comment->update($updateData);
        
        // Broadcast pembaruan komentar secara real-time
        try {
            broadcast(new \App\Events\CommentUpdated($comment))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Reverb broadcast failed: ' . $e->getMessage());
        }
        
        return redirect('/diskusi/' . $comment->discussion_id . '#comment-' . $comment->id);
    }

    public function destroyComment($id)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        
        // Tentukan penanda (dihapus oleh pengguna sendiri atau admin/moderator)
        $deleteMarker = (auth()->id() === $comment->user_id) ? '_deleted_by_user_' : '_deleted_by_admin_';
        
        // Update isi komentar menjadi penanda terhapus (record database, nama, & foto profil TETAP UTUH!)
        $comment->update(['content' => $deleteMarker]);
        
        // Broadcast pembaruan komentar secara real-time (menggunakan CommentUpdated karena record tetap ada)
        try {
            broadcast(new \App\Events\CommentUpdated($comment))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Reverb broadcast failed: ' . $e->getMessage());
        }
        
        return redirect('/diskusi/' . $comment->discussion_id . '#comment-' . $comment->id);
    }
}