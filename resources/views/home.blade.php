<!DOCTYPE html>
<html>
<head>
    <title>Homepage</title>
    <link rel="icon" type="image/png" href="/Gambar/logo_alinea_tanpa_tulisan2.png?v=2">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for Dropdowns */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #b0c8e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #1a3a5c; }

        .row-scroll::-webkit-scrollbar { height: 8px; }
        .row-scroll::-webkit-scrollbar-track { background: transparent; }
        .row-scroll::-webkit-scrollbar-thumb { background: transparent; border-radius: 10px; transition: all 0.3s; }
        .row-scroll:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }
        .row-scroll::-webkit-scrollbar-thumb:hover { background: #1a3a5c; }
        
        /* Hide scrollbar completely on mobile for swiping */
        @media (max-width: 768px) {
            .row-scroll::-webkit-scrollbar { display: none; }
        }
    </style>
</head>

<body class="bg-[#f5f5f5]">

    <x-header />

    {{-- HERO VIDEO FULLWIDTH --}}
    <div class="relative w-full overflow-hidden" style="height: 700px;">

        <video 
            autoplay 
            muted 
            loop 
            playsinline 
            preload="auto"
            class="absolute w-full h-full object-cover z-10 pointer-events-none"
            style="top: 50%; left: 50%; transform: translate(-50%, -50%); min-height: 100%; min-width: 100%;">
            <source src="https://www.fukujo.ac.jp/university/kansei-media/assets/movies/concept_pc.mp4" type="video/mp4">
        </video>

        {{-- OVERLAY --}}
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/10 z-20"></div>

        {{-- GRADIENT BOTTOM FADE TO MATCH BACKGROUND --}}
        <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-[#f5f5f5] via-[#f5f5f5]/50 to-transparent z-25"></div>

        {{-- TEKS QUOTES --}}
        <div class="absolute inset-0 flex flex-col justify-center px-16 z-30 max-w-2xl">
            <p class="text-white text-lg leading-relaxed mb-4 drop-shadow-md">
                Setiap hari kita dikelilingi informasi dan ekspresi,<br>
                terhubung dengan dunia melalui rasa.
            </p>
            <p class="text-white text-lg leading-relaxed mb-4 drop-shadow-md">
                Mengapa aku merasakan ini?<br>
                Bagaimana aku bisa membuat orang lain merasakannya?
            </p>
            <p class="text-white text-lg leading-relaxed mb-4 drop-shadow-md">
                Membaca bukan sekadar kebiasaan,<br>
                ia adalah ilmu yang mendalami kepekaan.
            </p>
            <p class="text-white text-lg leading-relaxed mb-4 drop-shadow-md">
                Kata yang menyentuh hati,<br>
                adalah kekuatan yang menggerakkan dunia.
            </p>
            <p class="text-white text-xl font-semibold leading-relaxed drop-shadow-lg">
                Kepekaan membaca, menggerakkan masa depan.
            </p>
        </div>

        {{-- WATERMARK ALINEA (RIGHT SIDE) --}}
        <div class="absolute right-12 top-0 bottom-0 flex items-center justify-center z-30 pointer-events-none">
            <p class="text-white text-5xl font-light uppercase opacity-20 tracking-[0.2em] drop-shadow-md" 
               style="writing-mode: vertical-rl; text-orientation: upright;">
                ALINEA
            </p>
        </div>

    </div>

{{-- STATISTIK & WELCOME (FLOATING OVERLAPPING CARD - BALANCED STYLE) --}}
<div class="relative z-30 -mt-16 mx-16 bg-white rounded-2xl shadow-xl px-10 py-7 mb-10 border border-gray-100">
    {{-- ROW 1: WELCOME & CTA --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-5 border-b border-gray-100">
        <div>
            <p class="text-xs text-[#5a7a9c] font-bold uppercase tracking-widest mb-1">Perpustakaan Komunitas</p>
            <h2 class="text-2xl font-extrabold text-[#1a3a5c] tracking-tight">
                Selamat datang, <span class="font-serif italic font-normal text-[#e84b7a]">
                    @auth{{ auth()->user()->name }}@else Pengunjung @endauth
                </span>
            </h2>
        </div>
        <div class="flex items-center gap-3">
            <a href="/perpustakaan"
                class="bg-[#1a3a5c] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#122b45] shadow-md hover:shadow-lg transition duration-300 flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i>
                Cari Buku
            </a>
            <a href="/komunitas"
                class="border-2 border-[#1a3a5c] text-[#1a3a5c] px-5 py-2 rounded-xl text-sm font-bold hover:bg-[#1a3a5c] hover:text-white transition duration-300 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4"></i>
                Ikuti Diskusi
            </a>
        </div>
    </div>

    {{-- ROW 2: 4 GRID STATISTICS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 pt-5">
        {{-- Card 1: Buku Dipinjam --}}
        <div class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-100/70 border border-slate-200/50 hover:bg-slate-200/50 hover:border-slate-300/50 transition duration-300">
            <div class="p-2.5 bg-[#1a3a5c]/8 text-[#1a3a5c] rounded-lg">
                <i data-lucide="book-open" class="w-5.5 h-5.5"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-[#1a3a5c] tracking-tight leading-none">{{ $totalBorrowed }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-1 whitespace-nowrap">Buku Dipinjam</p>
            </div>
        </div>
        
        {{-- Card 2: Diskusi Aktif --}}
        <div class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-100/70 border border-slate-200/50 hover:bg-slate-200/50 hover:border-slate-300/50 transition duration-300">
            <div class="p-2.5 bg-[#1a3a5c]/8 text-[#1a3a5c] rounded-lg">
                <i data-lucide="message-square" class="w-5.5 h-5.5"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-[#1a3a5c] tracking-tight leading-none">{{ $totalDiscussions }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-1 whitespace-nowrap">Diskusi Aktif</p>
            </div>
        </div>

        {{-- Card 3: Buku Dibagikan --}}
        <div class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-100/70 border border-slate-200/50 hover:bg-slate-200/50 hover:border-slate-300/50 transition duration-300">
            <div class="p-2.5 bg-[#1a3a5c]/8 text-[#1a3a5c] rounded-lg">
                <i data-lucide="share-2" class="w-5.5 h-5.5"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-[#1a3a5c] tracking-tight leading-none">{{ $myBooks }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-1 whitespace-nowrap">Buku Dibagikan</p>
            </div>
        </div>

        {{-- Card 4: Total Koleksi --}}
        <div class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-100/70 border border-slate-200/50 hover:bg-slate-200/50 hover:border-slate-300/50 transition duration-300">
            <div class="p-2.5 bg-[#1a3a5c]/8 text-[#1a3a5c] rounded-lg">
                <i data-lucide="library" class="w-5.5 h-5.5"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-[#1a3a5c] tracking-tight leading-none">{{ number_format($totalBooks) }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-1 whitespace-nowrap">Total Koleksi</p>
            </div>
        </div>
    </div>
</div>

{{-- GENRE MARQUEE --}}
<style>
    @keyframes marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-marquee {
    animation: marquee 25s linear infinite;
}
.animate-marquee:hover {
    animation-play-state: paused;
}
</style>
<div class="bg-[#ffffff] py-8 mb-6 overflow-hidden">
    <div class="flex items-center gap-6 animate-marquee whitespace-nowrap">
        @foreach([1,2] as $_)
            @foreach($genres as $genre)
                <a href="/perpustakaan?genre_ids[]={{ $genre->id }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full border-2 border-[#1a3a5c] text-[#1a3a5c] text-sm font-bold uppercase tracking-widest hover:bg-[#1a3a5c] hover:text-white transition-colors duration-300 flex-shrink-0">
                    {{ $genre->name }}
                </a>
            @endforeach
        @endforeach
    </div>
</div>

{{-- AKTIVITAS TERKINI — Magazine Style --}}
<div class="bg-[#ffffff] px-16 py-10 mb-6">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h3 class="text-3xl font-bold text-[#1a3a5c] tracking-tight">Aktivitas Terkini</h3>
            <p class="text-sm text-gray-400 mt-1">Buku terbaru di komunitas</p>
        </div>
        <a href="/koleksi" class="text-sm text-[#1a3a5c] font-semibold hover:underline">Lihat Semua →</a>
    </div>

    @php
        $bookList = $books ?? collect();
        $total    = $bookList->count();
    @endphp

    @if($total === 0)
        <p class="text-gray-400 text-sm">Belum ada buku.</p>
    @elseif($total < 4)
        <div class="grid grid-cols-{{ $total }} gap-4" style="height: 480px;">
            @foreach($bookList as $book)
                <x-book-card-magazine :id="$book->id" :image="$book->image" :title="$book->title"
                    :author="$book->author" :first-genre="$book->genres->first()?->name"
                    :owner-id="$book->user_id" :is-available="$book->isAvailable()" :is-google-api="false" />
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-2 gap-4" style="height: 560px;">
            <x-book-card-magazine :id="$bookList[0]->id" :image="$bookList[0]->image"
                :title="$bookList[0]->title" :author="$bookList[0]->author"
                :first-genre="$bookList[0]->genres->first()?->name"
                :owner-id="$bookList[0]->user_id" :is-available="$bookList[0]->isAvailable()" :is-google-api="false" />

            <div class="grid grid-rows-2 gap-4">
                <x-book-card-magazine :id="$bookList[1]->id" :image="$bookList[1]->image"
                    :title="$bookList[1]->title" :author="$bookList[1]->author"
                    :first-genre="$bookList[1]->genres->first()?->name"
                    :owner-id="$bookList[1]->user_id" :is-available="$bookList[1]->isAvailable()" :is-google-api="false" />

                <div class="grid grid-cols-2 gap-4">
                    @foreach($bookList->skip(2)->take(2) as $book)
                        <x-book-card-magazine :id="$book->id" :image="$book->image"
                            :title="$book->title" :author="$book->author"
                            :first-genre="$book->genres->first()?->name"
                            :owner-id="$book->user_id" :is-available="$book->isAvailable()" :is-google-api="false" />
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ACARA BERLANGSUNG --}}
<div class="bg-[#ffffff] px-16 py-10 mb-6">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h3 class="text-3xl font-bold text-[#1a3a5c] tracking-tight">Acara Berlangsung</h3>
            <p class="text-sm text-gray-400 mt-1">Ikuti keseruan acara dan kegiatan komunitas kami</p>
        </div>
    </div>

    @if($runningEvents->isEmpty())
        <div class="bg-gradient-to-r from-[#1a3a5c]/5 to-slate-50 border border-[#1a3a5c]/10 rounded-3xl p-8 text-center">
            <i data-lucide="calendar" class="w-12 h-12 text-[#1a3a5c]/35 mx-auto mb-3"></i>
            <p class="text-[#1a3a5c] font-bold text-lg mb-1">Belum Ada Acara Aktif</p>
            <p class="text-gray-400 text-sm max-w-md mx-auto">Nantikan info acara menarik selanjutnya yang akan diselenggarakan oleh admin di komunitas kami.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($runningEvents as $event)
                <div class="relative bg-white rounded-3xl overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-gray-100 flex flex-col md:flex-row group hover:shadow-lg transition duration-300">
                    {{-- Banner Image --}}
                    <div class="w-full md:w-2/5 h-48 md:h-auto min-h-[192px] bg-gray-100 relative overflow-hidden flex-shrink-0">
                        @if($event->image)
                            <img src="{{ \Illuminate\Support\Str::startsWith($event->image, 'http') ? $event->image : asset('storage/' . $event->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#1a3a5c]/20 to-[#1a3a5c]/5 text-[#1a3a5c]">
                                <i data-lucide="calendar" class="w-12 h-12 opacity-40"></i>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4 bg-emerald-500 text-white text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                            Running
                        </div>
                    </div>

                    {{-- Event Details --}}
                    <div class="p-6 flex flex-col justify-between flex-1">
                        <div>
                            <h4 class="text-xl font-extrabold text-[#1a3a5c] group-hover:text-[#e84b7a] transition duration-300 leading-snug mb-2">
                                {{ $event->title }}
                            </h4>
                            <p class="text-sm text-gray-500 line-clamp-3 mb-4 leading-relaxed">
                                {{ $event->description }}
                            </p>
                        </div>

                        <div>
                            {{-- Event Dates --}}
                            @if($event->start_date || $event->end_date)
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-4 bg-slate-50 p-2 rounded-xl border border-slate-100">
                                    <i data-lucide="clock" class="w-4 h-4 text-[#1a3a5c]"></i>
                                    <span>
                                        @if($event->start_date && $event->end_date)
                                            {{ $event->start_date->translatedFormat('d M Y H:i') }} - {{ $event->end_date->translatedFormat('d M Y H:i') }}
                                        @elseif($event->start_date)
                                            Mulai: {{ $event->start_date->translatedFormat('d M Y H:i') }}
                                        @else
                                            Selesai: {{ $event->end_date->translatedFormat('d M Y H:i') }}
                                        @endif
                                    </span>
                                </div>
                            @endif

                            {{-- Call to action --}}
                            @if($event->link)
                                <a href="{{ $event->link }}" target="_blank" class="w-full text-center inline-flex items-center justify-center gap-2 bg-[#1a3a5c] text-white font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-[#122b45] transition duration-300 shadow-sm">
                                    <span>Ikuti Acara</span>
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- DISKUSI TERHANGAT --}}
<div class="bg-[#ffffff] px-16 py-10 mb-6">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h3 class="text-3xl font-bold text-[#1a3a5c] tracking-tight">Diskusi Terhangat</h3>
            <p class="text-sm text-gray-400 mt-1">Topik yang paling banyak dibicarakan di komunitas</p>
        </div>
        <a href="/komunitas" class="text-sm text-[#1a3a5c] font-semibold hover:underline">Lihat Semua →</a>
    </div>

    @if($hotDiscussions->isEmpty())
        <div class="text-center py-12">
            <i data-lucide="message-square" class="w-12 h-12 text-[#d0e4f5] mx-auto mb-3"></i>
            <p class="text-gray-400 font-medium">Belum ada diskusi aktif.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($hotDiscussions as $discussion)
                <div class="bg-white border border-gray-100 rounded-3xl p-5 shadow-sm hover:shadow-md hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="relative w-full h-48 rounded-2xl overflow-hidden mb-4 bg-gray-100">
                            @if($discussion->image)
                                <img src="{{ \Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : asset('storage/' . $discussion->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#e8edf2] text-[#1a3a5c]">
                                    <i data-lucide="message-square" class="w-12 h-12 opacity-40"></i>
                                </div>
                            @endif
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-[#1a3a5c] shadow-sm">
                                {{ $discussion->genre ?? 'Umum' }}
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-bold text-[#1a3a5c] line-clamp-2 leading-tight mb-2">
                            {{ $discussion->title }}
                        </h4>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                            {{ strip_tags($discussion->content) }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-50 mt-auto">
                        <div class="flex items-center gap-2">
                            @if($discussion->user->profile_photo)
                                <img src="{{ \Illuminate\Support\Str::startsWith($discussion->user->profile_photo, 'http') ? $discussion->user->profile_photo : asset('storage/' . $discussion->user->profile_photo) }}" class="w-6 h-6 rounded-full object-cover">
                            @else
                                <div class="w-6 h-6 rounded-full bg-[#e8edf2] flex items-center justify-center font-bold text-[#1a3a5c] text-[10px]">
                                    {{ substr($discussion->user->name ?? 'U', 0, 1) }}
                                </div>
                            @endif
                            <span class="text-xs font-semibold text-[#1a3a5c] line-clamp-1 max-w-[100px]">{{ $discussion->user->name ?? 'Unknown' }}</span>
                        </div>
                        
                        <a href="/diskusi/{{ $discussion->id }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-[#1a3a5c] hover:bg-[#122b45] px-3 py-2 rounded-xl transition duration-300 shadow-sm">
                            <span>Detail</span>
                            <span class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]">{{ $discussion->comments_count }}</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
 
{{-- CTA GUEST SECTION --}}
@guest
<div class="max-w-4xl mx-auto px-6 mb-12">
    <div class="bg-white rounded-3xl p-12 text-center shadow-[0_10px_30px_rgba(0,0,0,0.03)] border border-gray-100/80 relative overflow-hidden">
        <!-- Background subtle glow elements -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-emerald-50 rounded-full blur-3xl opacity-60"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-sky-50 rounded-full blur-3xl opacity-60"></div>

        <!-- Book Icons Stack -->
        <div class="flex justify-center mb-6 relative z-10 pl-3">
            <!-- Green Book -->
            <div class="w-10 h-14 bg-emerald-500 rounded-l-md rounded-r shadow-md transform -rotate-12 translate-x-2 relative z-10 flex items-center justify-end pr-1 border-r-4 border-emerald-600">
                <div class="w-1 h-full bg-white opacity-20 mr-1"></div>
            </div>
            <!-- Pink Book -->
            <div class="w-10 h-14 bg-pink-500 rounded-l-md rounded-r shadow-md transform rotate-3 -translate-x-1 relative z-20 flex items-center justify-end pr-1 border-r-4 border-pink-600">
                <div class="w-1 h-full bg-white opacity-20 mr-1"></div>
            </div>
            <!-- Blue Book -->
            <div class="w-10 h-14 bg-sky-500 rounded-l-md rounded-r shadow-md transform rotate-12 -translate-x-4 translate-y-1 relative z-30 flex items-center justify-end pr-1 border-r-4 border-sky-600">
                <div class="w-1 h-full bg-white opacity-20 mr-1"></div>
            </div>
        </div>

        <div class="relative z-10">
            <h3 class="text-3xl sm:text-4xl font-extrabold text-[#1a3a5c] tracking-tight mb-4">
                Siap Mulai Membaca?
            </h3>
            <p class="text-base text-gray-500 max-w-xl mx-auto leading-relaxed mb-4">
                Bergabunglah dengan lebih dari 1.200 pembaca di Malang. Pinjam buku gratis dari koleksi anggota terdekatmu.
            </p>
            <p class="text-xs text-gray-400 font-semibold tracking-wide mb-8">
                Tidak perlu kartu kredit <span class="mx-1.5 text-gray-300">•</span> 100% gratis <span class="mx-1.5 text-gray-300">•</span> Komunitas terbuka
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                <a href="/register" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl bg-[#1a3a5c] text-white font-bold text-sm hover:bg-[#11263d] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 shadow-md">
                    Daftar Gratis Sekarang
                </a>
                <a href="/perpustakaan" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl border-2 border-gray-100 bg-white text-[#1a3a5c] font-bold text-sm hover:border-[#1a3a5c] hover:bg-gray-50 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                    Jelajahi Perpustakaan
                </a>
            </div>
        </div>
    </div>
</div>
@endguest

<x-footer />

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    // Intersection Observer — fade in saat scroll ke elemen
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('opacity-0', 'translate-y-8');
                entry.target.classList.add('opacity-100', 'translate-y-0');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.book-animate').forEach(el => observer.observe(el));
</script>
</body>
</html>