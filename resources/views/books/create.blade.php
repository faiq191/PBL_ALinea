<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#2c2c2c] min-h-screen" x-data="{ mode: 'manual' }">

    <x-header />

    <div class="p-8 flex justify-center">
        <div class="max-w-2xl w-full bg-[#e6ddd6] rounded-3xl p-8 shadow-xl">
            <h1 class="text-2xl font-bold text-[#4b3b3b] mb-2">Tambah Buku Baru</h1>
            <p class="text-sm text-gray-500 mb-6">Lengkapi detail buku untuk koleksi Ali.nea</p>

            <div class="flex gap-4 mb-8 bg-white/50 p-2 rounded-2xl">
                <button @click="mode = 'manual'" 
                    :class="mode === 'manual' ? 'bg-[#5a3e3e] text-white' : 'text-[#5a3e3e]'"
                    class="flex-1 py-2 rounded-xl font-bold transition">
                    Input Manual
                </button>
                <button @click="mode = 'existing'" 
                    :class="mode === 'existing' ? 'bg-[#5a3e3e] text-white' : 'text-[#5a3e3e]'"
                    class="flex-1 py-2 rounded-xl font-bold transition">
                    Pilih dari Perpustakaan
                </button>
            </div>

            <form action="/books" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="source_mode" :value="mode">

                <div x-show="mode === 'existing'" x-transition>
                    <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Pilih Buku dari Perpustakaan</label>
                    <select name="existing_book_id" class="w-full px-4 py-3 rounded-xl bg-white outline-none focus:ring-2 focus:ring-[#5a3e3e]">
                        <option value="">-- Cari Judul Buku --</option>
                        @foreach($allLibraryBooks as $libBook)
                            <option value="{{ $libBook->id }}">{{ $libBook->title }} - {{ $libBook->author }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="mode === 'manual'" x-transition class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Judul Buku</label>
                            <input type="text" name="title" :required="mode === 'manual'"
                                class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#5a3e3e]">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Penulis</label>
                            <input type="text" name="author" :required="mode === 'manual'"
                                class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#5a3e3e]">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Tipe</label>
                            <select name="type_id" :required="mode === 'manual'" class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Tahun</label>
                            <select name="year_id" :required="mode === 'manual'" class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                                @foreach($years as $year)
                                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Demografis</label>
                            <select name="demographic_id" :required="mode === 'manual'" class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                                @foreach($demographics as $demo)
                                    <option value="{{ $demo->id }}">{{ $demo->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Pilih Genre</label>
                        <div class="grid grid-cols-3 gap-2 bg-white p-4 rounded-2xl">
                            @foreach($genres as $genre)
                                <label class="flex items-center gap-2 text-sm text-[#4b3b3b]">
                                    <input type="checkbox" name="genre_ids[]" value="{{ $genre->id }}" class="rounded text-[#5a3e3e]">
                                    {{ $genre->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Sampul Buku</label>
                        <input type="file" name="image" :required="mode === 'manual'"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#5a3e3e] file:text-white hover:file:bg-[#4a3333]">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#5a3e3e]"></textarea>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-[#5a3e3e] text-white py-3 rounded-xl font-bold hover:bg-[#4a3333] transition">
                        Simpan ke Koleksi
                    </button>
                    <a href="/koleksi" class="px-8 py-3 bg-gray-400 text-white rounded-xl font-bold hover:bg-gray-500 transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>