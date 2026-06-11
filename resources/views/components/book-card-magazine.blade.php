@props(['id','image','title','author','firstGenre'=>null,'ownerId'=>null,'isAvailable'=>true,'isGoogleApi'=>false])

<a href="/books/{{ $id }}"
    class="group relative block rounded-2xl overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 h-full min-h-[200px] bg-gray-200">

    <img src="{{ ($image && \Illuminate\Support\Str::startsWith($image, 'http')) ? $image : asset('storage/' . $image) }}"
        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></div>

    {{-- Bottom info --}}
    <div class="absolute bottom-0 left-0 right-0 p-4 z-10 flex items-end justify-between">
        <div class="flex-1 min-w-0 pr-2">
            <p class="notranslate text-white font-bold text-sm uppercase tracking-wide leading-tight line-clamp-2">{{ $title }}</p>
            @if($firstGenre)
                <p class="text-white/60 text-[10px] mt-1 uppercase tracking-widest">{{ $firstGenre }}</p>
            @endif
        </div>
        <div class="flex flex-col items-center gap-1 flex-shrink-0">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($author) }}&background=d0e4f5&color=1a3a5c&size=64&bold=true"
                class="w-9 h-9 rounded-full border-2 border-white shadow">

        </div>
    </div>

    {{-- Hover pinjam button --}}
    @if(!$isGoogleApi && auth()->check() && $ownerId && auth()->id() !== $ownerId)
        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20">
            @if($isAvailable)
                <form action="/loans/{{ $id }}" method="POST" onclick="event.stopPropagation()">
                    @csrf
                    <button type="submit" class="bg-white text-[#1a3a5c] font-bold px-6 py-2 rounded-full text-sm shadow-lg hover:bg-[#1a3a5c] hover:text-white transition">Pinjam</button>
                </form>
            @else
                <span class="bg-white/80 text-gray-500 font-bold px-6 py-2 rounded-full text-sm">Tidak Tersedia</span>
            @endif
        </div>
    @endif
</a>
