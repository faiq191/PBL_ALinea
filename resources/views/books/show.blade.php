<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lihat Buku - {{ $book->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#2c2c2c] min-h-screen p-8 font-sans">

    <x-header />

    <!-- Container Utama -->
    <div class="max-w-6xl mx-auto mt-6 bg-[#e6ddd6] p-8 md:p-10 rounded-3xl shadow-xl flex flex-col md:flex-row gap-10 text-[#4b3b3b]">

        <!-- KOLOM KIRI (Gambar & Info) -->
        <div class="w-full md:w-1/3 lg:w-1/4 shrink-0">
            <!-- Gambar Buku -->
            <img src="{{ asset('storage/' . $book->image) }}" 
                 class="w-full aspect-[2/3] object-cover rounded-xl shadow-md mb-8">

            <!-- Bagian Informasi -->
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-2">Informasi</h3>
                <hr class="border-[#4b3b3b] border-t-[1.5px] mb-4">
                <ul class="text-sm space-y-4 font-bold">
                    <!-- Mengambil data dari tabel relasi yang di-load di controller -->
                    <li>Type : <span class="font-normal">{{ $book->type->name ?? '-' }}</span></li>
                    <li>Tahun : <span class="font-normal">{{ $book->year->year ?? '-' }}</span></li>
                    <li>Demografis : <span class="font-normal">{{ $book->demographic->name ?? '-' }}</span></li>
                    
                    <!-- Menampilkan Genre yang bisa lebih dari satu -->
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

        <!-- KOLOM KANAN (Judul, Sinopsis, Tombol Aksi) -->
        <div class="w-full md:w-2/3 lg:w-3/4 flex-1">
            
            <!-- Header Judul -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight text-[#4b3b3b]">
                    {{ $book->title }}
                </h1>
            </div>

            <!-- Bagian Sinopsis / Deskripsi -->
            <div class="mb-10">
                <h3 class="text-xl font-bold mb-2">Sinopsis</h3>
                <hr class="border-[#4b3b3b] border-t-[1.5px] mb-4">
                <div class="text-sm leading-loose font-medium space-y-4">
                    <!-- nl2br untuk mempertahankan enter/paragraf -->
                    <p>{!! nl2br(e($book->description ?? 'Deskripsi belum tersedia.')) !!}</p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t border-[#4b3b3b]/20">
                <a href="/koleksi" 
                   class="inline-flex items-center bg-gray-400 hover:bg-gray-500 transition duration-200 text-white font-bold px-6 py-2.5 rounded-xl shadow">
                    ← Kembali
                </a>

                @auth
                    <!-- Menyesuaikan dengan logika Otorisasi di Controller (Hanya Pemilik & Admin) -->
                    @if(auth()->id() === $book->user_id || auth()->user()->is_admin)
                        <a href="/books/{{ $book->id }}/edit" 
                           class="bg-[#5a3e3e] hover:bg-[#4a3333] transition duration-200 text-white font-bold px-6 py-2.5 rounded-xl shadow">
                            Edit Buku
                        </a>

                        <form action="/books/{{ $book->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button 
                                onclick="return confirm('Yakin mau hapus buku ini?')" 
                                class="bg-red-500 hover:bg-red-600 transition duration-200 text-white font-bold px-6 py-2.5 rounded-xl shadow">
                                Hapus Buku
                            </button>
                        </form>
                    @endif
                @endauth
            </div>

        </div>
    </div>
</body>
</html>