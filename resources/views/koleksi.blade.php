<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Buku Saya | Alinea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        [x-cloak] { display: none !important; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        /* Custom Premium Scrollbar */
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 99px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
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

<body class="bg-[#f5f5f5] min-h-screen text-[#1a3a5c] antialiased">

    <x-header />

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="fixed top-24 right-6 lg:right-12 z-[60] bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-semibold flex items-center gap-3 shadow-lg max-w-sm">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <p>{{ session('success') }}</p>
            <button @click="show = false" class="ml-2 text-emerald-400 hover:text-emerald-600 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="fixed top-24 right-6 lg:right-12 z-[60] bg-rose-50 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl text-sm font-semibold flex items-center gap-3 shadow-lg max-w-sm">
            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
            </div>
            <p>{{ session('error') }}</p>
            <button @click="show = false" class="ml-2 text-rose-400 hover:text-rose-600 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    {{-- Fluid full-width container to fill all screen margins --}}
    <div class="w-full pt-28 px-6 lg:px-12 pb-16">
        
        {{-- Header Section requested by user --}}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-[#1a3a5c]">Koleksi Buku Saya</h1>
            <p class="text-gray-400 mt-1 text-sm">Kelola katalog buku pribadi Anda serta transaksi peminjaman dengan rekan komunitas.</p>
        </div>

        {{-- MAIN LAYOUT: Left (2/3 width) and Right (1/3 width) to avoid stretched panels --}}
        <div id="koleksi-main-container" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- COLUMN LEFT: KATALOG BUKU (8 COLS) --}}
            <div class="lg:col-span-8 bg-white rounded-3xl p-6 shadow-xl shadow-slate-100/50 border border-slate-100/80 flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h1 class="text-lg font-bold text-[#1a3a5c]">Katalog Buku</h1>
                        <p class="text-xs text-gray-400">Koleksi pribadi</p>
                    </div>
                    <a href="/books/create"
                        class="bg-[#1a3a5c] hover:bg-[#122b45] text-white px-4 py-2 rounded-2xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
                    </a>
                </div>

                {{-- Search & Filter Form (Cleaner design) --}}
                <form method="GET" action="/koleksi" class="mt-2 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari judul atau penulis..."
                                class="w-full pl-11 pr-4 py-2.5 rounded-2xl bg-[#f1f5f9] border border-transparent focus:bg-white focus:border-[#1a3a5c]/30 outline-none text-xs transition placeholder-gray-400">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2"></i>
                        </div>
                        <button type="submit" class="bg-[#1a3a5c] text-white px-5 py-2.5 rounded-2xl text-xs font-bold hover:bg-[#122b45] transition shadow-sm">
                            Cari
                        </button>
                        
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true"
                                class="bg-slate-100 hover:bg-slate-200/80 text-[#1a3a5c] px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-1.5">
                                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i> Filter
                            </button>
                            
                            {{-- Filter Modal --}}
                            <div x-show="open" x-transition x-cloak
                                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50">
                                <div class="bg-white p-8 rounded-3xl w-96 shadow-2xl border border-slate-100/50">
                                    <div class="flex items-center justify-between mb-6">
                                        <h3 class="font-extrabold text-lg text-[#1a3a5c] flex items-center gap-2">
                                            <i data-lucide="sliders-horizontal" class="w-5 h-5 text-[#1a3a5c]"></i> Filter Buku
                                        </h3>
                                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 transition">
                                            <i data-lucide="x" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">Genre</label>
                                            <select name="genre_id" class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:border-[#1a3a5c]/30">
                                                <option value="">Semua Genre</option>
                                                @foreach ($genres as $genre)
                                                    <option value="{{ $genre->id }}" {{ request('genre_id') == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">Tipe</label>
                                            <select name="type_id" class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:border-[#1a3a5c]/30">
                                                <option value="">Semua Tipe</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">Tahun</label>
                                            <select name="year_id" class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:border-[#1a3a5c]/30">
                                                <option value="">Semua Tahun</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex justify-between mt-8 pt-4 border-t border-slate-100 gap-3">
                                        <button type="button" @click="open = false" class="text-sm font-bold text-gray-400 hover:text-gray-600 px-4 py-2 transition">Batal</button>
                                        <button type="submit" @click="open = false" class="bg-[#1a3a5c] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-[#122b45] transition shadow-md">Terapkan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Catalog Grid (Cleaner cards, perfectly proportioned aspect ratio) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 max-h-[700px] overflow-y-auto pr-1 custom-scroll">
                    @isset($books)
                        @forelse ($books as $book)
                            <x-book-card
                                :id="$book->id"
                                :image="$book->image"
                                :title="$book->title"
                                :author="$book->author"
                                :genre="$book->genres->map(fn($g) => '<span class=\'bg-[#e8edf2] text-[#1a3a5c] text-[9px] px-2.5 py-1 rounded-full font-bold mr-1\'>' . $g->name . '</span>')->join('')"
                                :show-atur="auth()->check() && $book->user_id === auth()->id()"
                                :owner-id="$book->user_id"
                                :is-available="$book->isAvailable()"
                            />
                        @empty
                            <div class="col-span-3 flex flex-col items-center justify-center py-20 text-center">
                                <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 mb-4">
                                    <i data-lucide="book-open" class="w-8 h-8"></i>
                                </div>
                                <p class="text-gray-400 text-xs mt-1">Belum ada buku.</p>
                                <a href="/books/create" class="mt-4 inline-flex bg-[#1a3a5c] text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-[#122b45] transition shadow-sm">+ Tambah Buku</a>
                            </div>
                        @endforelse
                    @endisset
                </div>
            </div>

            {{-- COLUMN RIGHT: STACKED LOANS & REQUESTS (4 COLS - Clean theme) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- PERMINTAAN PINJAMAN MASUK --}}
                <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-100/50 border border-slate-100/80 flex flex-col">
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shadow-sm">
                            <i data-lucide="inbox" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h1 class="text-base font-extrabold text-[#1a3a5c]">Permintaan Masuk</h1>
                            <p class="text-[10px] text-gray-400 mt-0.5">Ingin meminjam bukumu</p>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-[240px] overflow-y-auto pr-1 custom-scroll">
                        @forelse($incomingRequests ?? [] as $request)
                            <div onclick="window.openChatWithUser({{ $request->borrower->id }}, '{{ addslashes($request->borrower->name) }}', '{{ $request->borrower->profile_photo ? (str_starts_with($request->borrower->profile_photo, 'http') ? $request->borrower->profile_photo : asset('storage/' . $request->borrower->profile_photo)) : asset('Gambar/default_avatar.png') }}')" class="cursor-pointer bg-slate-50/80 border border-slate-100/80 p-4 rounded-2xl flex flex-col shadow-sm hover:shadow-md transition duration-300 gap-3 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1 bg-amber-400 h-full"></div>
                                <div class="flex items-center gap-3 pl-1">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($request->book->image, 'http') ? $request->book->image : asset('storage/' . $request->book->image) }}" class="w-10 h-14 object-cover rounded-xl shadow-sm border border-slate-200/50 flex-shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="font-extrabold text-[#1a3a5c] text-xs truncate leading-snug">{{ $request->book->title }}</p>
                                            <button type="button" onclick="event.stopPropagation(); window.openChatWithUser({{ $request->borrower->id }}, '{{ addslashes($request->borrower->name) }}', '{{ $request->borrower->profile_photo ? (str_starts_with($request->borrower->profile_photo, 'http') ? $request->borrower->profile_photo : asset('storage/' . $request->borrower->profile_photo)) : asset('Gambar/default_avatar.png') }}')" class="bg-rose-50 hover:bg-rose-100 text-[#e84b7a] border border-rose-100/80 text-[9px] px-2 py-0.5 rounded-full font-bold transition flex items-center gap-0.5 shrink-0 shadow-sm">
                                                <i data-lucide="message-circle" class="w-3 h-3"></i> Buka Obrolan
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-gray-500 mt-1">
                                            Peminjam: <a href="/users/{{ $request->borrower->id }}" onclick="event.stopPropagation()" class="font-extrabold hover:underline text-[#1a3a5c]">{{ $request->borrower->name }}</a>
                                        </p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-1" onclick="event.stopPropagation()">
                                    <form action="/loans/{{ $request->id }}/status" method="POST" class="m-0">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="dipinjam">
                                        <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] py-2 rounded-xl font-bold transition shadow-sm hover:shadow flex items-center justify-center gap-1">
                                            <i data-lucide="check" class="w-3 h-3"></i> Setujui
                                        </button>
                                    </form>
                                    <form action="/loans/{{ $request->id }}/status" method="POST" class="m-0">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="ditolak">
                                        <button class="w-full bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100/80 text-[10px] py-2 rounded-xl font-bold transition flex items-center justify-center gap-1">
                                            <i data-lucide="x" class="w-3 h-3"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 mb-2.5 mx-auto">
                                    <i data-lucide="inbox" class="w-5 h-5"></i>
                                </div>
                                <p class="text-gray-400 text-xs">Belum ada permintaan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- BUKU KOLEKSI YANG DIPINJAM --}}
                <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-100/50 border border-slate-100/80 flex flex-col">
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                            <i data-lucide="book-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h1 class="text-base font-extrabold text-[#1a3a5c]">Buku Dipinjam</h1>
                            <p class="text-[10px] text-gray-400 mt-0.5">Sedang dibawa orang lain</p>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1 custom-scroll">
                        @forelse($lentBooks ?? [] as $loan)
                            <div onclick="window.openChatWithUser({{ $loan->borrower->id }}, '{{ addslashes($loan->borrower->name) }}', '{{ $loan->borrower->profile_photo ? (str_starts_with($loan->borrower->profile_photo, 'http') ? $loan->borrower->profile_photo : asset('storage/' . $loan->borrower->profile_photo)) : asset('Gambar/default_avatar.png') }}')" class="cursor-pointer bg-slate-50/80 border border-slate-100/80 p-3.5 rounded-2xl flex items-center justify-between shadow-sm hover:shadow-md transition duration-300 gap-3 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1 bg-indigo-500 h-full"></div>
                                <div class="flex items-center gap-3 min-w-0 flex-1 pl-1">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($loan->book->image, 'http') ? $loan->book->image : asset('storage/' . $loan->book->image) }}" class="w-10 h-14 object-cover rounded-xl shadow-sm border border-slate-200/50 flex-shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-extrabold text-[#1a3a5c] text-xs truncate leading-snug">{{ $loan->book->title }}</p>
                                        <p class="text-[10px] text-gray-500 mt-1">
                                            Dipinjam: <a href="/users/{{ $loan->borrower->id }}" onclick="event.stopPropagation()" class="font-bold hover:underline text-[#1a3a5c]">{{ $loan->borrower->name }}</a>
                                        </p>
                                        <p class="text-[9px] text-gray-400 italic mt-0.5 flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-2.5 h-2.5"></i>
                                            {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-3" onclick="event.stopPropagation()">
                                    <button type="button" onclick="event.stopPropagation(); window.openChatWithUser({{ $loan->borrower->id }}, '{{ addslashes($loan->borrower->name) }}', '{{ $loan->borrower->profile_photo ? (str_starts_with($loan->borrower->profile_photo, 'http') ? $loan->borrower->profile_photo : asset('storage/' . $loan->borrower->profile_photo)) : asset('Gambar/default_avatar.png') }}')" class="bg-rose-50 hover:bg-rose-100 text-[#e84b7a] border border-rose-100/80 text-[10px] px-3 py-2 rounded-xl font-bold transition shadow-sm flex items-center gap-1">
                                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i> Buka Obrolan
                                    </button>
                                    <form action="/loans/{{ $loan->id }}/remind" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="bg-[#1a3a5c] hover:bg-[#122b45] text-white text-[10px] px-3.5 py-2 rounded-xl font-bold transition shadow-sm whitespace-nowrap">
                                            Tagih
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 mb-2.5 mx-auto">
                                    <i data-lucide="book-check" class="w-5 h-5"></i>
                                </div>
                                <p class="text-gray-400 text-xs">Belum ada.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- BUKU YANG SEDANG ANDA PINJAM --}}
                <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-100/50 border border-slate-100/80 flex flex-col">
                    <div class="flex items-center gap-3 mb-6 pb-3 border-b border-slate-50">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm">
                            <i data-lucide="book-marked" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h1 class="text-base font-extrabold text-[#1a3a5c]">Sedang Dipinjam</h1>
                            <p class="text-[10px] text-gray-400 mt-0.5">Dari koleksi teman</p>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1 custom-scroll">
                        @forelse($myLoans ?? [] as $loan)
                            <div onclick="window.openChatWithUser({{ $loan->book->user->id }}, '{{ addslashes($loan->book->user->name) }}', '{{ $loan->book->user->profile_photo ? (str_starts_with($loan->book->user->profile_photo, 'http') ? $loan->book->user->profile_photo : asset('storage/' . $loan->book->user->profile_photo)) : asset('Gambar/default_avatar.png') }}')" class="cursor-pointer bg-slate-50/80 border border-slate-100/80 p-3.5 rounded-2xl flex items-center justify-between shadow-sm hover:shadow-md transition duration-300 gap-3 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1 bg-amber-400 h-full"></div>
                                <div class="flex items-center gap-3 min-w-0 flex-1 pl-1">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($loan->book->image, 'http') ? $loan->book->image : asset('storage/' . $loan->book->image) }}" class="w-10 h-14 object-cover rounded-xl shadow-sm border border-slate-200/50 flex-shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="notranslate font-extrabold text-[#1a3a5c] text-xs truncate leading-snug">{{ $loan->book->title }}</p>
                                        <p class="notranslate text-[10px] text-gray-500 mt-1 truncate">{{ $loan->book->author }}</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">
                                            Pemilik: <a href="/users/{{ $loan->book->user->id }}" onclick="event.stopPropagation()" class="font-bold hover:underline text-[#1a3a5c]">{{ $loan->book->user->name }}</a>
                                        </p>
                                        <span class="inline-block mt-1.5 text-[8px] px-2 py-0.5 rounded-full font-bold
                                            {{ $loan->status === 'pending' ? 'bg-amber-50 text-amber-600 border border-amber-100/50' : 'bg-emerald-50 text-emerald-600 border border-emerald-100/50' }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-3" onclick="event.stopPropagation()">
                                    <button type="button" onclick="event.stopPropagation(); window.openChatWithUser({{ $loan->book->user->id }}, '{{ addslashes($loan->book->user->name) }}', '{{ $loan->book->user->profile_photo ? (str_starts_with($loan->book->user->profile_photo, 'http') ? $loan->book->user->profile_photo : asset('storage/' . $loan->book->user->profile_photo)) : asset('Gambar/default_avatar.png') }}')" class="bg-rose-50 hover:bg-rose-100 text-[#e84b7a] border border-rose-100/80 text-[10px] px-3 py-2 rounded-xl font-bold transition shadow-sm flex items-center gap-1">
                                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i> Buka Obrolan
                                    </button>
                                    @if($loan->status === 'dipinjam')
                                        <form action="/loans/{{ $loan->id }}/return" method="POST" class="m-0">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="bg-[#1a3a5c] hover:bg-slate-800 text-white text-[10px] px-3.5 py-2 rounded-xl font-bold transition shadow-sm whitespace-nowrap">
                                                Kembalikan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 mb-2.5 mx-auto">
                                    <i data-lucide="book-marked" class="w-5 h-5"></i>
                                </div>
                                <p class="text-gray-400 text-xs">Belum ada.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>

    <x-footer />

    <script>
        async function refreshKoleksiData() {
            try {
                const res = await fetch(window.location.href);
                if (!res.ok) return;
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const oldContainer = document.getElementById('koleksi-main-container');
                const newContainer = doc.getElementById('koleksi-main-container');
                
                if (oldContainer && newContainer) {
                    oldContainer.innerHTML = newContainer.innerHTML;
                    
                    // Re-initialize lucide icons since new HTML has been inserted
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                }
            } catch (err) {
                console.error("Failed to auto-refresh Koleksi data:", err);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Listen to real-time notification events to update loan statuses
            if (window.Echo) {
                const userId = '{{ auth()->id() }}';
                window.Echo.channel('user-notifications.' + userId)
                    .listen('.NotificationSent', (e) => {
                        console.log("Koleksi page received real-time notification:", e.notification);
                        const title = e.notification.title;
                        if (title.includes('Peminjaman') || title.includes('Pengembalian') || title.includes('Status') || title.includes('Tagihan')) {
                            // Delay slightly to ensure database transaction is fully committed on the server
                            setTimeout(refreshKoleksiData, 300);
                        }
                    });
            }
        });
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>

</body>
</html>
