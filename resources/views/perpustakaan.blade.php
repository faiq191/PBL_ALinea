<!DOCTYPE html>
<html>

<head>
    <title>Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#2c2c2c] min-h-screen">

    <x-header />

    <div class="p-8 flex justify-center">
        <div class="w-full max-w-5xl space-y-8">

            <div class="bg-[#e6ddd6] rounded-2xl p-8 shadow-xl">
                <div class="flex items-center justify-between mb-1">
                    <h1 class="text-xl font-semibold text-[#4b3b3b]">Cari Buku</h1>
                </div>
                <p class="text-sm text-gray-500 mb-6">Cari Buku yang anda ingin telusuri...</p>

                <form method="GET" action="/perpustakaan">
                    <div class="flex items-center gap-3 mb-6">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search..."
                            class="flex-1 px-4 py-2 rounded-full bg-[#d6c7be] outline-none text-sm placeholder-gray-400">

                        <button type="submit"
                            class="bg-[#5a3e3e] text-white px-5 py-2 rounded-full text-sm hover:bg-[#4a3333] transition">
                            Cari
                        </button>

                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true"
                                class="bg-[#5a3e3e] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#4a3333] transition flex items-center gap-2">
                                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filter
                            </button>

                            <div x-show="open" x-transition x-cloak
                                class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                <div class="bg-white p-6 rounded-2xl w-full max-w-2xl shadow-xl max-h-[90vh] overflow-y-auto" @click.away="open = false">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-bold text-[#4b3b3b]">Filter Pencarian</h3>
                                        <button type="button" @click="open = false"
                                            class="text-gray-400 hover:text-gray-600">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Genre</label>
                                            <div class="grid grid-cols-3 gap-2">
                                                @foreach($genres as $g)
                                                <label class="flex items-center gap-2 text-[#4b3b3b] text-sm cursor-pointer">
                                                    <input type="checkbox" name="genre_ids[]" value="{{ $g->id }}"
                                                        {{ is_array(request('genre_ids')) && in_array($g->id, request('genre_ids')) ? 'checked' : '' }}
                                                        class="w-4 h-4 rounded border-gray-300 text-[#5a3e3e] focus:ring-[#5a3e3e]">
                                                    <span class="truncate">{{ $g->name }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-3 gap-6">
                                            <div>
                                                <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Tipe</label>
                                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                                    @foreach($types as $t)
                                                    <label class="flex items-center gap-2 text-[#4b3b3b] text-sm cursor-pointer">
                                                        <input type="checkbox" name="type_ids[]" value="{{ $t->id }}"
                                                            {{ is_array(request('type_ids')) && in_array($t->id, request('type_ids')) ? 'checked' : '' }}
                                                            class="w-4 h-4 rounded border-gray-300 text-[#5a3e3e] focus:ring-[#5a3e3e]">
                                                        {{ $t->name }}
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Demografis</label>
                                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                                    @foreach($demographics as $d)
                                                    <label class="flex items-center gap-2 text-[#4b3b3b] text-sm cursor-pointer">
                                                        <input type="checkbox" name="demo_ids[]" value="{{ $d->id }}"
                                                            {{ is_array(request('demo_ids')) && in_array($d->id, request('demo_ids')) ? 'checked' : '' }}
                                                            class="w-4 h-4 rounded border-gray-300 text-[#5a3e3e] focus:ring-[#5a3e3e]">
                                                        {{ $d->name }}
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Tahun Rilis</label>
                                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                                    @foreach($years->take(10) as $y)
                                                    <label class="flex items-center gap-2 text-[#4b3b3b] text-sm cursor-pointer">
                                                        <input type="checkbox" name="year_ids[]" value="{{ $y->id }}"
                                                            {{ is_array(request('year_ids')) && in_array($y->id, request('year_ids')) ? 'checked' : '' }}
                                                            class="w-4 h-4 rounded border-gray-300 text-[#5a3e3e] focus:ring-[#5a3e3e]">
                                                        {{ $y->year }}
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between mt-8 pt-4 border-t border-gray-100">
                                        <a href="/perpustakaan"
                                            class="text-sm text-red-500 hover:text-red-700 font-medium transition">
                                            Reset Filter
                                        </a>
                                        <div class="flex gap-2">
                                            <button type="button" @click="open = false"
                                                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                                                Batal
                                            </button>
                                            <button type="submit" @click="open = false"
                                                class="bg-[#5a3e3e] text-white px-5 py-2 rounded-lg text-sm hover:bg-[#4a3333] transition">
                                                Terapkan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

<<<<<<< HEAD
                @if(request()->hasAny(['search', 'genre_ids', 'type_ids', 'demo_ids', 'year_ids']))
                    <div class="grid grid-cols-4 gap-6">
                        @forelse($books as $book)
                            <x-book-card :id="$book->id" :image="$book->image" :title="$book->title" :author="$book->author"
                                :genre="$book->genres->map(fn($g) => '<span class=\'bg-[#4b3b3b] text-white text-[10px] px-3 py-1 rounded-full mr-1\'>' . $g->name . '</span>')->join('')" />
=======
                @if($hasFilters)
                    <div class="grid grid-cols-4 gap-6">
                        @forelse($books as $book)
                            <x-book-card 
                                :id="$book->id" 
                                :image="$book->image" 
                                :title="$book->title" 
                                :author="$book->author"
                                :genre="$book->genres->map(fn($g) => '<span class=\'bg-[#4b3b3b] text-white text-[10px] px-3 py-1 rounded-full mr-1\'>' . $g->name . '</span>')->join('')"
                                :owner-id="$book->user_id"
                                :is-google-api="$book->is_google_api"
                                :google-url="$book->google_url ?? '#'"
                            />
>>>>>>> google-books-api
                        @empty
                            <div class="col-span-4 flex flex-col items-center justify-center py-16 text-center">
                                <i data-lucide="search-x" class="w-12 h-12 text-[#c9ae8e] mb-3"></i>
                                <p class="text-gray-500 text-sm">Tidak ada buku yang ditemukan.</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    @foreach($booksByGenre as $genre => $genreBooks)
                        <div class="mb-10 last:mb-0">
                            <div class="flex justify-between items-end mb-4">
                                <h3 class="text-xl font-bold text-[#4b3b3b]">{{ $genre }}</h3>
<<<<<<< HEAD
                                <span class="text-sm text-gray-400">{{ $genreBooks->count() }} buku</span>
                            </div>
                            <hr class="border-[#d6c7be] mb-6">
                            <div class="grid grid-cols-4 gap-6">
                                @foreach($genreBooks as $book)
                                    <x-book-card :id="$book->id" :image="$book->image" :title="$book->title" :author="$book->author"
                                        :genre="$book->genres->map(fn($g) => '<span class=\'bg-[#4b3b3b] text-white text-[10px] px-3 py-1 rounded-full mr-1\'>' . $g->name . '</span>')->join('')" />
=======
                            </div>
                            <div class="grid grid-cols-4 gap-6">
                                @foreach($genreBooks as $book)
                                    <x-book-card 
                                        :id="$book->id" 
                                        :image="$book->image" 
                                        :title="$book->title" 
                                        :author="$book->author"
                                        :genre="$book->genres->map(fn($g) => '<span class=\'bg-[#4b3b3b] text-white text-[10px] px-3 py-1 rounded-full mr-1\'>' . $g->name . '</span>')->join('')"
                                        :owner-id="$book->user_id"
                                        :is-google-api="$book->is_google_api"
                                        :google-url="$book->google_url ?? '#'"
                                    />
>>>>>>> google-books-api
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>

</body>

</html>