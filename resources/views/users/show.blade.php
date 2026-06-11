<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for Dropdowns */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #b0c8e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #1a3a5c; }

        .row-scroll::-webkit-scrollbar { height: 8px; }
        .row-scroll::-webkit-scrollbar-track { background: transparent; }
        .row-scroll::-webkit-scrollbar-thumb { background: transparent; border-radius: 10px; transition: all 0.3s; }
        .row-scroll:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }
        .row-scroll::-webkit-scrollbar-thumb:hover { background: #1a3a5c; }
        
        /* Hide scrollbar completely on mobile for swiping */
        @media (max-width: 768px) {
            .row-scroll::-webkit-scrollbar { display: none; }
        }
    </style>
</head>
<body class="bg-[#f5f5f5] min-h-screen pt-20 pb-16 font-sans">
    
    {{-- Header --}}
    <x-header />

    <div class="max-w-6xl mx-auto px-6 mt-8" x-data="{ activeTab: 'books' }">
        <!-- Back Button -->
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#1a3a5c] mb-6 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>

        <!-- Profile Hero Header -->
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100/80 mb-8 relative overflow-hidden">
            <!-- Background subtle gradient glow -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-80 pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-80 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                <!-- Avatar -->
                <div class="relative">
                    @if($user->profile_photo)
                        <img src="{{ \Illuminate\Support\Str::startsWith($user->profile_photo, 'http') ? $user->profile_photo : asset('storage/' . $user->profile_photo) }}"
                             class="w-32 h-32 rounded-full object-cover border-4 border-[#e84b7a]/20 shadow-lg"
                             x-on:error="$el.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ $user->name }}')">
                    @else
                        <div class="w-32 h-32 rounded-full bg-[#1a3a5c] flex items-center justify-center font-bold text-white text-4xl shadow-lg border-4 border-slate-100">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    
                    @if($user->is_admin)
                        <span class="absolute bottom-0 right-2 bg-[#e84b7a] text-white text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full shadow border-2 border-white">
                            Admin
                        </span>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-col md:flex-row md:items-center gap-3 justify-center md:justify-start mb-2">
                        <h1 class="text-3xl font-extrabold text-[#1a3a5c] tracking-tight">
                            {{ $user->name }}
                        </h1>
                    </div>
                    <p class="text-sm text-gray-500 font-medium mb-4 flex items-center justify-center md:justify-start gap-1.5">
                        <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i> {{ $user->email }}
                    </p>
                    <div class="flex items-center justify-center md:justify-start gap-4 text-xs text-gray-400 font-semibold uppercase tracking-wider">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#e84b7a]"></i> Member Sejak {{ $user->created_at->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>

                <!-- Action Button / Stats -->
                <div class="flex flex-col gap-3 min-w-[200px] w-full md:w-auto">
                    @if(auth()->check() && auth()->id() === $user->id)
                        <a href="/profile" class="w-full bg-[#1a3a5c] text-white text-center py-3 px-6 rounded-2xl font-bold text-sm hover:bg-[#122b45] shadow-md hover:shadow-lg transition duration-300 flex items-center justify-center gap-2">
                            <i data-lucide="settings" class="w-4 h-4"></i> Sunting Akun Saya
                        </a>
                    @else
                        @auth
                            <button onclick="window.openChatWithUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->profile_photo ? (str_starts_with($user->profile_photo, 'http') ? $user->profile_photo : asset('storage/' . $user->profile_photo)) : asset('Gambar/default_avatar.png') }}')" class="w-full bg-[#e84b7a] hover:bg-[#d83a69] text-white text-center py-3 px-6 rounded-2xl font-bold text-sm shadow-md hover:shadow-lg transition duration-300 flex items-center justify-center gap-2">
                                <i data-lucide="message-circle" class="w-4 h-4"></i> Kirim Pesan
                            </button>
                        @endauth

                        <button onclick="openReportModal('profile', null, {{ $user->id }}, '{{ addslashes($user->name) }}')" class="w-full bg-red-50 hover:bg-red-100 text-red-600 text-center py-2.5 px-6 rounded-2xl font-bold text-sm border border-red-200 transition duration-300 flex items-center justify-center gap-2" title="Laporkan Pengguna">
                            <i data-lucide="flag" class="w-4 h-4"></i> Laporkan Pengguna
                        </button>
                    @endif

                    <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="text-center">
                            <span class="block text-2xl font-extrabold text-[#1a3a5c]">{{ $user->books->count() }}</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Buku</span>
                        </div>
                        <div class="text-center border-l border-slate-200">
                            <span class="block text-2xl font-extrabold text-[#1a3a5c]">{{ $user->discussions->count() }}</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Diskusi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex border-b border-gray-200 mb-8 gap-6">
            <button @click="activeTab = 'books'" 
                    :class="activeTab === 'books' ? 'border-[#1a3a5c] text-[#1a3a5c] font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-4 border-b-2 text-sm uppercase tracking-wider transition flex items-center gap-2 focus:outline-none">
                <i data-lucide="book" class="w-4 h-4"></i> Koleksi Buku ({{ $user->books->count() }})
            </button>
            <button @click="activeTab = 'discussions'" 
                    :class="activeTab === 'discussions' ? 'border-[#1a3a5c] text-[#1a3a5c] font-extrabold' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-4 border-b-2 text-sm uppercase tracking-wider transition flex items-center gap-2 focus:outline-none">
                <i data-lucide="message-square" class="w-4 h-4"></i> Diskusi Dibuat ({{ $user->discussions->count() }})
            </button>
        </div>

        <!-- Tab Content: Books -->
        <div x-show="activeTab === 'books'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            @if($user->books->count() === 0)
                <div class="bg-white rounded-3xl p-12 text-center shadow-lg border border-gray-100">
                    <i data-lucide="book-x" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                    <p class="text-gray-500 font-medium text-lg mb-2">Belum ada buku dalam koleksi.</p>
                    <p class="text-gray-400 text-sm max-w-md mx-auto">Pengguna ini belum membagikan buku miliknya ke dalam koleksi perpustakaan Alinea.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($user->books as $book)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group flex flex-col">
                            <!-- Image Cover -->
                            <div class="relative aspect-[2/3] overflow-hidden bg-gray-100">
                                <img src="{{ \Illuminate\Support\Str::startsWith($book->image, 'http') ? $book->image : asset('storage/' . $book->image) }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                     alt="Sampul {{ $book->title }}">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <span class="text-[9px] font-extrabold text-[#e84b7a] uppercase tracking-wider block mb-1">
                                        {{ $book->genres->first()->name ?? 'Umum' }}
                                    </span>
                                    <h4 class="font-bold text-[#1a3a5c] text-sm leading-snug line-clamp-2 mb-1 group-hover:underline">
                                        {{ $book->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 font-medium line-clamp-1 mb-3">
                                        oleh {{ $book->author }}
                                    </p>
                                </div>
                                
                                <div class="flex items-center justify-between border-t border-gray-50 pt-3 mt-auto">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $book->isAvailable() ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                                        {{ $book->isAvailable() ? 'Tersedia' : 'Dipinjam' }}
                                    </span>
                                    
                                    <a href="/books/{{ $book->id }}" class="text-xs font-bold text-[#1a3a5c] hover:underline flex items-center gap-0.5">
                                        Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Tab Content: Discussions -->
        <div x-show="activeTab === 'discussions'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            @if($user->discussions->count() === 0)
                <div class="bg-white rounded-3xl p-12 text-center shadow-lg border border-gray-100">
                    <i data-lucide="message-square-off" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                    <p class="text-gray-500 font-medium text-lg mb-2">Belum ada diskusi yang dibuat.</p>
                    <p class="text-gray-400 text-sm max-w-md mx-auto">Pengguna ini belum pernah memulai diskusi baru di halaman forum komunitas Alinea.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($user->discussions as $discussion)
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                            <div class="flex gap-5">
                                <img src="{{ \Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : asset('storage/' . $discussion->image) }}" class="w-16 h-24 object-cover rounded-xl shadow-sm shrink-0">
                                <div class="flex-1 flex flex-col justify-center">
                                    <span class="text-[9px] font-bold text-[#e84b7a] uppercase tracking-wider mb-0.5">{{ $discussion->genre ?? 'Umum' }}</span>
                                    <h4 class="text-lg font-bold text-[#1a3a5c] leading-snug mb-1 hover:underline">
                                        <a href="/diskusi/{{ $discussion->id }}">{{ $discussion->title }}</a>
                                    </h4>
                                    <p class="text-xs text-gray-400 font-semibold mb-3 flex items-center gap-1.5">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> {{ $discussion->created_at->diffForHumans() }}
                                    </p>
                                    <a href="/diskusi/{{ $discussion->id }}" class="inline-flex items-center gap-1 text-[#1a3a5c] text-xs font-bold hover:underline w-max">
                                        Lihat Diskusi <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
    <x-report-modal />
</body>
</html>
