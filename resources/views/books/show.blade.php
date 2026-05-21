<!DOCTYPE html>
<html lang="id">

<head>
    <title>Lihat Buku - {{ $book->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c] min-h-screen font-sans">

    <x-header />

    <div class="p-8 flex justify-center">
        <div
            class="max-w-6xl w-full bg-[#e6ddd6] p-8 md:p-10 rounded-3xl shadow-xl flex flex-col md:flex-row gap-10 text-[#4b3b3b]">

            <div class="w-full md:w-1/3 lg:w-1/4 shrink-0">
                <img src="{{ \Illuminate\Support\Str::startsWith($book->image, 'http') ? $book->image : asset('storage/' . $book->image) }}"
                    class="w-full aspect-[2/3] object-cover rounded-xl shadow-md mb-8">

                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-2">Informasi</h3>
                    <hr class="border-[#4b3b3b] border-t-[1.5px] mb-4">
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

            <div class="w-full md:w-2/3 lg:w-3/4 flex-1">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight text-[#4b3b3b] mb-8">
                    {{ $book->title }}
                </h1>

                <div class="mb-10">
                    <h3 class="text-xl font-bold mb-2">Sinopsis</h3>
                    <hr class="border-[#4b3b3b] border-t-[1.5px] mb-4">
                    <div class="text-sm leading-loose font-medium space-y-4">
                        {!! strip_tags($book->description ?? 'Deskripsi belum tersedia.', '<br><p><b><i><strong><em><ul><li><ol>') !!}
                    </div>
                </div>

                <div class="mt-10 bg-white/50 p-6 rounded-2xl">
                    <h3 class="text-xl font-bold mb-4">Tersedia di Koleksi:</h3>
                    <div class="space-y-4">
                        @forelse($otherOwners as $otherBook)
                            <div class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-[#5a3e3e] flex items-center justify-center text-white font-bold">
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
                                        <button type="submit"
                                            class="bg-[#5a3e3e] text-white text-xs px-4 py-2 rounded-lg font-bold">
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

                <div class="flex flex-wrap gap-4 mt-8 pt-6 border-t border-[#4b3b3b]/20">
                    <a href="{{ request('from') === 'perpustakaan' ? '/perpustakaan' : '/koleksi' }}"
                        class="inline-flex items-center bg-gray-400 hover:bg-gray-500 text-white font-bold px-6 py-2.5 rounded-xl shadow">
                        ← Kembali
                    </a>
                    @auth
                        @if((auth()->id() === $book->user_id || auth()->user()->is_admin) && request('from') !== 'perpustakaan')
                            <a href="/books/{{ $book->id }}/edit"
                                class="bg-[#5a3e3e] text-white font-bold px-6 py-2.5 rounded-xl shadow">
                                Edit Buku
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</body>

</html>