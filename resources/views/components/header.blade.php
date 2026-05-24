<nav class="relative w-full bg-white border-b border-gray-200 text-[#1a3a5c] px-12 flex items-center justify-between sticky top-0 z-50 h-16">

    {{-- LEFT: Logo --}}
    <a href="/" class="font-serif font-bold text-xl tracking-tight text-[#1a3a5c] hover:text-[#5a7a9c] transition">
        ALinea
    </a>

    {{-- CENTER: Nav Links --}}
    <div class="flex items-center gap-8">

        <a href="/"
            class="text-xs font-bold uppercase tracking-widest transition
                {{ request()->is('/') ? 'text-[#e84b7a]' : 'text-[#1a3a5c] hover:text-[#5a7a9c]' }}">
            Beranda
        </a>

        <a href="/koleksi"
            class="text-xs font-bold uppercase tracking-widest transition
                {{ request()->is('koleksi') ? 'text-[#e84b7a]' : 'text-[#1a3a5c] hover:text-[#5a7a9c]' }}">
            Koleksi
        </a>

        <a href="/komunitas"
            class="text-xs font-bold uppercase tracking-widest transition
                {{ request()->is('komunitas') ? 'text-[#e84b7a]' : 'text-[#1a3a5c] hover:text-[#5a7a9c]' }}">
            Komunitas
        </a>

        <a href="/perpustakaan"
            class="text-xs font-bold uppercase tracking-widest transition
                {{ request()->is('perpustakaan') ? 'text-[#e84b7a]' : 'text-[#1a3a5c] hover:text-[#5a7a9c]' }}">
            Perpustakaan
        </a>

        <a href="/informasi"
            class="text-xs font-bold uppercase tracking-widest transition
                {{ request()->is('informasi') ? 'text-[#e84b7a]' : 'text-[#1a3a5c] hover:text-[#5a7a9c]' }}">
            Informasi
        </a>

    </div>

    {{-- RIGHT: Auth --}}
    <div class="flex items-center gap-4">

        @auth
        <div class="relative">
            <button type="button" onclick="toggleDropdown(event)" class="flex items-center gap-2">
                <img
                    src="{{ auth()->user()->profile_photo
                        ? asset('storage/' . auth()->user()->profile_photo)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                    class="w-8 h-8 rounded-full object-cover border border-gray-200">
                <span class="text-xs font-bold uppercase tracking-widest text-[#1a3a5c]">
                    {{ Str::before(auth()->user()->name, ' ') }}
                </span>
            </button>

            <div id="profileMenu"
                class="hidden absolute right-0 mt-3 w-52 bg-white text-[#1a3a5c] rounded-xl shadow-xl border border-gray-100 z-50">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                </div>
                <a href="/profile" class="block px-4 py-3 text-sm hover:bg-gray-50 transition">Akun Saya</a>
                <form method="POST" action="/logout">
                    @csrf
                    <button class="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
        @endauth

        @guest
        <a href="/login" class="text-xs font-bold uppercase tracking-widest text-[#1a3a5c] hover:text-[#5a7a9c] transition">
            Masuk
        </a>
        <a href="/register" class="text-xs font-bold uppercase tracking-widest bg-[#1a3a5c] text-white px-4 py-2 rounded hover:bg-[#122b45] transition">
            Daftar
        </a>
        @endguest

    </div>

</nav>

<script>
    function toggleDropdown(event) {
        event.stopPropagation();
        document.getElementById("profileMenu").classList.toggle("hidden");
    }
    window.addEventListener("click", function () {
        document.getElementById("profileMenu").classList.add("hidden");
    });
</script>
