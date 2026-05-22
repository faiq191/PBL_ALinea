<!DOCTYPE html>
<html>

<head>
    <title>Komunitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f5f5f5]">

    <x-header />

    <!-- SEARCH -->
    <div class="max-w-9xl mx-auto px-6 mb-6">
        <form method="GET" action="/komunitas">
            <div class="relative">
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full bg-[#e8edf2] text-[#1a3a5c] rounded-full py-3 px-12 focus:outline-none focus:ring-2 focus:ring-[#d0e4f5] placeholder-[#5a7a9c]"
                    placeholder="Cari diskusi atau topik...">
                <div class="absolute left-4 top-3.5">
                    <img src="Logo/search.png" class="w-5 h-5 opacity-40">
                </div>
            </div>
        </form>
    </div>

    <!-- CONTENT -->
    <div class="max-w-9xl mx-auto px-6 flex gap-6">

        <!-- LEFT -->
        <div class="flex-[3] bg-[#ffffff] p-8 rounded-3xl shadow-lg">

            <!-- HEADER -->
            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-[#d0e4f5] rounded-2xl">
                    <img src="Logo/group.png" class="w-10 h-10">
                </div>
                <div>
                    <h2 class="text-4xl font-bold text-[#1a3a5c]">Komunitas</h2>
                    <p class="text-gray-400 text-sm">Tempat berbagi pikiran dan inspirasi</p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-10">

                @auth
                    <a href="/diskusi/create"
                        class="bg-[#1a3a5c] text-white px-6 py-3 rounded-full flex items-center gap-3">
                        <img src="Logo/message-square-plus.png" class="w-5 h-5 invert">
                        Buat Diskusi Baru
                    </a>
                @endauth

                @guest
                    <a href="/login"
                        class="bg-gray-300 text-gray-600 px-6 py-3 rounded-full">
                        Login untuk buat diskusi
                    </a>
                @endguest

                <div class="flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}"
                        class="px-5 py-2 rounded-full text-sm {{ request('sort', 'terbaru') == 'terbaru' ? 'bg-[#1a3a5c] text-white' : 'bg-gray-200 text-gray-600' }}">
                        Terbaru
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-2xl font-bold mb-6 text-[#1a3a5c]">Diskusi Aktif</h3>

                @forelse ($discussions ?? [] as $discussion)
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-4">

                        <h4 class="text-lg font-bold text-[#1a3a5c]">
                            {{ $discussion->title }}
                        </h4>

                        <span class="inline-block bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full mt-2">
                            {{ $discussion->genre ?? 'Umum' }}
                        </span>

                        <div class="flex justify-between mt-4 text-sm text-gray-500">
                            <div class="flex flex-col gap-1">
                                <p class="font-medium text-[#1a3a5c]">{{ $discussion->user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $discussion->created_at->translatedFormat('d F Y, H:i') }} WIB
                                </p>
                            </div>

                            <div class="flex gap-2 items-center">
                                <a href="/diskusi/{{ $discussion->id }}"
                                    class="bg-gray-100 hover:bg-[#1a3a5c] hover:text-white text-gray-600 px-4 py-1.5 rounded-lg text-sm transition">
                                    Lihat
                                </a>
                            </div>
                        </div>

                    </div>
                @empty
                    <p class="text-gray-400 text-sm">Belum ada diskusi</p>
                @endforelse

            </div>

        </div>

        <!-- RIGHT -->
        <div class="flex-1">
            <div class="bg-[#ffffff] p-6 rounded-3xl shadow-md sticky top-6">
                <h3 class="text-xl font-bold mb-4 text-[#1a3a5c]">Filter</h3>

                <form method="GET" action="/komunitas">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <div x-data="{ open: false }">
                        <button type="button" @click="open = true"
                            class="w-full bg-[#1a3a5c] text-white p-3 rounded-xl flex justify-between items-center text-sm font-medium">
                            {{ request('genre') ? request('genre') : 'Genres' }}
                            <span>▼</span>
                        </button>

                        <div x-show="open"
                            x-transition
                            x-cloak
                            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-xl w-80 shadow-xl">
                                <h3 class="font-bold mb-4 text-[#1a3a5c]">Filter Genre</h3>

                                <select name="genre" class="w-full p-2 border border-gray-200 rounded-lg mb-4 text-[#1a3a5c] text-sm outline-none">
                                    <option value="">Semua</option>
                                    @foreach($genres as $g)
                                        <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>
                                            {{ $g }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="flex justify-between">
                                    <button type="button" @click="open = false"
                                        class="text-sm text-gray-500 hover:text-gray-700">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="bg-[#1a3a5c] text-white px-4 py-2 rounded-lg text-sm">
                                        Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(request('genre'))
                        <a href="/komunitas" class="block text-center text-xs text-[#5a7a9c] hover:underline mt-3">
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>
        </div>

    </div>

    <div class="pb-16"></div>

<style>[x-cloak] { display: none !important; }</style>
<script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
