<!DOCTYPE html>
<html>
<head>
    <title>Akun Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-[#f5f5f5] flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded-2xl w-96 shadow-xl"
     x-data="{
         photoMode: '{{ old('photo_source', \Illuminate\Support\Str::startsWith(auth()->user()->profile_photo, 'http') ? 'url' : 'file') }}',
         filePreview: '',
         imageUrl: '{{ old('profile_photo_url', \Illuminate\Support\Str::startsWith(auth()->user()->profile_photo, 'http') ? auth()->user()->profile_photo : '') }}',
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

    <!-- Back Button -->
    <a href="/" class="text-sm text-gray-500 hover:underline mb-4 inline-block">
        ← Kembali
    </a>

    <h2 class="text-xl font-semibold text-center mb-4">Akun Saya</h2>

    <!-- Profile Image -->
    <div class="relative w-24 h-24 mx-auto mb-4 group">
        <img
            :src="photoMode === 'file' 
                ? (filePreview ? filePreview : '{{ auth()->user()->profile_photo ? (\Illuminate\Support\Str::startsWith(auth()->user()->profile_photo, 'http') ? auth()->user()->profile_photo : asset('storage/' . auth()->user()->profile_photo)) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}')
                : (imageUrl ? imageUrl : '{{ auth()->user()->profile_photo ? (\Illuminate\Support\Str::startsWith(auth()->user()->profile_photo, 'http') ? auth()->user()->profile_photo : asset('storage/' . auth()->user()->profile_photo)) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}')"
            x-on:error="$el.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ auth()->user()->name }}')"
            class="w-24 h-24 rounded-full object-cover border border-gray-200 mx-auto transition duration-300 group-hover:scale-105 shadow-md">
    </div>

    <!-- FORM -->
    <form method="POST" action="/profile" enctype="multipart/form-data">
        @csrf

        <!-- Username -->
        <input
            type="text"
            name="name"
            value="{{ auth()->user()->name }}"
            placeholder="Nama"
            class="w-full mb-3 px-4 py-2 rounded-lg border outline-none">

        <!-- Email -->
        <input
            type="email"
            name="email"
            value="{{ auth()->user()->email }}"
            class="w-full mb-3 px-4 py-2 rounded-lg border outline-none">

        <!-- Photo Selection and Input -->
        <div class="mb-4">
            <label class="block text-xs font-semibold text-[#1a3a5c] mb-2 uppercase tracking-wide">Foto Profil</label>
            
            <!-- Tab Selector -->
            <div class="flex gap-2 mb-3 bg-gray-100 p-1 rounded-lg">
                <button type="button" @click="photoMode = 'file'"
                    :class="photoMode === 'file' ? 'bg-white text-[#1a3a5c] shadow' : 'text-gray-500'"
                    class="flex-1 py-1 text-[10px] font-bold rounded transition">
                    Upload File
                </button>
                <button type="button" @click="photoMode = 'url'"
                    :class="photoMode === 'url' ? 'bg-white text-[#1a3a5c] shadow' : 'text-gray-500'"
                    class="flex-1 py-1 text-[10px] font-bold rounded transition">
                    Link Gambar
                </button>
            </div>
            <input type="hidden" name="photo_source" :value="photoMode">

            <!-- Upload File Input -->
            <div x-show="photoMode === 'file'" x-transition>
                <input
                    type="file"
                    name="profile_photo"
                    @change="handleFileChange($event)"
                    class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-[#1a3a5c] file:text-white hover:file:bg-[#122b45] cursor-pointer">
                @error('profile_photo')
                    <p class="text-red-600 text-[10px] mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image URL Input -->
            <div x-show="photoMode === 'url'" x-transition>
                <input
                    type="url"
                    name="profile_photo_url"
                    x-model="imageUrl"
                    placeholder="https://example.com/foto.jpg"
                    class="w-full px-3 py-2 rounded-lg border outline-none text-xs focus:ring-1 focus:ring-[#1a3a5c]">
                @error('profile_photo_url')
                    <p class="text-red-600 text-[10px] mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button class="bg-[#1a3a5c] text-white px-4 py-2 rounded-lg w-full">
            Simpan Perubahan
        </button>

    </form>

</div>

</body>
</html>
