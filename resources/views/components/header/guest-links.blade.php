@props(['isHome'])

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
