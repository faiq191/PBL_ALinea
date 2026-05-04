<!DOCTYPE html>
<html>

<head>
    <title>Informasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-[#4a3734] text-[#4a3734] font-sans">

    <x-header />

    <div class="max-w-4xl mx-auto p-6 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-[#fdf2f0] rounded-2xl aspect-square flex items-center justify-center">
                <span class="text-[#c8b6b3] font-bold text-xl uppercase tracking-widest"></span>
            </div>
            <div class="md:col-span-2 bg-[#fdf2f0] rounded-2xl p-8 flex flex-col justify-center">
                <p class="text-lg leading-relaxed">
                    <span class="font-bold text-2xl">ALinea</span> bermula dari keinginan untuk menghidupkan kembali koleksi buku fisik yang terbengkalai di rak pribadi. Kami hadir sebagai jembatan digital bagi komunitas literasi kota untuk saling berbagi, meminjam, dan berdiskusi dalam satu ekosistem yang modern dan kolaboratif.
                </p>
            </div>
        </div>

        <div class="bg-[#fdf2f0] rounded-2xl p-6 text-center">
            <p class="text-xl italic px-4">
                "Mewujudkan masyarakat perkotaan yang cerdas dan kolaboratif melalui digitalisasi budaya literasi yang inklusif."
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-8">

            <div class="relative bg-[#fdf2f0] rounded-2xl p-6 pt-12 text-center">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-[#8c7672] rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-layer-group"></i>
                </div>
                <p><span class="font-bold">Membangun</span> wadah digital untuk pengelolaan katalog buku pribadi secara praktis.</p>
            </div>
            
            <div class="relative bg-[#fdf2f0] rounded-2xl p-6 pt-12 text-center">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-[#8c7672] rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-share-nodes"></i>
                </div>
                <p><span class="font-bold">Mengoptimalkan</span> sistem ekonomi berbagi melalui fitur peminjaman buku antarpengguna.</p>
            </div>
            
            <div class="relative bg-[#fdf2f0] rounded-2xl p-6 pt-12 text-center">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-[#8c7672] rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-location-dot"></i>
                </div>
                <p><span class="font-bold">Menyediakan</span> ruang diskusi berbasis lokasi dan minat genre yang aman bagi pembaca.</p>
            </div>
        </div>

        <div class="bg-[#fdf2f0] rounded-2xl p-8">
            <h2 class="text-2xl font-bold border-b-2 border-[#4a3734] pb-2 mb-6 inline-block">Keunggulan Platform</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border border-[#4a3734]/20 rounded-xl p-4">
                    <p><span class="font-bold">Ekonomi Berbagi:</span> Memperluas akses bacaan tanpa harus membeli buku baru.</p>
                </div>
                <div class="border border-[#4a3734]/20 rounded-xl p-4">
                    <p><span class="font-bold">Komunitas Lokal:</span> Ruang interaksi khusus bagi pembaca di wilayah geografis yang sama.</p>
                </div>
                <div class="border border-[#4a3734]/20 rounded-xl p-4">
                    <p><span class="font-bold">Katalog Digital:</span> Pendataan koleksi buku pribadi yang tersusun rapi dan mudah dikelola.</p>
                </div>
                <div class="border border-[#4a3734]/20 rounded-xl p-4">
                    <p><span class="font-bold">Panel Efisien:</span> Manajemen data yang cepat dan terintegrasi menggunakan teknologi terkini.</p>
                </div>
            </div>
        </div>

        <!-- Bagian Narahubung yang Diperbaiki -->
        <div class="bg-[#fdf2f0] rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-2">Narahubung</h2>
            <!-- Garis pemisah sekarang terpisah dan membentang penuh (w-full) -->
            <div class="border-b border-[#4a3734] w-full mb-6"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                <!-- Gunakan items-baseline agar titik (.) sejajar dengan teks -->
                <div class="flex items-baseline">
                    <!-- whitespace-nowrap mencegah label patah -->
                    <span class="font-bold mr-2 whitespace-nowrap">• Surel:</span>
                    <span>bantuan@alinea.id</span>
                </div>
                
                <div class="flex items-baseline">
                    <span class="font-bold mr-2 whitespace-nowrap">• Instagram:</span>
                    <span>@alinea.komunitas</span>
                </div>
                
                <div class="flex items-baseline">
                    <span class="font-bold mr-2 whitespace-nowrap">• Alamat:</span>
                    <span class="leading-snug">Jurusan Teknologi Informasi, Politeknik Negeri Malang.</span>
                </div>
                
                <div class="flex items-baseline">
                    <span class="font-bold mr-2 whitespace-nowrap">• Telepon:</span>
                    <span>(0341) 404424</span>
                </div>
            </div>
        </div>

    </div>
</body>

</html>