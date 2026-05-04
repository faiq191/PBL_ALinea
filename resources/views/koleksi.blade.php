<!DOCTYPE html>
<html>
<head>
    <title>Katalog Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#2c2c2c]">

<x-header />

<div class="p-8 flex justify-center">
    <div class="w-full max-w-5xl bg-[#e6ddd6] rounded-2xl p-8">

        <h1 class="text-xl font-semibold text-[#4b3b3b]">Katalog Buku</h1>
        <p class="text-sm text-gray-500 mb-6">Koleksi buku anda</p>

        <form method="GET" action="/koleksi">
            <div class="flex items-center gap-4 mb-6">
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Pencarian menampilkan buku..."
                    class="flex-1 px-4 py-2 rounded-full bg-[#d6c7be] outline-none">

                <button type="submit"
                    class="bg-[#5a3e3e] text-white px-5 py-2 rounded-full text-sm">
                    Cari
                </button>

            <x-button class="bg-yellow-400 px-4 py-2 rounded-full text-sm">
                + Popular
            </x-button>

            <div x-data="{ open: false }" x-init="open = false">

                <button @click="open = true"
                    class="bg-[#5a3e3e] text-white px-4 py-2 rounded-lg">
                    Filter
                </button>

                <div x-show="open"
                    x-transition
                    x-cloak
                    class="fixed inset-0 bg-black/50 flex items-center justify-center">

                    <div class="bg-white p-6 rounded-xl w-80">

                        <h3 class="font-bold mb-4">Filter Genre</h3>

                        <form method="GET">

                            <select name="genre" class="w-full p-2 border rounded mb-4">
                                <option value="">Semua</option>

                                @foreach ($genres as $genre)
                                    <option value="{{ $genre }}">
                                        {{ $genre }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="flex justify-between">
                                <button type="button" @click="open=false">Batal</button>

                                <button class="bg-[#5a3e3e] text-white px-4 py-2 rounded">
                                    Terapkan
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>

        </div>

        <div class="grid grid-cols-4 gap-6">
        @isset($books)
            @foreach ($books as $book)
                <x-book-card
                    :id="$book->id"
                    :image="$book->image"
                    :title="$book->title"
                    :author="$book->author"
                    :genre="$book->genre"
                    :show-atur="auth()->check() && $book->user_id === auth()->id()"
                />
             @endforeach
        @endisset
        </div>

        <div class="flex justify-center mt-8">
            <a href="/books/create"
                class="bg-green-500 text-white px-4 py-2 rounded-lg">
                + Tambah Buku
            </a>
        </div>

    </div>
</div>

<script>
    lucide.createIcons();
</script>
<script src="//unpkg.com/alpinejs" defer></script>

</body>
</html>
