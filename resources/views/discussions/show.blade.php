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
                    <div x-data="{ replying: false, editing: false }" id="comment-{{ $comment->id }}" class="flex gap-4 group">
                        
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
                                        <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                            <span>{{ $comment->created_at == $comment->updated_at ? $comment->created_at->diffForHumans() : $comment->updated_at->diffForHumans() }}</span>
                                            @if($comment->created_at != $comment->updated_at && !str_starts_with($comment->content, '_deleted_'))
                                                <span class="text-gray-300">•</span>
                                                <span class="italic text-[9px] text-gray-400">(diedit)</span>
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->is_admin) && !str_starts_with($comment->content, '_deleted_'))
                                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="editing = !editing; replying = false" class="text-gray-400 hover:text-[#1a3a5c] p-1"><i data-lucide="pencil" class="w-3 h-3"></i></button>
                                            <form action="/comments/{{ $comment->id }}" method="POST" onsubmit="return confirm('Hapus komentar?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 p-1"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                @if($comment->content === '_deleted_by_user_')
                                    <p class="text-xs text-gray-400 italic flex items-center gap-1.5 py-1">
                                        <i data-lucide="ban" class="w-3.5 h-3.5"></i> Pesan ini telah dihapus oleh pengguna
                                    </p>
                                @elseif($comment->content === '_deleted_by_admin_')
                                    <p class="text-xs text-red-400 italic flex items-center gap-1.5 py-1 bg-red-50/50 px-3 py-1 rounded-lg border border-red-100/50 w-fit">
                                        <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-red-500"></i> Pesan ini telah dihapus oleh moderator/admin
                                    </p>
                                @else
                                    <p x-show="!editing" class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br(e($comment->content)) !!}</p>
                                @endif
                                
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
                                @if(!str_starts_with($comment->content, '_deleted_'))
                                    <button @click="
                                        replying = !replying;
                                        editing = false;
                                        if (replying) {
                                            setTimeout(() => {
                                                const textarea = document.getElementById('reply-textarea-{{ $comment->id }}');
                                                if (textarea && !textarea.value) {
                                                    textarea.value = '{{ "@" . $comment->user->name }} ';
                                                    textarea.focus();
                                                }
                                            }, 50);
                                        }
                                    " class="text-xs font-bold text-gray-500 hover:text-[#1a3a5c] mb-4 flex items-center gap-1 transition">
                                        <i data-lucide="reply" class="w-3 h-3"></i> Balas
                                    </button>
                                @endif
                            @endauth

                            <div id="replies-{{ $comment->id }}" :class="(replying || {{ $comment->replies->count() }} > 0) ? 'relative ml-2 pl-6 border-l-2 border-gray-100 space-y-4 pt-2' : 'hidden'">
                                @foreach($comment->replies as $reply)
                                    <div x-data="{ editingReply: false }" id="comment-{{ $reply->id }}" class="relative group">
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
                                                        <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                                            <span>{{ $reply->created_at == $reply->updated_at ? $reply->created_at->diffForHumans() : $reply->updated_at->diffForHumans() }}</span>
                                                            @if($reply->created_at != $reply->updated_at && !str_starts_with($reply->content, '_deleted_'))
                                                                <span class="text-gray-300">•</span>
                                                                <span class="italic text-[9px] text-gray-400">(diedit)</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->is_admin) && !str_starts_with($reply->content, '_deleted_'))
                                                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <button @click="editingReply = !editingReply" class="text-gray-400 hover:text-[#1a3a5c] p-1"><i data-lucide="pencil" class="w-3 h-3"></i></button>
                                                            <form action="/comments/{{ $reply->id }}" method="POST" onsubmit="return confirm('Hapus balasan?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-gray-400 hover:text-red-600 p-1"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if($reply->content === '_deleted_by_user_')
                                                    <p class="text-xs text-gray-400 italic flex items-center gap-1.5 py-1">
                                                        <i data-lucide="ban" class="w-3.5 h-3.5"></i> Balasan ini telah dihapus oleh pengguna
                                                    </p>
                                                @elseif($reply->content === '_deleted_by_admin_')
                                                    <p class="text-xs text-red-400 italic flex items-center gap-1.5 py-1 bg-red-50/50 px-3 py-1 rounded-lg border border-red-100/50 w-fit">
                                                        <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-red-500"></i> Balasan ini telah dihapus oleh moderator/admin
                                                    </p>
                                                @else
                                                    @php
                                                        // Ambil semua username unik di thread ini
                                                        $threadUsernames = collect([$comment->user->name]);
                                                        foreach($comment->replies as $r) {
                                                            $threadUsernames->push($r->user->name);
                                                        }
                                                        $sortedUsernames = $threadUsernames->unique()->sortByDesc(function($name) {
                                                            return strlen($name);
                                                        })->values()->all();

                                                        $renderedContent = e($reply->content);
                                                        $matched = false;
                                                        foreach($sortedUsernames as $username) {
                                                            $escapedUsername = e($username);
                                                            $pattern = '/^@' . preg_quote($escapedUsername, '/') . '(\s|$)/';
                                                            if (preg_match($pattern, $renderedContent)) {
                                                                $renderedContent = preg_replace($pattern, '<span class="text-blue-500 font-semibold text-xs mr-1">@' . $escapedUsername . '</span>', $renderedContent);
                                                                $matched = true;
                                                                break;
                                                            }
                                                        }
                                                        if (!$matched) {
                                                            $renderedContent = preg_replace('/^@([a-zA-Z0-9_]+)/', '<span class="text-blue-500 font-semibold text-xs mr-1">@$1</span>', $renderedContent);
                                                        }
                                                    @endphp
                                                    <p x-show="!editingReply" class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br($renderedContent) !!}</p>
                                                @endif
                                                
                                                @auth
                                                    @if(!str_starts_with($reply->content, '_deleted_'))
                                                        <button @click="
                                                            const rootCommentEl = document.getElementById('comment-{{ $comment->id }}');
                                                            if (rootCommentEl) {
                                                                const alpineData = Alpine.$data(rootCommentEl);
                                                                alpineData.replying = true;
                                                                alpineData.editing = false;
                                                                setTimeout(() => {
                                                                    const textarea = document.getElementById('reply-textarea-{{ $comment->id }}');
                                                                    if (textarea) {
                                                                        textarea.value = '{{ "@" . $reply->user->name }} ';
                                                                        textarea.focus();
                                                                    }
                                                                }, 50);
                                                            }
                                                        " class="text-[10px] font-bold text-gray-400 hover:text-[#1a3a5c] mt-1.5 flex items-center gap-1 transition">
                                                            <i data-lucide="reply" class="w-3 h-3"></i> Balas
                                                        </button>
                                                    @endif
                                                @endauth
                                                
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
                                
                                @auth
                                    <form x-show="replying" x-cloak action="/diskusi/{{ $discussion->id }}/comment" method="POST" class="relative group animate-fade-in pt-2">
                                        <div class="absolute -left-6 top-7 w-6 h-4 border-b-2 border-l-2 border-gray-100 rounded-bl-xl"></div>
                                        <div class="flex gap-3 relative">
                                            @if(auth()->user()->profile_photo)
                                                <img src="{{ \Illuminate\Support\Str::startsWith(auth()->user()->profile_photo, 'http') ? auth()->user()->profile_photo : asset('storage/' . auth()->user()->profile_photo) }}" class="w-8 h-8 shrink-0 rounded-full object-cover shadow-sm">
                                            @else
                                                <div class="w-8 h-8 shrink-0 rounded-full bg-[#d0e4f5] flex items-center justify-center font-bold text-[#1a3a5c] text-xs">
                                                    {{ substr(auth()->user()->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="flex-1 bg-white border border-gray-100 rounded-2xl rounded-tl-none p-3 shadow-sm">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <textarea id="reply-textarea-{{ $comment->id }}" name="content" required rows="2" class="w-full bg-[#f5f5f5] border-none rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5c] resize-none" placeholder="Tulis balasan..."></textarea>
                                                <div class="flex justify-end gap-2 mt-2">
                                                    <button type="button" @click="replying = false" class="text-xs font-bold text-gray-500 hover:text-gray-700 px-3 py-1.5">Batal</button>
                                                    <button type="submit" class="bg-[#1a3a5c] text-white px-4 py-1.5 rounded-lg font-bold text-xs hover:bg-[#122b45] transition">Kirim Balasan</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endauth
                            </div>
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
        <!-- Include compiled JS assets yang berisi Laravel Echo -->
    <script>
        window.laravelReverb = {
            key: "{{ env('VITE_REVERB_APP_KEY', 'z2qmiwap8byabk4uu6vt') }}",
            host: "{{ env('VITE_REVERB_HOST', 'reverb-production-b867.up.railway.app') }}",
            port: "{{ env('VITE_REVERB_PORT', '443') }}",
            scheme: "{{ env('VITE_REVERB_SCHEME', 'https') }}"
        };
    </script>
    @vite(['resources/js/app.js'])

    <script>
        function formatCommentContent(content) {
            // Dapatkan semua nama pengguna unik dari halaman, diurutkan dari yang terpanjang
            const usernames = Array.from(document.querySelectorAll('.comment-username'))
                .map(el => el.textContent.trim())
                .filter((v, i, a) => a.indexOf(v) === i)
                .sort((a, b) => b.length - a.length);
            
            for (const username of usernames) {
                // Gunakan regex escape untuk mengantisipasi karakter khusus di nama
                const escapedName = username.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                const pattern = new RegExp('^@' + escapedName + '(\\s|$)');
                if (pattern.test(content)) {
                    return content.replace(pattern, (match, p1) => {
                        return `<span class="text-blue-500 font-semibold text-xs mr-1">@${username}</span>` + p1;
                    });
                }
            }
            // Fallback ke regex standar jika tidak ada nama yang terdaftar di DOM
            return content.replace(/^@([a-zA-Z0-9_]+)/, '<span class="text-blue-500 font-semibold text-xs mr-1">@$1</span>');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            const discussionId = "{{ $discussion->id }}";
            
            // Dengar channel public discussion.[id]
            window.Echo.channel('discussion.' + discussionId)
                .listen('.CommentSent', (e) => {
                    console.log("Komentar baru masuk:", e.comment);
                    
                    const comment = e.comment;
                    const commentUser = comment.user;
                    
                    // Tentukan avatar (foto profil atau inisial)
                    let avatarHtml = '';
                    if (commentUser.profile_photo) {
                        const isUrl = commentUser.profile_photo.startsWith('http');
                        const imgUrl = isUrl ? commentUser.profile_photo : '/storage/' + commentUser.profile_photo;
                        avatarHtml = `<img src="${imgUrl}" class="w-10 h-10 shrink-0 rounded-full object-cover shadow-sm z-10">`;
                    } else {
                        const initial = commentUser.name.substring(0, 1);
                        avatarHtml = `<div class="w-10 h-10 shrink-0 rounded-full bg-[#e8edf2] flex items-center justify-center font-bold text-[#1a3a5c] z-10">${initial}</div>`;
                    }
                    
                    if (comment.parent_id) {
                        // Jika ini adalah balasan (subcomment)
                        const repliesContainer = document.getElementById('replies-' + comment.parent_id);
                        if (repliesContainer) {
                            // Tampilkan container jika sebelumnya kosong/hidden
                            repliesContainer.classList.remove('hidden');
                            
                            // Tentukan avatar berukuran mini (w-8 h-8) untuk reply
                            let replyAvatarHtml = '';
                            if (commentUser.profile_photo) {
                                const isUrl = commentUser.profile_photo.startsWith('http');
                                const imgUrl = isUrl ? commentUser.profile_photo : '/storage/' + commentUser.profile_photo;
                                replyAvatarHtml = `<img src="${imgUrl}" class="w-8 h-8 shrink-0 rounded-full object-cover shadow-sm">`;
                            } else {
                                const initial = commentUser.name.substring(0, 1);
                                replyAvatarHtml = `<div class="w-8 h-8 shrink-0 rounded-full bg-[#d0e4f5] flex items-center justify-center font-bold text-[#1a3a5c] text-xs">${initial}</div>`;
                            }
                            
                            const newReplyHtml = `
                                <div id="comment-${comment.id}" class="relative group animate-fade-in">
                                    <div class="absolute -left-6 top-5 w-6 h-4 border-b-2 border-l-2 border-gray-100 rounded-bl-xl"></div>
                                    <div class="flex gap-3 relative">
                                        ${replyAvatarHtml}
                                        <div class="flex-1 bg-white border border-gray-100 rounded-2xl rounded-tl-none p-3 shadow-sm">
                                            <div class="flex justify-between items-start mb-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-sm text-[#1a3a5c] comment-username">${commentUser.name}</span>
                                                    <span class="text-[10px] text-gray-400">Baru saja</span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${formatCommentContent(comment.content)}</p>
                                            
                                            ${isAuthenticated ? `
                                                <button onclick="
                                                    const rootCommentEl = document.getElementById('comment-${comment.parent_id}');
                                                    if (rootCommentEl) {
                                                        const alpineData = Alpine.$data(rootCommentEl);
                                                        alpineData.replying = true;
                                                        alpineData.editing = false;
                                                        setTimeout(() => {
                                                            const textarea = document.getElementById('reply-textarea-${comment.parent_id}');
                                                            if (textarea) {
                                                                textarea.value = '@${commentUser.name} ';
                                                                textarea.focus();
                                                            }
                                                        }, 50);
                                                    }
                                                " class="text-[10px] font-bold text-gray-400 hover:text-[#1a3a5c] mt-1.5 flex items-center gap-1 transition">
                                                    <i data-lucide="reply" class="w-3 h-3"></i> Balas
                                                </button>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                            repliesContainer.insertAdjacentHTML('beforeend', newReplyHtml);
                        }
                    } else {
                        // Jika ini adalah komentar utama (root)
                        const newCommentHtml = `
                            <div x-data="{ replying: false, editing: false }" id="comment-${comment.id}" class="flex gap-4 group animate-fade-in">
                                ${avatarHtml}
                                <div class="flex-1 relative">
                                    <div class="bg-gray-50 rounded-2xl rounded-tl-none p-4 mb-2">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-sm text-[#1a3a5c] comment-username">${commentUser.name}</span>
                                                <span class="text-[10px] text-gray-400">Baru saja</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${comment.content}</p>
                                    </div>
                                    
                                    <div id="replies-${comment.id}" class="relative ml-2 pl-6 border-l-2 border-gray-100 space-y-4 pt-2 hidden"></div>
                                </div>
                            </div>
                        `;
                        
                        const commentContainer = document.querySelector('.space-y-6');
                        if (commentContainer) {
                            const emptyPlaceholder = commentContainer.querySelector('.text-center.py-8');
                            if (emptyPlaceholder) {
                                emptyPlaceholder.remove();
                            }
                            commentContainer.insertAdjacentHTML('beforeend', newCommentHtml);
                        }
                    }
                })
                .listen('.CommentUpdated', (e) => {
                    console.log("Komentar di-update:", e.comment);
                    const comment = e.comment;
                    
                    // Cari element pembungkus komentar/reply berdasarkan ID
                    const commentEl = document.getElementById('comment-' + comment.id);
                    if (commentEl) {
                        if (comment.content.startsWith('_deleted_')) {
                            // 1. Tampilkan teks "Pesan ini telah dihapus..."
                            const contentEl = commentEl.querySelector('p[x-show="!editing"]') || commentEl.querySelector('p[x-show="!editingReply"]') || commentEl.querySelector('.italic');
                            if (contentEl) {
                                if (comment.content === '_deleted_by_admin_') {
                                    contentEl.outerHTML = `
                                        <p class="text-xs text-red-400 italic flex items-center gap-1.5 py-1 bg-red-50/50 px-3 py-1 rounded-lg border border-red-100/50 w-fit">
                                            <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-red-500"></i> ${comment.parent_id ? 'Balasan' : 'Pesan'} ini telah dihapus oleh moderator/admin
                                        </p>
                                    `;
                                } else {
                                    contentEl.outerHTML = `
                                        <p class="text-xs text-gray-400 italic flex items-center gap-1.5 py-1">
                                            <i data-lucide="ban" class="w-3.5 h-3.5"></i> ${comment.parent_id ? 'Balasan' : 'Pesan'} ini telah dihapus oleh pengguna
                                        </p>
                                    `;
                                }
                                lucide.createIcons();
                            }
                            
                            // 2. Sembunyikan tombol edit/hapus
                            const actionsEl = commentEl.querySelector('.opacity-0');
                            if (actionsEl) {
                                actionsEl.remove();
                            }
                            
                            // 3. Sembunyikan tombol balas (Reply button)
                            const replyBtn = commentEl.querySelector('button[class*="hover:text-[#1a3a5c]"]');
                            if (replyBtn) {
                                replyBtn.remove();
                            }
                        } else {
                            // Proses edit komentar normal
                            const contentEl = commentEl.querySelector('p[x-show="!editing"]') || commentEl.querySelector('p[x-show="!editingReply"]');
                            if (contentEl) {
                                if (comment.parent_id) {
                                    contentEl.innerHTML = formatCommentContent(comment.content).replace(/\n/g, '<br>');
                                } else {
                                    contentEl.innerHTML = comment.content.replace(/\n/g, '<br>');
                                }
                            }
                            
                            // Update waktu menjadi "Baru saja" dan tambahkan penanda "(diedit)" agar sinkron instan di semua user!
                            const metaEl = commentEl.querySelector('.text-\\[10px\\]') || commentEl.querySelector('.text-gray-400');
                            if (metaEl) {
                                metaEl.innerHTML = `
                                    <span>Baru saja</span>
                                    <span class="text-gray-300">•</span>
                                    <span class="italic text-[9px] text-gray-400">(diedit)</span>
                                `;
                            }
                            
                            // Juga, update isi textarea di form edit agar sinkron jika di-edit lagi!
                            const textareaEl = commentEl.querySelector('textarea[name="content"]');
                            if (textareaEl) {
                                textareaEl.value = comment.content;
                            }
                        }
                    }
                })
                .listen('.CommentDeleted', (e) => {
                    console.log("Komentar dihapus:", e.commentId);
                    const commentEl = document.getElementById('comment-' + e.commentId);
                    if (commentEl) {
                        commentEl.remove();
                    }
                });
        });
    </script>

</body>
</html>