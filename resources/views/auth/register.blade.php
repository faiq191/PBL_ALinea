<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - ALinea</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="relative h-screen flex items-center justify-center bg-cover bg-center"
      style="background-image: url('{{ asset('GambarBackground/PerpusBG.jpg') }}')">

    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    </div>
<div class="w-full max-w-sm p-8 rounded-2xl backdrop-blur-lg bg-black/40 text-white shadow-lg">

    <h2 class="text-2xl text-center mb-6">ALinea</h2>

    <form method="POST" action="/register">
        @csrf

        <input type="text" name="name" placeholder="Nama Pengguna"
            class="w-full mb-4 px-4 py-2 rounded-full bg-white/20 placeholder-white focus:outline-none">

        <input type="email" name="email" placeholder="Email"
            class="w-full mb-4 px-4 py-2 rounded-full bg-white/20 placeholder-white focus:outline-none">

        <input type="password" name="password" placeholder="Kata Sandi"
            class="w-full mb-4 px-4 py-2 rounded-full bg-white/20 placeholder-white focus:outline-none">

        <input type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi"
            class="w-full mb-4 px-4 py-2 rounded-full bg-white/20 placeholder-white focus:outline-none">

        <button
            class="w-full bg-white text-black py-2 rounded-full hover:bg-gray-200 transition">
            Daftar
        </button>
    </form>

    <p class="text-center mt-4 text-sm">
        Sudah punya akun?
        <a href="/login" class="underline">Masuk</a>
    </p>

</div>

</body>
</html>
