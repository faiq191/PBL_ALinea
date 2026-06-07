<!DOCTYPE html>
<html>
<head>
    <title>Buat Diskusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen" x-data="{ 
    mode: '{{ old('source_mode', 'manual') }}', 
    cover_source: '{{ old('cover_source', 'file') }}',
    imageUrl: '{{ old('image_url', '') }}',
    filePreview: null,
    handleFileChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.filePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            this.filePreview = null;
        }
    }
}">
    <x-header />

    <div class="p-8 pt-24 flex justify-center">
        <div class="max-w-2xl w-full bg-[#ffffff] rounded-3xl p-8 shadow-xl border border-gray-100">
            <h1 class="text-2xl font-bold text-[#1a3a5c] mb-2">Mulai Diskusi Baru</h1>
            <p class="text-sm text-gray-500 mb-6">Pilih buku yang ingin didiskusikan dan sampaikan pikiranmu.</p>

            <div class="flex gap-2 mb-8 bg-[#e8edf2] p-2 rounded-2xl">
                <button type="button" @click="mode = 'manual'" :class="mode === 'manual' ? 'bg-[#1a3a5c] text-white shadow-md' : 'text-[#1a3a5c] hover:bg-white/50'" class="flex-1 py-2.5 rounded-xl text-xs font-bold transition">
                    Masukan Manual
                </button>
                <button type="button" @click="mode = 'existing'" :class="mode === 'existing' ? 'bg-[#1a3a5c] text-white shadow-md' : 'text-[#1a3a5c] hover:bg-white/50'" class="flex-1 py-2.5 rounded-xl text-xs font-bold transition">
                    Pilih dari Perpustakaan
                </button>
                <button type="button" @click="mode = 'google'" :class="mode === 'google' ? 'bg-[#1a3a5c] text-white shadow-md' : 'text-[#1a3a5c] hover:bg-white/50'" class="flex-1 py-2.5 rounded-xl text-xs font-bold transition">
                    Cari di Google Books
                </button>
            </div>

            <form action="/diskusi" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="source_mode" :value="mode">

                <div x-show="mode === 'existing'" x-transition x-cloak>
                    <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Pilih Buku Referensi</label>
                    <select name="existing_book_id" class="notranslate w-full px-4 py-3 rounded-xl bg-[#e8edf2] outline-none text-sm text-[#1a3a5c] focus:ring-2 focus:ring-[#1a3a5c] @error('existing_book_id') ring-2 ring-red-500 @enderror">
                        <option value="">-- Cari Judul Buku --</option>
                        @foreach($allLibraryBooks as $libBook)
                            <option value="{{ $libBook->id }}" {{ old('existing_book_id') == $libBook->id ? 'selected' : '' }}>{{ $libBook->title }} - {{ $libBook->author }}</option>
                        @endforeach
                    </select>
                    @error('existing_book_id')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="mode === 'google'" x-data="{ query: '', results: [], selected: null }" x-transition x-cloak class="space-y-4">
                    <label class="block text-sm font-bold text-[#1a3a5c]">Cari Buku Referensi</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="query" placeholder="Ketik judul buku..." class="flex-1 px-4 py-3 rounded-xl bg-[#e8edf2] outline-none text-sm text-[#1a3a5c] focus:ring-2 focus:ring-[#1a3a5c]">
                        <button type="button" @click="if(query) fetch(`/google-books/search?q=${query}`).then(r => r.json()).then(d => results = d)" class="bg-[#1a3a5c] text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-[#122b45] transition">Cari</button>
                    </div>

                    <div class="bg-[#e8edf2] rounded-xl divide-y divide-white/50 max-h-60 overflow-y-auto" x-show="results.length > 0">
                        <template x-for="book in results" :key="book.id">
                            <div @click="selected = book.volumeInfo; $refs.googleVolId.value = book.id; results = []" class="p-3 hover:bg-white cursor-pointer flex items-center gap-3 transition">
                                <img :src="book.volumeInfo.imageLinks?.thumbnail" class="w-9 h-12 object-cover rounded shadow-sm" x-show="book.volumeInfo.imageLinks?.thumbnail">
                                <div>
                                    <p class="notranslate font-bold text-sm text-[#1a3a5c]" x-text="book.volumeInfo.title"></p>
                                    <p class="notranslate text-xs text-gray-500" x-text="book.volumeInfo.authors ? book.volumeInfo.authors.join(', ') : 'Unknown Author'"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <input type="hidden" name="google_volume_id" x-ref="googleVolId">

                    <div class="bg-[#e8edf2] p-4 rounded-xl flex items-center gap-4" x-show="selected">
                        <img :src="selected?.imageLinks?.thumbnail" class="w-12 h-16 object-cover rounded shadow-sm" x-show="selected?.imageLinks?.thumbnail">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Referensi Terpilih</p>
                            <p class="notranslate text-sm font-bold text-[#1a3a5c]" x-text="selected?.title"></p>
                        </div>
                    </div>
                    @error('google_volume_id')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="mode === 'manual'" x-transition x-cloak class="space-y-6">
                    <!-- Image Upload Card -->
                    <div class="space-y-3">
                            <label class="block text-sm font-bold text-[#1a3a5c]">Gambar Referensi (Opsional)</label>
                            
                            <div class="bg-white/40 p-5 rounded-2xl border border-white/50 shadow-inner flex flex-col sm:flex-row gap-6 items-center">
                                <!-- Live Image Preview -->
                                <div class="flex-shrink-0 relative group">
                                    <!-- File Preview -->
                                    <template x-if="cover_source === 'file' && filePreview">
                                        <img :src="filePreview" class="w-24 h-32 object-cover rounded-xl shadow-md border-2 border-white transition group-hover:scale-105 duration-300">
                                    </template>
                                    
                                    <!-- URL Preview -->
                                    <template x-if="cover_source === 'url' && imageUrl">
                                        <img :src="imageUrl" x-on:error="$el.src = 'https://placehold.co/100x140?text=Galat'" class="w-24 h-32 object-cover rounded-xl shadow-md border-2 border-white transition group-hover:scale-105 duration-300">
                                    </template>

                                    <!-- Default Placeholder -->
                                    <template x-if="(cover_source === 'file' && !filePreview) || (cover_source === 'url' && !imageUrl)">
                                        <div class="w-24 h-32 bg-white/60 rounded-xl flex flex-col items-center justify-center border border-dashed border-[#1a3a5c]/35 text-[#1a3a5c]/60 shadow">
                                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-[9px] font-bold uppercase tracking-wider">Pratinjau</span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Upload / Link Controls -->
                                <div class="flex-1 w-full space-y-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <label class="block text-xs font-bold text-[#1a3a5c] uppercase tracking-wide">Pilih Sumber</label>
                                        
                                        <!-- Pill Switcher -->
                                        <div class="inline-flex p-1 bg-gray-200/60 rounded-xl">
                                            <button type="button" @click="cover_source = 'file'"
                                                :class="cover_source === 'file' ? 'bg-[#1a3a5c] text-white shadow-sm' : 'text-gray-500 hover:text-[#1a3a5c]'"
                                                class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all duration-200">
                                                Berkas
                                            </button>
                                            <button type="button" @click="cover_source = 'url'"
                                                :class="cover_source === 'url' ? 'bg-[#1a3a5c] text-white shadow-sm' : 'text-gray-500 hover:text-[#1a3a5c]'"
                                                class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all duration-200">
                                                Tautan URL
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="cover_source" :value="cover_source">

                                    <!-- File Input Zone -->
                                    <div x-show="cover_source === 'file'" x-transition class="space-y-2">
                                        <div class="relative flex items-center justify-center border border-dashed border-gray-300 hover:border-[#1a3a5c] bg-white/50 rounded-xl p-4 transition cursor-pointer group">
                                            <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="disc-image-input" @change="handleFileChange($event); document.getElementById('disc-file-chosen').textContent = $event.target.files[0]?.name || 'Pilih berkas gambar...'">
                                            <div class="text-center space-y-1.5 pointer-events-none">
                                                <svg class="w-6 h-6 mx-auto text-gray-400 group-hover:text-[#1a3a5c] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                <p id="disc-file-chosen" class="text-xs text-gray-500 font-medium group-hover:text-[#1a3a5c] transition">Pilih berkas gambar...</p>
                                            </div>
                                        </div>
                                        @error('image')
                                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- URL Input -->
                                    <div x-show="cover_source === 'url'" x-transition class="space-y-2">
                                        <div class="relative rounded-xl shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            </div>
                                            <input type="url" name="image_url" x-model="imageUrl"
                                                placeholder="https://example.com/gambar.jpg"
                                                class="block w-full pl-9 pr-4 py-2.5 text-sm rounded-xl bg-white border border-gray-200 outline-none focus:ring-2 focus:ring-[#1a3a5c] transition">
                                        </div>
                                        @error('image_url')
                                            <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Genre Selector -->
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-[#1a3a5c]">Genre Manual</label>
                            <div class="bg-white/40 p-4 rounded-2xl border border-white/50 shadow-inner h-[68px] flex items-center">
                                <select name="genre" class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 outline-none text-sm text-[#1a3a5c] focus:ring-2 focus:ring-[#1a3a5c] transition @error('genre') ring-2 ring-red-500 @enderror">
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->name }}">{{ $genre->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('genre')
                                <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                <hr class="border-gray-100">

                <div>
                    <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Judul Diskusi</label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Apa yang ingin kamu diskusikan?" class="notranslate w-full px-4 py-3 rounded-xl bg-[#e8edf2] border-none outline-none text-sm text-[#1a3a5c] focus:ring-2 focus:ring-[#1a3a5c] @error('title') ring-2 ring-red-500 @enderror">
                    @error('title')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Isi Diskusi</label>
                    <textarea name="content" rows="6" required placeholder="Tulis pendapat atau pertanyaanmu di sini..." class="notranslate w-full px-4 py-3 rounded-xl bg-[#e8edf2] border-none outline-none text-sm text-[#1a3a5c] focus:ring-2 focus:ring-[#1a3a5c] resize-none @error('content') ring-2 ring-red-500 @enderror">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-[#1a3a5c] text-white py-3 rounded-xl font-bold hover:bg-[#122b45] shadow-md transition">
                        Buat Diskusi
                    </button>
                    <a href="/komunitas" class="px-8 py-3 bg-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-300 transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>