<!DOCTYPE html>
<html>
<head>
    <title>Buku Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="bg-[#f5f5f5] min-h-screen">

    <x-header />

    <div class="pt-20 px-8 pb-8">

        <div class="grid grid-cols-2 gap-6 mb-6">

            {{-- KATALOG BUKU --}}
            <div class="bg-white rounded-2xl p-6 shadow-xl flex flex-col">
                <div class="flex items-center justify-between mb-1">
                    <h1 class="text-lg font-semibold text-[#1a3a5c]">Katalog Buku</h1>
                    <a href="/books/create"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition flex items-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i> Tambah
                    </a>
                </div>
                <p class="text-xs text-gray-500 mb-4">Koleksi pribadi</p>

                <form method="GET" action="/koleksi" class="mb-4">
                    <div class="flex items-center gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari judul atau penulis..."
                            class="flex-1 px-3 py-2 rounded-full bg-[#e8edf2] outline-none text-xs placeholder-gray-400">
                        <button type="submit" class="bg-[#1a3a5c] text-white px-4 py-2 rounded-full text-xs hover:bg-[#122b45] transition">
                            Cari
                        </button>
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true"
                                class="bg-[#1a3a5c] text-white px-3 py-2 rounded-lg text-xs hover:bg-[#122b45] transition flex items-center gap-1">
                                <i data-lucide="sliders-horizontal" class="w-3 h-3"></i>
                            </button>
                            <div x-show="open" x-transition x-cloak
                                class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                <div class="bg-white p-6 rounded-2xl w-80 shadow-xl">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="font-bold text-[#1a3a5c]">Filter Buku</h3>
                                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Genre</label>
                                            <select name="genre_id" class="w-full p-2 border border-gray-200 rounded-lg text-sm outline-none">
                                                <option value="">Semua Genre</option>
                                                @foreach ($genres as $genre)
                                                    <option value="{{ $genre->id }}" {{ request('genre_id') == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Tipe</label>
                                            <select name="type_id" class="w-full p-2 border border-gray-200 rounded-lg text-sm outline-none">
                                                <option value="">Semua Tipe</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Tahun</label>
                                            <select name="year_id" class="w-full p-2 border border-gray-200 rounded-lg text-sm outline-none">
                                                <option value="">Semua Tahun</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex justify-between mt-6">
                                        <button type="button" @click="open = false" class="text-sm text-gray-500 hover:text-gray-700">Batal</button>
                                        <button type="submit" @click="open = false" class="bg-[#1a3a5c] text-white px-5 py-2 rounded-lg text-sm hover:bg-[#122b45] transition">Terapkan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="grid grid-cols-3 gap-3 overflow-y-auto max-h-96">
                    @isset($books)
                        @forelse ($books as $book)
                            <x-book-card
                                :id="$book->id"
                                :image="$book->image"
                                :title="$book->title"
                                :author="$book->author"
                                :genre="$book->genres->map(fn($g) => '<span class=\'bg-[#1a3a5c] text-white text-[10px] px-2 py-0.5 rounded-full mr-1\'>' . $g->name . '</span>')->join('')"
                                :show-atur="auth()->check() && $book->user_id === auth()->id()"
                                :owner-id="$book->user_id"
                                :is-available="$book->isAvailable()"
                            />
                        @empty
                            <div class="col-span-3 flex flex-col items-center justify-center py-10 text-center">
                                <i data-lucide="book-open" class="w-10 h-10 text-[#b0c8e0] mb-2"></i>
                                <p class="text-gray-500 text-xs">Belum ada buku.</p>
                                <a href="/books/create" class="mt-2 text-xs text-[#1a3a5c] font-medium hover:underline">+ Tambah buku</a>
                            </div>
                        @endforelse
                    @endisset
                </div>
            </div>

            {{-- BUKU YANG SEDANG DIPINJAM --}}
            <div class="bg-white rounded-2xl p-6 shadow-xl flex flex-col">
                <h1 class="text-lg font-semibold text-[#1a3a5c] mb-1">Sedang Dipinjam</h1>
                <p class="text-xs text-gray-500 mb-4">Dari koleksi teman</p>

                <div class="grid grid-cols-3 gap-3 overflow-y-auto max-h-96">
                    @forelse($myLoans ?? [] as $loan)
                        <div class="bg-[#f5f5f5] rounded-xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
                            <img src="{{ \Illuminate\Support\Str::startsWith($loan->book->image, 'http') ? $loan->book->image : asset('storage/' . $loan->book->image) }}" class="w-full h-32 object-cover">
                            <div class="p-3 flex flex-col flex-1">
                                <p class="font-semibold text-xs text-[#1a3a5c] leading-snug">{{ $loan->book->title }}</p>
                                <p class="text-[10px] text-gray-400 mt-1">{{ $loan->book->author }}</p>
                                <div class="mt-2 flex flex-col gap-1">
                                    <span class="text-[10px] px-2 py-1 rounded-full text-center font-bold
                                        {{ $loan->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                    @if($loan->status === 'dipinjam')
                                        <form action="/loans/{{ $loan->id }}/return" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="w-full bg-[#1a3a5c] text-white text-[10px] py-1.5 rounded-lg font-bold hover:bg-gray-700 transition">
                                                Kembalikan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 flex flex-col items-center justify-center py-10 text-center">
                            <i data-lucide="book-marked" class="w-10 h-10 text-[#b0c8e0] mb-2"></i>
                            <p class="text-gray-500 text-xs">Belum ada.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="grid grid-cols-2 gap-6">

            {{-- PERMINTAAN PINJAMAN MASUK --}}
            <div class="bg-white rounded-2xl p-6 shadow-xl">
                <h1 class="text-lg font-semibold text-[#1a3a5c] mb-1">Permintaan Masuk</h1>
                <p class="text-xs text-gray-500 mb-4">Ingin meminjam bukumu</p>

                <div class="space-y-3 overflow-y-auto max-h-72">
                    @forelse($incomingRequests ?? [] as $request)
                        <div class="bg-[#f5f5f5] p-3 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ \Illuminate\Support\Str::startsWith($request->book->image, 'http') ? $request->book->image : asset('storage/' . $request->book->image) }}" class="w-10 h-14 object-cover rounded-lg">
                                <div>
                                    <p class="font-bold text-[#1a3a5c] text-xs">{{ $request->book->title }}</p>
                                    <p class="text-[10px] text-gray-500">Peminjam: <span class="font-bold">{{ $request->borrower->name }}</span></p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <form action="/loans/{{ $request->id }}/status" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="dipinjam">
                                    <button class="bg-green-600 text-white text-[10px] px-3 py-1.5 rounded-lg font-bold hover:bg-green-700 transition">Setujui</button>
                                </form>
                                <form action="/loans/{{ $request->id }}/status" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="ditolak">
                                    <button class="bg-red-500 text-white text-[10px] px-3 py-1.5 rounded-lg font-bold hover:bg-red-600 transition">Tolak</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <i data-lucide="inbox" class="w-10 h-10 text-[#b0c8e0] mb-2 mx-auto"></i>
                            <p class="text-gray-500 text-xs">Belum ada permintaan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- BUKU KOLEKSI YANG DIPINJAM --}}
            <div class="bg-white rounded-2xl p-6 shadow-xl">
                <h1 class="text-lg font-semibold text-[#1a3a5c] mb-1">Buku Dipinjam</h1>
                <p class="text-xs text-gray-500 mb-4">Sedang dibawa orang lain</p>

                <div class="space-y-3 overflow-y-auto max-h-72">
                    @forelse($lentBooks ?? [] as $loan)
                        <div class="bg-[#f5f5f5] p-3 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ \Illuminate\Support\Str::startsWith($loan->book->image, 'http') ? $loan->book->image : asset('storage/' . $loan->book->image) }}" class="w-10 h-14 object-cover rounded-lg">
                                <div>
                                    <p class="font-bold text-[#1a3a5c] text-xs">{{ $loan->book->title }}</p>
                                    <p class="text-[10px] text-gray-500">Dipinjam: <span class="font-bold">{{ $loan->borrower->name }}</span></p>
                                    <p class="text-[10px] text-gray-400 italic">{{ \Carbon\Carbon::parse($loan->borrowed_at)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <button class="bg-[#1a3a5c] text-white text-[10px] px-3 py-1.5 rounded-lg font-bold hover:bg-red-700 transition"
                                onclick="alert('Tagihan dikirim ke {{ $loan->borrower->name }}!')">
                                Tagih
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <i data-lucide="book-check" class="w-10 h-10 text-[#b0c8e0] mb-2 mx-auto"></i>
                            <p class="text-gray-500 text-xs">Belum ada.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <x-footer />

    <script>lucide.createIcons();</script>
    <script src="//unpkg.com/alpinejs" defer></script>

</body>
</html>
