<!DOCTYPE html>
<html>
<head>
    <title>Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#2c2c2c]">

    <x-header />

    <div class="p-6">
        <div class="bg-[#f2e9e4] rounded-2xl p-6">

            {{-- HEADER --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <p class="text-xs text-[#7a5c3e] uppercase tracking-widest font-medium">Koleksi Komunitas</p>
                    <h2 class="text-2xl font-bold text-[#2c2c2c]">Perpustakaan</h2>
                </div>

                @auth
                    @if(auth()->user()->is_admin)
                        <a href="/books/create"
                            class="bg-[#5a3e3e] text-white px-5 py-2 rounded-xl text-sm font-medium hover:bg-[#4a3333] transition">
                            + Tambah Buku
                        </a>
                    @endif
                @endauth
            </div>

            {{-- SEARCH & FILTER --}}
            <form method="GET" action="/perpustakaan">
                <div class="flex items-center gap-3 mb-6">
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari judul atau pengarang..."
                        class="flex-1 px-4 py-2 rounded-full bg-[#d6c7be] outline-none text-sm text-[#2c2c2c] placeholder-[#7a5c3e]">

                    <select name="genre"
                        class="px-4 py-2 rounded-full bg-[#d6c7be] outline-none text-sm text-[#2c2c2c]">
                        <option value="">Semua Genre</option>
                        @foreach($genres as $g)
                            <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>
                                {{ $g }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-[#5a3e3e] text-white px-5 py-2 rounded-full text-sm hover:bg-[#4a3333] transition">
                        Cari
                    </button>

                    @if(request('search') || request('genre'))
                        <a href="/perpustakaan"
                            class="text-sm text-[#7a5c3e] hover:underline">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- KONTEN --}}
            @if(request('search') || request('genre'))
                <h3 class="text-lg font-semibold text-[#2c2c2c] mb-4">
                    Hasil Pencarian
                    <span class="text-sm font-normal text-gray-500">({{ $books->count() }} buku)</span>
                </h3>

                @if($books->isEmpty())
                    <p class="text-gray-500 text-sm">Tidak ada buku ditemukan.</p>
                @else
                    <div class="grid grid-cols-4 gap-4 mb-8">
                        @foreach($books as $book)
                            <x-book-card
                                :id="$book->id"
                                :image="$book->image"
                                :title="$book->title"
                                :author="$book->author"
                                :genre="$book->genre"
                            />
                        @endforeach
                    </div>
                @endif

            @else
                {{-- Tampilkan per genre --}}
                @forelse($booksByGenre as $genre => $genreBooks)
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-semibold text-[#2c2c2c]">
                                {{ $genre ?? 'Tanpa Genre' }}
                            </h3>
                            <span class="text-xs text-gray-400">{{ $genreBooks->count() }} buku</span>
                        </div>
                        <hr class="border-[#c9ae8e] mb-4">

                        <div class="grid grid-cols-4 gap-4">
                            @foreach($genreBooks as $book)
                                <x-book-card
                                    :id="$book->id"
                                    :image="$book->image"
                                    :title="$book->title"
                                    :author="$book->author"
                                    :genre="$book->genre"
                                    :show-atur="auth()->check() && auth()->user()->is_admin"
                                />
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada buku di perpustakaan.</p>
                @endforelse
            @endif

        </div>
    </div>

</body>
</html>
