<!DOCTYPE html>
<html>

<head>
    <title>Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c]">

    <x-header />

    <div class="bg-[#f2e9e4] p-6 rounded-xl mb-6 flex gap-4">

        <!-- WELCOME (kiri) -->
        <div class="bg-[#d9c2a3] p-6 rounded-xl flex flex-col justify-between w-2/5">

            <div>
                <p class="text-xs text-[#7a5c3e] font-medium uppercase tracking-widest mb-1">Perpustakaan Komunitas</p>
                <h2 class="text-2xl font-bold text-[#2c2c2c]">
                    Selamat datang, <span class="italic text-[#5a3e3e]"></span><!-- auth()->user()->name -->
                </h2>
                <p class="text-sm mt-2 text-[#5c4a36]">
                    Temukan, bagikan, dan pinjam buku di komunitas Anda.
                </p>
            </div>

            <!-- MINI STATS -->
            <div class="grid grid-cols-2 gap-2 my-4">
                <div class="bg-[#c9ae8e] rounded-xl p-3">
                    <p class="text-xs text-[#5a3e3e] font-medium">Buku Saya</p>
                    <p class="text-2xl font-bold text-[#2c2c2c]">4</p>
                    <p class="text-xs text-[#7a5c3e]">sedang dipinjam</p>
                </div>
                <div class="bg-[#c9ae8e] rounded-xl p-3">
                    <p class="text-xs text-[#5a3e3e] font-medium">Poin Saya</p>
                    <p class="text-2xl font-bold text-[#2c2c2c]">120</p>
                    <p class="text-xs text-[#7a5c3e]">poin terkumpul</p>
                </div>
                <div class="bg-[#c9ae8e] rounded-xl p-3">
                    <p class="text-xs text-[#5a3e3e] font-medium">Ulasan</p>
                    <p class="text-2xl font-bold text-[#2c2c2c]">7</p>
                    <p class="text-xs text-[#7a5c3e]">telah ditulis</p>
                </div>
                <div class="bg-[#c9ae8e] rounded-xl p-3">
                    <p class="text-xs text-[#5a3e3e] font-medium">Bergabung</p>
                    <p class="text-2xl font-bold text-[#2c2c2c]">3</p>
                    <p class="text-xs text-[#7a5c3e]">bulan lalu</p>
                </div>
            </div>

            <!-- QUOTE -->
            <div class="bg-[#b89a78] rounded-xl p-3 mb-4">
                <p class="text-xs text-[#3e2c1e] italic leading-relaxed">
                    "Membaca adalah jendela dunia. Satu buku yang tepat bisa mengubah segalanya."
                </p>
            </div>

            <!-- BUTTONS -->
            <div class="flex gap-2">
                <button class="flex-1 bg-[#5a3e3e] text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Cari Buku
                </button>
                <button class="flex-1 border border-[#5a3e3e] text-[#5a3e3e] px-4 py-2 rounded-lg text-sm font-medium">
                    Ikuti Diskusi
                </button>
            </div>

        </div>

        <!-- STATISTIK (kanan) -->
        <div class="flex-1 flex flex-col">

            <div class="mb-4">
                <p class="text-sm text-gray-500">Ringkasan koleksi dan aktivitas pengguna</p>
                <h3 class="text-xl font-semibold text-[#2c2c2c]">Statistik Perpustakaan</h3>
            </div>

            <div class="grid grid-cols-2 gap-3 flex-1">

                {{-- Buku Dipinjam --}}
                <div class="bg-white rounded-2xl p-4 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3B6D11"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">Buku Dipinjam</span>
                    </div>
                    <div class="flex items-end gap-3">
                        <div class="w-16 h-16 flex-shrink-0">
                            <canvas id="borrowChart"></canvas>
                        </div>
                        <div>
                            <p class="text-3xl font-semibold text-[#2c2c2c] leading-none">2</p>
                            <p class="text-xs text-gray-400 mt-1">sedang dipinjam</p>
                        </div>
                    </div>
                </div>

                {{-- Diskusi Aktif --}}
                <div class="bg-white rounded-2xl p-4 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#185FA5"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">Diskusi Aktif</span>
                    </div>
                    <div class="flex items-end gap-3">
                        <div class="w-16 h-16 flex-shrink-0">
                            <canvas id="discussionChart"></canvas>
                        </div>
                        <div>
                            <p class="text-3xl font-semibold text-[#2c2c2c] leading-none">24</p>
                            <p class="text-xs text-gray-400 mt-1">topik berjalan</p>
                        </div>
                    </div>
                </div>

                {{-- Buku Dibagikan --}}
                <div class="bg-white rounded-2xl p-4 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#854F0B"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 12 20 22 4 22 4 12" />
                                <rect x="2" y="7" width="20" height="5" />
                                <line x1="12" y1="22" x2="12" y2="7" />
                                <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z" />
                                <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z" />
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">Buku Dibagikan</span>
                    </div>
                    <div class="flex items-end gap-3">
                        <div class="w-16 h-16 flex-shrink-0">
                            <canvas id="shareChart"></canvas>
                        </div>
                        <div>
                            <p class="text-3xl font-semibold text-[#2c2c2c] leading-none">8</p>
                            <p class="text-xs text-gray-400 mt-1">buku tersedia</p>
                        </div>
                    </div>
                </div>

                {{-- Total Koleksi --}}
                <div class="bg-white rounded-2xl p-4 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#534AB7"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="12" x2="21" y2="12" />
                                <line x1="3" y1="6" x2="21" y2="6" />
                                <line x1="3" y1="18" x2="21" y2="18" />
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">Total Koleksi</span>
                    </div>
                    <div class="flex items-end gap-3">
                        <div class="w-16 h-16 flex-shrink-0">
                            <canvas id="collectionChart"></canvas>
                        </div>
                        <div>
                            <p class="text-3xl font-semibold text-[#2c2c2c] leading-none">1.036</p>
                            <p class="text-xs text-gray-400 mt-1">judul terdaftar</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>


    <!-- AKTIVITAS -->
    <div class="bg-[#f2e9e4] p-6 rounded-xl">

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-[#2c2c2c]">
                Aktivitas Terkini
            </h3>

            <button
                class="bg-gray-200 px-4 py-2 rounded-xl text-sm hover:bg-gray-300 transition flex items-center gap-2">
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

        {{-- Script Chart.js (taruh sebelum </body> atau di @push('scripts')) --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function makeDonut(id, color) {
                new Chart(document.getElementById(id), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [100],
                            backgroundColor: [color],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    },
                    plugins: [{
                        id: 'trackRing',
                        beforeDraw(chart) {
                            const {
                                width,
                                height,
                                ctx
                            } = chart;
                            const cx = width / 2,
                                cy = height / 2;
                            const outerR = Math.min(width, height) / 2;
                            const innerR = outerR * 0.75;
                            const midR = (outerR + innerR) / 2;
                            const lineW = outerR - innerR;
                            ctx.save();
                            ctx.beginPath();
                            ctx.arc(cx, cy, midR, 0, Math.PI * 2);
                            ctx.strokeStyle = 'rgba(0,0,0,0.07)';
                            ctx.lineWidth = lineW;
                            ctx.stroke();
                            ctx.restore();
                        }
                    }]
                });
            }

            makeDonut('borrowChart', '#3B6D11');
            makeDonut('discussionChart', '#185FA5');
            makeDonut('shareChart', '#854F0B');
            makeDonut('collectionChart', '#534AB7');
        </script>

    </div>
    </div>

</body>

</html>
