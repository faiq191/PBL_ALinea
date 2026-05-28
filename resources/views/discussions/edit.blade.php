<!DOCTYPE html>
<html>
<head>
    <title>Edit Diskusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen" x-data="{ 
    cover_source: '{{ old('cover_source', \Illuminate\Support\Str::startsWith($discussion->image, 'http') ? 'url' : 'file') }}',
    imageUrl: '{{ old('image_url', \Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : '') }}',
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
            <h1 class="text-2xl font-bold text-[#1a3a5c] mb-6">Edit Diskusi</h1>

            <form action="/diskusi/{{ $discussion->id }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Image Card -->
                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-[#1a3a5c]">Gambar Referensi (Opsional)</label>
                        
                        <div class="bg-white/40 p-5 rounded-2xl border border-white/50 shadow-inner flex flex-col sm:flex-row gap-6 items-center">
                            <!-- Live Image Preview -->
                            <div class="flex-shrink-0 relative group">
                                <img
                                    :src="cover_source === 'file' 
                                        ? (filePreview ? filePreview : '{{ $discussion->image ? (\Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : asset('storage/' . $discussion->image)) : 'https://placehold.co/100x140?text=No+Image' }}')
                                        : (imageUrl ? imageUrl : '{{ $discussion->image ? (\Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : asset('storage/' . $discussion->image)) : 'https://placehold.co/100x140?text=No+Image' }}')"
                                    x-on:error="$el.src = 'https://placehold.co/100x140?text=Error'"
                                    class="w-24 h-32 object-cover rounded-xl shadow-md border-2 border-white transition group-hover:scale-105 duration-300">
                                
                                <!-- "Aktif" Badge (Only show if no new file/url is modified) -->
                                <template x-if="cover_source === 'file' ? !filePreview : (imageUrl === '{{ \Illuminate\Support\Str::startsWith($discussion->image, 'http') ? $discussion->image : '' }}')">
                                    @if($discussion->image)
                                        <div class="absolute -top-1.5 -right-1.5 bg-[#1a3a5c] text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full shadow border border-white">
                                            Aktif
                                        </div>
                                    @endif
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
                                            File
                                        </button>
                                        <button type="button" @click="cover_source = 'url'"
                                            :class="cover_source === 'url' ? 'bg-[#1a3a5c] text-white shadow-sm' : 'text-gray-500 hover:text-[#1a3a5c]'"
                                            class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all duration-200">
                                            Link Gambar
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="cover_source" :value="cover_source">

                                <!-- File Input Zone -->
                                <div x-show="cover_source === 'file'" x-transition class="space-y-2">
                                    <div class="relative flex items-center justify-center border border-dashed border-gray-300 hover:border-[#1a3a5c] bg-white/50 rounded-xl p-4 transition cursor-pointer group">
                                        <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="disc-image-input-edit" @change="handleFileChange($event); document.getElementById('disc-file-chosen-edit').textContent = $event.target.files[0]?.name || 'Pilih file gambar...'">
                                        <div class="text-center space-y-1.5 pointer-events-none">
                                            <svg class="w-6 h-6 mx-auto text-gray-400 group-hover:text-[#1a3a5c] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            <p id="disc-file-chosen-edit" class="text-xs text-gray-500 font-medium group-hover:text-[#1a3a5c] transition">Pilih file gambar...</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400">Kosongkan jika tidak ingin mengubah gambar</p>
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
                                    <p class="text-xs text-gray-400">Kosongkan jika tidak ingin mengubah gambar</p>
                                    @error('image_url')
                                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Genre Selector -->
                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-[#1a3a5c]">Genre</label>
                        <div class="bg-white/40 p-4 rounded-2xl border border-white/50 shadow-inner h-[68px] flex items-center">
                            <select name="genre" class="w-full px-4 py-3 rounded-xl bg-white border border-gray-200 outline-none text-sm text-[#1a3a5c] focus:ring-2 focus:ring-[#1a3a5c] transition">
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->name }}" {{ $discussion->genre == $genre->name ? 'selected' : '' }}>{{ $genre->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Judul Diskusi</label>
                    <input type="text" name="title" required value="{{ $discussion->title }}" class="w-full px-4 py-3 rounded-xl bg-[#e8edf2] border-none outline-none text-sm text-[#1a3a5c] focus:ring-2 focus:ring-[#1a3a5c]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Isi Diskusi</label>
                    <textarea name="content" rows="6" required class="w-full px-4 py-3 rounded-xl bg-[#e8edf2] border-none outline-none text-sm text-[#1a3a5c] focus:ring-2 focus:ring-[#1a3a5c] resize-none">{{ $discussion->content }}</textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-[#1a3a5c] text-white py-3 rounded-xl font-bold hover:bg-[#122b45] shadow-md transition">
                        Simpan Perubahan
                    </button>
                    <a href="/diskusi/{{ $discussion->id }}" class="px-8 py-3 bg-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-300 transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>