<!DOCTYPE html>
<html>
<head>
    <title>Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#f5f5f5]">

    <x-header />

    <div class="pt-24 px-6 pb-6">
        <div class="bg-[#ffffff] rounded-2xl p-6">

            {{-- HEADER --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-[#1a3a5c]">Cari Buku</h1>
                    <p class="text-gray-500">Cari Buku yang anda ingin telusuri...</p>
                </div>
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="/books/create"
                            class="bg-[#1a3a5c] text-white px-5 py-2 rounded-xl text-sm font-medium hover:bg-[#122b45] transition">
                            + Tambah Buku
                        </a>
                    @endif
                @endauth
            </div>

            {{-- SEARCH & FILTER FORM --}}
            <form method="GET" action="/perpustakaan" class="mb-10" x-data="{
                filterOpen: false,
                menuOpen: null,
                toggle(menu) { this.menuOpen = this.menuOpen === menu ? null : menu }
            }">

                {{-- Search Bar + Filter Button --}}
                <div class="flex items-center gap-4 mb-4">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search..."
                            class="w-full bg-[#e8edf2] rounded-full py-4 px-6 outline-none text-lg">
                    </div>
                    <button type="button" @click="filterOpen = !filterOpen"
                        class="bg-[#1a3a5c] text-white px-8 py-4 rounded-full flex items-center gap-2 font-bold hover:bg-[#122b45] transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'genre_ids', 'type_ids', 'demo_ids', 'year_ids', 'author']))
                        <a href="/perpustakaan"
                            class="bg-red-500 text-white px-5 py-4 rounded-full text-sm font-bold hover:bg-red-600 transition">
                            Reset
                        </a>
                    @endif
                </div>

                {{-- Filter Panel --}}
                <div x-show="filterOpen" x-transition x-cloak
                    class="bg-[#1a3a5c] p-8 rounded-3xl shadow-2xl mb-6">
                    <div class="flex flex-wrap gap-3 items-start">

                        {{-- GENRES --}}
                        <div class="relative">
                            <button type="button" @click="toggle('genres')"
                                class="bg-white/20 text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2 hover:bg-white/30 transition">
                                Genres
                                <span :class="menuOpen === 'genres' ? 'rotate-180' : ''" class="transition-transform inline-block">▼</span>
                            </button>
                            <div x-show="menuOpen === 'genres'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-white p-6 rounded-3xl shadow-xl w-[440px]">
                                <h4 class="text-[#1a3a5c] font-bold mb-4">Genre</h4>
                                <div class="grid grid-cols-3 gap-y-3 gap-x-4">
                                    @foreach($genres as $g)
                                    <label class="flex items-center gap-2 text-[#1a3a5c] text-sm cursor-pointer">
                                        <input type="checkbox" name="genre_ids[]" value="{{ $g->id }}"
                                            {{ is_array(request('genre_ids')) && in_array($g->id, request('genre_ids')) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded accent-[#1a3a5c]">
                                        {{ $g->name }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- TIPE --}}
                        <div class="relative">
                            <button type="button" @click="toggle('types')"
                                class="bg-white/20 text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2 hover:bg-white/30 transition">
                                Tipe
                                <span :class="menuOpen === 'types' ? 'rotate-180' : ''" class="transition-transform inline-block">▼</span>
                            </button>
                            <div x-show="menuOpen === 'types'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-white p-6 rounded-3xl shadow-xl w-56">
                                <h4 class="text-[#1a3a5c] font-bold mb-4">Tipe</h4>
                                <div class="space-y-3">
                                    @foreach($types as $t)
                                    <label class="flex items-center gap-2 text-[#1a3a5c] text-sm cursor-pointer">
                                        <input type="checkbox" name="type_ids[]" value="{{ $t->id }}"
                                            {{ is_array(request('type_ids')) && in_array($t->id, request('type_ids')) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded accent-[#1a3a5c]">
                                        {{ $t->name }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- DEMOGRAFIS --}}
                        <div class="relative">
                            <button type="button" @click="toggle('demo')"
                                class="bg-white/20 text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2 hover:bg-white/30 transition">
                                Demografis
                                <span :class="menuOpen === 'demo' ? 'rotate-180' : ''" class="transition-transform inline-block">▼</span>
                            </button>
                            <div x-show="menuOpen === 'demo'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-white p-6 rounded-3xl shadow-xl w-56">
                                <h4 class="text-[#1a3a5c] font-bold mb-4">Demografis</h4>
                                <div class="space-y-3">
                                    @foreach($demographics as $d)
                                    <label class="flex items-center gap-2 text-[#1a3a5c] text-sm cursor-pointer">
                                        <input type="checkbox" name="demo_ids[]" value="{{ $d->id }}"
                                            {{ is_array(request('demo_ids')) && in_array($d->id, request('demo_ids')) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded accent-[#1a3a5c]">
                                        {{ $d->name }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- PENGARANG --}}
                        <div class="relative">
                            <button type="button" @click="toggle('author')"
                                class="bg-white/20 text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2 hover:bg-white/30 transition">
                                Pengarang
                                <span :class="menuOpen === 'author' ? 'rotate-180' : ''" class="transition-transform inline-block">▼</span>
                            </button>
                            <div x-show="menuOpen === 'author'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-white p-6 rounded-3xl shadow-xl w-72">
                                <h4 class="text-[#1a3a5c] font-bold mb-4">Pengarang</h4>
                                <input type="text" name="author" value="{{ request('author') }}"
                                    placeholder="Cari pengarang..."
                                    class="w-full p-3 rounded-xl bg-[#e8edf2] text-[#1a3a5c] outline-none text-sm">
                            </div>
                        </div>

                        {{-- TAHUN RILIS --}}
                        <div class="relative">
                            <button type="button" @click="toggle('year')"
                                class="bg-white/20 text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2 hover:bg-white/30 transition">
                                Tahun Rilis
                                <span :class="menuOpen === 'year' ? 'rotate-180' : ''" class="transition-transform inline-block">▼</span>
                            </button>
                            <div x-show="menuOpen === 'year'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-white p-6 rounded-3xl shadow-xl w-72">
                                <h4 class="text-[#1a3a5c] font-bold mb-4">Tahun Rilis</h4>
                                <div class="flex gap-2">
                                    <select name="year_from" class="flex-1 p-2 rounded-xl bg-[#e8edf2] text-[#1a3a5c] text-sm outline-none">
                                        <option value="">Dari</option>
                                        @foreach($years as $y)
                                            <option value="{{ $y->year }}" {{ request('year_from') == $y->year ? 'selected' : '' }}>{{ $y->year }}</option>
                                        @endforeach
                                    </select>
                                    <select name="year_to" class="flex-1 p-2 rounded-xl bg-[#e8edf2] text-[#1a3a5c] text-sm outline-none">
                                        <option value="">Ke</option>
                                        @foreach($years as $y)
                                            <option value="{{ $y->year }}" {{ request('year_to') == $y->year ? 'selected' : '' }}>{{ $y->year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SUBMIT --}}
                        <div class="ml-auto">
                            <button type="submit"
                                class="bg-white text-[#1a3a5c] px-8 py-3 rounded-2xl font-bold hover:bg-[#d0e4f5] transition">
                                Terapkan
                            </button>
                        </div>

                    </div>
                </div>

            </form>

            {{-- RESULTS --}}
            @if($hasFilters)
                <h3 class="text-lg font-semibold text-[#1a3a5c] mb-4">
                    Hasil Pencarian
                    <span class="text-sm font-normal text-gray-400">({{ $books->count() }} buku)</span>
                </h3>

                @if($books->isEmpty())
                    <div class="text-center py-16">
                        <p class="text-gray-400 text-sm">Tidak ada buku ditemukan.</p>
                        <a href="/perpustakaan" class="mt-3 inline-block text-sm text-[#1a3a5c] hover:underline">Reset pencarian</a>
                    </div>
                @else
                    <div class="grid grid-cols-5 gap-4">
                        @foreach($books as $index => $book)
                            <div class="book-animate opacity-0 translate-y-6 transition-all duration-500"
                                style="transition-delay: {{ $index * 60 }}ms">
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
                {{-- Grouped by Genre --}}
                @forelse($booksByGenre as $genre => $genreBooks)
                    <div class="mb-12">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-2xl font-bold text-[#1a3a5c]">{{ $genre }}</h3>
                            <span class="text-sm text-gray-400">{{ $genreBooks->count() }} buku</span>
                        </div>
                        <hr class="border-[#b0c8e0] mb-5">
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($genreBooks as $index => $book)
                                <div class="book-animate opacity-0 translate-y-6 transition-all duration-500"
                                    style="transition-delay: {{ $index * 60 }}ms">
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
                    <div class="text-center py-16">
                        <p class="text-gray-400 text-sm">Belum ada buku di perpustakaan.</p>
                    </div>
                @endforelse
            @endif

        </div>
    </div>

    <script>lucide.createIcons();</script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('opacity-0', 'translate-y-6');
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.book-animate').forEach(el => observer.observe(el));
    </script>

</body>
</html>
