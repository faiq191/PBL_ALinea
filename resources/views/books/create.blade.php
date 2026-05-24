<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen" x-data="{ mode: '{{ old('source_mode', 'manual') }}' }">

    <x-header />

    <div class="p-8 flex justify-center">
        <div class="max-w-2xl w-full bg-[#e6ddd6] rounded-3xl p-8 shadow-xl">
            <h1 class="text-2xl font-bold text-[#1a3a5c] mb-2">Tambah Buku Baru</h1>
            <p class="text-sm text-gray-500 mb-6">Lengkapi detail buku untuk koleksi Ali.nea</p>

            <div class="flex gap-2 mb-8 bg-white/50 p-2 rounded-2xl">
                <button @click="mode = 'manual'"
                    :class="mode === 'manual' ? 'bg-[#1a3a5c] text-white' : 'text-[#1a3a5c]'"
                    class="flex-1 py-2 rounded-xl text-xs font-bold transition">
                    Input Manual
                </button>
                <button @click="mode = 'existing'"
                    :class="mode === 'existing' ? 'bg-[#1a3a5c] text-white' : 'text-[#1a3a5c]'"
                    class="flex-1 py-2 rounded-xl text-xs font-bold transition">
                    Pilih dari Perpustakaan
                </button>
                <button @click="mode = 'google'"
                    :class="mode === 'google' ? 'bg-[#1a3a5c] text-white' : 'text-[#1a3a5c]'"
                    class="flex-1 py-2 rounded-xl text-xs font-bold transition">
                    Cari Google Books
                </button>
            </div>

            <form action="/books" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="source_mode" :value="mode">

                <div x-show="mode === 'existing'" x-transition>
                    <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Pilih Buku dari Perpustakaan</label>
                    <select name="existing_book_id" class="w-full px-4 py-3 rounded-xl bg-white outline-none focus:ring-2 focus:ring-[#1a3a5c] @error('existing_book_id') ring-2 ring-red-500 @enderror">
                        <option value="">-- Cari Judul Buku --</option>
                        @foreach($allLibraryBooks as $libBook)
                            <option value="{{ $libBook->id }}" {{ old('existing_book_id') == $libBook->id ? 'selected' : '' }}>{{ $libBook->title }} - {{ $libBook->author }}</option>
                        @endforeach
                    </select>
                    @error('existing_book_id')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="mode === 'google'" x-data="{ query: '', results: [], selected: null }" x-transition class="space-y-4">
                    <label class="block text-sm font-bold text-[#1a3a5c]">Cari di Google Books</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="query" placeholder="Ketik judul buku atau pengarang..." class="flex-1 px-4 py-2 rounded-xl bg-white outline-none">
                        <button type="button" @click="if(query) fetch(`/google-books/search?q=${query}`).then(r => r.json()).then(d => results = d)" class="bg-[#1a3a5c] text-white px-5 py-2 rounded-xl font-bold text-sm">Cari</button>
                    </div>

                    <div class="bg-white rounded-xl divide-y divide-gray-100 max-h-60 overflow-y-auto" x-show="results.length > 0">
                        <template x-for="book in results" :key="book.id">
                            <div @click="selected = book.volumeInfo; $refs.googleVolId.value = book.id; results = []" class="p-3 hover:bg-gray-50 cursor-pointer flex items-center gap-3">
                                <img :src="book.volumeInfo.imageLinks?.thumbnail" class="w-9 h-12 object-cover rounded shadow-sm" x-show="book.volumeInfo.imageLinks?.thumbnail">
                                <div>
                                    <p class="font-bold text-sm text-[#1a3a5c]" x-text="book.volumeInfo.title"></p>
                                    <p class="text-xs text-gray-500" x-text="book.volumeInfo.authors ? book.volumeInfo.authors.join(', ') : 'Unknown Author'"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <input type="hidden" name="google_volume_id" x-ref="googleVolId">

                    <div class="bg-white/60 p-4 rounded-xl flex items-center gap-4" x-show="selected">
                        <img :src="selected?.imageLinks?.thumbnail" class="w-12 h-16 object-cover rounded shadow-sm" x-show="selected?.imageLinks?.thumbnail">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Buku Terpilih</p>
                            <p class="text-sm font-bold text-[#1a3a5c]" x-text="selected?.title"></p>
                            <p class="text-xs text-gray-600" x-text="selected?.authors ? selected.authors.join(', ') : ''"></p>
                        </div>
                    </div>
                    @error('google_volume_id')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="mode === 'manual'" x-transition class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Judul Buku</label>
                            <input type="text" name="title" :required="mode === 'manual'" value="{{ old('title') }}"
                                class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#1a3a5c] @error('title') ring-2 ring-red-500 @enderror">
                            @error('title')
                                <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Penulis</label>
                            <input type="text" name="author" :required="mode === 'manual'" value="{{ old('author') }}"
                                class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#1a3a5c] @error('author') ring-2 ring-red-500 @enderror">
                            @error('author')
                                <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Sampul Buku</label>
                        <input type="file" name="image" :required="mode === 'manual'"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#1a3a5c] file:text-white hover:file:bg-[122b45]">
                        @error('image')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Deskripsi</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#1a3a5c]">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div x-show="mode === 'manual'" x-transition class="space-y-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Tipe</label>
                            <select name="type_id" class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Tahun</label>
                            <select name="year_id" class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                                @foreach($years as $year)
                                    <option value="{{ $year->id }}" {{ old('year_id') == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Demografis</label>
                            <select name="demographic_id" class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                                @foreach($demographics as $demo)
                                    <option value="{{ $demo->id }}" {{ old('demographic_id') == $demo->id ? 'selected' : '' }}>{{ $demo->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Pilih Genre</label>
                        <div class="grid grid-cols-3 gap-2 bg-white p-4 rounded-2xl @error('genre_ids') ring-2 ring-red-500 @enderror">
                            @foreach($genres as $genre)
                                <label class="flex items-center gap-2 text-sm text-[#1a3a5c]">
                                    <input type="checkbox" name="genre_ids[]" value="{{ $genre->id }}"
                                        {{ is_array(old('genre_ids')) && in_array($genre->id, old('genre_ids')) ? 'checked' : '' }}
                                        class="rounded text-[#1a3a5c]">
                                    {{ $genre->name }}
                                </label>
                            @endforeach
                        </div>
                        @error('genre_ids')
                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-[#1a3a5c] text-white py-3 rounded-xl font-bold hover:bg-[122b45] transition">
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
