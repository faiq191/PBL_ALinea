<!DOCTYPE html>
<html>
<head>
    <title>Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c]">

<x-header />

<div class="p-6">

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

    <div class="bg-[#f2e9e4] p-6 rounded-xl">
        <div class="flex justify-between mb-4">
            <h3 class="font-semibold">Aktivitas Terkini</h3>
            <button class="text-sm">Lihat Semua</button>
        </div>

        <div class="grid grid-cols-4 gap-4">

            <div class="bg-white rounded-lg p-3">
                <img src="" class="rounded mb-2">
                <p class="text-sm">The Midnight Library</p>
                <button class="mt-2 text-xs bg-[#5a3e3e] text-white px-3 py-1 rounded">
                    Lihat
                </button>
            </div>

            <div class="bg-white rounded-lg p-3">
                <img src="" class="rounded mb-2">
                <p class="text-sm">One Piece</p>
                <button class="mt-2 text-xs bg-[#5a3e3e] text-white px-3 py-1 rounded">
                    Lihat
                </button>
            </div>

            <div class="bg-white rounded-lg p-3">
                <img src="" class="rounded mb-2">
                <p class="text-sm">Metamorphosis</p>
                <button class="mt-2 text-xs bg-[#5a3e3e] text-white px-3 py-1 rounded">
                    Lihat
                </button>
            </div>

            <div class="bg-white rounded-lg p-3">
                <img src="" class="rounded mb-2">
                <p class="text-sm">Another Episode</p>
                <button class="mt-2 text-xs bg-[#5a3e3e] text-white px-3 py-1 rounded">
                    Lihat
                </button>
            </div>

        </div>
    </div>

</div>

</body>
</html>
