<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lihat Buku - {{ $book->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen font-sans pt-10">
    
    {{-- Header Navigation --}}
    <x-header />
    
    <div class="p-8 flex justify-center pt-10">
        <div class="max-w-6xl w-full bg-[#e6ddd6] p-8 md:p-10 rounded-3xl shadow-xl flex flex-col md:flex-row gap-10 text-[#1a3a5c]">
            
            {{-- Kiri: Sampul & Informasi --}}
            <div class="w-full md:w-1/3 lg:w-1/4 shrink-0">
                <img src="{{ \Illuminate\Support\Str::startsWith($book->image, 'http') ? $book->image : asset('storage/' . $book->image) }}"
                    class="w-full aspect-[2/3] object-cover rounded-xl shadow-md mb-8">
                
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-2">Informasi</h3>
                    <hr class="border-[#1a3a5c] border-t-[1.5px] mb-4">
                    <ul class="text-sm space-y-4 font-bold">
                        <li>Type : <span class="font-normal">{{ $book->type->name ?? '-' }}</span></li>
                        <li>Tahun : <span class="font-normal">{{ $book->year->year ?? '-' }}</span></li>
                        <li>Demografis : <span class="font-normal">{{ $book->demographic->name ?? '-' }}</span></li>
                        <li>Genre : <span class="font-normal">
                                @if($book->genres && $book->genres->count() > 0)
                                    {{ $book->genres->pluck('name')->implode(', ') }}
                                @else
                                    -
                                @endif
                            </span></li>
                        <li>Pengarang : <span class="font-normal">{{ $book->author ?? '-' }}</span></li>
                    </ul>
                </div>
            </div>

            {{-- Kanan: Judul, Sinopsis, & Aksi --}}
            <div class="w-full md:w-2/3 lg:w-3/4 flex-1">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight text-[#1a3a5c] mb-8">
                    {{ $book->title }}
                </h1>
                
                {{-- Pesan Error (Jika form Tambah ke Koleksi gagal) --}}
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
                    <hr class="border-[#1a3a5c] border-t-[1.5px] mb-4">
                    <div class="text-sm leading-loose font-medium space-y-4">
                        {!! strip_tags($book->description ?? 'Deskripsi belum tersedia.', '<br><p><b><i><strong><em><ul><li><ol>') !!}
                    </div>
                </div>

                {{-- Daftar Pemilik Lain & Request Pinjam --}}
                <div class="mt-10 bg-white/50 p-6 rounded-2xl">
                    <h3 class="text-xl font-bold mb-4">Tersedia di Koleksi:</h3>
                    <div class="space-y-4">
                        @forelse($otherOwners as $otherBook)
                            <div class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#1a3a5c] flex items-center justify-center text-white font-bold">
                                        {{ substr($otherBook->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm">{{ $otherBook->user->name }}</p>
                                        <p class="text-xs text-gray-500">Pemilik Buku</p>
                                    </div>
                                </div>

                                @if($otherBook->isAvailable())
                                    <form action="/loans/{{ $otherBook->id }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-[#1a3a5c] text-white text-xs px-4 py-2 rounded-lg font-bold hover:bg-[#122b45] transition">
                                            Request Pinjam
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-red-500 font-bold italic">Sedang Dipinjam</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic">Belum ada pengguna lain yang mengoleksi buku ini.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Kumpulan Tombol Aksi --}}
                <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t border-[#1a3a5c]/20">
                    
                    {{-- Tombol Kembali --}}
                    <a href="{{ url()->previous() }}"
                        class="inline-flex items-center bg-gray-400 hover:bg-gray-500 text-white font-bold px-6 py-2.5 rounded-xl shadow transition">
                        ← Kembali
                    </a>
                    
                    @auth
                        @php
                            $userOwnsBook = \App\Models\Book::where('user_id', auth()->id())
                                ->where('title', $book->title)
                                ->exists();
                        @endphp

                        {{-- Tombol Edit Buku (Hanya muncul jika buku milik user & berasal dari database lokal) --}}
                        @if(!isset($book->is_google_api) || !$book->is_google_api)
                            @if(auth()->id() === $book->user_id || auth()->user()->is_admin)
                                <a href="/books/{{ $book->id }}/edit" class="bg-[#1a3a5c] text-white font-bold px-6 py-2.5 rounded-xl shadow hover:bg-[#122b45] transition">
                                    Edit Buku
                                </a>
                            @endif
                        @endif

                        {{-- Tombol Tambahkan ke Koleksi (Hanya muncul jika user belum memiliki buku ini) --}}
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
                                <button type="submit" class="bg-[#1a3a5c] hover:bg-[#122b45] text-white font-bold px-6 py-2.5 rounded-xl shadow transition">
                                    Tambahkan ke Koleksi
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</body>
</html>