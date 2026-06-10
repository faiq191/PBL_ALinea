        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 mb-8">
            <div class="flex gap-8">
                <img src="{{ \Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : asset('storage/' . $discussion->image) }}" class="w-32 h-48 object-cover rounded-2xl shadow-md">
                
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold text-white bg-[#1a3a5c] px-3 py-1 rounded-full uppercase tracking-wider">{{ $discussion->genre ?? 'Umum' }}</span>
                        
                        <div class="flex gap-2">
                            @if(auth()->check() && (auth()->id() === $discussion->user_id || auth()->user()->is_admin))
                                <a href="/diskusi/{{ $discussion->id }}/edit" class="p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-50 rounded-xl transition" title="Sunting Diskusi">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <form action="/diskusi/{{ $discussion->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus diskusi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Hapus Diskusi">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                            @if(!auth()->check() || auth()->id() !== $discussion->user_id)
                                <button onclick="openReportModal('discussion', {{ $discussion->id }}, {{ $discussion->user_id }}, '{{ addslashes($discussion->user->name) }}')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Laporkan Diskusi">
                                    <i data-lucide="flag" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </div>
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
