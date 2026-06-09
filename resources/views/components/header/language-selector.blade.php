@props(['isHome', 'activeLang'])

<div id="nav-lang-container" class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest transition-colors duration-300 {{ $isHome ? 'text-white' : 'text-[#1a3a5c]' }}">
    <button type="button" onclick="changeLanguage('en')" 
       class="transition-all hover:opacity-80 outline-none {{ $activeLang === 'en' ? 'text-[#e84b7a] scale-110' : '' }}">
        EN
    </button>
    <span class="opacity-40 font-normal">|</span>
    <button type="button" onclick="changeLanguage('id')" 
       class="transition-all hover:opacity-80 outline-none {{ $activeLang === 'id' ? 'text-[#e84b7a] scale-110' : '' }}">
        ID
    </button>
</div>
