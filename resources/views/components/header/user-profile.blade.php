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
        @if(auth()->user()->is_admin || str_ends_with(auth()->user()->email, '@admin.com'))
            <a href="/admin" class="block px-4 py-3 text-sm font-semibold hover:bg-gray-50 transition flex items-center gap-2 border-b border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#1a3a5c]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.524a1.125 1.125 0 0 1 1.48.175l.773.772a1.125 1.125 0 0 1 .176 1.48l-.524.738c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.524.738a1.125 1.125 0 0 1-.175 1.48l-.772.773a1.125 1.125 0 0 1-1.48.175l-.738-.524c-.35-.25-.806-.272-1.204-.107-.397.165-.71.505-.78.93l-.15.893c-.09.543-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.149-.894c-.07-.424-.383-.764-.78-.93-.398-.164-.854-.142-1.204.108l-.738.524a1.125 1.125 0 0 1-1.48-.175l-.772-.772a1.125 1.125 0 0 1-.175-1.48l.524-.738c.25-.35.272-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.524-.738a1.125 1.125 0 0 1 .175-1.48l.772-.772a1.125 1.125 0 0 1 1.48-.175l.738.524c.35.25.806.272 1.204.107.397-.165.71-.505.78-.93l.15-.894Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Halaman Admin
            </a>
        @endif
        <a href="/users/{{ auth()->user()->id }}" class="block px-4 py-3 text-sm hover:bg-gray-50 transition">Akun Saya</a>
        <form method="POST" action="/logout">
            @csrf
            <button class="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition">
                Keluar
            </button>
        </form>
    </div>
</div>
