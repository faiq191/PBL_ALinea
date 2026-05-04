<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<x-header />

<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-xl shadow">

    <h2 class="text-xl font-bold mb-4">Edit Buku</h2>

<form method="POST" action="/books/{{ $book->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="text" name="title"
            value="{{ $book->title }}"
            class="w-full mb-3 p-3 border rounded-lg">

        <input type="text" name="author"
            value="{{ $book->author }}"
            class="w-full mb-3 p-3 border rounded-lg">

            <input type="file" name="image"
        class="w-full mb-3 p-3 border rounded-lg">

        <select name="genre" class="w-full mb-3 p-3 border rounded-lg">
            <option value="">Pilih Genre</option>

            @foreach ($genres as $genre)
                <option value="{{ $genre }}"
                    {{ ($book->genre ?? '') == $genre ? 'selected' : '' }}>
                    {{ $genre }}
                </option>
            @endforeach
        </select>

        <button
            class="bg-[#5a3e3e] text-white px-4 py-2 rounded">
            Update
        </button>
    </form>

</div>
