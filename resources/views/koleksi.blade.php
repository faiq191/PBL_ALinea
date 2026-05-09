<!DOCTYPE html>
<html>

<head>
    <title>Katalog Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#2c2c2c] min-h-screen">

    <x-header />

    <div class="p-8 flex justify-center">
        <div class="w-full max-w-5xl space-y-8">

            <div class="bg-[#e6ddd6] rounded-2xl p-8 shadow-xl">
                <div class="flex items-center justify-between mb-1">
                    <h1 class="text-xl font-semibold text-[#4b3b3b]">Katalog Buku</h1>
                    <a href="/books/create"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Buku
                    </a>
                </div>
                <p class="text-sm text-gray-500 mb-6">Koleksi buku anda</p>

                <form method="GET" action="/koleksi">
                    <div class="flex items-center gap-3 mb-6">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari judul atau penulis..."
                            class="flex-1 px-4 py-2 rounded-full bg-[#d6c7be] outline-none text-sm placeholder-gray-400">

                        <button type="submit"
                            class="bg-[#5a3e3e] text-white px-5 py-2 rounded-full text-sm hover:bg-[#4a3333] transition">
                            Cari
                        </button>

                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true"
                                class="bg-[#5a3e3e] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#4a3333] transition flex items-center gap-2">
                                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i> Filter
                            </button>

                            <div x-show="open" x-transition x-cloak
                                class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                <div class="bg-white p-6 rounded-2xl w-80 shadow-xl">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-bold text-[#4b3b3b]">Filter Buku</h3>
                                        <button type="button" @click="open = false"
                                            class="text-gray-400 hover:text-gray-600">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Genre</label>
                                            <select name="genre_id" class="w-full p-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#5a3e3e]">
                                                <option value="">Semua Genre</option>
                                                @foreach ($genres as $genre)
                                                    <option value="{{ $genre->id }}" {{ request('genre_id') == $genre->id ? 'selected' : '' }}>
                                                        {{ $genre->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Tipe</label>
                                            <select name="type_id" class="w-full p-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#5a3e3e]">
                                                <option value="">Semua Tipe</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Tahun</label>
                                            <select name="year_id" class="w-full p-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#5a3e3e]">
                                                <option value="">Semua Tahun</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>
                                                        {{ $year->year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="flex justify-between mt-6">
                                        <button type="button" @click="open = false"
                                            class="text-sm text-gray-500 hover:text-gray-700 transition">
                                            Batal
                                        </button>
                                        <button type="submit" @click="open = false"
                                            class="bg-[#5a3e3e] text-white px-5 py-2 rounded-lg text-sm hover:bg-[#4a3333] transition">
                                            Terapkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="grid grid-cols-4 gap-6">
                    @isset($books)
                        @forelse ($books as $book)
                            <x-book-card
                                :id="$book->id"
                                :image="$book->image"
                                :title="$book->title"
                                :author="$book->author"
                                :genre="$book->genres->map(fn($g) => '<span class=\'bg-[#4b3b3b] text-white text-[10px] px-3 py-1 rounded-full mr-1\'>' . $g->name . '</span>')->join('')"
                                :show-atur="auth()->check() && $book->user_id === auth()->id()"
                                :owner-id="$book->user_id"
                                :is-available="$book->isAvailable()"
                            />
                        @empty
                            <div class="col-span-4 flex flex-col items-center justify-center py-16 text-center">
                                <i data-lucide="book-open" class="w-12 h-12 text-[#c9ae8e] mb-3"></i>
                                <p class="text-gray-500 text-sm">Belum ada buku dalam koleksi.</p>
                                <a href="/books/create" class="mt-3 text-sm text-[#5a3e3e] font-medium hover:underline">
                                    + Tambah buku pertamamu
                                </a>
                            </div>
                        @endforelse
                    @endisset
                </div>
            </div>

            <div class="bg-[#e6ddd6] rounded-2xl p-8 shadow-xl">
                <h1 class="text-xl font-semibold text-[#4b3b3b] mb-1">Permintaan Pinjaman Masuk</h1>
                <p class="text-sm text-gray-500 mb-6">Orang yang ingin meminjam buku koleksimu</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($incomingRequests ?? [] as $request)
                        <div class="bg-white p-4 rounded-xl shadow-sm flex items-center justify-between border border-gray-100">
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . $request->book->image) }}" class="w-12 h-16 object-cover rounded-lg">
                                <div>
                                    <p class="font-bold text-[#2c2c2c] text-sm">{{ $request->book->title }}</p>
                                    <p class="text-xs text-[#5a3e3e]">Peminjam: <span class="font-bold">{{ $request->borrower->name }}</span></p>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <form action="/loans/{{ $request->id }}/status" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="dipinjam">
                                    <button class="bg-green-600 text-white text-xs px-3 py-2 rounded-lg font-bold hover:bg-green-700 transition">
                                        Setujui
                                    </button>
                                </form>
                                <form action="/loans/{{ $request->id }}/status" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="ditolak">
                                    <button class="bg-red-500 text-white text-xs px-3 py-2 rounded-lg font-bold hover:bg-red-600 transition">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-10">
                            <p class="text-gray-500 text-sm">Tidak ada permintaan pinjaman saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-[#e6ddd6] rounded-2xl p-8 shadow-xl">
                <h1 class="text-xl font-semibold text-[#4b3b3b] mb-1">Buku Koleksiku yang Dipinjam</h1>
                <p class="text-sm text-gray-500 mb-6">Daftar buku kamu yang sedang dibawa orang lain</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($lentBooks ?? [] as $loan)
                        <div class="bg-white p-4 rounded-xl shadow-sm flex items-center justify-between border border-gray-100">
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . $loan->book->image) }}" class="w-12 h-16 object-cover rounded-lg">
                                <div>
                                    <p class="font-bold text-[#2c2c2c] text-sm">{{ $loan->book->title }}</p>
                                    <p class="text-xs text-[#5a3e3e]">Dipinjam oleh: <span class="font-bold">{{ $loan->borrower->name }}</span></p>
                                    <p class="text-[10px] text-gray-400 italic">Sejak: {{ \Carbon\Carbon::parse($loan->borrowed_at)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <button class="bg-[#5a3e3e] text-white text-xs px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition" 
                                    onclick="alert('Pemberitahuan tagihan telah dikirim ke {{ $loan->borrower->name }}!')">
                                Tagih Buku
                            </button>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-10">
                            <p class="text-gray-500 text-sm">Tidak ada buku kamu yang sedang dipinjam.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-[#e6ddd6] rounded-2xl p-8 shadow-xl">
                <h1 class="text-xl font-semibold text-[#4b3b3b] mb-1">Buku yang Sedang Aku Pinjam</h1>
                <p class="text-sm text-gray-500 mb-6">Buku yang kamu pinjam dari koleksi teman</p>

                <div class="grid grid-cols-4 gap-6">
                    @forelse($myLoans ?? [] as $loan)
                        <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition duration-300 flex flex-col border border-gray-100">
                            <img src="{{ asset('storage/' . $loan->book->image) }}" class="w-full h-48 object-cover">
                            <div class="p-4 flex flex-col flex-1">
                                <p class="font-semibold text-sm text-[#2c2c2c] leading-snug">
                                    {{ $loan->book->title }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $loan->book->author }}</p>

                                <div class="mt-4 flex flex-col gap-2">
                                    <span class="text-[10px] px-2 py-1 rounded-full text-center font-bold 
                                        {{ $loan->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>

                                    @if($loan->status === 'dipinjam')
                                        <form action="/loans/{{ $loan->id }}/return" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="w-full bg-[#4b3b3b] text-white text-[10px] py-2 rounded-lg font-bold hover:bg-gray-700 transition">
                                                Kembalikan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-4 flex flex-col items-center justify-center py-16 text-center">
                            <i data-lucide="book-marked" class="w-12 h-12 text-[#c9ae8e] mb-3"></i>
                            <p class="text-gray-500 text-sm">Belum ada buku yang dipinjam.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>lucide.createIcons();</script>
    <script src="//unpkg.com/alpinejs" defer></script>

</body>

</html>