<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discussion;
use App\Models\Comment;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $genres = ['Drama', 'Fantasi', 'Romansa', 'Misteri', 'Komedi', 'Horor', 'Sejarah', 'Sains'];

        $query = Discussion::withCount('comments');

        if ($request->genre) {
            $query->where('genre', $request->genre);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->sort == 'terpopuler') {
            $query->orderBy('comments_count', 'desc');
        } else {
            $query->latest();
        }

        $discussions = $query->get();

        return view('komunitas', compact('discussions', 'genres'));
    }

    public function create()
    {
        $genres = ['Drama', 'Fantasi', 'Romansa', 'Misteri', 'Komedi', 'Horor', 'Sejarah', 'Sains'];
        return view('discussions.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'genre' => 'nullable',
            'category' => 'nullable',
        ]);

        Discussion::create([
            'title' => $request->title,
            'genre' => $request->genre,
            'category' => $request->category,
            'user_id' => auth()->id(),
        ]);

        return redirect('/komunitas');
    }

    public function show($id)
    {
        $discussion = Discussion::with(['user', 'comments.user'])->findOrFail($id);
        return view('discussions.show', compact('discussion'));
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required'
        ]);

        Comment::create([
            'discussion_id' => $id,
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        return back();
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }
}
