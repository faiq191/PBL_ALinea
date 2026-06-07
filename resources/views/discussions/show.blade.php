<!DOCTYPE html>
@php
    if (!function_exists('parseGifsInContent')) {
        function parseGifsInContent($escapedContent) {
            // 1. Identify GIF URLs and replace with placeholders
            $gifRegex = '/(https?:\/\/[^\s<>\"]+?\.(?:gif)(?:[?#][^\s<>\"]*)?|https?:\/\/(?:www\.)?media\.tenor\.com\/[^\s<>\"]+|https?:\/\/(?:www\.)?tenor\.com\/view\/[^\s<>\"]+)/i';
            $gifPlaceholders = [];
            $escapedContent = preg_replace_callback($gifRegex, function($matches) use (&$gifPlaceholders) {
                $placeholder = '___GIF_PLACEHOLDER_' . count($gifPlaceholders) . '___';
                $gifPlaceholders[$placeholder] = html_entity_decode($matches[1]);
                return $placeholder;
            }, $escapedContent);
            
            // 2. Match any other URLs and replace with clickable warning links (blue highlight)
            $urlRegex = '/(https?:\/\/[^\s<>\"]+)/i';
            $escapedContent = preg_replace_callback($urlRegex, function($matches) {
                $url = html_entity_decode($matches[1]);
                $safeUrl = e($url);
                return '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer" ' .
                       'class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 underline font-medium bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 text-xs transition duration-200" ' .
                       'title="Peringatan: Tautan eksternal dari pengguna lain. Harap berhati-hati saat membuka tautan dari luar." ' .
                       'onclick="return confirm(\'Peringatan Keamanan: Tautan ini berasal dari luar ALinea. Membuka tautan eksternal dari orang asing berpotensi bahaya (phishing, malware, dll).\\n\\nApakah Anda yakin ingin membuka: ' . addslashes($safeUrl) . '?\')">' .
                       '<span>' . $safeUrl . '</span>' .
                       '<i data-lucide="external-link" class="w-3 h-3 text-blue-500"></i>' .
                       '</a>';
            }, $escapedContent);
            
            // 3. Restore GIF placeholders with image tags
            foreach ($gifPlaceholders as $placeholder => $url) {
                $imgHtml = '<div class="relative max-w-xs overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition mt-2">' .
                           '<img src="' . e($url) . '" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox(\'' . e($url) . '\')">' .
                           '</div>';
                $escapedContent = str_replace($placeholder, $imgHtml, $escapedContent);
            }
            
            return $escapedContent;
        }
    }
@endphp
<html>
<head>
    <title>Detail Diskusi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        [x-cloak] { display: none !important; }
        .hash-loading body { opacity: 0 !important; }
        @keyframes scaleUp {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .animate-scale-up {
            animation: scaleUp 0.2s ease-out forwards;
        }
        #leaflet-map {
            width: 100%;
            height: 100%;
            z-index: 10;
        }
    </style>
    <script>
        const isReload = performance.getEntriesByType('navigation')[0]?.type === 'reload';
        if (isReload) {
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            if (window.location.hash) {
                history.replaceState(null, null, window.location.pathname + window.location.search);
            }
            window.scrollTo(0, 0);
        }
        if (!isReload && window.location.hash && window.location.hash.startsWith('#comment-')) {
            document.documentElement.classList.add('hash-loading');
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
        }
    </script>
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
                    
                    <h1 class="notranslate text-3xl font-extrabold text-[#1a3a5c] leading-tight mb-4">
                        {{ $discussion->title }}
                    </h1>
                    
                    <a href="/users/{{ $discussion->user->id ?? '#' }}" class="flex items-center gap-3 text-sm text-gray-500 mb-6 pb-6 border-b border-gray-100 w-fit hover:opacity-85 transition">
                        @if($discussion->user->profile_photo)
                            <img src="{{ \Illuminate\Support\Str::startsWith($discussion->user->profile_photo, 'http') ? $discussion->user->profile_photo : asset('storage/' . $discussion->user->profile_photo) }}" class="w-8 h-8 rounded-full object-cover shadow-sm">
                        @else
                            <div class="w-8 h-8 rounded-full bg-[#e8edf2] flex items-center justify-center font-bold text-[#1a3a5c]">
                                {{ substr($discussion->user->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                        <span class="notranslate font-bold text-[#1a3a5c] hover:underline">{{ $discussion->user->name ?? 'Unknown' }}</span>
                        <span>•</span>
                        <span>{{ $discussion->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </a>

                    <div class="notranslate text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br(e($discussion->content)) !!}</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            <h3 class="text-xl font-bold text-[#1a3a5c] mb-6 flex items-center gap-2">
                <i data-lucide="messages-square" class="w-5 h-5"></i> Diskusi
            </h3>

            @auth
                <form action="/diskusi/{{ $discussion->id }}/comment" method="POST" enctype="multipart/form-data" class="mb-10 attachment-form relative">
                    @csrf
                    <textarea name="content" rows="3" class="w-full bg-[#e8edf2] border-none rounded-2xl p-4 text-sm text-[#1a3a5c] outline-none focus:ring-2 focus:ring-[#1a3a5c] resize-none @error('content') ring-2 ring-red-500 @enderror" placeholder="Tulis balasanmu di sini... (Bisa paste screenshot/gambar juga!)"></textarea>
                    @error('content')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror

                    <!-- Preview Box -->
                    <div class="attachment-preview-box hidden mt-3 p-3 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-between animate-fade-in"></div>

                    <!-- Inline Tenor Popover Container (Light Theme) -->
                    <div class="tenor-popover hidden absolute bottom-16 left-6 right-6 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 flex flex-col max-h-[450px] overflow-hidden animate-scale-up">
                        <div class="p-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <span class="text-xs font-bold text-[#1a3a5c] flex items-center gap-1.5">
                                <i data-lucide="film" class="w-4 h-4 text-[#1a3a5c]"></i> Cari GIF Tenor
                            </span>
                            <button type="button" class="btn-close-tenor text-gray-400 hover:text-gray-600 transition">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <div class="p-3 bg-gray-50/50 border-b border-gray-100 relative flex items-center gap-2">
                            <div class="relative flex-1">
                                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                                <input type="text" class="tenor-popover-search w-full bg-white text-gray-800 border border-gray-200 rounded-xl pl-9 pr-8 py-2 text-xs outline-none focus:border-[#1a3a5c] focus:ring-1 focus:ring-[#1a3a5c] transition" placeholder="Cari di Tenor...">
                                <button type="button" class="btn-clear-search hidden absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 transition">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="tenor-popover-results flex-1 overflow-y-auto p-3 grid grid-cols-2 gap-2 min-h-[180px] max-h-[320px] bg-white">
                            <!-- Categories or results will be injected here -->
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-1.5">
                            <input type="file" name="attachment" class="hidden attachment-file-input">
                            <input type="hidden" name="attachment_type" class="attachment-type-input">
                            <input type="hidden" name="attachment_url" class="attachment-url-input">
                            
                            <button type="button" class="btn-attach-image p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-100 rounded-xl transition" title="Unggah Gambar">
                                <i data-lucide="image" class="w-5 h-5"></i>
                            </button>
                            <button type="button" class="btn-attach-tenor p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-100 rounded-xl transition" title="Cari GIF Tenor">
                                <i data-lucide="film" class="w-5 h-5"></i>
                            </button>
                            <button type="button" class="btn-attach-gmaps p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-100 rounded-xl transition" title="Bagikan Lokasi">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </button>
                            <button type="button" class="btn-attach-emoji p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-100 rounded-xl transition relative" title="Pilih Emoji">
                                <i data-lucide="smile" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <button type="submit" class="bg-[#1a3a5c] text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-[#122b45] transition">Kirim</button>
                    </div>
                </form>
            @else
                <div class="bg-[#e8edf2] p-6 rounded-2xl text-center mb-10">
                    <p class="text-sm text-[#5a7a9c] font-medium mb-3">Masuk untuk ikut berdiskusi</p>
                    <a href="/login" class="bg-[#1a3a5c] text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-[#122b45] transition">Masuk</a>
                </div>
            @endauth

            <div class="space-y-6">
                @forelse ($discussion->comments as $comment)
                    <div x-data="{ replying: false, editing: false }" id="comment-{{ $comment->id }}" class="flex gap-4 group">
                        
                        <a href="/users/{{ $comment->user->id }}">
                            @if($comment->user->profile_photo)
                                <img src="{{ \Illuminate\Support\Str::startsWith($comment->user->profile_photo, 'http') ? $comment->user->profile_photo : asset('storage/' . $comment->user->profile_photo) }}" class="w-10 h-10 shrink-0 rounded-full object-cover shadow-sm z-10 hover:opacity-85 transition">
                            @else
                                <div class="w-10 h-10 shrink-0 rounded-full bg-[#e8edf2] flex items-center justify-center font-bold text-[#1a3a5c] z-10 hover:opacity-85 transition">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        
                        <div class="flex-1 relative">
                            <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-none p-4 mb-2 shadow-sm hover:shadow-md hover:bg-gray-50/80 hover:border-gray-200 transition-all duration-300">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-2">
                                        <a href="/users/{{ $comment->user->id }}" class="notranslate font-bold text-sm text-[#1a3a5c] hover:underline">{{ $comment->user->name }}</a>
                                        <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                            <span>{{ $comment->created_at == $comment->updated_at ? $comment->created_at->diffForHumans() : $comment->updated_at->diffForHumans() }}</span>
                                            @if($comment->created_at != $comment->updated_at && !str_starts_with($comment->content, '_deleted_'))
                                                <span class="text-gray-300">•</span>
                                                <span class="italic text-[9px] text-gray-400">(disunting)</span>
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
                                    <p x-show="!editing" class="notranslate text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br(parseGifsInContent(e($comment->content))) !!}</p>
                                @endif

                                @if($comment->attachment_path && !str_starts_with($comment->content, '_deleted_'))
                                    <div class="mt-3">
                                        @if($comment->attachment_type === 'image')
                                            <div class="relative max-w-sm overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                                                <img src="{{ asset('storage/' . $comment->attachment_path) }}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('{{ asset('storage/' . $comment->attachment_path) }}')">
                                            </div>
                                        @elseif($comment->attachment_type === 'file')
                                            <a href="{{ asset('storage/' . $comment->attachment_path) }}" download class="inline-flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl p-3 hover:bg-gray-100 transition max-w-xs">
                                                <div class="w-10 h-10 bg-[#e8edf2] rounded-xl flex items-center justify-center text-[#1a3a5c]">
                                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-[#1a3a5c] truncate">{{ $comment->attachment_name }}</p>
                                                    <p class="text-[10px] text-gray-400">Klik untuk unduh</p>
                                                </div>
                                                <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                                            </a>
                                        @elseif($comment->attachment_type === 'giphy' || $comment->attachment_type === 'tenor')
                                            <div class="relative max-w-xs overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                                                <img src="{{ $comment->attachment_path }}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('{{ $comment->attachment_path }}')">
                                            </div>
                                        @elseif($comment->attachment_type === 'gmaps')
                                            <div data-coords="{{ $comment->attachment_path }}" data-name="{{ $comment->attachment_name ?? 'Lokasi Terbagikan' }}" onclick="viewSharedLocation(this)" class="inline-flex items-center gap-3 bg-[#e8edf2] border border-[#d0e4f5] rounded-2xl p-3 hover:bg-[#d8e4f5] transition max-w-xs cursor-pointer">
                                                <div class="w-10 h-10 bg-[#1a3a5c] rounded-xl flex items-center justify-center text-white">
                                                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-[#1a3a5c] truncate">{{ $comment->attachment_name ?? 'Lokasi Terbagikan' }}</p>
                                                    <p class="text-[10px] text-gray-500">Lihat Peta</p>
                                                </div>
                                                <i data-lucide="map" class="w-4 h-4 text-[#1a3a5c]"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                <form x-show="editing" x-cloak action="/comments/{{ $comment->id }}" method="POST" class="mt-2">
                                    @csrf @method('PUT')
                                    <textarea name="content" required rows="2" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm outline-none focus:border-[#1a3a5c] mb-2 @error('content') ring-2 ring-red-500 @enderror">{{ $comment->content }}</textarea>
                                    @error('content')
                                        <p class="text-red-600 text-xs mt-1 mb-2 font-semibold">{{ $message }}</p>
                                    @enderror

                                    @if($comment->attachment_type)
                                        <div class="mb-3 p-3 bg-gray-50 rounded-xl border border-gray-100 space-y-2">
                                            <div>
                                                <label class="text-[10px] font-bold text-gray-500 block mb-1">
                                                    @if($comment->attachment_type === 'gmaps')
                                                        Koordinat Lokasi (Format: lat,lng)
                                                    @else
                                                        Tautan Lampiran ({{ strtoupper($comment->attachment_type) }})
                                                    @endif
                                                </label>
                                                <input type="text" name="attachment_path" value="{{ $comment->attachment_path }}" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs outline-none focus:border-[#1a3a5c] transition">
                                            </div>
                                            @if($comment->attachment_type === 'gmaps' || $comment->attachment_type === 'file')
                                                <div>
                                                    <label class="text-[10px] font-bold text-gray-500 block mb-1">Nama Lampiran</label>
                                                    <input type="text" name="attachment_name" value="{{ $comment->attachment_name }}" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs outline-none focus:border-[#1a3a5c] transition">
                                                </div>
                                            @endif
                                        </div>
                                    @endif

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
                                            <a href="/users/{{ $reply->user->id }}">
                                                @if($reply->user->profile_photo)
                                                    <img src="{{ \Illuminate\Support\Str::startsWith($reply->user->profile_photo, 'http') ? $reply->user->profile_photo : asset('storage/' . $reply->user->profile_photo) }}" class="w-8 h-8 shrink-0 rounded-full object-cover shadow-sm hover:opacity-85 transition">
                                                @else
                                                    <div class="w-8 h-8 shrink-0 rounded-full bg-[#d0e4f5] flex items-center justify-center font-bold text-[#1a3a5c] text-xs hover:opacity-85 transition">
                                                        {{ substr($reply->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </a>
                                            <div class="flex-1 bg-white border border-gray-100 rounded-2xl rounded-tl-none p-3 shadow-sm hover:shadow-md hover:bg-gray-50/80 hover:border-gray-200 transition-all duration-300">
                                                <div class="flex justify-between items-start mb-1">
                                                    <div class="flex items-center gap-2">
                                                        <a href="/users/{{ $reply->user->id }}" class="notranslate font-bold text-sm text-[#1a3a5c] hover:underline">{{ $reply->user->name }}</a>
                                                        <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                                            <span>{{ $reply->created_at == $reply->updated_at ? $reply->created_at->diffForHumans() : $reply->updated_at->diffForHumans() }}</span>
                                                            @if($reply->created_at != $reply->updated_at && !str_starts_with($reply->content, '_deleted_'))
                                                                <span class="text-gray-300">•</span>
                                                                <span class="italic text-[9px] text-gray-400">(disunting)</span>
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

                                                        $renderedContent = parseGifsInContent(e($reply->content));
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
                                                    <p x-show="!editingReply" class="notranslate text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br($renderedContent) !!}</p>
                                                @endif

                                                @if($reply->attachment_path && !str_starts_with($reply->content, '_deleted_'))
                                                    <div class="mt-3">
                                                        @if($reply->attachment_type === 'image')
                                                            <div class="relative max-w-sm overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                                                                <img src="{{ asset('storage/' . $reply->attachment_path) }}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('{{ asset('storage/' . $reply->attachment_path) }}')">
                                                            </div>
                                                        @elseif($reply->attachment_type === 'file')
                                                            <a href="{{ asset('storage/' . $reply->attachment_path) }}" download class="inline-flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl p-3 hover:bg-gray-100 transition max-w-xs">
                                                                    <div class="w-10 h-10 bg-[#e8edf2] rounded-xl flex items-center justify-center text-[#1a3a5c]">
                                                                        <i data-lucide="file-text" class="w-5 h-5"></i>
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <p class="text-xs font-bold text-[#1a3a5c] truncate">{{ $reply->attachment_name }}</p>
                                                                        <p class="text-[10px] text-gray-400">Klik untuk unduh</p>
                                                                    </div>
                                                                    <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                                                                </a>
                                                        @elseif($reply->attachment_type === 'giphy' || $reply->attachment_type === 'tenor')
                                                            <div class="relative max-w-xs overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                                                                <img src="{{ $reply->attachment_path }}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('{{ $reply->attachment_path }}')">
                                                            </div>
                                                        @elseif($reply->attachment_type === 'gmaps')
                                                            <div data-coords="{{ $reply->attachment_path }}" data-name="{{ $reply->attachment_name ?? 'Lokasi Terbagikan' }}" onclick="viewSharedLocation(this)" class="inline-flex items-center gap-3 bg-[#e8edf2] border border-[#d0e4f5] rounded-2xl p-3 hover:bg-[#d8e4f5] transition max-w-xs cursor-pointer">
                                                                <div class="w-10 h-10 bg-[#1a3a5c] rounded-xl flex items-center justify-center text-white">
                                                                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-xs font-bold text-[#1a3a5c] truncate">{{ $reply->attachment_name ?? 'Lokasi Terbagikan' }}</p>
                                                                    <p class="text-[10px] text-gray-500">Lihat Peta</p>
                                                                </div>
                                                                <i data-lucide="map" class="w-4 h-4 text-[#1a3a5c]"></i>
                                                            </div>
                                                        @endif
                                                    </div>
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
                                                    <textarea name="content" required rows="2" class="w-full bg-[#f5f5f5] border-none rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5c] mb-2 @error('content') ring-2 ring-red-500 @enderror">{{ $reply->content }}</textarea>
                                                    @error('content')
                                                        <p class="text-red-600 text-xs mt-1 mb-2 font-semibold">{{ $message }}</p>
                                                    @enderror

                                                    @if($reply->attachment_type)
                                                        <div class="mb-3 p-3 bg-gray-50 rounded-xl border border-gray-100 space-y-2">
                                                            <div>
                                                                <label class="text-[10px] font-bold text-gray-500 block mb-1">
                                                                    @if($reply->attachment_type === 'gmaps')
                                                                        Koordinat Lokasi (Format: lat,lng)
                                                                    @else
                                                                        Tautan Lampiran ({{ strtoupper($reply->attachment_type) }})
                                                                    @endif
                                                                </label>
                                                                <input type="text" name="attachment_path" value="{{ $reply->attachment_path }}" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs outline-none focus:border-[#1a3a5c] transition">
                                                            </div>
                                                            @if($reply->attachment_type === 'gmaps' || $reply->attachment_type === 'file')
                                                                <div>
                                                                    <label class="text-[10px] font-bold text-gray-500 block mb-1">Nama Lampiran</label>
                                                                    <input type="text" name="attachment_name" value="{{ $reply->attachment_name }}" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs outline-none focus:border-[#1a3a5c] transition">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

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
                                    <form x-show="replying" x-cloak action="/diskusi/{{ $discussion->id }}/comment" method="POST" enctype="multipart/form-data" class="relative group animate-fade-in pt-2 attachment-form">
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
                                                <textarea id="reply-textarea-{{ $comment->id }}" name="content" rows="2" class="w-full bg-[#f5f5f5] border-none rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5c] resize-none @error('content') ring-2 ring-red-500 @enderror" placeholder="Tulis balasan... (Bisa paste screenshot juga)"></textarea>
                                                @error('content')
                                                    <p class="text-red-600 text-xs mt-1 mb-2 font-semibold">{{ $message }}</p>
                                                @enderror

                                                <!-- Preview Box -->
                                                <div class="attachment-preview-box hidden mt-2 p-2 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-between animate-fade-in"></div>

                                                <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                                                    <div class="flex items-center gap-1">
                                                        <input type="file" name="attachment" class="hidden attachment-file-input">
                                                        <input type="hidden" name="attachment_type" class="attachment-type-input">
                                                        <input type="hidden" name="attachment_url" class="attachment-url-input">
                                                        
                                                        <button type="button" class="btn-attach-image p-1.5 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-lg transition" title="Unggah Gambar">
                                                            <i data-lucide="image" class="w-4 h-4"></i>
                                                        </button>
                                                        <button type="button" class="btn-attach-tenor p-1.5 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-lg transition" title="Cari GIF Tenor">
                                                            <i data-lucide="film" class="w-4 h-4"></i>
                                                        </button>
                                                        <button type="button" class="btn-attach-gmaps p-1.5 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-lg transition" title="Bagikan Lokasi">
                                                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                                                        </button>
                                                        <button type="button" class="btn-attach-emoji p-1.5 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-lg transition relative" title="Pilih Emoji">
                                                            <i data-lucide="smile" class="w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button type="button" @click="replying = false" class="text-xs font-bold text-gray-500 hover:text-gray-700 px-3 py-1.5">Batal</button>
                                                        <button type="submit" class="bg-[#1a3a5c] text-white px-4 py-1.5 rounded-lg font-bold text-xs hover:bg-[#122b45] transition">Balas</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Inline Tenor Popover Container (Light Theme) -->
                                         <div class="tenor-popover hidden absolute bottom-14 left-14 right-3 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 flex flex-col max-h-[400px] overflow-hidden animate-scale-up">
                                             <div class="p-2.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                                                 <span class="text-xs font-bold text-[#1a3a5c] flex items-center gap-1">
                                                     <i data-lucide="film" class="w-3.5 h-3.5 text-[#1a3a5c]"></i> Cari GIF Tenor
                                                 </span>
                                                 <button type="button" class="btn-close-tenor text-gray-400 hover:text-gray-600 transition">
                                                     <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                 </button>
                                             </div>
                                             <div class="p-2.5 bg-gray-50/50 border-b border-gray-100 relative flex items-center gap-2">
                                                 <div class="relative flex-1">
                                                     <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-2.5"></i>
                                                     <input type="text" class="tenor-popover-search w-full bg-white text-gray-800 border border-gray-200 rounded-xl pl-9 pr-8 py-2 text-xs outline-none focus:border-[#1a3a5c] focus:ring-1 focus:ring-[#1a3a5c] transition" placeholder="Cari di Tenor...">
                                                     <button type="button" class="btn-clear-search hidden absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 transition">
                                                         <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                                     </button>
                                                 </div>
                                             </div>
                                             <div class="tenor-popover-results flex-1 overflow-y-auto p-2.5 grid grid-cols-2 gap-1.5 min-h-[150px] max-h-[280px] bg-white">
                                                 <!-- Categories or results will be injected here -->
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
            // 1. Escape HTML first to prevent XSS (since we inject with innerHTML)
            let escaped = content
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");

            // 2. Parse Mention
            const usernames = Array.from(document.querySelectorAll('.comment-username'))
                .map(el => el.textContent.trim())
                .filter((v, i, a) => a.indexOf(v) === i)
                .sort((a, b) => b.length - a.length);
            
            let mentionMatched = false;
            for (const username of usernames) {
                const escapedName = username.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                const pattern = new RegExp('^@' + escapedName + '(\\s|$)');
                if (pattern.test(escaped)) {
                    escaped = escaped.replace(pattern, (match, p1) => {
                        return `<span class="text-blue-500 font-semibold text-xs mr-1">@${username}</span>` + p1;
                    });
                    mentionMatched = true;
                    break;
                }
            }
            if (!mentionMatched) {
                escaped = escaped.replace(/^@([a-zA-Z0-9_]+)/, '<span class="text-blue-500 font-semibold text-xs mr-1">@$1</span>');
            }

            // 3. GIF placeholders
            const gifRegex = /(https?:\/\/[^\s<>\"]+?\.(?:gif)(?:[?#][^\s<>\"]*)?|https?:\/\/(?:www\.)?media\.tenor\.com\/[^\s<>\"]+|https?:\/\/(?:www\.)?tenor\.com\/view\/[^\s<>\"]+)/ig;
            const gifPlaceholders = [];
            escaped = escaped.replace(gifRegex, (match) => {
                const placeholder = `___GIF_PLACEHOLDER_${gifPlaceholders.length}___`;
                const decodedUrl = match.replace(/&amp;/g, '&');
                gifPlaceholders.push({ placeholder, url: decodedUrl });
                return placeholder;
            });

            // 4. Match any other URLs and replace with clickable warning links (blue highlight)
            const urlRegex = /(https?:\/\/[^\s<>\"]+)/ig;
            escaped = escaped.replace(urlRegex, (match) => {
                const decodedUrl = match.replace(/&amp;/g, '&');
                const safeUrlForJS = decodedUrl.replace(/'/g, "\\'");
                return `<a href="${decodedUrl}" target="_blank" rel="noopener noreferrer" ` +
                       `class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 underline font-medium bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 text-xs transition duration-200" ` +
                       `title="Peringatan: Tautan eksternal dari pengguna lain. Harap berhati-hati saat membuka tautan dari luar." ` +
                       `onclick="return confirm('Peringatan Keamanan: Tautan ini berasal dari luar ALinea. Membuka tautan eksternal dari orang asing berpotensi bahaya (phishing, malware, dll).\\n\\nApakah Anda yakin ingin membuka: ${safeUrlForJS}?')">` +
                       `<span>${decodedUrl}</span>` +
                       `<i data-lucide="external-link" class="w-3 h-3 text-blue-500"></i>` +
                       `</a>`;
            });

            // 5. Restore GIFs
            gifPlaceholders.forEach(item => {
                const imgHtml = `<div class="relative max-w-xs overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition mt-2">` +
                               `<img src="${item.url}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('${item.url}')">` +
                               `</div>`;
                escaped = escaped.replace(item.placeholder, imgHtml);
            });

            return escaped;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            const discussionId = "{{ $discussion->id }}";
            
            let activeAnchor = (window.location.hash && window.location.hash.startsWith('#comment-')) 
                ? window.location.hash.substring(1) 
                : null;

            const scrollToAnchor = () => {
                const isReload = performance.getEntriesByType('navigation')[0]?.type === 'reload';
                if (isReload) {
                    window.scrollTo(0, 0);
                    document.documentElement.classList.remove('hash-loading');
                    return;
                }
                
                if (activeAnchor) {
                    const target = document.getElementById(activeAnchor);
                    if (target) {
                        // Instant scroll to keep target centered
                        target.scrollIntoView({ behavior: 'auto', block: 'center' });
                        
                        // Reveal page content once we are locked on target element
                        document.documentElement.classList.remove('hash-loading');
                        
                        // Strip the hash from the URL bar immediately so that subsequent manual refreshes go to the top
                        if (window.location.hash) {
                            history.replaceState(null, null, window.location.pathname + window.location.search);
                        }
                        
                        // Highlight comment box with a beautiful premium animation
                        const cardInner = target.querySelector('.bg-white.border');
                        if (cardInner && !cardInner.classList.contains('ring-2')) {
                            cardInner.classList.add('ring-2', 'ring-[#1a3a5c]/40', 'scale-[1.01]', 'duration-700', 'transition-all');
                            setTimeout(() => {
                                cardInner.classList.remove('ring-2', 'ring-[#1a3a5c]/40', 'scale-[1.01]');
                            }, 2500);
                        }
                        
                        // Reset scroll restoration
                        setTimeout(() => {
                            if ('scrollRestoration' in history) {
                                history.scrollRestoration = 'auto';
                            }
                        }, 200);
                    }
                } else {
                    document.documentElement.classList.remove('hash-loading');
                }
            };

            // Run immediately
            scrollToAnchor();
            // Run on window load (after all assets and layout settle height)
            window.addEventListener('load', scrollToAnchor);
            // Run in multiple passes to cancel out Alpine.js dynamic height layout shifts
            setTimeout(scrollToAnchor, 100);
            setTimeout(scrollToAnchor, 300);
            setTimeout(scrollToAnchor, 600);
            
            // Safety fallback to ensure page is always visible
            setTimeout(() => {
                document.documentElement.classList.remove('hash-loading');
            }, 850);

            // AJAX Comment & Reply Submit Handler (100% SPA experience, no page reload or jumps to top!)
            document.addEventListener('submit', async (e) => {
                const form = e.target;
                if (form.action && (form.action.includes('/comment') || form.action.includes('/comments'))) {
                    e.preventDefault();
                    
                    // 1. Snapshot old comment IDs IMMEDIATELY before fetch or WebSockets can run!
                    const oldIds = Array.from(document.querySelectorAll('[id^="comment-"]')).map(el => el.id);
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;
                    
                    try {
                        const formData = new FormData(form);
                        const csrfToken = form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        
                        // Set headers including Laravel Socket ID to exclude sender from Echo broadcast
                        const headers = {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        };
                        if (window.Echo && window.Echo.socketId()) {
                            headers['X-Socket-ID'] = window.Echo.socketId();
                        }

                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: headers
                        });
                        
                        if (response.ok) {
                            const html = await response.text();
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            
                            // 2. Replace comments container
                            const newComments = doc.querySelector('.space-y-6');
                            const currentComments = document.querySelector('.space-y-6');
                            if (newComments && currentComments) {
                                currentComments.innerHTML = newComments.innerHTML;
                                if (window.lucide) {
                                    window.lucide.createIcons();
                                }
                            }
                            
                            // Clear textarea & attachments
                            const textarea = form.querySelector('textarea');
                            if (textarea) textarea.value = '';
                            clearAttachment(form);
                            
                            // 3. Find the newly added comment ID from the fetched response doc compared to our pre-submit snapshot
                            const newIds = Array.from(doc.querySelectorAll('[id^="comment-"]')).map(el => el.id);
                            const addedId = newIds.find(id => !oldIds.includes(id));
                            
                            if (addedId) {
                                window.location.hash = addedId;
                                const target = document.getElementById(addedId);
                                if (target) {
                                    setTimeout(() => {
                                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        
                                        const cardInner = target.querySelector('.bg-white.border');
                                        if (cardInner) {
                                            cardInner.classList.add('ring-2', 'ring-[#1a3a5c]/40', 'scale-[1.01]', 'duration-700', 'transition-all');
                                            setTimeout(() => {
                                                cardInner.classList.remove('ring-2', 'ring-[#1a3a5c]/40', 'scale-[1.01]');
                                            }, 2500);
                                        }
                                    }, 120);
                                }
                            }
                        } else {
                            form.submit();
                        }
                    } catch (err) {
                        console.error(err);
                        form.submit();
                    } finally {
                        if (submitBtn) submitBtn.disabled = false;
                    }
                }
            });
            
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
                                            ${renderAttachmentHtml(comment.attachment_type, comment.attachment_path, comment.attachment_name)}
                                            
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
                                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${formatCommentContent(comment.content)}</p>
                                        ${renderAttachmentHtml(comment.attachment_type, comment.attachment_path, comment.attachment_name)}
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
                    if (window.lucide) window.lucide.createIcons();
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
                                    contentEl.innerHTML = formatCommentContent(comment.content).replace(/\n/g, '<br>');
                                }
                            }
                            
                            // Update waktu menjadi "Baru saja" dan tambahkan penanda "(disunting)" agar sinkron instan di semua user!
                            const metaEl = commentEl.querySelector('.text-\\[10px\\]') || commentEl.querySelector('.text-gray-400');
                            if (metaEl) {
                                metaEl.innerHTML = `
                                    <span>Baru saja</span>
                                    <span class="text-gray-300">•</span>
                                    <span class="italic text-[9px] text-gray-400">(disunting)</span>
                                `;
                            }
                            
                            // Juga, update isi textarea di form edit agar sinkron jika di-edit lagi!
                            const textareaEl = commentEl.querySelector('textarea[name="content"]');
                            if (textareaEl) {
                                textareaEl.value = comment.content;
                            }
                        }
                    }
                    if (window.lucide) window.lucide.createIcons();
                })
                .listen('.CommentDeleted', (e) => {
                    console.log("Komentar dihapus:", e.commentId);
                    const commentEl = document.getElementById('comment-' + e.commentId);
                    if (commentEl) {
                        commentEl.remove();
                    }
                });

            // Bind all attachment forms existing on load
            document.querySelectorAll('.attachment-form').forEach(form => {
                initAttachmentFormBindings(form);
            });
            
            // Watch for reply forms being dynamically shown/hidden via Alpine
            // (Alternative: let's bind when reply button is clicked)
            document.addEventListener('click', (e) => {
                if (e.target.closest('button') && e.target.closest('button').textContent.trim() === 'Balas') {
                    setTimeout(() => {
                        document.querySelectorAll('.attachment-form').forEach(form => {
                            if (!form.dataset.bound) {
                                initAttachmentFormBindings(form);
                                form.dataset.bound = "true";
                            }
                        });
                    }, 100);
                }
            });
        });

        // ==========================================
        // ATTACHMENTS & MODALS JS ENGINE
        // ==========================================
        
        function renderAttachmentHtml(type, path, name) {
            if (!path) return '';
            
            let html = '<div class="mt-3">';
            if (type === 'image') {
                const fullUrl = path.startsWith('http') ? path : '/storage/' + path;
                html += `
                    <div class="relative max-w-sm overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                        <img src="${fullUrl}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('${fullUrl}')">
                    </div>
                `;
            } else if (type === 'file') {
                const fullUrl = '/storage/' + path;
                html += `
                    <a href="${fullUrl}" download class="inline-flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl p-3 hover:bg-gray-100 transition max-w-xs">
                        <div class="w-10 h-10 bg-[#e8edf2] rounded-xl flex items-center justify-center text-[#1a3a5c]">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-[#1a3a5c] truncate">${name || 'Berkas'}</p>
                            <p class="text-[10px] text-gray-400">Klik untuk unduh</p>
                        </div>
                        <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                    </a>
                `;
            } else if (type === 'giphy' || type === 'tenor') {
                html += `
                    <div class="relative max-w-xs overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                        <img src="${path}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('${path}')">
                    </div>
                `;
            } else if (type === 'gmaps') {
                const locName = name || 'Lokasi Terbagikan';
                html += `
                    <div data-coords="${path}" data-name="${locName}" onclick="viewSharedLocation(this)" class="inline-flex items-center gap-3 bg-[#e8edf2] border border-[#d0e4f5] rounded-2xl p-3 hover:bg-[#d8e4f5] transition max-w-xs cursor-pointer">
                        <div class="w-10 h-10 bg-[#1a3a5c] rounded-xl flex items-center justify-center text-white">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-[#1a3a5c] truncate">${locName}</p>
                            <p class="text-[10px] text-gray-500">Lihat Peta</p>
                        </div>
                        <i data-lucide="map" class="w-4 h-4 text-[#1a3a5c]"></i>
                    </div>
                `;
            }
            html += '</div>';
            return html;
        }

        const TENOR_API_KEY = 'LIVDSRZULELA';

        const TENOR_CATEGORIES = [
            { name: 'Favorit', query: 'love', overlayClass: 'bg-[#ff4b5c]/60 hover:bg-[#ff4b5c]/50' },
            { name: 'Tren Baru', query: 'trending', overlayClass: 'bg-black/40 hover:bg-black/30' },
            { name: 'Keren', query: 'awesome', overlayClass: 'bg-black/40 hover:bg-black/30' },
            { name: 'Bercanda', query: 'jk', overlayClass: 'bg-black/40 hover:bg-black/30' },
            { name: 'Semoga Sukses', query: 'good luck', overlayClass: 'bg-black/40 hover:bg-black/30' },
            { name: 'Tos', query: 'high five', overlayClass: 'bg-black/40 hover:bg-black/30' }
        ];

        window.tenorCategoryCache = window.tenorCategoryCache || {};

        function getFavoritedGifs() {
            try {
                const favs = localStorage.getItem('alinea_tenor_favorites');
                return favs ? JSON.parse(favs) : [];
            } catch (e) {
                return [];
            }
        }

        function toggleFavoriteGif(gifUrl) {
            try {
                let favs = getFavoritedGifs();
                const idx = favs.indexOf(gifUrl);
                if (idx > -1) {
                    favs.splice(idx, 1);
                } else {
                    favs.push(gifUrl);
                }
                localStorage.setItem('alinea_tenor_favorites', JSON.stringify(favs));
                return idx === -1;
            } catch (e) {
                return false;
            }
        }

        function renderFavoritesList(formEl) {
            const popover = formEl.querySelector('.tenor-popover');
            if (!popover) return;

            const resultsContainer = popover.querySelector('.tenor-popover-results');
            const clearBtn = popover.querySelector('.btn-clear-search');
            if (clearBtn) clearBtn.classList.remove('hidden');

            resultsContainer.innerHTML = '';
            const favs = getFavoritedGifs();
            
            if (favs.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="col-span-2 flex flex-col items-center justify-center py-8 px-4 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-400 mb-2 fill-yellow-400 animate-pulse" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <p class="text-xs font-bold text-[#1a3a5c]">Favorit Kosong</p>
                        <p class="text-[10px] text-gray-400 mt-1 max-w-[200px] leading-relaxed">Belum ada GIF favorit. Klik bintang pada GIF apa saja untuk menyimpannya di sini.</p>
                    </div>
                `;
                return;
            }

            favs.forEach(gifUrl => {
                const container = document.createElement('div');
                container.className = 'relative group w-full h-24 rounded-lg overflow-hidden border border-gray-100 shadow-sm bg-gray-50';

                const img = document.createElement('img');
                img.src = gifUrl;
                img.className = 'w-full h-full object-cover cursor-pointer hover:scale-105 transition duration-200';
                img.onclick = () => {
                    selectGifForForm(formEl, gifUrl);
                };

                const favBtn = document.createElement('button');
                favBtn.type = 'button';
                favBtn.className = 'absolute top-1 right-1 p-1 rounded-full bg-black/40 hover:bg-black/60 transition z-10 text-white';
                favBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`;
                
                favBtn.onclick = (e) => {
                    e.stopPropagation();
                    toggleFavoriteGif(gifUrl);
                    container.remove();
                    if (resultsContainer.children.length === 0) {
                        resultsContainer.innerHTML = `
                            <div class="col-span-2 flex flex-col items-center justify-center py-8 px-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-400 mb-2 fill-yellow-400 animate-pulse" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <p class="text-xs font-bold text-[#1a3a5c]">Favorit Kosong</p>
                                <p class="text-[10px] text-gray-400 mt-1 max-w-[200px] leading-relaxed">Belum ada GIF favorit. Klik bintang pada GIF apa saja untuk menyimpannya di sini.</p>
                            </div>
                        `;
                    }
                };

                container.appendChild(img);
                container.appendChild(favBtn);
                resultsContainer.appendChild(container);
            });
        }

        function toggleTenorPopover(formEl) {
            console.log("toggleTenorPopover toggled for form:", formEl);
            // Close any other open tenor popovers first
            document.querySelectorAll('.tenor-popover').forEach(pop => {
                if (pop !== formEl.querySelector('.tenor-popover')) {
                    pop.classList.add('hidden');
                }
            });

            const popover = formEl.querySelector('.tenor-popover');
            if (!popover) {
                console.log("Popover element not found inside form!");
                return;
            }

            if (popover.classList.contains('hidden')) {
                console.log("Popover was hidden, showing it now");
                popover.classList.remove('hidden');
                const searchInput = popover.querySelector('.tenor-popover-search');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                
                // Show categories by default when opened
                renderTenorCategories(formEl);
            } else {
                console.log("Popover was visible, hiding it now");
                popover.classList.add('hidden');
            }
        }

        function renderTenorCategories(formEl) {
            const popover = formEl.querySelector('.tenor-popover');
            if (!popover) return;

            const resultsContainer = popover.querySelector('.tenor-popover-results');
            const clearBtn = popover.querySelector('.btn-clear-search');
            if (clearBtn) clearBtn.classList.add('hidden');

            resultsContainer.innerHTML = '';
            TENOR_CATEGORIES.forEach(cat => {
                const card = document.createElement('div');
                card.className = 'relative h-20 rounded-xl overflow-hidden cursor-pointer hover:scale-[1.02] active:scale-[0.98] transition duration-200 shadow-sm border border-gray-200 bg-gray-50';
                
                const img = document.createElement('img');
                img.className = 'absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300';
                
                if (window.tenorCategoryCache[cat.name]) {
                    img.src = window.tenorCategoryCache[cat.name];
                    img.classList.remove('opacity-0');
                } else {
                    fetch(`https://g.tenor.com/v1/search?key=${TENOR_API_KEY}&q=${encodeURIComponent(cat.query)}&limit=1`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.results && data.results[0] && data.results[0].media && data.results[0].media[0]) {
                                const mediaObj = data.results[0].media[0];
                                const previewUrl = (mediaObj.tinygif && mediaObj.tinygif.url) ? mediaObj.tinygif.url : mediaObj.gif.url;
                                window.tenorCategoryCache[cat.name] = previewUrl;
                                img.src = previewUrl;
                                img.classList.remove('opacity-0');
                            }
                        })
                        .catch(err => console.error("Error loading category image:", err));
                }
                
                const overlay = document.createElement('div');
                overlay.className = `absolute inset-0 flex items-center justify-center font-bold text-white text-xs tracking-wide transition-colors duration-200 ${cat.overlayClass}`;
                
                if (cat.name === 'Tren Baru') {
                    overlay.innerHTML = `<span class="flex items-center gap-1"><i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Tren Baru</span>`;
                } else {
                    overlay.textContent = cat.name;
                }
                
                card.appendChild(img);
                card.appendChild(overlay);
                
                card.onclick = () => {
                    const searchInput = popover.querySelector('.tenor-popover-search');
                    if (searchInput) {
                        searchInput.value = cat.name;
                        if (clearBtn) clearBtn.classList.remove('hidden');
                    }
                    if (cat.name === 'Favorit') {
                        renderFavoritesList(formEl);
                    } else {
                        fetchTenorForForm(formEl, 'search', cat.query);
                    }
                };
                
                resultsContainer.appendChild(card);
            });
            if (window.lucide) window.lucide.createIcons();
        }

        async function fetchTenorForForm(formEl, type, query = '') {
            const popover = formEl.querySelector('.tenor-popover');
            if (!popover) return;

            const resultsContainer = popover.querySelector('.tenor-popover-results');
            const clearBtn = popover.querySelector('.btn-clear-search');
            
            // Show clear button if there's a search active
            if (clearBtn) {
                if (query || type === 'search') {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            resultsContainer.innerHTML = '<div class="col-span-2 flex justify-center py-6"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#1a3a5c]"></div></div>';
            
            let url = `https://g.tenor.com/v1/trending?key=${TENOR_API_KEY}&limit=12`;
            if (type === 'search' && query) {
                url = `https://g.tenor.com/v1/search?key=${TENOR_API_KEY}&q=${encodeURIComponent(query)}&limit=12`;
            }
            
            try {
                const res = await fetch(url);
                const data = await res.json();
                
                resultsContainer.innerHTML = '';
                if (data.results && data.results.length > 0) {
                    data.results.forEach(item => {
                        if (item.media && item.media[0]) {
                            const mediaObj = item.media[0];
                            const gifUrl = mediaObj.gif.url;
                            const previewUrl = (mediaObj.tinygif && mediaObj.tinygif.url) ? mediaObj.tinygif.url : gifUrl;
                            
                            const container = document.createElement('div');
                            container.className = 'relative group w-full h-24 rounded-lg overflow-hidden border border-gray-100 shadow-sm bg-gray-50';

                            const img = document.createElement('img');
                            img.src = previewUrl;
                            img.className = 'w-full h-full object-cover cursor-pointer hover:scale-105 transition duration-200';
                            img.onclick = () => {
                                selectGifForForm(formEl, gifUrl);
                            };

                            const favBtn = document.createElement('button');
                            favBtn.type = 'button';
                            const isFav = getFavoritedGifs().includes(gifUrl);
                            favBtn.className = 'absolute top-1 right-1 p-1 rounded-full bg-black/40 hover:bg-black/60 transition z-10 text-white';
                            favBtn.innerHTML = isFav 
                                ? `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`
                                : `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-200 hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.25.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 9.42c-.783-.57-.384-1.81.587-1.81H8.48a1 1 0 00.95-.69L11.05 2.92z"/></svg>`;
                            
                            favBtn.onclick = (e) => {
                                e.stopPropagation();
                                const added = toggleFavoriteGif(gifUrl);
                                if (added) {
                                    favBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`;
                                } else {
                                    favBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-200 hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.25.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 9.42c-.783-.57-.384-1.81.587-1.81H8.48a1 1 0 00.95-.69L11.05 2.92z"/></svg>`;
                                }
                            };

                            container.appendChild(img);
                            container.appendChild(favBtn);
                            resultsContainer.appendChild(container);
                        }
                    });
                } else {
                    resultsContainer.innerHTML = '<p class="col-span-2 text-center text-gray-400 py-6 text-[11px]">GIF tidak ditemukan.</p>';
                }
            } catch (err) {
                console.error(err);
                resultsContainer.innerHTML = '<p class="col-span-2 text-center text-red-500 py-6 text-[11px]">Gagal memuat GIF.</p>';
            }
        }

        function selectGifForForm(formEl, gifUrl) {
            clearAttachment(formEl);
            formEl.querySelector('.attachment-type-input').value = 'tenor';
            formEl.querySelector('.attachment-url-input').value = gifUrl;
            renderPreview(formEl, 'tenor', gifUrl);
            
            // Close the popover
            const popover = formEl.querySelector('.tenor-popover');
            if (popover) popover.classList.add('hidden');
        }

        // Close tenor popovers & emoji pickers when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.tenor-popover').forEach(pop => {
                pop.classList.add('hidden');
            });
            document.querySelectorAll('.emoji-picker-panel').forEach(panel => {
                panel.remove();
            });
        });

        function insertTextAtCursor(textarea, text) {
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const val = textarea.value;
            textarea.value = val.substring(0, start) + text + val.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + text.length;
            textarea.focus();
            
            // Trigger input event to update limits/listeners
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
        }

        const EMOJI_DATA = [
            {
                category: 'Ekspresi',
                icon: '😀',
                emojis: ['😀','😃','😄','😁','😆','😅','😂','🤣','😊','😇','🙂','🙃','😉','😌','😍','🥰','😘','😗','😙','😚','😋','😛','😝','😜','🤪','🤨','🧐','🤓','😎','🥸','🤩','🥳','😏','😒','😞','😔','😟','😕','🙁','☹️','😣','😖','😫','😩','🥺','😢','😭','😤','😠','😡','🤬','🤯','😳','🥵','🥶','😱','😨','😰','😥','😓','🤔','🫣','🤭','🫢','🫡','🤫','🫠','🤥','😶','😐','😑','😬','🙄','😯','😦','😧','😮','😲','🥱','😴','🤤','😪','😵','😵‍💫','🥴','🤢','🤮','🤧','😷','🤒','🤕','🤑','🤠','😈','👿','👹','👺','💀','☠️','👽','👾','🤖','🎃']
            },
            {
                category: 'Tangan & Tubuh',
                icon: '👋',
                emojis: ['👋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🫰','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏','✍️','💅','🤳','💪','🦾','👂','🦻','👃','🧠','🫀','🫁','🦷','🦴','👀','👁️','👅','👄']
            },
            {
                category: 'Hati & Cinta',
                icon: '❤️',
                emojis: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❤️‍🔥','❤️‍🩹','❣️','💕','💞','💓','💗','💖','💘','💝','💟','💌','💋']
            },
            {
                category: 'Hewan & Alam',
                icon: '🐱',
                emojis: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐽','🐸','🐵','🙈','🙉','🙊','🐒','🐔','🐧','🐦','🐤','🐣','🐥','🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🪱','🐛','🦋','🐌','🐞','🐜','🪰','🪲','🦗','🕷️','🕸️','Scorpion','🐢','🐍','🦎','🐙','🦑','🦞','🦀','🐡','🐠','🐟','🐬','🐳','🐋','🦈','🐊','🐆','🐅']
            },
            {
                category: 'Makanan & Minuman',
                icon: '🍎',
                emojis: ['🍎','🍊','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🥑','🥦','🥬','🥒','🌶️','🫑','🌽','🥕','🍞','🥐','🥞','🧇','🧀','🍖','🍗','🥩','🥓','🍔','🍟','🌭','🥪','🌮','🌯',' Salad','🍿','🍳','🥤','🧋','☕','🍵','🍺','🍻','🍷']
            },
            {
                category: 'Aktivitas',
                icon: '⚽',
                emojis: ['⚽','🏀','🏈','⚾','🥎','🎾','🏐','🎱','🏓','🏸','⛳','🏹','🥊','🥋','🛹','⛸️','⛷️','🏂','🏋️','🤸','⛹️','🤺','🤾','🏌️','🧘','🏄','🏊','🚣','🧗','🚴','🏆','🥇','🥈','🥉','🏅','🎫','🎬','🎨','🎧']
            },
            {
                category: 'Perjalanan & Tempat',
                icon: '🚗',
                emojis: ['🚗','🚕','🚙','🚌','🏎️','🚓','🚒','🚚','🛵','🏍️','🚲','🚨','🛑','✈️','🚀','🛸','🚁','⛵','⚓','🗺️','🧭','🏔️','⛰️','🌋','🗻','🏕️','⛺','🏠','🏡','🏢','🏥','🏦','🏨','🏫','🏰','🏯','🗼','🗽','⛩️','🕋','🪐','🌑','🌕','☀️','⭐','☁️','🌧️','❄️','🔥']
            },
            {
                category: 'Objek & Simbol',
                icon: '💡',
                emojis: ['⌚','📱','💻','⌨️','📷','📺','📻','🕯️','💡','🔦','🧱','🔪','🛡️','🚬','🔮','🧿','💈','🧲','🧪','🧬','🗝️','🔑','🔨','🪛','🔧','🪚','⚙️','⚖️','🔗','⛓️','🩹','🩺','📦','✉️','🏷️','✏️','✒️','📝','💼','📁','📅','🗑️','🔒','🔓','🔔','📣','❓','❔','❗','❕','💯']
            }
        ];

        function toggleEmojiPicker(buttonEl, formEl) {
            // Close any other open emoji pickers first
            document.querySelectorAll('.emoji-picker-panel').forEach(panel => {
                if (panel !== formEl.querySelector('.emoji-picker-panel')) {
                    panel.remove();
                }
            });
            // Also close tenor popover
            const tenorPopover = formEl.querySelector('.tenor-popover');
            if (tenorPopover) tenorPopover.classList.add('hidden');

            let picker = formEl.querySelector('.emoji-picker-panel');
            if (picker) {
                picker.remove();
                return;
            }

            picker = document.createElement('div');
            picker.className = 'emoji-picker-panel absolute bottom-14 left-4 right-4 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 flex flex-col max-h-[280px] overflow-hidden animate-scale-up';
            picker.onclick = (e) => e.stopPropagation();

            picker.innerHTML = `
                <div class="p-2 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <span class="text-xs font-bold text-[#1a3a5c] flex items-center gap-1">
                        <i data-lucide="smile" class="w-3.5 h-3.5 text-[#1a3a5c]"></i> Pilih Emoji
                    </span>
                    <input type="text" placeholder="Cari emoji..." class="emoji-search-input bg-white text-[11px] text-gray-800 border border-gray-200 rounded-xl px-2.5 py-0.5 outline-none focus:border-[#1a3a5c] w-32 transition">
                </div>
                <div class="emoji-categories flex gap-1 border-b border-gray-100 p-1 overflow-x-auto bg-gray-50/30">
                    ${EMOJI_DATA.map((cat, idx) => `
                        <button type="button" class="category-tab-btn p-1 hover:bg-gray-100 rounded-lg text-sm transition" data-idx="${idx}" title="${cat.category}">
                            ${cat.icon}
                        </button>
                    `).join('')}
                </div>
                <div class="emoji-grid flex-1 overflow-y-auto p-2 grid grid-cols-8 gap-1 bg-white max-h-[170px]">
                    <!-- Emojis will go here -->
                </div>
            `;

            formEl.appendChild(picker);
            if (window.lucide) window.lucide.createIcons();

            const searchInput = picker.querySelector('.emoji-search-input');
            const grid = picker.querySelector('.emoji-grid');
            const textarea = formEl.querySelector('textarea');

            const renderEmojis = (filteredList) => {
                grid.innerHTML = filteredList.map(emoji => `
                    <button type="button" class="emoji-btn hover:scale-125 hover:bg-gray-50 rounded-lg p-1 text-lg transition duration-100 flex items-center justify-center">
                        ${emoji}
                    </button>
                `).join('');
                
                grid.querySelectorAll('.emoji-btn').forEach(btn => {
                    btn.onclick = () => {
                        insertTextAtCursor(textarea, btn.textContent.trim());
                    };
                });
            };

            const showCategory = (idx) => {
                picker.querySelectorAll('.category-tab-btn').forEach(btn => {
                    btn.classList.toggle('bg-gray-100', parseInt(btn.dataset.idx) === idx);
                });
                renderEmojis(EMOJI_DATA[idx].emojis);
            };

            picker.querySelectorAll('.category-tab-btn').forEach(btn => {
                btn.onclick = () => {
                    showCategory(parseInt(btn.dataset.idx));
                    if (searchInput) searchInput.value = '';
                };
            });

            if (searchInput) {
                searchInput.oninput = (e) => {
                    const val = e.target.value.trim().toLowerCase();
                    if (!val) {
                        showCategory(0);
                        return;
                    }
                    
                    let matchedEmojis = [];
                    EMOJI_DATA.forEach(cat => {
                        if (cat.category.toLowerCase().includes(val)) {
                            matchedEmojis = matchedEmojis.concat(cat.emojis);
                        }
                    });
                    
                    const keywordDict = {
                        'love': ['❤️','😍','🥰','😘','💖','💕','❤️‍🔥'],
                        'smile': ['😀','😃','😄','😁','😆','😊','🙂'],
                        'laugh': ['😂','🤣','😆','😅'],
                        'sad': ['😢','😭','😞','😔','🥺'],
                        'angry': ['😠','😡','🤬','😤'],
                        'heart': ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','💖','💕'],
                        'thumbs': ['👍','👎'],
                        'yes': ['👍','👌','✔️'],
                        'no': ['👎','❌'],
                        'fire': ['🔥','❤️‍🔥'],
                        'star': ['⭐','🌟','✨','💫'],
                        'ok': ['👌','👍'],
                        'clap': ['👏'],
                        'cool': ['😎']
                    };
                    
                    Object.keys(keywordDict).forEach(key => {
                        if (key.includes(val)) {
                            matchedEmojis = matchedEmojis.concat(keywordDict[key]);
                        }
                    });
                    
                    matchedEmojis = Array.from(new Set(matchedEmojis));
                    
                    if (matchedEmojis.length > 0) {
                        renderEmojis(matchedEmojis);
                    } else {
                        grid.innerHTML = '<span class="col-span-8 text-[10px] text-gray-400 text-center py-4">Tidak ada hasil</span>';
                    }
                };
            }

            showCategory(0);
        }

        // --- MAPS (LEAFLET) MODAL CONTROLLER ---
        let leafletMap = null;
        let leafletMarker = null;
        let currentTileLayer = null;
        let selectedLat = -6.2088;
        let selectedLng = 106.8456;
        let selectedLocationName = "Jakarta, Indonesia";

        const MAP_LAYERS = {
            streets: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }),
            satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
            }),
            dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
            })
        };

        function switchMapLayer(layerName) {
            if (!leafletMap) return;
            
            // Remove current active layer
            if (currentTileLayer) {
                leafletMap.removeLayer(currentTileLayer);
            }
            
            // Add new layer
            currentTileLayer = MAP_LAYERS[layerName];
            currentTileLayer.addTo(leafletMap);
            
            // Update active button state style
            ['streets', 'satellite', 'dark'].forEach(name => {
                const btn = document.getElementById(`layer-btn-${name}`);
                if (btn) {
                    if (name === layerName) {
                        btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white shadow-sm";
                    } else {
                        btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200";
                    }
                }
            });
        }

        function openMapModal(formEl) {
            window.activeAttachmentForm = formEl;
            document.getElementById('map-modal').classList.remove('hidden');
            document.getElementById('map-search-input').value = '';
            document.getElementById('map-search-results').classList.add('hidden');
            document.getElementById('map-search-results').innerHTML = '';
            
            setTimeout(() => {
                if (!leafletMap) {
                    leafletMap = L.map('leaflet-map').setView([selectedLat, selectedLng], 13);
                    
                    // Set default layer (streets)
                    currentTileLayer = MAP_LAYERS.streets;
                    currentTileLayer.addTo(leafletMap);
                    
                    leafletMarker = L.marker([selectedLat, selectedLng], { draggable: true }).addTo(leafletMap);
                    
                    const updateCoords = (lat, lng) => {
                        selectedLat = lat;
                        selectedLng = lng;
                        document.getElementById('selected-coords-text').textContent = `Koordinat: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                        
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                            .then(res => res.json())
                            .then(data => {
                                selectedLocationName = data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                                document.getElementById('selected-coords-text').textContent = `Lokasi: ${selectedLocationName}`;
                                leafletMarker.bindPopup(`<b>Lokasi Terpilih</b><br><span class="text-xs text-gray-600">${selectedLocationName}</span>`).openPopup();
                            })
                            .catch(() => {
                                selectedLocationName = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            });
                    };
                    
                    leafletMarker.on('dragend', function(e) {
                        const position = leafletMarker.getLatLng();
                        updateCoords(position.lat, position.lng);
                    });
                    
                    leafletMap.on('click', function(e) {
                        leafletMarker.setLatLng(e.latlng);
                        updateCoords(e.latlng.lat, e.latlng.lng);
                    });
                } else {
                    leafletMap.invalidateSize();
                }
            }, 200);
        }

        function closeMapModal() {
            document.getElementById('map-modal').classList.add('hidden');
        }

        async function getCurrentLocation() {
            const locBtn = document.querySelector('button[onclick="getCurrentLocation()"]');
            if (locBtn) locBtn.disabled = true;

            if (!navigator.geolocation) {
                alert('Geolokasi tidak didukung oleh browser Anda.');
                if (locBtn) locBtn.disabled = false;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    selectedLat = lat;
                    selectedLng = lon;

                    leafletMap.setView([lat, lon], 17);
                    leafletMarker.setLatLng([lat, lon]);

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(res => res.json())
                        .then(data => {
                            selectedLocationName = data.display_name || `${lat.toFixed(4)}, ${lon.toFixed(4)}`;
                            document.getElementById('selected-coords-text').textContent = `Lokasi: ${selectedLocationName}`;
                            leafletMarker.bindPopup(`<b>Lokasi Saya</b><br><span class="text-xs text-gray-600">${selectedLocationName}</span>`).openPopup();
                        })
                        .catch(() => {
                            selectedLocationName = `${lat.toFixed(4)}, ${lon.toFixed(4)}`;
                            document.getElementById('selected-coords-text').textContent = `Lokasi: ${selectedLocationName}`;
                        });
                    
                    if (locBtn) locBtn.disabled = false;
                },
                (error) => {
                    console.error(error);
                    let errMsg = 'Gagal mendapatkan lokasi Anda saat ini.';
                    if (error.code === error.PERMISSION_DENIED) {
                        errMsg += '\n\nIzin akses lokasi ditolak oleh browser. Silakan aktifkan izin lokasi untuk situs ini di pengaturan browser Anda.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        errMsg += '\n\nLayanan lokasi perangkat tidak tersedia. Jika Anda menggunakan Windows, silakan aktifkan dengan cara:\n1. Buka Settings > Privacy & security > Location.\n2. Aktifkan/Nyalakan "Location services".\n3. Pastikan izin akses lokasi untuk browser Anda juga sudah dicentang/diaktifkan.';
                    } else if (error.code === error.TIMEOUT) {
                        errMsg += '\n\nWaktu permintaan habis. Coba ulangi beberapa saat lagi.';
                    }
                    alert(errMsg);
                    if (locBtn) locBtn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        }

        async function searchLocation() {
            const query = document.getElementById('map-search-input').value.trim();
            if (!query) return;
            
            const searchBtn = document.querySelector('#map-modal button[onclick="searchLocation()"]');
            const resultsContainer = document.getElementById('map-search-results');
            searchBtn.disabled = true;
            
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`);
                const data = await res.json();
                
                resultsContainer.innerHTML = '';
                if (data && data.length > 0) {
                    resultsContainer.classList.remove('hidden');
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-gray-50 cursor-pointer transition text-xs text-gray-700 flex items-start gap-2';
                        div.innerHTML = `
                            <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="truncate">${item.display_name}</span>
                        `;
                        div.onclick = () => {
                            const lat = parseFloat(item.lat);
                            const lon = parseFloat(item.lon);
                            selectedLat = lat;
                            selectedLng = lon;
                            selectedLocationName = item.display_name;
                            
                            leafletMap.setView([lat, lon], 17);
                            leafletMarker.setLatLng([lat, lon]);
                            document.getElementById('selected-coords-text').textContent = `Lokasi: ${selectedLocationName}`;
                            leafletMarker.bindPopup(`<b>${item.type || 'Lokasi'}</b><br><span class="text-xs text-gray-600">${selectedLocationName}</span>`).openPopup();
                            
                            resultsContainer.classList.add('hidden');
                            document.getElementById('map-search-input').value = item.display_name;
                        };
                        resultsContainer.appendChild(div);
                    });
                } else {
                    resultsContainer.innerHTML = '<p class="p-3 text-xs text-gray-400 text-center">Lokasi tidak ditemukan.</p>';
                    resultsContainer.classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
                alert('Gagal mencari lokasi.');
            } finally {
                searchBtn.disabled = false;
            }
        }

        // Close search results dropdown on click outside
        document.addEventListener('click', (e) => {
            const results = document.getElementById('map-search-results');
            if (results && !results.contains(e.target) && e.target.id !== 'map-search-input') {
                results.classList.add('hidden');
            }
        });

        document.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && e.target && e.target.id === 'map-search-input') {
                searchLocation();
            }
        });

        function confirmShareLocation() {
            const form = window.activeAttachmentForm;
            if (form) {
                clearAttachment(form);
                form.querySelector('.attachment-type-input').value = 'gmaps';
                form.querySelector('.attachment-url-input').value = `${selectedLat},${selectedLng}|${selectedLocationName}`;
                renderPreview(form, 'gmaps', selectedLocationName);
            }
            closeMapModal();
        }

        // --- VIEW MAP MODAL CONTROLLER ---
        let viewLeafletMap = null;
        let viewLeafletMarker = null;
        let currentViewTileLayer = null;
        let viewSelectedLat = -6.2088;
        let viewSelectedLng = 106.8456;

        const VIEW_MAP_LAYERS = {
            streets: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }),
            satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri'
            }),
            dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO'
            })
        };

        function switchViewMapLayer(layerName) {
            if (!viewLeafletMap) return;
            if (currentViewTileLayer) {
                viewLeafletMap.removeLayer(currentViewTileLayer);
            }
            currentViewTileLayer = VIEW_MAP_LAYERS[layerName];
            currentViewTileLayer.addTo(viewLeafletMap);
            
            ['streets', 'satellite', 'dark'].forEach(name => {
                const btn = document.getElementById(`view-layer-btn-${name}`);
                if (btn) {
                    if (name === layerName) {
                        btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white shadow-sm";
                    } else {
                        btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200";
                    }
                }
            });
        }

        function viewSharedLocation(elOrCoordsStr, locationName) {
            let coordsStr = '';
            let locName = '';
            
            if (typeof elOrCoordsStr === 'object' && elOrCoordsStr !== null) {
                coordsStr = elOrCoordsStr.getAttribute('data-coords') || '';
                locName = elOrCoordsStr.getAttribute('data-name') || 'Lokasi Terbagikan';
            } else {
                coordsStr = elOrCoordsStr || '';
                locName = locationName || 'Lokasi Terbagikan';
            }

            const parts = coordsStr.split('|')[0].split(',');
            if (parts.length < 2) return;
            
            const lat = parseFloat(parts[0]);
            const lng = parseFloat(parts[1]);
            viewSelectedLat = lat;
            viewSelectedLng = lng;

            document.getElementById('view-map-title').textContent = locName;
            document.getElementById('view-map-coords-text').textContent = `Koordinat: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            document.getElementById('view-map-gmaps-link').href = `https://www.google.com/maps?q=${lat},${lng}`;
            
            document.getElementById('view-map-modal').classList.remove('hidden');

            setTimeout(() => {
                if (!viewLeafletMap) {
                    viewLeafletMap = L.map('view-leaflet-map').setView([lat, lng], 17);
                    currentViewTileLayer = VIEW_MAP_LAYERS.streets;
                    currentViewTileLayer.addTo(viewLeafletMap);
                    
                    viewLeafletMarker = L.marker([lat, lng]).addTo(viewLeafletMap);
                } else {
                    viewLeafletMap.setView([lat, lng], 17);
                    viewLeafletMarker.setLatLng([lat, lng]);
                    viewLeafletMap.invalidateSize();
                }
                viewLeafletMarker.bindPopup(`<b>${locName}</b>`).openPopup();
                
                // Reset layer buttons to streets
                switchViewMapLayer('streets');
            }, 200);
        }

        function closeViewMapModal() {
            document.getElementById('view-map-modal').classList.add('hidden');
        }

        // Close modals on Escape key press
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMapModal();
                closeViewMapModal();
                closeLightbox();
            }
        });

        // --- ATTACHMENT BINDINGS ---
        function initAttachmentFormBindings(form) {
            const fileInput = form.querySelector('.attachment-file-input');
            const typeInput = form.querySelector('.attachment-type-input');
            const urlInput = form.querySelector('.attachment-url-input');
            
            const btnImage = form.querySelector('.btn-attach-image');
            const btnTenor = form.querySelector('.btn-attach-tenor');
            const btnGmaps = form.querySelector('.btn-attach-gmaps');
            const btnEmoji = form.querySelector('.btn-attach-emoji');
            
            if (btnImage && fileInput) {
                btnImage.onclick = () => {
                    fileInput.setAttribute('accept', 'image/*');
                    fileInput.click();
                };
            }

            if (btnEmoji) {
                btnEmoji.onclick = (e) => {
                    e.stopPropagation();
                    toggleEmojiPicker(btnEmoji, form);
                };
            }
            
            if (fileInput) {
                fileInput.onchange = (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        if (typeInput) typeInput.value = '';
                        if (urlInput) urlInput.value = '';
                        
                        const isImg = file.type.startsWith('image/');
                        
                        if (isImg) {
                            const reader = new FileReader();
                            reader.onload = (evt) => {
                                renderPreview(form, 'image', evt.target.result);
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                };
            }
            
            if (btnTenor) {
                btnTenor.onclick = (e) => {
                    e.stopPropagation();
                    toggleTenorPopover(form);
                };
            }
            
            // Popover search, clear, and close controls
            const popover = form.querySelector('.tenor-popover');
            if (popover) {
                popover.onclick = (e) => {
                    e.stopPropagation();
                };
                const closeBtn = popover.querySelector('.btn-close-tenor');
                if (closeBtn) {
                    closeBtn.onclick = () => popover.classList.add('hidden');
                }
                
                const searchInput = popover.querySelector('.tenor-popover-search');
                const clearBtn = popover.querySelector('.btn-clear-search');
                
                if (searchInput) {
                    let timeout = null;
                    searchInput.oninput = (e) => {
                        clearTimeout(timeout);
                        const query = e.target.value.trim();
                        if (clearBtn) {
                            if (query) {
                                clearBtn.classList.remove('hidden');
                            } else {
                                clearBtn.classList.add('hidden');
                            }
                        }
                        timeout = setTimeout(() => {
                            if (query) {
                                fetchTenorForForm(form, 'search', query);
                            } else {
                                renderTenorCategories(form);
                            }
                        }, 400);
                    };
                }

                if (clearBtn) {
                    clearBtn.onclick = () => {
                        if (searchInput) searchInput.value = '';
                        clearBtn.classList.add('hidden');
                        renderTenorCategories(form);
                    };
                }
            }
            
            if (btnGmaps) {
                btnGmaps.onclick = () => openMapModal(form);
            }
            
            const textarea = form.querySelector('textarea');
            if (textarea) {
                // Auto-detect pasted/typed GIF link and convert to attachment preview
                const checkGif = () => {
                    const content = textarea.value;
                    const gifRegex = /(https?:\/\/[^\s<>\"]+?\.(?:gif)(?:[?#][^\s<>\"]*)?|https?:\/\/(?:www\.)?media\.tenor\.com\/[^\s<>\"]+)/i;
                    const match = content.match(gifRegex);
                    if (match) {
                        const gifUrl = match[1];
                        textarea.value = content.replace(gifUrl, '').trim();
                        selectGifForForm(form, gifUrl);
                    }
                };
                
                textarea.addEventListener('input', checkGif);
                textarea.addEventListener('change', checkGif);

                textarea.addEventListener('paste', (e) => {
                    const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                    for (let index in items) {
                        const item = items[index];
                        if (item.kind === 'file' && item.type.startsWith('image/')) {
                            const blob = item.getAsFile();
                            const file = new File([blob], "screenshot_" + Date.now() + ".png", { type: blob.type });
                            
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            fileInput.files = dataTransfer.files;
                            
                            if (typeInput) typeInput.value = '';
                            if (urlInput) urlInput.value = '';
                            
                            const reader = new FileReader();
                            reader.onload = (evt) => {
                                renderPreview(form, 'image', evt.target.result);
                            };
                            reader.readAsDataURL(file);
                            
                            e.preventDefault();
                            break;
                        }
                    }
                    setTimeout(checkGif, 10);
                });
            }
        }

        function renderPreview(form, type, value) {
            const previewBox = form.querySelector('.attachment-preview-box');
            if (!previewBox) return;
            
            previewBox.classList.remove('hidden');
            previewBox.innerHTML = '';
            
            let previewHtml = '';
            if (type === 'image' || type === 'giphy' || type === 'tenor') {
                previewHtml = `
                    <div class="flex items-center gap-3">
                        <img src="${value}" class="w-12 h-12 object-cover rounded-xl border border-gray-200">
                        <div>
                            <p class="text-xs font-bold text-[#1a3a5c]">${type === 'image' ? 'Unggahan Gambar' : 'GIF Terpilih'}</p>
                            <p class="text-[10px] text-gray-400">Siap dikirim</p>
                        </div>
                    </div>
                `;
            } else if (type === 'file') {
                previewHtml = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center text-[#1a3a5c]">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#1a3a5c] truncate max-w-[180px]">${value}</p>
                            <p class="text-[10px] text-gray-400">Berkas unggahan</p>
                        </div>
                    </div>
                `;
            } else if (type === 'gmaps') {
                previewHtml = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#e8edf2] rounded-xl flex items-center justify-center text-[#1a3a5c]">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#1a3a5c] truncate max-w-[180px]">${value}</p>
                            <p class="text-[10px] text-gray-400">Lokasi terbagikan</p>
                        </div>
                    </div>
                `;
            }
            
            previewHtml += `
                <button type="button" class="btn-clear-attachment p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            `;
            
            previewBox.innerHTML = previewHtml;
            if (window.lucide) window.lucide.createIcons();
            
            previewBox.querySelector('.btn-clear-attachment').onclick = () => {
                clearAttachment(form);
            };
        }

        function clearAttachment(form) {
            const fileInput = form.querySelector('.attachment-file-input');
            if (fileInput) fileInput.value = '';
            
            const typeInput = form.querySelector('.attachment-type-input');
            if (typeInput) typeInput.value = '';
            
            const urlInput = form.querySelector('.attachment-url-input');
            if (urlInput) urlInput.value = '';
            
            const previewBox = form.querySelector('.attachment-preview-box');
            if (previewBox) {
                previewBox.classList.add('hidden');
                previewBox.innerHTML = '';
            }
        }

        // --- LIGHTBOX CONTROLLER ---
        function openLightbox(url) {
            const lb = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            img.src = url;
            lb.classList.remove('hidden');
            lb.classList.remove('pointer-events-none');
            setTimeout(() => {
                lb.classList.add('opacity-100');
                img.classList.remove('scale-95');
                img.classList.add('scale-100');
            }, 10);
        }

        function closeLightbox() {
            const lb = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            lb.classList.remove('opacity-100');
            img.classList.remove('scale-100');
            img.classList.add('scale-95');
            setTimeout(() => {
                lb.classList.add('hidden');
                lb.classList.add('pointer-events-none');
            }, 300);
        }
    </script>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black/90 z-[9999] hidden flex items-center justify-center opacity-0 transition-opacity duration-300 pointer-events-none" onclick="closeLightbox()">
        <button class="absolute top-6 right-6 text-white/70 hover:text-white transition">
            <i data-lucide="x" class="w-8 h-8"></i>
        </button>
        <img id="lightbox-img" class="max-w-[90%] max-h-[90%] object-contain rounded-lg shadow-2xl transform scale-95 transition-transform duration-300">
    </div>



    <!-- Map Modal -->
    <div id="map-modal" class="fixed inset-0 bg-black/60 z-[999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeMapModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[85vh] animate-scale-up">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-[#1a3a5c] flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5"></i> Bagikan Lokasi
                </h3>
                <button onclick="closeMapModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-4 bg-gray-50 border-b border-gray-100 flex flex-col gap-2 relative">
                <div class="flex gap-2">
                    <input type="text" id="map-search-input" class="flex-1 bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-[#1a3a5c] transition" placeholder="Cari nama lokasi atau alamat...">
                    <button onclick="searchLocation()" class="bg-[#1a3a5c] text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-[#122b45] transition flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="search" class="w-4 h-4"></i> Cari
                    </button>
                    <button onclick="getCurrentLocation()" class="bg-white text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-100 transition flex items-center gap-1.5 shadow-sm" title="Gunakan Lokasi Saat Ini">
                        <i data-lucide="locate" class="w-4 h-4 text-rose-500 animate-pulse"></i> Lokasi Saya
                    </button>
                </div>
                <!-- Search Results Suggestion List -->
                <div id="map-search-results" class="hidden absolute top-full left-4 right-4 bg-white border border-gray-200 rounded-xl shadow-xl z-[1000] max-h-48 overflow-y-auto divide-y divide-gray-100 mt-1">
                </div>
            </div>
            <!-- Info Geolocation Accuracy Box -->
            <div class="px-4 py-2.5 bg-blue-50/80 border-b border-blue-100 flex items-start gap-2 text-[10px] text-blue-700 leading-normal">
                <i data-lucide="info" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                <span><b>Tips Akurasi:</b> Pencarian otomatis di laptop/PC berbasis alamat IP sehingga bisa kurang akurat. Untuk akurasi GPS terbaik, disarankan mengakses via HP/perangkat ber-GPS. Lu juga bisa menggeser pin biru langsung di peta atau mencari alamat manual di kolom pencarian.</span>
            </div>
            <div id="leaflet-map-container" class="flex-1 min-h-[380px] relative">
                <div id="leaflet-map" class="absolute inset-0"></div>
                <!-- Layer Selector Floating Control -->
                <div class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur-sm p-1.5 rounded-xl border border-gray-200 shadow-lg flex gap-1">
                    <button onclick="switchMapLayer('streets')" id="layer-btn-streets" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white">Jalan</button>
                    <button onclick="switchMapLayer('satellite')" id="layer-btn-satellite" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Satelit</button>
                    <button onclick="switchMapLayer('dark')" id="layer-btn-dark" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Gelap</button>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                <p class="text-xs text-gray-500 max-w-[200px] sm:max-w-sm truncate" id="selected-coords-text">Koordinat: -6.2088, 106.8456</p>
                <div class="flex gap-2 shrink-0">
                    <button type="button" onclick="closeMapModal()" class="px-4 py-2.5 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-100 transition">Batal</button>
                    <button onclick="confirmShareLocation()" class="bg-[#1a3a5c] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#122b45] transition shadow-sm">Bagikan Lokasi Ini</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Map Modal -->
    <div id="view-map-modal" class="fixed inset-0 bg-black/60 z-[999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeViewMapModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[85vh] animate-scale-up">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-[#1a3a5c] flex items-center gap-2 truncate max-w-[80%]">
                    <i data-lucide="map-pin" class="w-5 h-5 text-rose-500"></i> <span id="view-map-title">Lokasi Terbagikan</span>
                </h3>
                <button onclick="closeViewMapModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div id="view-leaflet-map-container" class="flex-1 min-h-[400px] relative">
                <div id="view-leaflet-map" class="absolute inset-0"></div>
                <!-- Layer Selector Floating Control -->
                <div class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur-sm p-1.5 rounded-xl border border-gray-200 shadow-lg flex gap-1">
                    <button onclick="switchViewMapLayer('streets')" id="view-layer-btn-streets" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white">Jalan</button>
                    <button onclick="switchViewMapLayer('satellite')" id="view-layer-btn-satellite" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Satelit</button>
                    <button onclick="switchViewMapLayer('dark')" id="view-layer-btn-dark" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Gelap</button>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                <p class="text-xs text-gray-500 max-w-[200px] sm:max-w-sm truncate" id="view-map-coords-text">Koordinat: -6.2088, 106.8456</p>
                <div class="flex gap-2 shrink-0">
                    <button type="button" onclick="closeViewMapModal()" class="px-4 py-2.5 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-100 transition">Tutup</button>
                    <a id="view-map-gmaps-link" href="#" target="_blank" class="bg-[#1a3a5c] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#122b45] transition shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>