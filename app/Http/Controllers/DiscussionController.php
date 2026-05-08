<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discussion;
use App\Models\Comment;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $query = Discussion::with('user')->latest();

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->genre) {
            $query->where('genre', $request->genre);
        }

        $discussions = $query->get();

        // Ambil semua genre unik dari diskusi yang ada
        $genres = Discussion::whereNotNull('genre')
            ->distinct()
            ->pluck('genre');

        return view('komunitas', compact('discussions', 'genres'));
    }

    public function create()
    {
        return view('discussions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required',
            'genre'    => 'nullable',
            'category' => 'nullable',
        ]);

        Discussion::create([
            'title'    => $request->title,
            'genre'    => $request->genre,
            'category' => $request->category,
            'user_id'  => auth()->id(),
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
            'user_id'       => auth()->id(),
            'content'       => $request->content,
        ]);

        return back();
    }
}