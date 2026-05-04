@props(['genres'])

<div x-data="{ open: false }">

    <button @click="open = true"
        class="bg-[#5a3e3e] text-white px-4 py-2 rounded-lg">
        Filter
    </button>

    <div x-show="open"
        class="fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white p-6 rounded-xl w-80">

            <h3 class="font-bold mb-4">Filter Genre</h3>

            <form method="GET">

                <select name="genre" class="w-full p-2 border rounded mb-4">
                    <option value="">Semua</option>

                    @foreach ($genres as $genre)
                        <option value="{{ $genre }}">
                            {{ $genre }}
                        </option>
                    @endforeach

                </select>

                <div class="flex justify-between">
                    <button type="button" @click="open=false">Batal</button>

                    <button class="bg-[#5a3e3e] text-white px-4 py-2 rounded">
                        Terapkan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
