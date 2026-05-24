@php
    $isHome = request()->is('/');
@endphp
<nav id="main-nav"
    class="fixed top-0 left-0 w-full transition-all duration-300 px-12 flex items-center justify-between z-50 h-16
    {{ $isHome ? 'bg-transparent' : 'bg-white shadow-md border-b border-gray-200' }}">

    {{-- LEFT: Logo --}}
    <a href="/" id="nav-logo"
        class="font-serif font-bold text-xl tracking-tight transition hover:opacity-80
        {{ $isHome ? 'text-white' : 'text-[#1a3a5c]' }}">
        ALinea
    </a>

    {{-- CENTER: Nav Links --}}
    <div class="flex items-center gap-8">
        @php
            $navItems = [
                ['name' => 'Beranda', 'url' => '/'],
                ['name' => 'Koleksi', 'url' => '/koleksi'],
                ['name' => 'Komunitas', 'url' => '/komunitas'],
                ['name' => 'Perpustakaan', 'url' => '/perpustakaan'],
                ['name' => 'Informasi', 'url' => '/informasi'],
            ];
        @endphp

        @foreach ($navItems as $item)
            @php
                $isActive = request()->is(ltrim($item['url'], '/')) || (request()->is('/') && $item['url'] == '/');
            @endphp
            <a href="{{ $item['url'] }}"
                class="nav-link text-xs font-bold uppercase tracking-widest transition-colors duration-300
                {{ $isActive ? 'text-[#e84b7a]' : ($isHome ? 'text-white' : 'text-[#1a3a5c]') }}">
                {{ $item['name'] }}
            </a>
        @endforeach
    </div>

    {{-- RIGHT: Auth --}}
    <div class="flex items-center gap-4">

        @auth
            <div class="relative">
                <button type="button" id="nav-user-btn" onclick="toggleDropdown(event)" 
                    class="flex items-center gap-2 px-3 py-1.5 rounded-full transition-all duration-300 outline-none
                    {{ $isHome ? 'bg-white/10 border border-white/20 hover:bg-white/20' : 'bg-slate-100 border border-slate-200 hover:bg-slate-200/80' }}">
                    <img src="{{ auth()->user()->profile_photo
                        ? asset('storage/' . auth()->user()->profile_photo)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                        class="w-7 h-7 rounded-full object-cover border-2 border-[#e84b7a]">
                    <span id="nav-user-name"
                        class="text-xs font-bold uppercase tracking-widest transition-colors duration-300
                        {{ $isHome ? 'text-white' : 'text-[#1a3a5c]' }}">
                        {{ Str::before(auth()->user()->name, ' ') }}
                    </span>
                    <svg id="nav-user-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" 
                        class="w-3 h-3 transition-colors duration-300 {{ $isHome ? 'text-white' : 'text-[#1a3a5c]' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
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
            <a href="/login" id="nav-login"
                class="text-xs font-bold uppercase tracking-widest hover:opacity-80 transition-colors duration-300
                {{ $isHome ? 'text-white' : 'text-[#1a3a5c]' }}">
                Masuk
            </a>
            <a href="/register" id="nav-register-btn"
                class="text-xs font-bold uppercase tracking-widest px-4 py-2 rounded transition-all
                {{ $isHome ? 'bg-white text-[#1a3a5c] hover:bg-gray-200' : 'bg-[#1a3a5c] text-white hover:bg-[#122b45]' }}">
                Daftar
            </a>
        @endguest

    </div>

</nav>
<script>
    // 1. Fungsi Toggle Dropdown
    function toggleDropdown(event) {
        event.stopPropagation();
        document.getElementById("profileMenu").classList.toggle("hidden");
    }

    window.addEventListener("click", function() {
        const menu = document.getElementById("profileMenu");
        if (menu) menu.classList.add("hidden");
    });

    // 2. Fungsi Scroll Effect (Hanya berjalan di Beranda)
    const isHome = @json($isHome);
    
    if (isHome) {
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('main-nav');
            const logo = document.getElementById('nav-logo');
            const loginBtn = document.getElementById('nav-login');
            const registerBtn = document.getElementById('nav-register-btn');
            const userName = document.getElementById('nav-user-name');
            const navLinks = document.querySelectorAll('.nav-link');
            const userBtn = document.getElementById('nav-user-btn');
            const userChevron = document.getElementById('nav-user-chevron');

            if (window.scrollY > 50) {
                // SAAT SCROLL KE BAWAH (Header Putih)
                nav.classList.replace('bg-transparent', 'bg-white');
                nav.classList.add('shadow-md', 'border-b', 'border-gray-200');

                logo.classList.replace('text-white', 'text-[#1a3a5c]');
                if (loginBtn) loginBtn.classList.replace('text-white', 'text-[#1a3a5c]');

                // Ubah tombol daftar jadi solid biru
                if (registerBtn) {
                    registerBtn.classList.replace('bg-white', 'bg-[#1a3a5c]');
                    registerBtn.classList.replace('text-[#1a3a5c]', 'text-white');
                }

                // Ubah capsule user profile
                if (userBtn) {
                    userBtn.classList.replace('bg-white/10', 'bg-slate-100');
                    userBtn.classList.replace('border-white/20', 'border-slate-200');
                    userBtn.classList.replace('hover:bg-white/20', 'hover:bg-slate-200/80');
                }
                if (userName) {
                    userName.classList.replace('text-white', 'text-[#1a3a5c]');
                }
                if (userChevron) {
                    userChevron.classList.replace('text-white', 'text-[#1a3a5c]');
                }

                navLinks.forEach(link => {
                    if (!link.classList.contains('text-[#e84b7a]')) {
                        link.classList.replace('text-white', 'text-[#1a3a5c]');
                    }
                });

            } else {
                // SAAT DI ATAS (Header Transparan)
                nav.classList.replace('bg-white', 'bg-transparent');
                nav.classList.remove('shadow-md', 'border-b', 'border-gray-200');

                logo.classList.replace('text-[#1a3a5c]', 'text-white');
                if (loginBtn) loginBtn.classList.replace('text-[#1a3a5c]', 'text-white');

                // Kembalikan tombol daftar jadi putih
                if (registerBtn) {
                    registerBtn.classList.replace('bg-[#1a3a5c]', 'bg-white');
                    registerBtn.classList.replace('text-white', 'text-[#1a3a5c]');
                }

                // Kembalikan capsule user profile
                if (userBtn) {
                    userBtn.classList.replace('bg-slate-100', 'bg-white/10');
                    userBtn.classList.replace('border-slate-200', 'border-white/20');
                    userBtn.classList.replace('hover:bg-slate-200/80', 'hover:bg-white/20');
                }
                if (userName) {
                    userName.classList.replace('text-[#1a3a5c]', 'text-white');
                }
                if (userChevron) {
                    userChevron.classList.replace('text-[#1a3a5c]', 'text-white');
                }

                navLinks.forEach(link => {
                    if (!link.classList.contains('text-[#e84b7a]')) {
                        link.classList.replace('text-[#1a3a5c]', 'text-white');
                    }
                });
            }
        });
    }
</script>
