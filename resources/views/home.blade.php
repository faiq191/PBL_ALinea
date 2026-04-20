<!DOCTYPE html>
<html>

<head>
    <title>Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c]">

<x-header />

<div class="p-6">

    <!-- WELCOME -->
    <div class="bg-[#d9c2a3] p-6 rounded-xl mb-6">
        <h2 class="text-xl font-semibold">
            Selamat datang, <span class="italic">Eiyu</span>
        </h2>

        <p class="text-sm mt-2 text-gray-700">
            Temukan, bagikan, dan pinjam buku di komunitas Anda.
        </p>

        <div class="mt-4 flex gap-3">
            <button class="bg-[#5a3e3e] text-white px-4 py-2 rounded-lg">
                Cari Buku
            </button>

            <button class="border px-4 py-2 rounded-lg">
                Ikuti Diskusi
            </button>
        </div>
    </div>

    <!-- STAT -->
    <div class="bg-[#f2e9e4] p-6 rounded-xl mb-6">
        <h3 class="mb-4 font-semibold">Statistik</h3>

        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg text-center">
                <p class="text-sm">Buku Dipinjam</p>
                <h2 class="text-xl font-bold">2</h2>
            </div>

            <div class="bg-white p-4 rounded-lg text-center">
                <p class="text-sm">Diskusi Aktif</p>
                <h2 class="text-xl font-bold">24</h2>
            </div>

            <div class="bg-white p-4 rounded-lg text-center">
                <p class="text-sm">Buku Dibagikan</p>
                <h2 class="text-xl font-bold">8</h2>
            </div>

            <div class="bg-white p-4 rounded-lg text-center">
                <p class="text-sm">Total Koleksi</p>
                <h2 class="text-xl font-bold">36</h2>
            </div>
        </div>
    </div>

    <!-- AKTIVITAS -->
    <div class="bg-[#f2e9e4] p-6 rounded-xl">

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-[#2c2c2c]">
                Aktivitas Terkini
            </h3>

            <button class="bg-gray-200 px-4 py-2 rounded-xl text-sm hover:bg-gray-300 transition flex items-center gap-2">
                <i data-lucide="eye" class="w-4 h-4"></i>
                <span>Lihat Semua</span>
            </button>
        </div>

        <div class="grid grid-cols-4 gap-5">

<div class="grid grid-cols-4 gap-5">

@foreach ($books as $book)
    <div class="bg-white rounded-xl p-4 shadow">

        <img src="{{ asset('storage/' . $book->image) }}"
            class="w-full h-72 object-contain rounded-lg mb-4">

        <h4 class="font-semibold text-sm">
            {{ $book->title }}
        </h4>

        <p class="text-xs text-gray-500 mb-4">
            {{ $book->author }}
        </p>

        <div class="flex gap-2">
            <button class="flex-1 bg-gray-200 py-2 rounded-lg text-sm">
                Lihat
            </button>

            <button class="flex-1 bg-[#5a3e3e] text-white py-2 rounded-lg text-sm">
                Atur
            </button>
        </div>

    </div>
@endforeach

</div>

</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>

</body>
</html>
