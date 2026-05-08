<!DOCTYPE html>
<html>

<head>
    <title>Buat Diskusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c]">

    <x-header />

    <div class="max-w-3xl mx-auto mt-10 bg-[#f2e9e4] p-8 rounded-3xl shadow-lg">

        <h2 class="text-2xl font-bold mb-6 text-[#2c2c2c]">
            Buat Diskusi Baru
        </h2>

        <form method="POST" action="/diskusi">
            @csrf

            <!-- TITLE -->
            <div class="mb-4">
                <label class="block text-sm mb-2">Judul Diskusi</label>
                <input type="text" name="title"
                    class="w-full p-3 rounded-xl border"
                    placeholder="Masukkan judul..."
                    required>
            </div>

            <!-- GENRE -->
            <div class="mb-4">
                <label class="block text-sm mb-2">Genre</label>
                <input type="text" name="genre"
                    class="w-full p-3 rounded-xl border"
                    placeholder="Contoh: Fantasi">
            </div>

            <!-- CATEGORY -->
            <div class="mb-6">
                <label class="block text-sm mb-2">Kategori</label>
                <input type="text" name="category"
                    class="w-full p-3 rounded-xl border"
                    placeholder="Contoh: Novel / Self-Help">
            </div>

            <!-- BUTTON -->
            <div class="flex justify-between">

                <a href="/"
                    class="px-5 py-2 bg-gray-300 rounded-xl">
                    Kembali
                </a>

                <button type="submit"
                    class="px-6 py-2 bg-[#5a3e3e] text-white rounded-xl">
                    Buat Diskusi
                </button>

            </div>

        </form>

    </div>

</body>

</html>
