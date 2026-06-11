<!DOCTYPE html>
<html lang="id">

<head>
    <title>Lihat Buku - {{ $book->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-[#f5f5f5] min-h-screen font-sans pt-10">

    {{-- Header Navigation --}}
    <x-header />

    <div class="p-8 flex justify-center pt-10">
        <div
            class="max-w-6xl w-full bg-[#ffffff] border border-gray-100 p-8 md:p-10 rounded-3xl shadow-xl flex flex-col md:flex-row gap-10 text-[#1a3a5c]">

            {{-- Kiri: Sampul & Informasi --}}
            <div class="w-full md:w-1/3 lg:w-1/4 shrink-0">
                <img src="{{ ($book->image && \Illuminate\Support\Str::startsWith($book->image, 'http')) ? $book->image : asset('storage/' . $book->image) }}"
                    class="w-full aspect-[2/3] object-cover rounded-xl shadow-md mb-8">

                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-2">Informasi</h3>
                    <hr class="border-[#1a3a5c]/20 border-t-[1.5px] mb-4">
                    <ul class="text-sm space-y-4 font-bold">
                        <li>Tipe : <span class="font-normal">{{ $book->type->name ?? '-' }}</span></li>
                        <li>Tahun : <span class="font-normal">{{ $book->year->year ?? '-' }}</span></li>
                        <li>Demografi : <span class="font-normal">{{ $book->demographic->name ?? '-' }}</span></li>
                        <li>Genre : <span class="font-normal">
                                @if($book->genres && $book->genres->count() > 0)
                                    {{ $book->genres->pluck('name')->implode(', ') }}
                                @else
                                    -
                                @endif
                            </span></li>
                        <li>Pengarang : <span class="notranslate font-normal">{{ $book->author ?? '-' }}</span></li>
                    </ul>
                </div>
            </div>

            {{-- Kanan: Judul, Sinopsis, & Aksi --}}
            <div class="w-full md:w-2/3 lg:w-3/4 flex-1">
                <h1 class="notranslate text-3xl md:text-5xl font-extrabold leading-tight text-[#1a3a5c] mb-8">
                    {{ $book->title }}
                </h1>

                {{-- Pesan Galat --}}
                @if($errors->any())
                    <div class="bg-red-500 text-white p-4 rounded-xl mb-6 shadow-md">
                        <ul class="list-disc pl-5 text-sm font-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Sinopsis Buku --}}
                <div class="mb-10">
                    <h3 class="text-xl font-bold mb-2">Sinopsis</h3>
                    <hr class="border-[#1a3a5c]/20 border-t-[1.5px] mb-4">
                    <div class="text-sm leading-loose font-medium space-y-4 text-gray-700">
                        {!! strip_tags($book->description ?? 'Deskripsi belum tersedia.', '<br><p><b><i><strong><em><ul><li><ol>') !!}
                    </div>
                </div>

                {{-- Daftar Pemilik Lain & Ajukan Peminjaman --}}
                <div class="mt-10 bg-[#f8fafc] p-6 rounded-2xl border border-gray-100">
                    <h3 class="text-xl font-bold mb-4">Tersedia di Koleksi:</h3>
                    <div class="space-y-4">
                        @forelse($otherOwners as $otherBook)
                            <div
                                class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                <a href="/users/{{ $otherBook->user->id }}"
                                    class="flex items-center gap-3 hover:opacity-85 transition">
                                    @if($otherBook->user->profile_photo)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($otherBook->user->profile_photo, 'http') ? $otherBook->user->profile_photo : asset('storage/' . $otherBook->user->profile_photo) }}"
                                             class="w-10 h-10 rounded-full object-cover shadow-sm border border-gray-100">
                                    @else
                                        <div
                                            class="notranslate w-10 h-10 rounded-full bg-[#1a3a5c] flex items-center justify-center text-white font-bold shadow-sm">
                                            {{ substr($otherBook->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="notranslate font-bold text-sm hover:underline">
                                            {{ $otherBook->user->name }}</p>
                                        <p class="text-xs text-gray-500">Pemilik Buku</p>
                                    </div>
                                </a>

                                @if($otherBook->isAvailable())
                                    <form action="/loans/{{ $otherBook->id }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-[#1a3a5c] text-white text-xs px-4 py-2 rounded-lg font-bold hover:bg-[#122b45] transition shadow-md">
                                            Ajukan Peminjaman
                                        </button>
                                    </form>
                                @else
                                    <span
                                        class="text-xs text-red-500 font-bold italic bg-red-50 px-3 py-1.5 rounded-lg border border-red-100">Sedang
                                        Dipinjam</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic">Belum ada pengguna lain yang mengoleksi buku ini.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Kumpulan Tombol Aksi --}}
                <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t border-gray-200">

                     @php
                        $previousUrl = url()->previous();
                        if ($previousUrl === request()->url()) {
                            $previousUrl = '/perpustakaan';
                        }
                     @endphp
                     {{-- Tombol Kembali --}}
                     <a href="{{ $previousUrl }}"
                         class="inline-flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-6 py-2.5 rounded-xl transition">
                         ← Kembali
                     </a>

                    <button type="button" onclick="openBookReportModal({{ $book->id }}, '{{ addslashes($book->title) }}', {{ $book->user_id ?? 0 }}, '{{ addslashes($book->user->name ?? 'Pengunggah') }}')" class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold px-6 py-2.5 rounded-xl transition flex items-center gap-2">
                        <i data-lucide="flag" class="w-4 h-4"></i>
                        Laporkan Buku
                    </button>

                    @auth
                        @php
                            $userOwnsBook = \App\Models\Book::where('user_id', auth()->id())
                                ->where('title', $book->title)
                                ->exists();
                        @endphp

                        {{-- Tombol Sunting & Hapus Buku --}}
                        @if(!isset($book->is_google_api) || !$book->is_google_api)
                            @if(auth()->id() === $book->user_id || auth()->user()->is_admin)
                                <a href="/books/{{ $book->id }}/edit"
                                    class="bg-[#e8edf2] text-[#1a3a5c] font-bold px-6 py-2.5 rounded-xl hover:bg-[#d0e4f5] transition">
                                    Sunting Buku
                                </a>

                                {{-- Tombol Hapus Buku --}}
                                <form action="/books/{{ $book->id }}" method="POST" class="inline-flex"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini? Tindakan ini akan menghapus data secara permanen.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Buku
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- Tombol Tambahkan ke Koleksi --}}
                        @if(!$userOwnsBook)
                            <form action="/books" method="POST" class="inline-flex">
                                @csrf
                                @if(isset($book->is_google_api) && $book->is_google_api)
                                    <input type="hidden" name="source_mode" value="google">
                                    <input type="hidden" name="google_volume_id" value="{{ $book->google_id }}">
                                @else
                                    <input type="hidden" name="source_mode" value="existing">
                                    <input type="hidden" name="existing_book_id" value="{{ $book->id }}">
                                @endif
                                <button type="submit"
                                    class="bg-[#1a3a5c] hover:bg-[#122b45] text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition">
                                    Tambahkan ke Koleksi
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
    <x-book-report-modal />
</body>

</html>