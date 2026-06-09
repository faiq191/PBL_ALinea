@props(['isHome'])

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
