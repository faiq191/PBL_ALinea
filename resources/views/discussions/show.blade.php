<!DOCTYPE html>
<html>

<head>
    <title>Detail Diskusi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#2c2c2c] text-white">

    <x-header />

    <div class="max-w-3xl mx-auto mt-10 bg-white text-black p-6 rounded-xl shadow">

        <h1 class="text-2xl font-bold mb-2">
            {{ $discussion->title }}
        </h1>

        <span class="bg-blue-400 text-white text-xs px-3 py-1 rounded-full">
            {{ $discussion->genre ?? 'Umum' }}
        </span>

        <p class="text-sm text-gray-500 mt-2">
            Dibuat oleh: {{ $discussion->user->name ?? 'Unknown' }}
        </p>

        <hr class="my-4">

        <p class="text-gray-700">
            {{ $discussion->content ?? 'Tidak ada isi diskusi' }}
        </p>

        @auth
            <form action="/diskusi/{{ $discussion->id }}/comment" method="POST" class="mt-6">
                @csrf

                <textarea name="content"
                    class="w-full border rounded-lg p-3"
                    placeholder="Tulis komentar..."></textarea>

                <button class="mt-2 bg-[#5a3e3e] text-white px-4 py-2 rounded">
                    Kirim Komentar
                </button>
            </form>
        @endauth

        @guest
            <p class="mt-6 text-gray-500">
                <a href="/login" class="text-blue-500">Login</a> untuk komentar
            </p>
        @endguest

        <div class="mt-8">
            <h3 class="font-bold mb-4">Komentar</h3>

            @forelse ($discussion->comments as $comment)
                <div class="bg-gray-100 p-3 rounded mb-3">

                    <p class="text-sm font-semibold">
                        {{ $comment->user->name }}
                    </p>

                    <p class="text-sm text-gray-700">
                        {{ $comment->content }}
                    </p>

                </div>
            @empty
                <p class="text-gray-500">Belum ada komentar</p>
            @endforelse
        </div>

        <a href="/komunitas"
            class="inline-block mt-6 bg-[#5a3e3e] text-white px-4 py-2 rounded">
            ← Kembali
        </a>

    </div>

</body>

</html>
