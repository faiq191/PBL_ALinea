<!DOCTYPE html>
<html>

<head>
    <title>Homepage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c]">

    <x-header />

    <!-- SEARCH -->
    <div class="max-w-7xl mx-auto px-6 mb-6">
        <div class="relative">
            <input type="text"
                class="w-full bg-[#4a4a4a] text-white rounded-full py-3 px-12 focus:outline-none focus:ring-2 focus:ring-[#d9c2a3]"
                placeholder="Cari diskusi atau topik...">

            <div class="absolute left-4 top-3.5">
                <img src="Logo/search.png" class="w-5 h-5 opacity-50 invert">
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="max-w-7xl mx-auto px-6 flex gap-6">

        <!-- LEFT -->
        <div class="flex-[3] bg-[#f2e9e4] p-8 rounded-3xl shadow-lg">

            <!-- HEADER -->
            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-[#d9c2a3] rounded-2xl">
                    <img src="Logo/group.png" class="w-10 h-10">
                </div>
                <div>
                    <h2 class="text-4xl font-bold text-[#2c2c2c]">Komunitas</h2>
                    <p class="text-[#5c4a36] text-sm">Tempat berbagi pikiran dan inspirasi</p>
                </div>
            </div>

            <!-- BUTTON -->
            <div class="flex justify-between items-center mb-10">

                @auth
                    <a href="/diskusi/create"
                        class="bg-[#5a3e3e] text-white px-6 py-3 rounded-full flex items-center gap-3">
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
                    <button class="bg-[#4a3232] text-white px-5 py-2 rounded-full text-sm">
                        Terbaru
                    </button>
                    <button class="bg-[#f9a01b] text-white px-5 py-2 rounded-full text-sm">
                        Terpopuler
                    </button>
                </div>
            </div>

            <!-- DISCUSSIONS -->
            <div>
                <h3 class="text-2xl font-bold mb-6 text-[#2c2c2c]">Diskusi Aktif</h3>

                @forelse ($discussions ?? [] as $discussion)
                    <div class="bg-white rounded-2xl p-6 shadow mb-4">

                        <h4 class="text-lg font-bold text-[#2c2c2c]">
                            {{ $discussion->title }}
                        </h4>

                        <span class="inline-block bg-blue-400 text-white text-xs px-3 py-1 rounded-full mt-2">
                            {{ $discussion->genre ?? 'Umum' }}
                        </span>

                        <div class="flex justify-between mt-4 text-sm text-gray-500">
                            <p>{{ $discussion->user->name ?? 'Unknown' }}</p>

                            <div class="flex gap-2">
                                <a href="/diskusi/{{ $discussion->id }}"
                                    class="bg-gray-200 px-3 py-1 rounded">
                                    Lihat
                                </a>

                            </div>
                        </div>

                    </div>
                @empty
                    <p class="text-gray-500">Belum ada diskusi</p>
                @endforelse

            </div>

        </div>

        <!-- RIGHT -->
        <div class="flex-1">
            <div class="bg-[#fcf7f4] p-6 rounded-3xl shadow-md sticky top-6">
                <h3 class="text-2xl font-bold mb-4">Filter</h3>

                <button class="w-full bg-[#5a3e3e] text-white p-3 rounded-xl flex justify-between">
                    Genres
                    <span>▼</span>
                </button>
            </div>
        </div>

    </div>

</body>

</html>
