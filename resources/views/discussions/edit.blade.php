<!DOCTYPE html>
<html>
<head>
    <title>Edit Diskusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen">
    <x-header />

    <div class="p-8 pt-24 flex justify-center">
        <div class="max-w-2xl w-full bg-[#ffffff] rounded-3xl p-8 shadow-xl border border-gray-100">
            <h1 class="text-2xl font-bold text-[#1a3a5c] mb-6">Edit Diskusi</h1>

            <form action="/diskusi/{{ $discussion->id }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Ganti Gambar (Opsional)</label>
                        <input type="file" name="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-[#e8edf2] file:text-[#1a3a5c] hover:file:bg-gray-200 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#1a3a5c] mb-2">Genre</label>
                        <select name="genre" class="w-full px-4 py-3 rounded-xl bg-[#e8edf2] outline-none text-sm text-[#1a3a5c]">
                            @foreach($genres as $genre)
                                <option value="{{ $genre->name }}" {{ $discussion->genre == $genre->name ? 'selected' : '' }}>{{ $genre->name }}</option>
                            @endforeach
                        </select>
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