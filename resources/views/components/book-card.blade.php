<div class="bg-white rounded-xl p-4 shadow hover:shadow-xl hover:-translate-y-2 transition duration-300 flex flex-col">

    <img src="{{ asset('storage/' . $image) }}"
        class="w-full h-72 object-cover rounded-lg mb-3">

    <h4 class="font-semibold text-sm text-[#2c2c2c]">
        {{ $title }}
    </h4>

    <p class="text-xs text-gray-500 mt-1">
        {{ $author }}
    </p>

    <span class="inline-block mt-2 text-xs bg-[#5a3e3e] text-white px-3 py-1 rounded-full self-start">
        {{ $genre ?? 'Tanpa Genre' }}
    </span>

    <div class="flex-1"></div>

    <div class="flex gap-2 mt-3">
        <a href="/books/{{ $id }}"
            class="flex-1 bg-gray-200 py-2 rounded-lg text-sm text-center hover:bg-gray-300 transition">
            Lihat
        </a>

        @if($showAtur ?? false)
        <a href="/books/{{ $id }}/edit"
            class="flex-1 bg-[#5a3e3e] text-white py-2 rounded-lg text-sm text-center">
            Atur
        </a>
        @endif
    </div>

</div>
