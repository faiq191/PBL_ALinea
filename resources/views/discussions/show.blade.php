<!DOCTYPE html>
<html>
<head>
    <title>Detail Diskusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-[#f5f5f5]">
    <x-header />

    <div class="max-w-4xl mx-auto pt-24 px-6 mb-12">
        <a href="/komunitas" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#1a3a5c] mb-6 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Komunitas
        </a>

        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 mb-8">
            <div class="flex gap-8">
                <img src="{{ \Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : asset('storage/' . $discussion->image) }}" class="w-32 h-48 object-cover rounded-2xl shadow-md">
                
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold text-white bg-[#1a3a5c] px-3 py-1 rounded-full uppercase tracking-wider">{{ $discussion->genre ?? 'Umum' }}</span>
                        
                        @if(auth()->check() && (auth()->id() === $discussion->user_id || auth()->user()->is_admin))
                            <div class="flex gap-2">
                                <a href="/diskusi/{{ $discussion->id }}/edit" class="p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-xl transition">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="/diskusi/{{ $discussion->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus diskusi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    <h1 class="text-3xl font-extrabold text-[#1a3a5c] leading-tight mb-4">
                        {{ $discussion->title }}
                    </h1>
                    
                    <div class="flex items-center gap-3 text-sm text-gray-500 mb-6 pb-6 border-b border-gray-100">
                        @if($discussion->user->profile_photo)
                            <img src="{{ \Illuminate\Support\Str::startsWith($discussion->user->profile_photo, 'http') ? $discussion->user->profile_photo : asset('storage/' . $discussion->user->profile_photo) }}" class="w-8 h-8 rounded-full object-cover shadow-sm">
                        @else
                            <div class="w-8 h-8 rounded-full bg-[#e8edf2] flex items-center justify-center font-bold text-[#1a3a5c]">
                                {{ substr($discussion->user->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <span class="font-bold text-[#1a3a5c]">{{ $discussion->user->name ?? 'Unknown' }}</span>
                        <span>•</span>
                        <span>{{ $discussion->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>

                    <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br(e($discussion->content)) !!}</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            <h3 class="text-xl font-bold text-[#1a3a5c] mb-6 flex items-center gap-2">
                <i data-lucide="messages-square" class="w-5 h-5"></i> Diskusi
            </h3>

            @auth
                <form action="/diskusi/{{ $discussion->id }}/comment" method="POST" class="mb-10 relative">
                    @csrf
                    <textarea name="content" required rows="3" class="w-full bg-[#e8edf2] border-none rounded-2xl p-4 text-sm text-[#1a3a5c] outline-none focus:ring-2 focus:ring-[#1a3a5c] resize-none" placeholder="Tulis balasanmu di sini..."></textarea>
                    <div class="absolute right-3 bottom-4">
                        <button type="submit" class="bg-[#1a3a5c] text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-[#122b45] transition">Kirim</button>
                    </div>
                </form>
            @else
                <div class="bg-[#e8edf2] p-6 rounded-2xl text-center mb-10">
                    <p class="text-sm text-[#5a7a9c] font-medium mb-3">Login untuk ikut berdiskusi</p>
                    <a href="/login" class="bg-[#1a3a5c] text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-[#122b45] transition">Login</a>
                </div>
            @endauth

            <div class="space-y-6">
                @forelse ($discussion->comments as $comment)
                    <div x-data="{ replying: false, editing: false }" class="flex gap-4 group">
                        
                        @if($comment->user->profile_photo)
                            <img src="{{ \Illuminate\Support\Str::startsWith($comment->user->profile_photo, 'http') ? $comment->user->profile_photo : asset('storage/' . $comment->user->profile_photo) }}" class="w-10 h-10 shrink-0 rounded-full object-cover shadow-sm z-10">
                        @else
                            <div class="w-10 h-10 shrink-0 rounded-full bg-[#e8edf2] flex items-center justify-center font-bold text-[#1a3a5c] z-10">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                        @endif
                        
                        <div class="flex-1 relative">
                            <div class="bg-gray-50 rounded-2xl rounded-tl-none p-4 mb-2">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-[#1a3a5c]">{{ $comment->user->name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->is_admin))
                                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="editing = !editing; replying = false" class="text-gray-400 hover:text-[#1a3a5c] p-1"><i data-lucide="pencil" class="w-3 h-3"></i></button>
                                            <form action="/comments/{{ $comment->id }}" method="POST" onsubmit="return confirm('Hapus komentar?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 p-1"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <p x-show="!editing" class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br(e($comment->content)) !!}</p>
                                
                                <form x-show="editing" x-cloak action="/comments/{{ $comment->id }}" method="POST" class="mt-2">
                                    @csrf @method('PUT')
                                    <textarea name="content" required rows="2" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm outline-none focus:border-[#1a3a5c] mb-2">{{ $comment->content }}</textarea>
                                    <div class="flex gap-2 justify-end">
                                        <button type="button" @click="editing = false" class="text-xs font-bold text-gray-500 hover:text-gray-700 px-3 py-1.5">Batal</button>
                                        <button type="submit" class="bg-[#1a3a5c] text-white text-xs font-bold px-4 py-1.5 rounded-lg">Simpan</button>
                                    </div>
                                </form>
                            </div>

                            @auth
                                <button @click="replying = !replying; editing = false" class="text-xs font-bold text-gray-500 hover:text-[#1a3a5c] mb-4 flex items-center gap-1 transition">
                                    <i data-lucide="reply" class="w-3 h-3"></i> Balas
                                </button>
                                
                                <form x-show="replying" x-cloak action="/diskusi/{{ $discussion->id }}/comment" method="POST" class="mb-4 relative">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <textarea name="content" required rows="2" class="w-full bg-[#e8edf2] border-none rounded-xl p-3 text-sm text-[#1a3a5c] outline-none focus:ring-2 focus:ring-[#1a3a5c] resize-none" placeholder="Balas ke {{ $comment->user->name }}..."></textarea>
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button type="button" @click="replying = false" class="text-xs font-bold text-gray-500 hover:text-gray-700 px-3 py-1.5">Batal</button>
                                        <button type="submit" class="bg-[#1a3a5c] text-white px-4 py-1.5 rounded-lg font-bold text-xs hover:bg-[#122b45] transition">Kirim Balasan</button>
                                    </div>
                                </form>
                            @endauth

                            @if($comment->replies->count() > 0)
                                <div class="relative ml-2 pl-6 border-l-2 border-gray-100 space-y-4 pt-2">
                                    @foreach($comment->replies as $reply)
                                        <div x-data="{ editingReply: false }" class="relative group">
                                            <div class="absolute -left-6 top-5 w-6 h-4 border-b-2 border-l-2 border-gray-100 rounded-bl-xl"></div>
                                            
                                            <div class="flex gap-3 relative">
                                                @if($reply->user->profile_photo)
                                                    <img src="{{ \Illuminate\Support\Str::startsWith($reply->user->profile_photo, 'http') ? $reply->user->profile_photo : asset('storage/' . $reply->user->profile_photo) }}" class="w-8 h-8 shrink-0 rounded-full object-cover shadow-sm">
                                                @else
                                                    <div class="w-8 h-8 shrink-0 rounded-full bg-[#d0e4f5] flex items-center justify-center font-bold text-[#1a3a5c] text-xs">
                                                        {{ substr($reply->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="flex-1 bg-white border border-gray-100 rounded-2xl rounded-tl-none p-3 shadow-sm">
                                                    <div class="flex justify-between items-start mb-1">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-bold text-sm text-[#1a3a5c]">{{ $reply->user->name }}</span>
                                                            <span class="text-[10px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->is_admin))
                                                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button @click="editingReply = !editingReply" class="text-gray-400 hover:text-[#1a3a5c] p-1"><i data-lucide="pencil" class="w-3 h-3"></i></button>
                                                                <form action="/comments/{{ $reply->id }}" method="POST" onsubmit="return confirm('Hapus balasan?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-1"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <p x-show="!editingReply" class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap"><span class="text-blue-500 font-semibold text-xs mr-1">{{ '@'.$comment->user->name }}</span>{!! nl2br(e($reply->content)) !!}</p>
                                                    
                                                    <form x-show="editingReply" x-cloak action="/comments/{{ $reply->id }}" method="POST" class="mt-2">
                                                        @csrf @method('PUT')
                                                        <textarea name="content" required rows="2" class="w-full bg-[#f5f5f5] border-none rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5c] mb-2">{{ $reply->content }}</textarea>
                                                        <div class="flex gap-2 justify-end">
                                                            <button type="button" @click="editingReply = false" class="text-xs font-bold text-gray-500 hover:text-gray-700 px-3 py-1.5">Batal</button>
                                                            <button type="submit" class="bg-[#1a3a5c] text-white text-xs font-bold px-4 py-1.5 rounded-lg">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-400 text-sm">Belum ada balasan. Jadilah yang pertama berkomentar!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <x-footer />
    <script>lucide.createIcons();</script>
</body>
</html>