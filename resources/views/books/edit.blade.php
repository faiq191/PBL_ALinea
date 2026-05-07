<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<x-header />

<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-4">Edit Buku</h2>

    <form action="/books/{{ $book->id }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Judul Buku</label>
                <input type="text" name="title" required
                    value="{{ $book->title }}"
                    class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#5a3e3e]">
            </div>
            <div>
                <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Penulis</label>
                <input type="text" name="author" required
                    value="{{ $book->author }}"
                    class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#5a3e3e]">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Tipe</label>
                <select name="type_id" required class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ $book->type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Tahun</label>
                <select name="year_id" required class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                    @foreach($years as $year)
                        <option value="{{ $year->id }}" {{ $book->year_id == $year->id ? 'selected' : '' }}>
                            {{ $year->year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Demografis</label>
                <select name="demographic_id" required class="w-full px-4 py-2 rounded-xl bg-white outline-none">
                    @foreach($demographics as $demo)
                        <option value="{{ $demo->id }}" {{ $book->demographic_id == $demo->id ? 'selected' : '' }}>
                            {{ $demo->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Pilih Genre (Bisa lebih dari satu)</label>
            <div class="grid grid-cols-3 gap-2 bg-white p-4 rounded-2xl">
                @foreach($genres as $genre)
                    <label class="flex items-center gap-2 text-sm text-[#4b3b3b]">
                        <input type="checkbox" name="genre_ids[]" value="{{ $genre->id }}"
                            {{ $book->genres->contains($genre->id) ? 'checked' : '' }}
                            class="rounded text-[#5a3e3e]">
                        {{ $genre->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Sampul Buku</label>
            @if($book->image)
                <img src="{{ asset('storage/' . $book->image) }}" class="w-24 h-32 object-cover rounded-lg mb-2">
            @endif
            <input type="file" name="image"
                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#5a3e3e] file:text-white hover:file:bg-[#4a3333]">
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti gambar</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-[#4b3b3b] mb-2">Deskripsi</label>
            <textarea name="description" rows="3"
                class="w-full px-4 py-2 rounded-xl bg-white border-none outline-none focus:ring-2 focus:ring-[#5a3e3e]">{{ $book->description }}</textarea>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="flex-1 bg-[#5a3e3e] text-white py-3 rounded-xl font-bold hover:bg-[#4a3333] transition">
                Simpan Buku
            </button>
            <a href="/koleksi" class="px-8 py-3 bg-gray-400 text-white rounded-xl font-bold hover:bg-gray-500 transition">
                Batal
            </a>
        </div>
    </form>

</div>
