<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c] flex justify-center items-center h-screen">

<div class="bg-white p-6 rounded-xl w-96">

    <h2 class="text-lg font-semibold mb-4">Tambah Buku</h2>

    <form method="POST" action="/books" enctype="multipart/form-data">
        @csrf

        <input type="text" name="title" placeholder="Judul"
            class="w-full mb-3 px-3 py-2 border rounded">

        <input type="text" name="author" placeholder="Author"
            class="w-full mb-3 px-3 py-2 border rounded">

        <input type="file" name="image"
            class="w-full mb-3">

        <select name="genre" class="w-full mb-3 p-3 border rounded-lg">
            <option value="">Pilih Genre</option>

            @foreach ($genres as $genre)
                <option value="{{ $genre }}"
                    {{ ($book->genre ?? '') == $genre ? 'selected' : '' }}>
                    {{ $genre }}
                </option>
            @endforeach
        </select>

        <button class="w-full bg-[#5a3e3e] text-white py-2 rounded">
            Simpan
        </button>
    </form>

</div>

</body>
</html>
