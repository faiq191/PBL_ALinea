<!DOCTYPE html>
@include("discussions.partials.helpers")
<html>
<head>
    <title>Detail Diskusi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        [x-cloak] { display: none !important; }
        .hash-loading body { opacity: 0 !important; }
        @keyframes scaleUp {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .animate-scale-up {
            animation: scaleUp 0.2s ease-out forwards;
        }
        #leaflet-map {
            width: 100%;
            height: 100%;
            z-index: 10;
        }
    </style>
    <script>
        const isReload = performance.getEntriesByType('navigation')[0]?.type === 'reload';
        if (isReload) {
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
            if (window.location.hash) {
                history.replaceState(null, null, window.location.pathname + window.location.search);
            }
            window.scrollTo(0, 0);
        }
        if (!isReload && window.location.hash && window.location.hash.startsWith('#comment-')) {
            document.documentElement.classList.add('hash-loading');
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }
        }
    </script>
</head>
<body class="bg-[#f5f5f5]">
    <x-header />

    <div class="max-w-4xl mx-auto pt-24 px-6 mb-12">
        <a href="/komunitas" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#1a3a5c] mb-6 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Komunitas
        </a>

        @include("discussions.partials.topic-header")

        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            <h3 class="text-xl font-bold text-[#1a3a5c] mb-6 flex items-center gap-2">
                <i data-lucide="messages-square" class="w-5 h-5"></i> Diskusi
            </h3>

            @auth
                @include("discussions.partials.comment-form")
            @else
                <div class="bg-[#e8edf2] p-6 rounded-2xl text-center mb-10">
                    <p class="text-sm text-[#5a7a9c] font-medium mb-3">Masuk untuk ikut berdiskusi</p>
                    <a href="/login" class="bg-[#1a3a5c] text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-[#122b45] transition">Masuk</a>
                </div>
            @endauth
            <div class="space-y-6">
                @forelse ($discussion->comments as $comment)
                    @include("discussions.partials.comment-item", ["comment" => $comment, "discussion" => $discussion])
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-400 text-sm">Belum ada balasan. Jadilah yang pertama berkomentar!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <x-footer />
    <script>lucide.createIcons();</script>

    @include("discussions.partials.scripts")
    @include("discussions.partials.modals")
</body>
</html>
