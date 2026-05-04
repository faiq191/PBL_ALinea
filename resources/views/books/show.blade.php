<!DOCTYPE html>
<html>
<head>
    <title>Lihat Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<x-header />

<div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded-xl shadow">

    <img src="{{ asset('storage/' . $book->image) }}"
        class="w-full h-80 object-cover rounded-lg mb-6">

    <h1 class="text-2xl font-bold text-[#2c2c2c]">
        {{ $book->title }}
    </h1>

    <p class="text-gray-500 mt-2">
        {{ $book->author }}
    </p>

    <form action="/books/{{ $book->id }}" method="POST" class="mt-4">
    @csrf
    @method('DELETE')

    <button
        onclick="return confirm('Yakin mau hapus buku ini?')"
        class="bg-red-500 text-white px-4 py-2 rounded">
        Hapus
    </button>
</form>
    <a href="/"
        class="inline-block mt-6 bg-[#5a3e3e] text-white px-4 py-2 rounded">
        ← Kembali
    </a>

</div>
