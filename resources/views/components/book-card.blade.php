@props([
    'id',
    'image',
    'title',
    'author',
    'genre' => null,
    'showAtur' => false,
    'ownerId' => null,
    'isAvailable' => true,
])

<div class="bg-white rounded-xl p-4 shadow hover:shadow-xl hover:-translate-y-2 transition duration-300 flex flex-col">

    <img src="{{ asset('storage/' . $image) }}"
        class="w-full h-72 object-cover rounded-lg mb-3">

    <h4 class="font-semibold text-sm text-[#2c2c2c]">
        {{ $title }}
    </h4>

    <p class="text-xs text-gray-500 mt-1">
        {{ $author }}
    </p>

    <div class="flex flex-wrap gap-1 mt-2">
        {!! $genre ?? '<span class="bg-[#5a3e3e] text-white text-[10px] px-3 py-1 rounded-full">Tanpa Genre</span>' !!}
    </div>

    <div class="flex-1"></div>

    <div class="flex gap-2 mt-3">
        <a href="/books/{{ $id }}?from={{ request()->is('perpustakaan') ? 'perpustakaan' : 'koleksi' }}"
            class="flex-1 bg-gray-200 py-2 rounded-lg text-sm text-center hover:bg-gray-300 transition">
            Lihat
        </a>

        @if($showAtur)
            <a href="/books/{{ $id }}/edit"
                class="flex-1 bg-[#5a3e3e] text-white py-2 rounded-lg text-sm text-center hover:bg-[#4a3333] transition">
                Atur
            </a>
        @elseif(auth()->check() && $ownerId && auth()->id() !== $ownerId)
            @if($isAvailable)
                <form action="/loans/{{ $id }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full bg-[#5a3e3e] text-white py-2 rounded-lg text-sm hover:bg-[#4a3333] transition">
                        Pinjam
                    </button>
                </form>
            @else
                <button disabled
                    class="flex-1 bg-gray-200 text-gray-400 py-2 rounded-lg text-sm cursor-not-allowed">
                    Tidak Tersedia
                </button>
            @endif
        @endif
    </div>

</div>