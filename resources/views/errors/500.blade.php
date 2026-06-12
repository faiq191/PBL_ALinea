<!DOCTYPE html>
<html lang="id">

<head>
    <title>Terjadi Kesalahan - Alinea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .fade-in-delay-1 {
            animation-delay: 0.15s;
            opacity: 0;
        }

        .fade-in-delay-2 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        .fade-in-delay-3 {
            animation-delay: 0.45s;
            opacity: 0;
        }
    </style>
</head>

<body class="bg-[#f5f5f5] min-h-screen">

    <x-header />

    <div class="min-h-[calc(100vh-80px)] flex items-center justify-center px-6 pt-20 pb-16">
        <div class="w-full max-w-lg text-center">

            <div class="fade-in relative mx-auto mb-4 float-animation">
                <img src="/Gambar/sleeping-cute.gif" alt="Zzz..."
                    class="w-48 h-48 mx-auto object-contain drop-shadow-sm">
            </div>

            <div class="fade-in fade-in-delay-1">
                <span
                    class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-[11px] font-bold tracking-widest uppercase bg-rose-500/10 text-rose-500 border border-rose-500/20 mb-4">
                    <i data-lucide="alert-circle" class="w-3 h-3"></i> 500
                </span>

                <h1 class="text-2xl md:text-3xl font-extrabold text-[#1a3a5c] tracking-tight mb-2">
                    Terjadi Kesalahan Server
                </h1>
                <p class="text-sm text-gray-400 leading-relaxed max-w-sm mx-auto mb-8">
                    Maaf, terjadi kesalahan pada server kami. Tim kami sedang bekerja untuk memperbaikinya. Silakan coba
                    lagi nanti.
                </p>
            </div>

            <div class="fade-in fade-in-delay-2 flex flex-col sm:flex-row gap-3 justify-center items-center mb-6">
                <a href="javascript:location.reload()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#1a3a5c] hover:bg-[#122b45] text-white font-bold text-sm px-7 py-3 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 active:scale-[0.98]">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i> Coba Lagi
                </a>
                <a href="/"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 border-2 border-gray-200 hover:border-[#1a3a5c]/30 text-[#1a3a5c] font-bold text-sm px-7 py-3 rounded-xl transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.98]">
                    <i data-lucide="home" class="w-4 h-4"></i> Beranda
                </a>
            </div>

            <div class="fade-in fade-in-delay-3 flex items-center justify-center gap-4 text-xs text-gray-400">
                <a href="/perpustakaan" class="hover:text-[#1a3a5c] transition">Perpustakaan</a>
                <span class="text-gray-200">•</span>
                <a href="/informasi" class="hover:text-[#1a3a5c] transition">Informasi</a>
                <span class="text-gray-200">•</span>
                <a href="/komunitas" class="hover:text-[#1a3a5c] transition">Komunitas</a>
            </div>

        </div>
    </div>

    <x-footer />

    <script>
        if (window.lucide) { window.lucide.createIcons(); }
    </script>
</body>

</html>