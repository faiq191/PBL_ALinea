<!DOCTYPE html>
<html>

<head>
    <title>Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c]">

    <x-header />
    <!-- SEARCH BAR (Atas) -->
    <div class="max-w-7xl mx-auto px-6 mb-6">
        <div class="relative">
            <input type="text"
                class="w-full bg-[#4a4a4a] text-white rounded-full py-3 px-12 focus:outline-none focus:ring-2 focus:ring-[#d9c2a3] placeholder-gray-400"
                placeholder="Cari diskusi atau topik...">
            <div class="absolute left-4 top-3.5">
                <!-- Pakai search.png -->
                <img src="Logo/search.png" class="w-5 h-5 opacity-50 brightness-0 invert" alt="Search">
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="max-w-7xl mx-auto px-6 flex gap-6">

        <!-- KOLOM KIRI: KOMUNITAS & DISKUSI -->
        <div class="flex-[3] bg-[#f2e9e4] p-8 rounded-3xl shadow-lg">

            <!-- Header Komunitas -->
            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-[#d9c2a3] rounded-2xl">
                    <!-- Pakai group.png -->
                    <img src="Logo/group.png" class="w-10 h-10" alt="Community">
                </div>
                <div>
                    <h2 class="text-4xl font-bold text-[#2c2c2c] mb-1">Komunitas</h2>
                    <p class="text-[#5c4a36] text-sm">Tempat berbagi pikiran dan inspirasi antar pembaca.</p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-between items-center mb-10">
                <button
                    class="bg-[#5a3e3e] text-white px-6 py-3 rounded-full text-sm font-medium flex items-center gap-3 hover:bg-[#4a3232] transition">
                    <!-- Pakai message-square-plus.png + Invert Putih -->
                    <img src="Logo/message-square-plus.png" class="w-5 h-5 brightness-0 invert" alt="">
                    Buat Diskusi Baru
                </button>

                <div class="flex gap-2">
                    <button
                        class="bg-[#4a3232] text-white px-5 py-2.5 rounded-full text-sm font-medium flex items-center gap-2">
                        <!-- Pakai history.png + Invert Putih -->
                        <img src="Logo/history.png" class="w-4 h-4 brightness-0 invert" alt="">
                        Terbaru
                    </button>
                    <button
                        class="bg-[#f9a01b] text-white px-5 py-2.5 rounded-full text-sm font-medium flex items-center gap-2">
                        <!-- Pakai trending-up.png + Invert Putih -->
                        <img src="Logo/trending-up.png" class="w-4 h-4 brightness-0 invert" alt="">
                        Terpopuler
                    </button>
                </div>
            </div>

            <!-- Bagian Daftar Diskusi -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <!-- Pakai two-books.png -->
                    <img src="Logo/two-books.png" class="w-7 h-7" alt="">
                    <h3 class="text-2xl font-bold text-[#2c2c2c]">Diskusi Aktif</h3>
                </div>

                <!-- Card Diskusi -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 group">
                    <h4 class="text-lg font-bold text-[#2c2c2c] mb-2">Apakah "The Midnight Library" Mudah Dibaca?</h4>
                    <span
                        class="inline-block bg-[#60a5fa] text-white text-[10px] px-3 py-1 rounded-full font-bold uppercase mb-6">
                        Fantasi
                    </span>

                    <div class="flex justify-between items-end">
                        <div class="text-xs text-gray-500">
                            <p class="font-medium text-[#2c2c2c]">Matt Haig</p>
                            <div class="flex items-center gap-1">
                                <!-- Pakai info.png untuk detail kecil -->
                                <img src="Logo/info.png" class="w-3 h-3 opacity-50" alt="">
                                <span>Novel / Self-Help</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2">
                                <!-- Pakai eye.png -->
                                <img src="Logo/eye.png" class="w-4 h-4 opacity-70" alt="">
                                Lihat
                            </button>
                            <button
                                class="bg-[#5a3e3e] text-white px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2">
                                <!-- Pakai pencil.png + Invert Putih -->
                                <img src="Logo/pencil.png" class="w-4 h-4 brightness-0 invert" alt="">
                                Komen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: FILTER -->
        <div class="flex-1">
            <div class="bg-[#fcf7f4] p-6 rounded-3xl shadow-md sticky top-6">
                <div class="flex items-center gap-3 mb-3">
                    <!-- Pakai bookshelf.png -->
                    <img src="Logo/bookshelf.png" class="w-6 h-6" alt="">
                    <h3 class="text-2xl font-bold text-[#2c2c2c]">Filter</h3>
                </div>
                <div class="h-0.5 bg-[#2c2c2c] w-full mb-6"></div>

                <div class="space-y-4">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Pilih
                        Genre</label>
                    <button
                        class="w-full bg-[#5a3e3e] text-white p-3 rounded-xl flex justify-between items-center text-sm">
                        <span>Genres</span>
                        <!-- Pakai chevron-down.png + Invert Putih -->
                        <img src="Logo/chevron-down.png" class="w-4 h-4 brightness-0 invert" alt="">
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>