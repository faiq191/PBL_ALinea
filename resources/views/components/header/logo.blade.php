@props(['isHome'])

<a href="/" id="nav-logo"
    class="flex items-center gap-3 font-serif font-bold text-2xl tracking-tight transition hover:opacity-80
    {{ $isHome ? 'text-white' : 'text-[#1a3a5c]' }}">
    
    <img src="{{ asset('Gambar/logo_alinea_tanpa_tulisan2.png') }}" alt="Logo Alinea" class="h-12 w-auto object-contain">
    
    <span>Alinea</span>
</a>
