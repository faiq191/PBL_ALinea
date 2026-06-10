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
                                    
                                    @if(!str_starts_with($comment->content, '_deleted_'))
                                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->is_admin))
                                                <button @click="editing = !editing; replying = false" class="text-gray-400 hover:text-[#1a3a5c] p-1" title="Sunting Komentar"><i data-lucide="pencil" class="w-3 h-3"></i></button>
                                                <form action="/comments/{{ $comment->id }}" method="POST" onsubmit="return confirm('Hapus komentar?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-1" title="Hapus Komentar"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                                                </form>
                                            @endif
                                            @if(!auth()->check() || auth()->id() !== $comment->user_id)
                                                <button type="button" onclick="openReportModal('comment', {{ $comment->id }}, {{ $comment->user_id }}, '{{ addslashes($comment->user->name) }}')" class="text-gray-400 hover:text-red-600 p-1" title="Laporkan Komentar">
                                                    <i data-lucide="flag" class="w-3 h-3"></i>
                                                </button>
                                            @endif
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
                                    <p x-show="!editing" class="notranslate text-sm text-gray-700 leading-relaxed whitespace-pre-wrap break-all">{!! nl2br(parseGifsInContent(e($comment->content))) !!}</p>
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
                                    <textarea name="content" required maxlength="256" rows="2" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm outline-none focus:border-[#1a3a5c] mb-2 @error('content') ring-2 ring-red-500 @enderror">{{ $comment->content }}</textarea>
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
                                                    @if(!str_starts_with($reply->content, '_deleted_'))
                                                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->is_admin))
                                                                <button @click="editingReply = !editingReply" class="text-gray-400 hover:text-[#1a3a5c] p-1" title="Sunting Balasan"><i data-lucide="pencil" class="w-3 h-3"></i></button>
                                                                <form action="/comments/{{ $reply->id }}" method="POST" onsubmit="return confirm('Hapus balasan?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-1" title="Hapus Balasan"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                                                                </form>
                                                            @endif
                                                            @if(!auth()->check() || auth()->id() !== $reply->user_id)
                                                                <button type="button" onclick="openReportModal('comment', {{ $reply->id }}, {{ $reply->user_id }}, '{{ addslashes($reply->user->name) }}')" class="text-gray-400 hover:text-red-600 p-1" title="Laporkan Balasan">
                                                                    <i data-lucide="flag" class="w-3 h-3"></i>
                                                                </button>
                                                            @endif
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
                                                    <p x-show="!editingReply" class="notranslate text-sm text-gray-700 leading-relaxed whitespace-pre-wrap break-all">{!! nl2br($renderedContent) !!}</p>
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
                                                    <textarea name="content" required maxlength="256" rows="2" class="w-full bg-[#f5f5f5] border-none rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5c] mb-2 @error('content') ring-2 ring-red-500 @enderror">{{ $reply->content }}</textarea>
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
                                                <textarea id="reply-textarea-{{ $comment->id }}" name="content" maxlength="256" rows="2" class="w-full bg-[#f5f5f5] border-none rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5c] resize-none @error('content') ring-2 ring-red-500 @enderror" placeholder="Tulis balasan... (Bisa paste screenshot juga)"></textarea>
                                                @error('content')
                                                    <p class="text-red-600 text-xs mt-1 mb-2 font-semibold">{{ $message }}</p>
                                                @enderror

                                                <!-- Preview Box -->
                                                <div class="attachment-preview-box hidden mt-2 p-2 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-between animate-fade-in"></div>

                                                <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                                                    <div class="flex items-center gap-1">
                                                        <input type="file" name="attachment" class="hidden attachment-file-input" onchange="(function(fi){var f=fi.closest('.attachment-form');var file=fi.files[0];if(!file)return;var t=f.querySelector('.attachment-type-input');var u=f.querySelector('.attachment-url-input');if(t)t.value='';if(u)u.value='';if(file.type.startsWith('image/')){var r=new FileReader();r.onload=function(e){renderPreview(f,'image',e.target.result)};r.readAsDataURL(file)}})(this)">
                                                        <input type="hidden" name="attachment_type" class="attachment-type-input">
                                                        <input type="hidden" name="attachment_url" class="attachment-url-input">
                                                        
                                                        <button type="button" onclick="var fi=this.closest('.attachment-form').querySelector('.attachment-file-input');fi.setAttribute('accept','image/*');fi.click(); event.stopPropagation();" class="btn-attach-image p-1.5 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-lg transition" title="Unggah Gambar">
                                                            <i data-lucide="image" class="w-4 h-4"></i>
                                                        </button>
                                                        <button type="button" onclick="toggleTenorPopover(this.closest('.attachment-form')); event.stopPropagation();" class="btn-attach-tenor p-1.5 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-lg transition" title="Cari GIF Tenor">
                                                            <i data-lucide="film" class="w-4 h-4"></i>
                                                        </button>
                                                        <button type="button" onclick="openMapModal(this.closest('.attachment-form')); event.stopPropagation();" class="btn-attach-gmaps p-1.5 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-lg transition" title="Bagikan Lokasi">
                                                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                                                        </button>
                                                        <button type="button" onclick="toggleEmojiPicker(this,this.closest('.attachment-form')); event.stopPropagation();" class="btn-attach-emoji p-1.5 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-lg transition relative" title="Pilih Emoji">
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
