<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun | Alinea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#f1f5f9] min-h-screen flex items-center justify-center p-4 antialiased">

    <!-- Card Container -->
    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl shadow-slate-200/50 overflow-hidden"
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

        <!-- Cover Photo Banner -->
        <div class="h-32 bg-gradient-to-r from-[#1a3a5c] to-[#3b6b9e] relative">
            <!-- Back Button -->
            <a href="/" class="absolute top-5 left-5 bg-white/20 hover:bg-white/30 backdrop-blur-md text-white p-2 rounded-full transition duration-300">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
        </div>

        <!-- Profile Section -->
        <div class="px-8 pb-8 relative">
            
            <!-- Floating Avatar -->
            <div class="relative w-28 h-28 mx-auto -mt-14 mb-4 group">
                <img
                    :src="photoMode === 'file' 
                        ? (filePreview ? filePreview : '{{ auth()->user()->profile_photo ? (\Illuminate\Support\Str::startsWith(auth()->user()->profile_photo, 'http') ? auth()->user()->profile_photo : asset('storage/' . auth()->user()->profile_photo)) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}')
                        : (imageUrl ? imageUrl : '{{ auth()->user()->profile_photo ? (\Illuminate\Support\Str::startsWith(auth()->user()->profile_photo, 'http') ? auth()->user()->profile_photo : asset('storage/' . auth()->user()->profile_photo)) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}')"
                    x-on:error="$el.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ auth()->user()->name }}')"
                    class="w-28 h-28 rounded-full object-cover border-4 border-white bg-white shadow-md transition duration-300 group-hover:scale-105">
                
                <!-- Camera Icon Badge -->
                <div class="absolute bottom-0 right-0 bg-[#1a3a5c] text-white p-2 rounded-full border-4 border-white shadow-sm">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-extrabold text-[#1a3a5c]">Profil Saya</h2>
                <p class="text-sm text-gray-400 mt-1">Perbarui detail akun dan foto profil Anda</p>
            </div>

            <!-- FORM -->
            <form method="POST" action="/profile" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Username -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider ml-1">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="name"
                            value="{{ auth()->user()->name }}"
                            placeholder="Masukkan nama Anda"
                            class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-[#1a3a5c]/50 focus:ring-4 focus:ring-[#1a3a5c]/10 outline-none text-sm text-[#1a3a5c] font-medium transition-all">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider ml-1">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            value="{{ auth()->user()->email }}"
                            class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-[#1a3a5c]/50 focus:ring-4 focus:ring-[#1a3a5c]/10 outline-none text-sm text-[#1a3a5c] font-medium transition-all">
                    </div>
                </div>

                <!-- Photo Selection Area -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <label class="block text-xs font-bold text-gray-400 mb-3 uppercase tracking-wider text-center">Sumber Foto Profil</label>
                    
                    <!-- Segmented Tab Selector -->
                    <div class="flex gap-1 mb-4 bg-slate-200/60 p-1.5 rounded-xl">
                        <button type="button" @click="photoMode = 'file'"
                            :class="photoMode === 'file' ? 'bg-white text-[#1a3a5c] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-2 text-xs font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <i data-lucide="upload-cloud" class="w-4 h-4"></i> Upload File
                        </button>
                        <button type="button" @click="photoMode = 'url'"
                            :class="photoMode === 'url' ? 'bg-white text-[#1a3a5c] shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-2 text-xs font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <i data-lucide="link" class="w-4 h-4"></i> Link URL
                        </button>
                    </div>
                    <input type="hidden" name="photo_source" :value="photoMode">

                    <!-- Upload File Input -->
                    <div x-show="photoMode === 'file'" x-transition x-cloak>
                        <input
                            type="file"
                            name="profile_photo"
                            @change="handleFileChange($event)"
                            class="w-full text-sm text-gray-500 
                                   file:mr-4 file:py-2.5 file:px-4 
                                   file:rounded-xl file:border-0 
                                   file:text-xs file:font-bold 
                                   file:bg-[#e8edf2] file:text-[#1a3a5c] 
                                   hover:file:bg-[#d0e4f5] cursor-pointer transition">
                        @error('profile_photo')
                            <p class="text-red-500 text-xs mt-2 font-semibold flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image URL Input -->
                    <div x-show="photoMode === 'url'" x-transition x-cloak>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="image" class="w-4 h-4 text-gray-400"></i>
                            </div>
                            <input
                                type="url"
                                name="profile_photo_url"
                                x-model="imageUrl"
                                placeholder="https://contoh.com/foto.jpg"
                                class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-slate-200 outline-none text-sm focus:border-[#1a3a5c]/50 focus:ring-4 focus:ring-[#1a3a5c]/10 transition-all">
                        </div>
                        @error('profile_photo_url')
                            <p class="text-red-500 text-xs mt-2 font-semibold flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-[#1a3a5c] text-white px-4 py-3.5 rounded-xl font-bold text-sm hover:bg-[#122b45] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2 mt-4">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>

            </form>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
        
        // Re-initialize icons when Alpine changes DOM
        document.addEventListener('alpine:initialized', () => {
            Alpine.effect(() => {
                lucide.createIcons();
            });
        });
    </script>
</body>
</html>