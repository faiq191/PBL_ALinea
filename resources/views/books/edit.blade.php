<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen" 
      x-data="{ 
          cover_source: '{{ old('cover_source', \Illuminate\Support\Str::startsWith($book->image, 'http') ? 'url' : 'file') }}',
          filePreview: '',
          imageUrl: '{{ old('image_url', \Illuminate\Support\Str::startsWith($book->image, 'http') ? $book->image : '') }}',
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

    <x-header />

    <div class="p-8 flex justify-center pt-28">
        {{-- Card Utama diubah dari bg-[#e6ddd6] menjadi bg-[#ffffff] border border-gray-100 --}}
        <div class="max-w-2xl w-full bg-[#ffffff] border border-gray-100 rounded-3xl p-8 shadow-xl">
            <h2 class="text-2xl font-bold text-[#1a3a5c] mb-6">Edit Buku</h2>

            <form action="/books/{{ $book->id }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Judul Buku</label>
                        <input type="text" name="title" required value="{{ $book->title }}"
                            class="w-full px-4 py-2 rounded-xl bg-[#e8edf2] border-none outline-none focus:ring-2 focus:ring-[#1a3a5c]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Penulis</label>
                        <input type="text" name="author" required value="{{ $book->author }}"
                            class="w-full px-4 py-2 rounded-xl bg-[#e8edf2] border-none outline-none focus:ring-2 focus:ring-[#1a3a5c]">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Tipe</label>
                        <select name="type_id" required class="w-full px-4 py-2 rounded-xl bg-[#e8edf2] outline-none">
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ $book->type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Tahun</label>
                        <select name="year_id" required class="w-full px-4 py-2 rounded-xl bg-[#e8edf2] outline-none">
                            @foreach($years as $year)
                                <option value="{{ $year->id }}" {{ $book->year_id == $year->id ? 'selected' : '' }}>
                                    {{ $year->year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Demografis</label>
                        <select name="demographic_id" required class="w-full px-4 py-2 rounded-xl bg-[#e8edf2] outline-none">
                            @foreach($demographics as $demo)
                                <option value="{{ $demo->id }}" {{ $book->demographic_id == $demo->id ? 'selected' : '' }}>
                                    {{ $demo->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Pilih Genre</label>
                    <div class="grid grid-cols-3 gap-2 bg-white border border-gray-100 p-4 rounded-2xl">
                        @foreach($genres as $genre)
                            <label class="flex items-center gap-2 text-sm text-[#1a3a5c] cursor-pointer">
                                <input type="checkbox" name="genre_ids[]" value="{{ $genre->id }}"
                                    {{ $book->genres->contains($genre->id) ? 'checked' : '' }}
                                    class="rounded text-[#1a3a5c] focus:ring-[#1a3a5c]">
                                {{ $genre->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-bold text-[#1a3a5c]">Sampul Buku</label>
                    
                    <div class="bg-[#f8fafc] p-5 rounded-2xl border border-gray-100 flex flex-col md:flex-row gap-5 items-center">
                        <div class="flex-shrink-0 relative group">
                            <img
                                :src="cover_source === 'file' 
                                    ? (filePreview ? filePreview : '{{ $book->image ? (\Illuminate\Support\Str::startsWith($book->image, 'http') ? $book->image : asset('storage/' . $book->image)) : 'https://placehold.co/100x140?text=No+Cover' }}')
                                    : (imageUrl ? imageUrl : '{{ $book->image ? (\Illuminate\Support\Str::startsWith($book->image, 'http') ? $book->image : asset('storage/' . $book->image)) : 'https://placehold.co/100x140?text=No+Cover' }}')"
                                x-on:error="$el.src = 'https://placehold.co/100x140?text=Error'"
                                class="w-24 h-32 object-cover rounded-xl shadow-lg border-2 border-white transition group-hover:scale-105 duration-300">
                            
                            <template x-if="cover_source === 'file' ? !filePreview : (imageUrl === '{{ \Illuminate\Support\Str::startsWith($book->image, 'http') ? $book->image : '' }}')">
                                @if($book->image)
                                    <div class="absolute -top-1.5 -right-1.5 bg-[#1a3a5c] text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full shadow border border-white">
                                        Aktif
                                    </div>
                                @endif
                            </template>
                        </div>

                        <div class="flex-1 w-full space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-[#1a3a5c] uppercase tracking-wide mb-1.5">Pilih Sumber Gambar</label>
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

                            <div x-show="cover_source === 'file'" x-transition class="space-y-1">
                                <div class="relative flex items-center justify-center border border-dashed border-gray-300 hover:border-[#1a3a5c] bg-white rounded-xl p-3 transition cursor-pointer group shadow-sm">
                                    <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="image-upload-input-edit" @change="handleFileChange($event); document.getElementById('file-chosen-text-edit').textContent = $event.target.files[0]?.name || 'Pilih file sampul...'">
                                    <div class="text-center space-y-1 pointer-events-none">
                                        <svg class="w-5 h-5 mx-auto text-gray-400 group-hover:text-[#1a3a5c] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p id="file-chosen-text-edit" class="text-[11px] text-gray-500 font-medium group-hover:text-[#1a3a5c] transition">Pilih file sampul atau seret ke sini...</p>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-show="cover_source === 'url'" x-transition class="space-y-2">
                                <div class="relative rounded-xl shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </div>
                                    <input type="url" name="image_url" x-model="imageUrl"
                                        placeholder="https://example.com/sampul-buku.jpg"
                                        class="block w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-white border border-gray-200 outline-none focus:ring-2 focus:ring-[#1a3a5c] transition">
                                </div>
                                @error('image_url')
                                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <p class="text-[10px] text-gray-500 italic">Kosongkan jika tidak ingin mengganti gambar sampul aktif.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 rounded-xl bg-[#e8edf2] border-none outline-none focus:ring-2 focus:ring-[#1a3a5c]">{{ $book->description }}</textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-[#1a3a5c] text-white py-3 rounded-xl font-bold hover:bg-[122b45] transition shadow-md">
                        Simpan Perubahan
                    </button>
                    <a href="/koleksi" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-500 transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>