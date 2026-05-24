<!DOCTYPE html>
<html>
<head>
    <title>Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f5f5f5]">

    <x-header />

    {{-- HERO VIDEO FULLWIDTH --}}
    <div class="relative w-full overflow-hidden" style="height: 750px;">

        <iframe
            src="https://www.youtube.com/embed/-93wzMrKTSg?autoplay=1&mute=1&loop=1&playlist=-93wzMrKTSg&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1"
            frameborder="0"
            allow="autoplay; encrypted-media"
            class="absolute pointer-events-none"
            style="width: 100vw; height: 56.25vw; min-height: 100%; min-width: 177.78vh; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        </iframe>

        {{-- OVERLAY --}}
        <div class="absolute inset-0 bg-black/50"></div>

        {{-- TEKS QUOTES --}}
        <div class="absolute inset-0 flex flex-col justify-center px-16 z-10 max-w-2xl">
            <p class="text-white text-lg leading-relaxed mb-4">
                Setiap hari kita dikelilingi informasi dan ekspresi,<br>
                terhubung dengan dunia melalui rasa.
            </p>
            <p class="text-white text-lg leading-relaxed mb-4">
                Mengapa aku merasakan ini?<br>
                Bagaimana aku bisa membuat orang lain merasakannya?
            </p>
            <p class="text-white text-lg leading-relaxed mb-4">
                Membaca bukan sekadar kebiasaan,<br>
                ia adalah ilmu yang mendalami kepekaan.
            </p>
            <p class="text-white text-lg leading-relaxed mb-4">
                Kata yang menyentuh hati,<br>
                adalah kekuatan yang menggerakkan dunia.
            </p>
            <p class="text-white text-xl font-semibold leading-relaxed">
                Kepekaan membaca, menggerakkan masa depan.
            </p>
        </div>

        {{-- WATERMARK ALINEA (RIGHT SIDE) --}}
        <div class="absolute right-12 top-0 bottom-0 flex items-center justify-center z-10 pointer-events-none">
            <p class="text-white text-5xl font-light uppercase opacity-30 tracking-[0.2em]" 
               style="writing-mode: vertical-rl; text-orientation: upright;">
                ALINEA
            </p>
        </div>

    </div>

{{-- STATISTIK --}}
<div class="bg-[#ffffff] px-16 py-10 mb-6">
    <div class="flex items-center justify-between">

        {{-- STATS --}}
        <div class="flex items-center gap-20">
            <div>
                <p class="text-4xl font-bold text-[#1a3a5c]">2</p>
                <p class="text-sm text-gray-500 mt-1">Buku Dipinjam</p>
            </div>
            <div>
                <p class="text-4xl font-bold text-[#1a3a5c]">{{ $discussions->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">Diskusi Aktif</p>
            </div>
            <div>
                <p class="text-4xl font-bold text-[#1a3a5c]">{{ $myBooks }}</p>
                <p class="text-sm text-gray-500 mt-1">Buku Dibagikan</p>
            </div>
            <div>
                <p class="text-4xl font-bold text-[#1a3a5c]">{{ number_format($totalBooks) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Koleksi</p>
            </div>
        </div>

        {{-- DIVIDER --}}
        <div class="h-16 w-px bg-gray-200 mx-8"></div>

        {{-- WELCOME --}}
        <div class="flex items-center justify-between flex-1">
            <div>
                <p class="text-xs text-[#5a7a9c] font-medium uppercase tracking-widest mb-1">Perpustakaan Komunitas</p>
                <h2 class="text-2xl font-bold text-[#1a3a5c]">
                    Selamat datang, <span class="font-serif italic font-normal">
                        @auth{{ auth()->user()->name }}@else Pengunjung @endauth
                    </span>
                </h2>
            </div>
            <div class="flex gap-3">
                <a href="/perpustakaan"
                    class="bg-[#1a3a5c] text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-[#122b45] transition">
                    Cari Buku
                </a>
                <a href="/komunitas"
                    class="border border-[#1a3a5c] text-[#1a3a5c] px-5 py-2 rounded-lg text-sm font-medium hover:bg-[#f0f4f8] transition">
                    Ikuti Diskusi
                </a>
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