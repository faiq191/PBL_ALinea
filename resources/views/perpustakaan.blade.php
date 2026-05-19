<!DOCTYPE html>
<html>

<head>
    <title>Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-[#2c2c2c]" x-data="{ filterOpen: false }">

    <x-header />

    <div class="p-6">
        <div class="bg-[#f2e9e4] rounded-2xl p-8 relative min-h-screen">

            <div class="mb-8">
                <h1 class="text-4xl font-bold text-[#4b3b3b]">Cari Buku</h1>
                <p class="text-gray-500">Cari Buku yang anda ingin telusuri...</p>
            </div>

            <form method="GET" action="/perpustakaan" class="mb-12" x-data="{
                menuOpen: null,
                toggle(menu) { this.menuOpen = this.menuOpen === menu ? null : menu }
            }">
                <div class="flex items-center gap-4 mb-6">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                            class="w-full bg-[#d6c7be] rounded-full py-4 px-12 outline-none text-lg">
                        <div class="absolute left-5 top-4 opacity-50"></div>
                    </div>

                    <button type="button" @click="filterOpen = !filterOpen"
                        class="bg-[#4b3b3b] text-white px-8 py-4 rounded-full flex items-center gap-2 font-bold hover:bg-[#3a2e2e] transition">
                        Filter
                    </button>
                </div>

                <div x-show="filterOpen" x-transition x-cloak
                    class="absolute z-20 mt-4 bg-[#e6ddd6] p-8 rounded-[2.5rem] shadow-2xl border-8 border-[#f2e9e4] w-full max-w-4xl">

                    <div class="flex flex-wrap gap-3 items-center">
                        <div class="relative">
                            <button type="button" @click="toggle('genres')"
                                class="bg-[#5a3e3e] text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2">
                                Genres <span :class="menuOpen === 'genres' ? 'rotate-180' : ''" class="transition-transform">▼</span>
                            </button>
                            <div x-show="menuOpen === 'genres'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-[#5a3e3e] p-6 rounded-3xl shadow-xl w-[500px]">
                                <h4 class="text-white font-bold mb-4">Genre</h4>
                                <div class="grid grid-cols-3 gap-y-3 gap-x-4">
                                    @foreach($genres as $g)
                                    <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                        <input type="checkbox" name="genre_ids[]" value="{{ $g->id }}"
                                            {{ is_array(request('genre_ids')) && in_array($g->id, request('genre_ids')) ? 'checked' : '' }}
                                            class="w-5 h-5 rounded border-none bg-white checked:bg-[#d9c2a3] focus:ring-0">
                                        {{ $g->name }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <button type="button" @click="toggle('types')"
                                class="bg-[#5a3e3e] text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2">
                                Tipe <span :class="menuOpen === 'types' ? 'rotate-180' : ''" class="transition-transform">▼</span>
                            </button>
                            <div x-show="menuOpen === 'types'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-[#5a3e3e] p-6 rounded-3xl shadow-xl w-64">
                                <h4 class="text-white font-bold mb-4">Type</h4>
                                <div class="space-y-3">
                                    @foreach($types as $t)
                                    <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                        <input type="checkbox" name="type_ids[]" value="{{ $t->id }}"
                                            {{ is_array(request('type_ids')) && in_array($t->id, request('type_ids')) ? 'checked' : '' }}
                                            class="w-5 h-5 rounded border-none bg-white checked:bg-[#d9c2a3]">
                                        {{ $t->name }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <button type="button" @click="toggle('demo')"
                                class="bg-[#5a3e3e] text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2">
                                Demografis <span :class="menuOpen === 'demo' ? 'rotate-180' : ''" class="transition-transform">▼</span>
                            </button>
                            <div x-show="menuOpen === 'demo'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-[#5a3e3e] p-6 rounded-3xl shadow-xl w-64">
                                <h4 class="text-white font-bold mb-4">Demografis</h4>
                                <div class="space-y-3">
                                    @foreach($demographics as $d)
                                    <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                        <input type="checkbox" name="demo_ids[]" value="{{ $d->id }}"
                                            {{ is_array(request('demo_ids')) && in_array($d->id, request('demo_ids')) ? 'checked' : '' }}
                                            class="w-5 h-5 rounded border-none bg-white checked:bg-[#d9c2a3]">
                                        {{ $d->name }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <button type="button" @click="toggle('year')"
                                class="bg-[#5a3e3e] text-white px-6 py-3 rounded-2xl text-sm font-bold flex items-center gap-2">
                                Tahun Rilis <span :class="menuOpen === 'year' ? 'rotate-180' : ''" class="transition-transform">▼</span>
                            </button>
                            <div x-show="menuOpen === 'year'" @click.away="menuOpen = null"
                                class="absolute z-30 mt-2 bg-[#5a3e3e] p-6 rounded-3xl shadow-xl w-64">
                                <h4 class="text-white font-bold mb-4">Tahun Rilis</h4>
                                <div class="space-y-3">
                                    @foreach($years->take(10) as $y)
                                    <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                        <input type="checkbox" name="year_ids[]" value="{{ $y->id }}"
                                            {{ is_array(request('year_ids')) && in_array($y->id, request('year_ids')) ? 'checked' : '' }}
                                            class="w-5 h-5 rounded border-none bg-white checked:bg-[#d9c2a3]">
                                        {{ $y->year }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 ml-auto">
                            <a href="/perpustakaan" class="bg-red-600 text-white p-4 rounded-2xl hover:bg-red-700 transition flex items-center">
                                Reset
                            </a>
                            <button type="submit" class="bg-[#4b3b3b] text-white px-8 py-4 rounded-2xl font-bold flex items-center gap-2 hover:bg-[#3a2e2e] transition">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </form>

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
                    @empty
                        <div class="col-span-4 text-center py-16 text-gray-500">
                            Tidak ada buku yang ditemukan.
                        </div>
                    @endforelse
                </div>
            @else
                @foreach($booksByGenre as $genre => $genreBooks)
                    <div class="mb-12">
                        <div class="flex justify-between items-end mb-4">
                            <h3 class="text-2xl font-bold text-[#4b3b3b]">{{ $genre }}</h3>
                        </div>
                        <hr class="border-[#d6c7be] mb-6">
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
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
</body>

</html>