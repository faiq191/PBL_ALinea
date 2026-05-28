<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen" 
      x-data="{ 
          mode: '{{ old('source_mode', 'manual') }}', 
          cover_source: '{{ old('cover_source', 'file') }}',
          filePreview: '',
          imageUrl: '{{ old('image_url') }}',
          handleFileChange(event) {
              const file = event.target.files[0];
              if (file) {
                  const reader = new FileReader();
                  reader.onload = (e) => {
                      this.filePreview = e.target.result;
                  };
                  reader.readAsDataURL(file);
              } else {
                  this.filePreview = '';
              }
          }
      }">

    {{-- Header Navigation --}}
    <x-header />

    {{-- Main Content Wrapper with top padding applied (pt-28) --}}
    <div class="p-8 pt-28 flex justify-center">
        <div class="max-w-2xl w-full bg-[#e6ddd6] rounded-3xl p-8 shadow-xl">
            <h1 class="text-2xl font-bold text-[#1a3a5c] mb-2">Tambah Buku Baru</h1>
            <p class="text-sm text-gray-500 mb-6">Lengkapi detail buku untuk koleksi Ali.nea</p>

            {{-- Mode Selection Buttons --}}
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

            {{-- Form Start --}}
            <form action="/books" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="source_mode" :value="mode">

                {{-- Existing Book Mode --}}
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

                {{-- Google Books Mode --}}
                <div x-show="mode === 'google'" x-data="{ query: '', results: [], selected: null }" x-transition class="space-y-4">
                    <label class="block text-sm font-bold text-[#1a3a5c]">Cari di Google Books</label>
                    <div class="flex gap-2">
                        {{-- Input with Enter key prevention (@keydown.enter.prevent) --}}
                        <input type="text" x-model="query" 
                            @keydown.enter.prevent="if(query) fetch(`/google-books/search?q=${query}`).then(r => r.json()).then(d => results = d)"
                            placeholder="Ketik judul buku atau pengarang..." 
                            class="flex-1 px-4 py-2 rounded-xl bg-white outline-none focus:ring-2 focus:ring-[#1a3a5c]">
                        <button type="button" 
                            @click="if(query) fetch(`/google-books/search?q=${query}`).then(r => r.json()).then(d => results = d)" 
                            class="bg-[#1a3a5c] text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-[#122b45] transition">Cari</button>
                    </div>

                    {{-- Search Results Dropdown --}}
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

                    {{-- Selected Book Preview --}}
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

                {{-- Manual Input Mode --}}
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

                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-[#1a3a5c]">Sampul Buku</label>
                        
                        <div class="bg-white/40 p-5 rounded-2xl border border-white/50 shadow-inner flex flex-col md:flex-row gap-5 items-center">
                            <!-- Live Image Preview -->
                            <div class="flex-shrink-0 relative group">
                                <!-- File Preview -->
                                <template x-if="cover_source === 'file' && filePreview">
                                    <img :src="filePreview" class="w-24 h-32 object-cover rounded-xl shadow-lg border-2 border-white transition group-hover:scale-105 duration-300">
                                </template>
                                
                                <!-- URL Preview -->
                                <template x-if="cover_source === 'url' && imageUrl">
                                    <img :src="imageUrl" x-on:error="$el.src = 'https://placehold.co/100x140?text=Error'" class="w-24 h-32 object-cover rounded-xl shadow-lg border-2 border-white transition group-hover:scale-105 duration-300">
                                </template>

                                <!-- Default Placeholder -->
                                <template x-if="(cover_source === 'file' && !filePreview) || (cover_source === 'url' && !imageUrl)">
                                    <div class="w-24 h-32 bg-white/60 rounded-xl flex flex-col items-center justify-center border border-dashed border-[#1a3a5c]/35 text-[#1a3a5c]/60 shadow">
                                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[9px] font-bold uppercase tracking-wider">Sampul</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Upload / Link Controls -->
                            <div class="flex-1 w-full space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-[#1a3a5c] uppercase tracking-wide mb-1.5">Pilih Sumber Gambar</label>
                                    <!-- Tab Switcher (Pill Style) -->
                                    <div class="inline-flex p-1 bg-gray-200/60 rounded-xl w-full max-w-[260px]">
                                        <button type="button" @click="cover_source = 'file'"
                                            :class="cover_source === 'file' ? 'bg-[#1a3a5c] text-white shadow-sm' : 'text-gray-500 hover:text-[#1a3a5c]'"
                                            class="flex-1 py-1 text-xs font-bold rounded-lg transition-all duration-200">
                                            Upload File
                                        </button>
                                        <button type="button" @click="cover_source = 'url'"
                                            :class="cover_source === 'url' ? 'bg-[#1a3a5c] text-white shadow-sm' : 'text-gray-500 hover:text-[#1a3a5c]'"
                                            class="flex-1 py-1 text-xs font-bold rounded-lg transition-all duration-200">
                                            Link Gambar
                                        </button>
                                    </div>
                                    <input type="hidden" name="cover_source" :value="cover_source">
                                </div>

                                <!-- File Upload Area -->
                                <div x-show="cover_source === 'file'" x-transition class="space-y-1">
                                    <div class="relative flex items-center justify-center border border-dashed border-gray-300 hover:border-[#1a3a5c] bg-white/50 rounded-xl p-3 transition cursor-pointer group">
                                        <input type="file" name="image" :required="mode === 'manual' && cover_source === 'file'" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="image-upload-input-create" @change="handleFileChange($event); document.getElementById('file-chosen-text-create').textContent = $event.target.files[0]?.name || 'Pilih file sampul...'">
                                        <div class="text-center space-y-1 pointer-events-none">
                                            <svg class="w-5 h-5 mx-auto text-gray-400 group-hover:text-[#1a3a5c] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            <p id="file-chosen-text-create" class="text-[11px] text-gray-500 font-medium group-hover:text-[#1a3a5c] transition">Pilih file sampul atau seret ke sini...</p>
                                        </div>
                                    </div>
                                    @error('image')
                                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- URL Input -->
                                <div x-show="cover_source === 'url'" x-transition class="space-y-2">
                                    <div class="relative rounded-xl shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        </div>
                                        <input type="url" name="image_url" :required="mode === 'manual' && cover_source === 'url'"
                                            x-model="imageUrl"
                                            placeholder="https://example.com/sampul-buku.jpg"
                                            class="block w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-white border border-gray-200 outline-none focus:ring-2 focus:ring-[#1a3a5c] transition">
                                    </div>
                                    @error('image_url')
                                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
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

                {{-- Manual Input Mode (Dropdowns & Checkboxes) --}}
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

                {{-- Form Actions --}}
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-[#1a3a5c] text-white py-3 rounded-xl font-bold hover:bg-[#122b45] transition">
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