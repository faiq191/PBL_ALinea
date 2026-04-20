<div class="bg-white rounded-xl p-4 shadow hover:shadow-xl hover:-translate-y-2 transition duration-300">

    <img src="{{ asset('storage/' . $image) }}"
        class="w-full h-72 object-cover rounded-lg mb-4">

    <h4 class="font-semibold text-sm text-[#2c2c2c]">
        {{ $title }}
    </h4>

    <p class="text-xs text-gray-500 mb-4">
        {{ $author }}
    </p>

    <div class="flex gap-2">
        <button
            class="flex-1 bg-gray-200 py-2 rounded-lg text-sm hover:bg-gray-300 transition flex items-center justify-center gap-2">
            <i data-lucide="eye"></i>
            Lihat
        </button>

        <button
            class="flex-1 bg-[#5a3e3e] text-white py-2 rounded-lg text-sm flex items-center justify-center gap-2">
            <i data-lucide="square-pen"></i>
            Atur
        </button>
    </div>

</div>
