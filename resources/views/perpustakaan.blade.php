<!DOCTYPE html>
<html lang="id">
<head>
    <title>Perpustakaan | Alinea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for Dropdowns */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #b0c8e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #1a3a5c; }

        /* Netflix-style Horizontal Scrollbar for Book Rows */
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
<body class="bg-[#f5f5f5] text-[#1a3a5c] font-sans antialiased overflow-x-hidden">

    <x-header />

    <!-- Lebarkan container untuk layout ala Netflix -->
    <div class="pt-24 pb-12 w-full">
        
        <!-- HERO BANNER -->
        <div class="px-6 lg:px-12 mb-8">
            <div class="bg-[#1a3a5c] rounded-[2.5rem] p-10 md:p-16 relative overflow-hidden shadow-2xl flex flex-col justify-center min-h-[300px]">
                <!-- Dekorasi Background -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 left-20 w-48 h-48 bg-[#e84b7a]/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight mb-4 flex items-center gap-4">
                            <i data-lucide="library" class="w-10 h-10 md:w-14 md:h-14 text-[#e84b7a]"></i>
                            Eksplorasi
                        </h1>
                        <p class="text-[#e8edf2] text-lg md:text-xl max-w-xl font-light">
                            Temukan cerita, wawasan, dan petualangan baru di koleksi perpustakaan kami.
                        </p>
                    </div>
                    
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="/books/create"
                                class="bg-white text-[#1a3a5c] px-8 py-4 rounded-2xl text-sm font-bold flex items-center gap-2 hover:bg-[#e8edf2] hover:shadow-[0_0_20px_rgba(255,255,255,0.3)] transition-all active:scale-95 shrink-0">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                                Tambah Koleksi
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- SEARCH & FILTER SECTION (Floating style) -->
        <div class="px-6 lg:px-12 mb-12 relative z-20 -mt-16">
            <div class="bg-white p-4 md:p-6 rounded-[2rem] shadow-xl border border-gray-100 mx-auto max-w-6xl">
                <form method="GET" action="/perpustakaan" x-data="{
                    filterOpen: false,
                    menuOpen: null,
                    toggle(menu) { this.menuOpen = this.menuOpen === menu ? null : menu }
                }">
                    
                    @if(request('local_only'))
                        <input type="hidden" name="local_only" value="true">
                    @endif

                    <div class="flex flex-col md:flex-row items-center gap-3">
                        <!-- Search Bar -->
                        <div class="relative flex-1 w-full group">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-5 h-5 text-gray-400 group-focus-within:text-[#1a3a5c] transition-colors"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari judul, penulis, atau kata kunci..."
                                class="w-full bg-[#f8fafc] hover:bg-[#f1f5f9] rounded-2xl py-4 pl-14 pr-6 outline-none text-base text-[#1a3a5c] placeholder:text-gray-400 focus:bg-white focus:ring-2 focus:ring-[#1a3a5c]/20 transition-all border border-transparent focus:border-[#1a3a5c]/10">
                        </div>

                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <button type="submit" class="bg-[#1a3a5c] text-white px-8 py-4 rounded-2xl font-bold hover:bg-[#122b45] transition-all shadow-md active:scale-95 flex-1 md:flex-none">
                                Cari
                            </button>

                            <button type="button" @click="filterOpen = !filterOpen"
                                class="bg-slate-100 text-[#1a3a5c] px-6 py-4 rounded-2xl flex justify-center items-center gap-2 font-bold hover:bg-slate-200 transition-all active:scale-95 flex-1 md:flex-none"
                                :class="filterOpen ? 'ring-2 ring-[#1a3a5c]' : ''">
                                <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                                <span class="hidden md:inline">Filter</span>
                            </button>

                            @if(request()->hasAny(['search', 'genre_ids', 'type_ids', 'demo_ids', 'year_ids', 'author']))
                                <a href="/perpustakaan" title="Reset Pencarian"
                                    class="bg-red-50 text-red-500 p-4 rounded-2xl font-bold hover:bg-red-500 hover:text-white transition-all active:scale-95 flex items-center justify-center shrink-0">
                                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Filter Dropdowns Panel -->
                    <div x-show="filterOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         x-cloak
                         class="bg-slate-50 p-6 md:p-8 rounded-[1.5rem] mt-4 border border-slate-100 relative">
                         
                        <div class="flex flex-wrap gap-4 items-start">

                            <!-- Genre Filter -->
                            <div class="relative">
                                <button type="button" @click="toggle('genres')"
                                    class="bg-white border border-slate-200 text-[#1a3a5c] px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:border-[#1a3a5c]/30 transition-all shadow-sm">
                                    Genres
                                    <i data-lucide="chevron-down" :class="menuOpen === 'genres' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                                </button>
                                <div x-show="menuOpen === 'genres'" @click.away="menuOpen = null" x-transition x-cloak
                                    class="absolute left-0 mt-3 bg-white p-6 rounded-2xl shadow-2xl w-screen max-w-[400px] border border-gray-100 max-h-80 overflow-y-auto z-50">
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
                                    class="bg-white border border-slate-200 text-[#1a3a5c] px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:border-[#1a3a5c]/30 transition-all shadow-sm">
                                    Tipe Buku
                                    <i data-lucide="chevron-down" :class="menuOpen === 'types' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                                </button>
                                <div x-show="menuOpen === 'types'" @click.away="menuOpen = null" x-transition x-cloak
                                    class="absolute left-0 mt-3 bg-white p-5 rounded-2xl shadow-2xl w-64 border border-gray-100 z-50">
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
                                    class="bg-white border border-slate-200 text-[#1a3a5c] px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:border-[#1a3a5c]/30 transition-all shadow-sm">
                                    Target Pembaca
                                    <i data-lucide="chevron-down" :class="menuOpen === 'demo' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                                </button>
                                <div x-show="menuOpen === 'demo'" @click.away="menuOpen = null" x-transition x-cloak
                                    class="absolute left-0 mt-3 bg-white p-5 rounded-2xl shadow-2xl w-64 border border-gray-100 z-50">
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
                                    class="bg-white border border-slate-200 text-[#1a3a5c] px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:border-[#1a3a5c]/30 transition-all shadow-sm">
                                    Pengarang
                                    <i data-lucide="chevron-down" :class="menuOpen === 'author' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                                </button>
                                <div x-show="menuOpen === 'author'" @click.away="menuOpen = null" x-transition x-cloak
                                    class="absolute left-0 mt-3 bg-white p-5 rounded-2xl shadow-2xl w-72 border border-gray-100 z-50">
                                    <h4 class="text-[#1a3a5c] font-bold mb-3">Nama Pengarang</h4>
                                    <div class="relative">
                                        <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                        <input type="text" name="author" value="{{ request('author') }}"
                                            placeholder="Ketik nama..."
                                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-[#e8edf2] text-[#1a3a5c] outline-none text-sm focus:ring-2 focus:ring-[#1a3a5c]/30 border border-transparent focus:border-[#1a3a5c]/20">
                                    </div>
                                </div>
                            </div>

                            <!-- Tahun Rilis Filter -->
                            <div class="relative">
                                <button type="button" @click="toggle('year')"
                                    class="bg-white border border-slate-200 text-[#1a3a5c] px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:border-[#1a3a5c]/30 transition-all shadow-sm">
                                    Tahun Rilis
                                    <i data-lucide="chevron-down" :class="menuOpen === 'year' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"></i>
                                </button>
                                <div x-show="menuOpen === 'year'" @click.away="menuOpen = null" x-transition x-cloak
                                    class="absolute left-0 mt-3 bg-white p-5 rounded-2xl shadow-2xl w-80 border border-gray-100 z-50">
                                    <h4 class="text-[#1a3a5c] font-bold mb-3 border-b pb-2">Tahun Terbit</h4>
                                    <div class="flex items-center gap-3">
                                        <select name="year_from" class="flex-1 p-2.5 rounded-xl bg-[#e8edf2] text-[#1a3a5c] text-sm outline-none cursor-pointer border border-transparent focus:border-[#1a3a5c]/20">
                                            <option value="">Dari Tahun</option>
                                            @foreach($years as $y)
                                                <option value="{{ $y->year }}" {{ request('year_from') == $y->year ? 'selected' : '' }}>{{ $y->year }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-gray-400">-</span>
                                        <select name="year_to" class="flex-1 p-2.5 rounded-xl bg-[#e8edf2] text-[#1a3a5c] text-sm outline-none cursor-pointer border border-transparent focus:border-[#1a3a5c]/20">
                                            <option value="">Ke Tahun</option>
                                            @foreach($years as $y)
                                                <option value="{{ $y->year }}" {{ request('year_to') == $y->year ? 'selected' : '' }}>{{ $y->year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Terapkan Button -->
                            <div class="ml-auto w-full md:w-auto mt-2 md:mt-0">
                                <button type="submit"
                                    class="w-full md:w-auto bg-[#1a3a5c] text-white px-8 py-2.5 rounded-xl font-bold flex justify-center items-center gap-2 hover:bg-[#122b45] hover:shadow-md transition-all active:scale-95">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    Terapkan Filter
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- RESULTS / MAIN CONTENT SECTION -->
        <div class="px-6 lg:px-12">
            @if($hasFilters)
                <!-- Grid Tampilan Hasil Pencarian (Search Active) -->
                <div class="flex items-center gap-3 mb-8 pl-2">
                    <h3 class="text-2xl font-bold text-[#1a3a5c]">Hasil Pencarian</h3>
                    <span class="bg-[#1a3a5c] text-white py-1 px-4 rounded-full text-sm font-semibold">{{ $books->count() }} Ditemukan</span>
                </div>

                @if($books->isEmpty())
                    <div class="flex flex-col items-center justify-center py-32 bg-white rounded-[2.5rem] shadow-sm border border-gray-100">
                        <i data-lucide="search-x" class="w-20 h-20 text-gray-300 mb-6"></i>
                        <p class="text-gray-500 font-medium text-xl">Waduh, buku yang Anda cari tidak ditemukan.</p>
                        <a href="/perpustakaan" class="mt-6 px-8 py-3 bg-[#1a3a5c] text-white rounded-xl text-sm font-bold hover:bg-[#122b45] transition-all shadow-md">Reset Pencarian</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-x-6 gap-y-10">
                        @foreach($books as $index => $book)
                            <!-- Disesuaikan agar card memiliki proporsi memanjang secara natural -->
                            <div class="book-animate opacity-0 translate-y-8 transition-all duration-500 ease-out hover:-translate-y-2 hover:scale-105 hover:z-10"
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
                <!-- NETFLIX STYLE CAROUSEL ROWS (No Search) -->
                @forelse($booksByGenre as $genre => $genreBooks)
                    <div class="mb-14 relative group">
                        
                        <!-- Row Header -->
                        <div class="flex justify-between items-end mb-4 px-2 md:px-4">
                            <div class="flex items-center gap-3">
                                <h3 class="text-2xl font-extrabold text-[#1a3a5c] tracking-tight">{{ $genre }}</h3>
                            </div>

                            @if($genreBooks->count() > 4)
                                @php
                                    $genreId = $genreBooks->first()->genres->where('name', $genre)->first()->id ?? '';
                                @endphp
                                <a href="/perpustakaan?genre_ids[]={{ $genreId }}&local_only=true" 
                                   class="text-sm font-bold text-[#e84b7a] hover:text-[#1a3a5c] transition-colors flex items-center gap-1 group-hover:underline underline-offset-4">
                                    Lihat Semua 
                                    <i data-lucide="chevron-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            @endif
                        </div>
                        
                        <!-- Horizontal Scroll Container -->
                        <!-- Padding Y (py) diperbesar agar animasi scale dan bayangan tidak terpotong -->
                        <div class="flex gap-4 md:gap-5 overflow-x-auto py-8 -my-6 px-2 md:px-4 row-scroll snap-x snap-mandatory">
                            @foreach($genreBooks->take(10) as $index => $book)
                                <!-- Lebar dikurangi sedikit agar rasionya lebih memanjang ke bawah (portrait) -->
                                <div class="w-[140px] sm:w-[160px] md:w-[180px] lg:w-[200px] h-[300px] shrink-0 snap-start book-animate opacity-0 translate-y-8 transition-all duration-300 ease-out hover:-translate-y-2 hover:scale-105 hover:z-20 flex flex-col"
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
                            <!-- Gradient Fade for right edge hinting more content -->
                            <div class="w-[40px] shrink-0 pointer-events-none"></div>
                        </div>
                        
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl shadow-sm border border-gray-100">
                        <i data-lucide="book-dashed" class="w-16 h-16 text-gray-300 mb-4"></i>
                        <p class="text-gray-500 font-medium text-lg">Belum ada koleksi buku di perpustakaan ini.</p>
                    </div>
                @endforelse
            @endif

        </div>
    </div>

    <x-footer />

    <script>
        lucide.createIcons();
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        // Smooth Scroll Entry Animation
        const observerOptions = {
            root: null,
            rootMargin: '50px',
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