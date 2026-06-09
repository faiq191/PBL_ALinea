@props(['isHome'])

<div class="relative">
    <button type="button" id="nav-user-btn" onclick="toggleDropdown(event)" 
        class="flex items-center gap-2 px-3 py-1.5 rounded-full transition-all duration-300 outline-none
        {{ $isHome ? 'bg-white/10 border border-white/20 hover:bg-white/20' : 'bg-slate-100 border border-slate-200 hover:bg-slate-200/80' }}">
        <img src="{{ auth()->user()->profile_photo
            ? (\Illuminate\Support\Str::startsWith(auth()->user()->profile_photo, 'http') ? auth()->user()->profile_photo : asset('storage/' . auth()->user()->profile_photo))
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
        <a href="/users/{{ auth()->user()->id }}" class="block px-4 py-3 text-sm hover:bg-gray-50 transition">Akun Saya</a>
        <form method="POST" action="/logout">
            @csrf
            <button class="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition">
                Keluar
            </button>
        </form>
    </div>
</div>
