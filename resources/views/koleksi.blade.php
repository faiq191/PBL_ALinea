<!DOCTYPE html>
<html>
<head>
    <title>Katalog Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#2c2c2c]">

<x-header />

<div class="p-8 flex justify-center">
    <div class="w-full max-w-5xl bg-[#e6ddd6] rounded-2xl p-8">

        <h1 class="text-xl font-semibold text-[#4b3b3b]">Katalog Buku</h1>
        <p class="text-sm text-gray-500 mb-6">Koleksi buku anda</p>

        <div class="flex items-center gap-4 mb-6">
            <input type="text"
                placeholder="Pencarian menampilkan buku..."
                class="flex-1 px-4 py-2 rounded-full bg-[#d6c7be] outline-none">

            <x-button class="bg-yellow-400 px-4 py-2 rounded-full text-sm">
                + Popular
            </x-button>

            <x-button class="bg-[#5a3e3e] text-white px-4 py-2 rounded-full text-sm">
                Filter
            </x-button>
        </div>

        <div class="grid grid-cols-4 gap-6">
        @isset($books)
            @foreach ($books as $book)
                            <x-book-card
                                :image="$book->image"
                                :title="$book->title"
                                :author="$book->author"
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

</body>
</html>
