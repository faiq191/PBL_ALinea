<!DOCTYPE html>
<html>

<head>
    <title>Informasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-[#f5f5f5] text-[#1a3a5c] font-sans">

    <x-header />

    <div class="max-w-6xl mx-auto pt-24 px-8 pb-8 space-y-6">

        {{-- ABOUT --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-[#1a3a5c] rounded-2xl aspect-square flex items-center justify-center">
                <img src ="Gambar\logo_alinea_tanpa_tulisan2.png">
            </div>
            <div class="md:col-span-2 bg-[#ffffff] rounded-2xl p-8 flex flex-col justify-center shadow-sm">
                <p class="text-lg leading-relaxed text-[#1a3a5c]">
                    <span class="font-bold text-2xl">ALinea</span> bermula dari keinginan untuk menghidupkan kembali koleksi buku fisik yang terbengkalai di rak pribadi. Kami hadir sebagai jembatan digital bagi komunitas literasi kota untuk saling berbagi, meminjam, dan berdiskusi dalam satu ekosistem yang modern dan kolaboratif.
                </p>
            </div>
        </div>

        {{-- QUOTE --}}
        <div class="bg-[#1a3a5c] rounded-2xl p-8 text-center">
            <p class="text-xl italic text-white px-4">
                "Mewujudkan masyarakat perkotaan yang cerdas dan kolaboratif melalui digitalisasi budaya literasi yang inklusif."
            </p>
        </div>

        {{-- 3 PILLARS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">

            <div class="relative bg-[#ffffff] rounded-2xl p-6 pt-14 text-center shadow-sm">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-[#1a3a5c] rounded-full flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-layer-group"></i>
                </div>
                <p class="text-[#1a3a5c]"><span class="font-bold">Membangun</span> wadah digital untuk pengelolaan katalog buku pribadi secara praktis.</p>
            </div>

            <div class="relative bg-[#ffffff] rounded-2xl p-6 pt-14 text-center shadow-sm">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-[#1a3a5c] rounded-full flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-share-nodes"></i>
                </div>
                <p class="text-[#1a3a5c]"><span class="font-bold">Mengoptimalkan</span> sistem ekonomi berbagi melalui fitur peminjaman buku antarpengguna.</p>
            </div>

            <div class="relative bg-[#ffffff] rounded-2xl p-6 pt-14 text-center shadow-sm">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-[#1a3a5c] rounded-full flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-location-dot"></i>
                </div>
                <p class="text-[#1a3a5c]"><span class="font-bold">Menyediakan</span> ruang diskusi berbasis lokasi dan minat genre yang aman bagi pembaca.</p>
            </div>

        </div>

        {{-- KEUNGGULAN --}}
        <div class="bg-[#ffffff] rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-[#1a3a5c] border-b-2 border-[#1a3a5c] pb-2 mb-6 inline-block">Keunggulan Platform</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-[#1a3a5c]/20 rounded-xl p-4 hover:bg-[#f0f4f8] transition">
                    <p class="text-[#1a3a5c]"><span class="font-bold">Ekonomi Berbagi:</span> Memperluas akses bacaan tanpa harus membeli buku baru.</p>
                </div>
                <div class="border border-[#1a3a5c]/20 rounded-xl p-4 hover:bg-[#f0f4f8] transition">
                    <p class="text-[#1a3a5c]"><span class="font-bold">Komunitas Lokal:</span> Ruang interaksi khusus bagi pembaca di wilayah geografis yang sama.</p>
                </div>
                <div class="border border-[#1a3a5c]/20 rounded-xl p-4 hover:bg-[#f0f4f8] transition">
                    <p class="text-[#1a3a5c]"><span class="font-bold">Katalog Digital:</span> Pendataan koleksi buku pribadi yang tersusun rapi dan mudah dikelola.</p>
                </div>
                <div class="border border-[#1a3a5c]/20 rounded-xl p-4 hover:bg-[#f0f4f8] transition">
                    <p class="text-[#1a3a5c]"><span class="font-bold">Panel Efisien:</span> Manajemen data yang cepat dan terintegrasi menggunakan teknologi terkini.</p>
                </div>
            </div>
        </div>

        {{-- NARAHUBUNG --}}
        <div class="bg-[#1a3a5c] rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-white mb-2">Narahubung</h2>
            <div class="border-b border-white/30 w-full mb-6"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-white whitespace-nowrap">• Surel:</span>
                    <span class="text-white/80">bantuan@alinea.id</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-white whitespace-nowrap">• Instagram:</span>
                    <span class="text-white/80">@alinea.komunitas</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-white whitespace-nowrap">• Alamat:</span>
                    <span class="text-white/80 leading-snug">Jurusan Teknologi Informasi, Politeknik Negeri Malang.</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-white whitespace-nowrap">• Telepon:</span>
                    <span class="text-white/80">(0341) 404424</span>
                </div>
            </div>
        </div>

        <div class="pb-8"></div>

    </div>

    <x-footer />

</body>

</html>
