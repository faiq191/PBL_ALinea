@php
    $isHome = request()->is('/');
    // Check if the user has a Google Translate cookie set to English
    $googtrans = isset($_COOKIE['googtrans']) ? $_COOKIE['googtrans'] : '';
    $activeLang = ($googtrans === '/id/en') ? 'en' : 'id';
@endphp

{{-- Hide the default Google Translate banner and popup --}}
<style>
    .skiptranslate iframe { display: none !important; }
    body { top: 0 !important; }
    .goog-te-banner-frame { display: none !important; }
    #goog-gt-tt { display: none !important; }
    .goog-tooltip skiptranslate { display: none !important; }
    .goog-text-highlight { background-color: transparent !important; box-shadow: none !important; }
</style>

<nav id="main-nav"
    class="fixed top-0 left-0 w-full transition-all duration-300 px-12 flex items-center justify-between z-50 h-16
    {{ $isHome ? 'bg-transparent' : 'bg-white shadow-md border-b border-gray-200' }}">

    {{-- LEFT: Logo --}}
    <x-header.logo :isHome="$isHome" />

    {{-- CENTER: Nav Links --}}
    <x-header.nav-links :isHome="$isHome" />

    {{-- RIGHT: Auth & Lang --}}
    <div class="flex items-center gap-4">

        {{-- Language Selector (EN / ID) --}}
        <x-header.language-selector :isHome="$isHome" :activeLang="$activeLang" />

        @auth
            {{-- Notification Dropdown --}}
            <x-header.notifications :isHome="$isHome" />

            {{-- User Profile --}}
            <x-header.user-profile :isHome="$isHome" />
        @endauth

        @guest
            <x-header.guest-links :isHome="$isHome" />
        @endguest

    </div>

</nav>

{{-- Hidden Google Translate Element --}}
<div id="google_translate_element" style="display:none;"></div>

{{-- Scripts --}}
<x-header.scripts.translate />
<x-header.scripts.dropdown />
<x-header.scripts.scroll :isHome="$isHome" />

@auth
    <script>
        window.laravelReverb = {
            key: "{{ env('VITE_REVERB_APP_KEY', 'z2qmiwap8byabk4uu6vt') }}",
            host: "{{ env('VITE_REVERB_HOST', 'reverb-production-b867.up.railway.app') }}",
            port: "{{ env('VITE_REVERB_PORT', '443') }}",
            scheme: "{{ env('VITE_REVERB_SCHEME', 'https') }}"
        };
    </script>
    @vite(['resources/js/app.js'])

    <x-header.scripts.swipe-delete />
    <x-header.scripts.echo-listener />
    <x-header.scripts.alinea-formatter />
    
    <x-chat />
@endauth