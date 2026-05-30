<!DOCTYPE html>
<html>
<head>
    <title>Komunitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-[#f5f5f5]">
    <x-header />

    <div class="max-w-7xl mx-auto pt-24 px-6 mb-6">
        <form method="GET" action="/komunitas">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full bg-[#e8edf2] text-[#1a3a5c] rounded-full py-4 px-14 focus:outline-none focus:ring-2 focus:ring-[#1a3a5c] placeholder-[#5a7a9c] text-lg"
                    placeholder="Cari diskusi atau topik...">
                <div class="absolute left-5 top-4">
                    <i data-lucide="search" class="w-6 h-6 text-[#5a7a9c]"></i>
                </div>
            </div>
        </form>
    </div>

    <div class="max-w-7xl mx-auto px-6 flex gap-6">
        <div class="flex-[3] bg-[#ffffff] p-8 rounded-3xl shadow-xl border border-gray-100">
            
            <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-100">
                <div>
                    <h2 class="text-3xl font-bold text-[#1a3a5c]">Komunitas</h2>
                    <p class="text-gray-400 mt-1">Tempat berbagi pikiran dan inspirasi</p>
                </div>
                
                @auth
                    <a href="/diskusi/create" class="bg-[#1a3a5c] text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold hover:bg-[#122b45] transition shadow-md">
                        <i data-lucide="message-square-plus" class="w-5 h-5"></i>
                        Buat Diskusi Baru
                    </a>
                @else
                    <a href="/login" class="bg-gray-200 text-gray-600 px-6 py-3 rounded-xl font-bold hover:bg-gray-300 transition">
                        Login untuk diskusi
                    </a>
                @endauth
            </div>

            <div class="space-y-4">
                @forelse ($discussions ?? [] as $discussion)
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                        <div class="flex gap-5">
                            <img src="{{ \Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : asset('storage/' . $discussion->image) }}" class="w-20 h-28 object-cover rounded-xl shadow-sm">
                            <div class="flex-1 flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-[#1a3a5c] uppercase tracking-wider mb-1">{{ $discussion->genre ?? 'Umum' }}</span>
                                <h4 class="text-xl font-bold text-[#1a3a5c] leading-tight mb-2">
                                    {{ $discussion->title }}
                                </h4>
                                <a href="/users/{{ $discussion->user->id ?? '#' }}" class="flex items-center gap-2 text-xs text-gray-500 mb-3 hover:opacity-85 transition">
                                    @if($discussion->user->profile_photo)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($discussion->user->profile_photo, 'http') ? $discussion->user->profile_photo : asset('storage/' . $discussion->user->profile_photo) }}" class="w-5 h-5 rounded-full object-cover shadow-sm">
                                    @else
                                        <div class="w-5 h-5 rounded-full bg-[#e8edf2] flex items-center justify-center font-bold text-[#1a3a5c] text-[8px]">
                                            {{ substr($discussion->user->name ?? 'U', 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="font-bold text-[#1a3a5c] hover:underline">{{ $discussion->user->name ?? 'Unknown' }}</span>
                                    <span>•</span>
                                    <span>{{ $discussion->created_at->diffForHumans() }}</span>
                                </a>
                                <a href="/diskusi/{{ $discussion->id }}" class="inline-flex items-center gap-1 text-[#1a3a5c] text-sm font-bold hover:underline w-max">
                                    Lihat Diskusi <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i data-lucide="message-circle" class="w-12 h-12 text-[#d0e4f5] mx-auto mb-3"></i>
                        <p class="text-gray-400 font-medium">Belum ada diskusi.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="flex-1">
            <div class="bg-[#ffffff] p-6 rounded-3xl shadow-xl border border-gray-100 sticky top-6">
                <h3 class="text-lg font-bold mb-4 text-[#1a3a5c]">Filter Genre</h3>
                <form method="GET" action="/komunitas">
                    
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    
                    <div class="space-y-2 max-h-[60vh] overflow-y-auto pr-2 mb-6">
                        @foreach($genres as $g)
                            <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 cursor-pointer transition">
                                <input type="checkbox" name="genres[]" value="{{ $g }}" 
                                    {{ is_array(request('genres')) && in_array($g, request('genres')) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded text-[#1a3a5c] focus:ring-[#1a3a5c] accent-[#1a3a5c]">
                                <span class="text-sm font-medium text-gray-700">{{ $g }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="submit" class="w-full bg-[#1a3a5c] text-white py-2.5 rounded-xl font-bold text-sm hover:bg-[#122b45] shadow-md transition">
                            Terapkan Filter
                        </button>
                        
                        @if(request()->hasAny(['genres', 'search']))
                            <a href="/komunitas" class="w-full bg-gray-100 text-gray-600 text-center py-2.5 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                                Reset Pencarian
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="pb-16"></div>
    <x-footer />
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>lucide.createIcons();</script>
</body>
</html>