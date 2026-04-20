<!DOCTYPE html>
<html>
<head>
    <title>Akun Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c] flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded-2xl w-96 shadow-xl">

    <!-- Back Button -->
    <a href="/" class="text-sm text-gray-500 hover:underline mb-4 inline-block">
        ← Kembali
    </a>

    <h2 class="text-xl font-semibold text-center mb-4">Akun Saya</h2>

    <!-- Profile Image -->
    <img
        src="{{ auth()->user()->profile_photo
            ? asset('storage/' . auth()->user()->profile_photo)
            : 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}"
        class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border">

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

        <!-- Upload Photo -->
        <input
            type="file"
            name="profile_photo"
            class="w-full mb-4">

        <button class="bg-[#5a3e3e] text-white px-4 py-2 rounded-lg w-full">
            Simpan Perubahan
        </button>

    </form>

</div>

</body>
</html>
