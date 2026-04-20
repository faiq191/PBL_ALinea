<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c] flex justify-center items-center h-screen">

<form action="/books" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl w-96">
    @csrf

    <h2 class="text-xl mb-4 font-semibold">Tambah Buku</h2>

    <input name="title" placeholder="Judul"
        class="w-full mb-3 p-2 border rounded">

    <input name="author" placeholder="Author"
        class="w-full mb-3 p-2 border rounded">

    <!-- THIS IS THE COVER UPLOAD -->
    <input type="file" name="image"
        class="w-full mb-3 p-2 border rounded">

    <button class="bg-green-500 text-white px-4 py-2 rounded w-full">
        Simpan
    </button>
</form>

</body>
</html>
