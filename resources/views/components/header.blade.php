<nav
    class="relative w-full h-20 bg-gradient-to-b from-[#6b4a4a] via-[#5a3e3e] to-[#4a3333] backdrop-blur-md text-white px-6 flex items-center justify-center sticky top-0 z-50 shadow-[0_4px_20px_rgba(0,0,0,0.4)] border-b border-[#d9c2a3]/20 overflow-visible">

    <div class="absolute inset-0 w-full h-full pointer-events-none">
        <div
            class="absolute top-0 -left-[100%] w-[50%] h-full bg-gradient-to-r from-transparent via-white/5 to-transparent skew-x-[-30deg] animate-[shimmer_8s_infinite]">
        </div>
    </div>

    <div
        class="flex items-center gap-4 bg-gradient-to-tr from-black/20 to-white/5 p-2 rounded-2xl border border-white/10 shadow-inner relative z-10">

        <a href="/"
            class="group flex items-center gap-0 hover:gap-3 px-4 py-2 rounded-xl transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:bg-gradient-to-r hover:from-[#fdfcfb] hover:to-[#e2d1c3] hover:text-[#5a3e3e] hover:shadow-[0_0_25px_rgba(217,194,163,0.5)] active:scale-95">
            <img src="{{ asset('Logo/open-book.png') }}"
                class="w-6 h-6 invert group-hover:invert-0 transition-all duration-500 group-hover:rotate-[360deg] group-hover:scale-125"
                alt="Logo">
            <span
                class="max-w-0 overflow-hidden font-bold text-lg group-hover:max-w-xs transition-all duration-500 whitespace-nowrap tracking-tight font-serif">
                ALinea
            </span>
        </a>

        <a href="/koleksi"
            class="group flex items-center gap-0 hover:gap-3 px-4 py-2 rounded-xl transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:bg-gradient-to-r hover:from-[#fdfcfb] hover:to-[#e2d1c3] hover:text-[#5a3e3e] hover:shadow-[0_0_20px_rgba(217,194,163,0.4)] active:scale-95">
            <img src="{{ asset('Logo/bookshelf.png') }}"
                class="w-6 h-6 invert group-hover:invert-0 transition-all duration-500 group-hover:-translate-y-1"
                alt="Koleksi">
            <span
                class="max-w-0 overflow-hidden font-medium group-hover:max-w-xs transition-all duration-500 whitespace-nowrap font-serif">
                Koleksi
            </span>
        </a>

        <a href="/komunitas"
            class="group flex items-center gap-0 hover:gap-3 px-4 py-2 rounded-xl transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:bg-gradient-to-r hover:from-[#fdfcfb] hover:to-[#e2d1c3] hover:text-[#5a3e3e] hover:shadow-[0_0_20px_rgba(217,194,163,0.4)] active:scale-95">
            <img src="{{ asset('Logo/group.png') }}"
                class="w-6 h-6 invert group-hover:invert-0 transition-all duration-500 group-hover:animate-bounce"
                alt="Komunitas">
            <span
                class="max-w-0 overflow-hidden font-medium group-hover:max-w-xs transition-all duration-500 whitespace-nowrap font-serif">
                Komunitas
            </span>
        </a>

        <a href="/perpustakaan"
            class="group flex items-center gap-0 hover:gap-3 px-4 py-2 rounded-xl transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:bg-gradient-to-r hover:from-[#fdfcfb] hover:to-[#e2d1c3] hover:text-[#5a3e3e] hover:shadow-[0_0_20px_rgba(217,194,163,0.4)] active:scale-95">
            <img src="{{ asset('Logo/two-books.png') }}"
                class="w-6 h-6 invert group-hover:invert-0 transition-all duration-500 group-hover:scale-110 group-hover:-rotate-12"
                alt="Perpustakaan">
            <span
                class="max-w-0 overflow-hidden font-medium group-hover:max-w-xs transition-all duration-500 whitespace-nowrap font-serif">
                Perpustakaan
            </span>
        </a>

        <a href="/informasi"
            class="group flex items-center gap-0 hover:gap-3 px-4 py-2 rounded-xl transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:bg-gradient-to-r hover:from-[#fdfcfb] hover:to-[#e2d1c3] hover:text-[#5a3e3e] hover:shadow-[0_0_20px_rgba(217,194,163,0.4)] active:scale-95">
            <img src="{{ asset('Logo/info.png') }}"
                class="w-6 h-6 invert group-hover:invert-0 transition-all duration-500 group-hover:animate-pulse"
                alt="Informasi">
            <span
                class="max-w-0 overflow-hidden font-medium group-hover:max-w-xs transition-all duration-500 whitespace-nowrap font-serif">
                Informasi
            </span>
        </a>

    </div>

    {{-- // Profile Avatar with Dropdown Button // --}}
    <div class="absolute right-8 flex items-center">
        <div class="relative group animate-[float_4s_infinite_ease-in-out]">
            {{-- // Button Avatar // --}}
            <button type="button" onclick="toggleDropdown(event)"
                class="p-[3px] rounded-full bg-gradient-to-tr from-[#d9c2a3] via-[#f5e6d3] to-[#a68b6d] shadow-[0_0_15px_rgba(217,194,163,0.3)] cursor-pointer hover:scale-110 hover:rotate-3 hover:shadow-[0_0_25px_rgba(217,194,163,0.6)] transition-all duration-500 ease-out">

                <div class="p-[2px] rounded-full bg-[#5a3e3e]">
                    <img src="https://i.pinimg.com/474x/5d/a3/60/5da360c98b9af0ad709fe18606992229.jpg"
                        class="w-12 h-12 rounded-full border-2 border-[#d9c2a3]/30 object-cover" alt="Profile">
                </div>
            </button>

            <div
                class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-tr from-[#d9c2a3] to-white rounded-full border-2 border-[#5a3e3e] scale-0 group-hover:scale-100 transition-transform duration-700 shadow-md animate-pulse">
            </div>
            {{-- // Dropdown Menu // --}}
            <div id="profileMenu"
                class="hidden absolute right-0 mt-4 w-52 bg-white/95 backdrop-blur-xl text-[#5a3e3e] rounded-2xl shadow-2xl overflow-hidden z-50 border border-[#d9c2a3]/40">

                <div class="px-4 py-3 border-b border-gray-200">
                    <p class="font-semibold">Eiyu</p>
                    <p class="text-sm text-gray-500">user@email.com</p>
                </div>

                <a href="#" class="block px-4 py-3 hover:bg-[#f5ede4] transition">
                    Akun Saya
                </a>

                <a href="#" class="block px-4 py-3 hover:bg-[#f5ede4] transition">
                    Settings
                </a>

                <a href="#" class="block px-4 py-3 hover:bg-red-50 text-red-500 transition">
                    Logout
                </a>

            </div>
        </div>
    </div>
</nav>

<style>
    @keyframes shimmer {
        0% {
            transform: translateX(-100%) skewX(-30deg);
        }

        100% {
            transform: translateX(300%) skewX(-30deg);
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }
</style>
<script>
    function toggleDropdown(event) {
        event.stopPropagation();
        document.getElementById("profileMenu").classList.toggle("hidden");
    }

    window.addEventListener("click", function() {
        document.getElementById("profileMenu").classList.add("hidden");
    });
</script>
