<!DOCTYPE html>
<html>
<head>
    <title>Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for better UX inside dropdowns */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #b0c8e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #1a3a5c; }
    </style>
</head>
<body class="bg-[#f5f5f5] text-[#1a3a5c] font-sans antialiased">

    <x-header />

    <div class="pt-28 px-6 pb-12 max-w-7xl mx-auto">
        <!-- Main Card Container with subtle shadow -->
        <div class="bg-[#ffffff] rounded-3xl p-8 md:p-10 shadow-sm border border-gray-100">

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold text-[#1a3a5c] tracking-tight mb-2 flex items-center gap-3">
                        <i data-lucide="library" class="w-10 h-10 text-[#1a3a5c]"></i>
                        Eksplorasi Buku
                    </h1>
                    <p class="text-gray-500 text-lg">Temukan bacaan favorit Anda di perpustakaan kami...</p>
                </div>
                
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="/books/create"
                            class="bg-[#1a3a5c] text-white px-6 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-[#122b45] hover:shadow-lg hover:-translate-y-0.5 transition-all active:scale-95">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Tambah Buku
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Search & Filter Form -->
            <form method="GET" action="/perpustakaan" class="mb-12" x-data="{
                filterOpen: false,
                menuOpen: null,
                toggle(menu) { this.menuOpen = this.menuOpen === menu ? null : menu }
            }">
                
                @if(request('local_only'))
                    <input type="hidden" name="local_only" value="true">
                @endif

                <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
                    <!-- Enhanced Search Bar -->
                    <div class="relative flex-1 w-full group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400 group-focus-within:text-[#1a3a5c] transition-colors"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari judul buku, penulis, atau kata kunci..."
                            class="w-full bg-[#e8edf2] rounded-full py-4 pl-14 pr-6 outline-none text-lg text-[#1a3a5c] placeholder:text-gray-400 focus:ring-2 focus:ring-[#1a3a5c]/20 transition-all shadow-inner">
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <button type="button" @click="filterOpen = !filterOpen"
                            class="flex-1 md:flex-none bg-[#1a3a5c] text-white px-8 py-4 rounded-full flex justify-center items-center gap-2 font-bold hover:bg-[#122b45] hover:shadow-md transition-all active:scale-95"
                            :class="filterOpen ? 'ring-4 ring-[#1a3a5c]/20' : ''">
                            <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                            Filter
                        </button>

                        @if(request()->hasAny(['search', 'genre_ids', 'type_ids', 'demo_ids', 'year_ids', 'author']))
                            <a href="/perpustakaan" title="Reset Pencarian"
                                class="bg-red-50 text-red-500 p-4 rounded-full font-bold hover:bg-red-500 hover:text-white hover:shadow-md transition-all active:scale-95 flex items-center justify-center">
                                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Filter Dropdowns Panel (Sleeker & More Organized) -->
                <div x-show="filterOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-4"
                     x-cloak
                     class="bg-[#1a3a5c] p-6 md:p-8 rounded-[2rem] shadow-xl mb-6 relative z-20">
                     
                    <div class="flex flex-wrap gap-4 items-start">

                        <!-- Genre Filter -->
                        <div class="relative">
                            <button type="button" @click="toggle('genres')"
                                class="bg-white/10 border border-white/20 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/20 transition-all">
                                Genres
                                <i data-lucide="chevron-down" :class="menuOpen === 'genres' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                            </button>
                            <div x-show="menuOpen === 'genres'" @click.away="menuOpen = null" x-transition x-cloak
                                class="absolute left-0 mt-3 bg-white p-6 rounded-2xl shadow-2xl w-screen max-w-[400px] border border-gray-100 max-h-80 overflow-y-auto">
                                <h4 class="text-[#1a3a5c] font-bold mb-4 border-b pb-2">Pilih Genre</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach($genres as $g)
                                    <label class="flex items-start gap-3 text-[#1a3a5c] text-sm cursor-pointer group p-2 rounded-lg hover:bg-[#e8edf2] transition">
                                        <input type="checkbox" name="genre_ids[]" value="{{ $g->id }}"
                                            {{ is_array(request('genre_ids')) && in_array($g->id, request('genre_ids')) ? 'checked' : '' }}
                                            class="mt-0.5 w-4 h-4 rounded text-[#1a3a5c] focus:ring-[#1a3a5c]">
                                        <span class="group-hover:font-medium">{{ $g->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Tipe Filter -->
                        <div class="relative">
                            <button type="button" @click="toggle('types')"
                                class="bg-white/10 border border-white/20 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/20 transition-all">
                                Tipe Buku
                                <i data-lucide="chevron-down" :class="menuOpen === 'types' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                            </button>
                            <div x-show="menuOpen === 'types'" @click.away="menuOpen = null" x-transition x-cloak
                                class="absolute left-0 mt-3 bg-white p-5 rounded-2xl shadow-2xl w-64 border border-gray-100">
                                <h4 class="text-[#1a3a5c] font-bold mb-3 border-b pb-2">Tipe Buku</h4>
                                <div class="flex flex-col gap-2">
                                    @foreach($types as $t)
                                    <label class="flex items-center gap-3 text-[#1a3a5c] text-sm cursor-pointer p-2 rounded-lg hover:bg-[#e8edf2] transition">
                                        <input type="checkbox" name="type_ids[]" value="{{ $t->id }}"
                                            {{ is_array(request('type_ids')) && in_array($t->id, request('type_ids')) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded text-[#1a3a5c] focus:ring-[#1a3a5c]">
                                        {{ $t->name }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Demografis Filter -->
                        <div class="relative">
                            <button type="button" @click="toggle('demo')"
                                class="bg-white/10 border border-white/20 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/20 transition-all">
                                Target Pembaca
                                <i data-lucide="chevron-down" :class="menuOpen === 'demo' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                            </button>
                            <div x-show="menuOpen === 'demo'" @click.away="menuOpen = null" x-transition x-cloak
                                class="absolute left-0 mt-3 bg-white p-5 rounded-2xl shadow-2xl w-64 border border-gray-100">
                                <h4 class="text-[#1a3a5c] font-bold mb-3 border-b pb-2">Demografis</h4>
                                <div class="flex flex-col gap-2">
                                    @foreach($demographics as $d)
                                    <label class="flex items-center gap-3 text-[#1a3a5c] text-sm cursor-pointer p-2 rounded-lg hover:bg-[#e8edf2] transition">
                                        <input type="checkbox" name="demo_ids[]" value="{{ $d->id }}"
                                            {{ is_array(request('demo_ids')) && in_array($d->id, request('demo_ids')) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded text-[#1a3a5c] focus:ring-[#1a3a5c]">
                                        {{ $d->name }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Pengarang Filter -->
                        <div class="relative">
                            <button type="button" @click="toggle('author')"
                                class="bg-white/10 border border-white/20 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/20 transition-all">
                                Pengarang
                                <i data-lucide="chevron-down" :class="menuOpen === 'author' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                            </button>
                            <div x-show="menuOpen === 'author'" @click.away="menuOpen = null" x-transition x-cloak
                                class="absolute left-0 mt-3 bg-white p-5 rounded-2xl shadow-2xl w-72 border border-gray-100">
                                <h4 class="text-[#1a3a5c] font-bold mb-3">Nama Pengarang</h4>
                                <div class="relative">
                                    <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input type="text" name="author" value="{{ request('author') }}"
                                        placeholder="Ketik nama..."
                                        class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-[#e8edf2] text-[#1a3a5c] outline-none text-sm focus:ring-2 focus:ring-[#1a3a5c]/30">
                                </div>
                            </div>
                        </div>

                        <!-- Tahun Rilis Filter -->
                        <div class="relative">
                            <button type="button" @click="toggle('year')"
                                class="bg-white/10 border border-white/20 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-white/20 transition-all">
                                Tahun Rilis
                                <i data-lucide="chevron-down" :class="menuOpen === 'year' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                            </button>
                            <div x-show="menuOpen === 'year'" @click.away="menuOpen = null" x-transition x-cloak
                                class="absolute left-0 mt-3 bg-white p-5 rounded-2xl shadow-2xl w-80 border border-gray-100">
                                <h4 class="text-[#1a3a5c] font-bold mb-3 border-b pb-2">Tahun Terbit</h4>
                                <div class="flex items-center gap-3">
                                    <select name="year_from" class="flex-1 p-2.5 rounded-xl bg-[#e8edf2] text-[#1a3a5c] text-sm outline-none cursor-pointer">
                                        <option value="">Dari Tahun</option>
                                        @foreach($years as $y)
                                            <option value="{{ $y->year }}" {{ request('year_from') == $y->year ? 'selected' : '' }}>{{ $y->year }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-gray-400">-</span>
                                    <select name="year_to" class="flex-1 p-2.5 rounded-xl bg-[#e8edf2] text-[#1a3a5c] text-sm outline-none cursor-pointer">
                                        <option value="">Ke Tahun</option>
                                        @foreach($years as $y)
                                            <option value="{{ $y->year }}" {{ request('year_to') == $y->year ? 'selected' : '' }}>{{ $y->year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Terapkan Button -->
                        <div class="ml-auto w-full md:w-auto mt-4 md:mt-0">
                            <button type="submit"
                                class="w-full md:w-auto bg-white text-[#1a3a5c] px-8 py-2.5 rounded-xl font-bold flex justify-center items-center gap-2 hover:bg-[#d0e4f5] hover:shadow-md transition-all active:scale-95">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Terapkan Filter
                            </button>
                        </div>

                    </div>
                </div>
            </form>

            <!-- Results Section -->
            @if($hasFilters)
                <div class="flex items-center gap-3 mb-6">
                    <h3 class="text-2xl font-bold text-[#1a3a5c]">Hasil Pencarian</h3>
                    <span class="bg-[#e8edf2] text-[#1a3a5c] py-1 px-4 rounded-full text-sm font-semibold">{{ $books->count() }} Buku Ditemukan</span>
                </div>

                @if($books->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                        <i data-lucide="book-x" class="w-16 h-16 text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-medium text-lg">Waduh, buku yang Anda cari tidak ditemukan.</p>
                        <a href="/perpustakaan" class="mt-4 px-6 py-2 bg-white border border-gray-200 rounded-lg text-sm text-[#1a3a5c] font-semibold hover:bg-gray-50 transition-colors shadow-sm">Reset Pencarian</a>
                    </div>
                @else
                    <!-- Grid Responsive untuk Buku -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-x-6 gap-y-10">
                        @foreach($books as $index => $book)
                            <div class="book-animate opacity-0 translate-y-8 transition-all duration-700 ease-out hover:-translate-y-2"
                                style="transition-delay: {{ $index * 50 }}ms">
                                <x-book-card-magazine
                                    :id="$book->id"
                                    :image="$book->image"
                                    :title="$book->title"
                                    :author="$book->author"
                                    :first-genre="$book->genres->first()?->name ?? null"
                                    :owner-id="$book->user_id"
                                    :is-available="!$book->is_google_api && method_exists($book, 'isAvailable') ? $book->isAvailable() : true"
                                    :is-google-api="$book->is_google_api ?? false"
                                />
                            </div>
                        @endforeach
                    </div>
                @endif

            @else
                <!-- Kategori/Genre Section -->
                @forelse($booksByGenre as $genre => $genreBooks)
                    <div class="mb-14">
                        
                        <div class="flex justify-between items-end mb-4 group">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-8 bg-[#e84b7a] rounded-full"></div>
                                <h3 class="text-2xl font-bold text-[#1a3a5c] tracking-tight">{{ $genre }}</h3>
                                <span class="text-xs font-bold bg-[#e8edf2] text-[#1a3a5c] px-3 py-1 rounded-full">{{ $genreBooks->count() }} Buku</span>
                            </div>

                            @if($genreBooks->count() > 4)
                                @php
                                    $genreId = $genreBooks->first()->genres->where('name', $genre)->first()->id ?? '';
                                @endphp
                                <a href="/perpustakaan?genre_ids[]={{ $genreId }}&local_only=true" 
                                   class="text-sm font-bold text-[#1a3a5c] hover:text-[#e84b7a] transition-colors flex items-center gap-1 group-hover:underline underline-offset-4">
                                    Lihat Semua 
                                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            @endif
                        </div>
                        
                        <hr class="border-gray-100 mb-6">
                        
                        <!-- Grid Responsive untuk Buku (Batas 6 di layar besar) -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-x-6 gap-y-10">
                            @foreach($genreBooks->take(6) as $index => $book)
                                <div class="book-animate opacity-0 translate-y-8 transition-all duration-700 ease-out hover:-translate-y-2"
                                    style="transition-delay: {{ $index * 50 }}ms">
                                    <x-book-card-magazine
                                        :id="$book->id"
                                        :image="$book->image"
                                        :title="$book->title"
                                        :author="$book->author"
                                        :first-genre="$book->genres->first()?->name ?? null"
                                        :owner-id="$book->user_id"
                                        :is-available="method_exists($book, 'isAvailable') ? $book->isAvailable() : true"
                                        :is-google-api="false"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                        <i data-lucide="library" class="w-16 h-16 text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-medium text-lg">Belum ada koleksi buku di perpustakaan ini.</p>
                    </div>
                @endforelse
            @endif

        </div>
    </div>

    <x-footer />

    <script>
        // Inisialisasi ikon Lucide
        lucide.createIcons();
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        // Animasi Scroll yang lebih smooth
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('opacity-0', 'translate-y-8');
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.book-animate').forEach(el => observer.observe(el));
    </script>

</body>
</html>