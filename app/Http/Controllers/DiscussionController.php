<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discussion;
use App\Models\Comment;

class DiscussionController extends Controller
{
    public function index()
    {
        $discussions = Discussion::latest()->get();
        return view('komunitas', compact('discussions'));
    }

    public function create()
    {
        return view('discussions.create');
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
