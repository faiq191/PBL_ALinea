@php
    $isHome = request()->is('/');
@endphp
<nav id="main-nav"
    class="fixed top-0 left-0 w-full transition-all duration-300 px-12 flex items-center justify-between z-50 h-16
    {{ $isHome ? 'bg-transparent' : 'bg-white shadow-md border-b border-gray-200' }}">

    {{-- LEFT: Logo --}}
    <a href="/" id="nav-logo"
        class="flex items-center gap-3 font-serif font-bold text-2xl tracking-tight transition hover:opacity-80
        {{ $isHome ? 'text-white' : 'text-[#1a3a5c]' }}">
        
        {{-- Ubah h-8 menjadi h-12 atau h-14 agar lebih besar --}}
        <img src="{{ asset('Gambar/logo_alinea_tanpa_tulisan2.png') }}" alt="Logo Alinea" class="h-12 w-auto object-contain">
        
        <span>Alinea</span>
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
            {{-- Notification Dropdown --}}
            <div class="relative">
                <button type="button" id="nav-notif-btn" onclick="toggleNotifDropdown(event)" 
                    class="relative flex items-center justify-center p-2 rounded-full transition-all duration-300 outline-none
                    {{ $isHome ? 'bg-white/10 text-white hover:bg-white/20' : 'bg-slate-100 text-[#1a3a5c] hover:bg-slate-200/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    
                    @php
                        $unreadCount = auth()->user()->unreadCustomNotificationsCount();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-[#e84b7a] text-[9px] font-extrabold text-white animate-pulse">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>

                <div id="notifMenu"
                    class="hidden absolute right-0 mt-3 w-80 bg-white text-[#1a3a5c] rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                        <span class="font-bold text-sm">Notifikasi</span>
                        @if($unreadCount > 0)
                            <form method="POST" action="{{ route('notifications.readAll') }}" id="read-all-form" class="m-0">
                                @csrf
                                <button type="submit" class="text-xs text-[#e84b7a] hover:underline font-semibold">Tandai semua dibaca</button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="max-h-72 overflow-y-auto">
                        @php
                            $notifs = auth()->user()->customNotifications()->take(10)->get();
                        @endphp
                        
                        @forelse($notifs as $notif)
                            @php
                                $escapedContent = e($notif->content);
                                $renderedNotifContent = $escapedContent;
                                
                                // Format book title in quotes (use single quotes for HTML classes to avoid regex quote collision)
                                $renderedNotifContent = preg_replace('/&quot;([^&]+)&quot;/', '<span class=\'font-bold text-[#1a3a5c]\'>&ldquo;$1&rdquo;</span>', $renderedNotifContent);
                                $renderedNotifContent = preg_replace('/"([^"]+)"/', '<span class=\'font-bold text-[#1a3a5c]\'>&ldquo;$1&rdquo;</span>', $renderedNotifContent);
                                
                                // Highlight sender's name
                                if ($notif->sender) {
                                    $senderName = e($notif->sender->name);
                                    $renderedNotifContent = str_replace($senderName, '<span class=\'font-bold text-slate-800\'>' . $senderName . '</span>', $renderedNotifContent);
                                    $firstName = e(Str::before($notif->sender->name, ' '));
                                    $renderedNotifContent = str_replace($firstName, '<span class=\'font-bold text-slate-800\'>' . $firstName . '</span>', $renderedNotifContent);
                                }
                                
                                // Highlight user mentions
                                if (preg_match_all('/@([a-zA-Z0-9\s\-]{2,50})/', $escapedContent, $matches)) {
                                    foreach ($matches[1] as $rawMatch) {
                                        $parts = explode(' ', trim($rawMatch));
                                        $tempName = '';
                                        foreach ($parts as $part) {
                                            $tempName = $tempName === '' ? $part : $tempName . ' ' . $part;
                                            $userExists = \App\Models\User::where('name', $tempName)->exists();
                                            if ($userExists) {
                                                $escapedTempName = e($tempName);
                                                $renderedNotifContent = str_replace('@' . $escapedTempName, '<span class=\'text-blue-500 font-semibold\'>@' . $escapedTempName . '</span>', $renderedNotifContent);
                                            }
                                        }
                                    }
                                }

                                // Generate badge dynamically
                                $badgeHtml = '';
                                if (\Illuminate\Support\Str::contains($notif->title, ['Peminjaman', 'Pinjam', 'Borrow'])) {
                                    $badgeHtml = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 text-amber-600 border border-amber-100/50 mb-1.5"><i data-lucide="book-open" class="w-2.5 h-2.5"></i> Peminjaman Buku</span>';
                                } elseif (\Illuminate\Support\Str::contains($notif->title, ['Komentar', 'Balasan', 'Reply', 'Comment'])) {
                                    $badgeHtml = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-50 text-blue-600 border border-blue-100/50 mb-1.5"><i data-lucide="message-square" class="w-2.5 h-2.5"></i> Diskusi</span>';
                                }
                            @endphp
                            <div class="dropdown-notif-wrapper relative overflow-hidden bg-slate-50/50" data-id="{{ $notif->id }}" style="transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                                {{-- Underlay Revealed on Swipe --}}
                                <div class="absolute inset-0 bg-[#fecaca]/40 flex items-center pl-6 text-[#7f1d1d] transition-all duration-300" id="dd-underlay-{{ $notif->id }}">
                                    <div class="flex items-center gap-2 transform -translate-x-4 opacity-0 transition-all duration-300" id="dd-underlay-content-{{ $notif->id }}">
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm border border-red-100">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-red-600 animate-pulse"></i>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-[10px] text-[#7f1d1d]">Tahan & Geser...</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card Content --}}
                                <div class="dropdown-notif-card border-b border-gray-50 last:border-b-0 relative group transition-all duration-200 border-l-2 {{ $notif->is_read ? 'bg-white border-transparent hover:bg-slate-50' : 'bg-white border-transparent hover:bg-slate-50 hover:border-[#1a3a5c]' }} cursor-pointer select-none"
                                     style="touch-action: pan-y; transform: translateX(0px);"
                                     data-id="{{ $notif->id }}">
                                    <div class="w-full text-left block pl-4 pr-10 py-3.5 outline-none dropdown-notif-clickable">
                                        <div class="flex gap-3 items-start">
                                            <span class="w-1.5 h-1.5 mt-2 rounded-full flex-shrink-0 {{ $notif->is_read ? 'bg-transparent' : 'bg-[#1a3a5c]' }}"></span>
                                            
                                            {{-- Profile Photo --}}
                                            @if($notif->sender)
                                                <img src="{{ $notif->sender->profile_photo
                                                    ? (\Illuminate\Support\Str::startsWith($notif->sender->profile_photo, 'http') ? $notif->sender->profile_photo : asset('storage/' . $notif->sender->profile_photo))
                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($notif->sender->name) }}"
                                                    class="w-9 h-9 rounded-full object-cover border border-slate-100 flex-shrink-0 shadow-sm">
                                            @else
                                                <div class="w-9 h-9 rounded-full bg-[#e8edf2] flex items-center justify-center text-[#1a3a5c] flex-shrink-0 shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#1a3a5c]">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                                    </svg>
                                                </div>
                                            @endif
 
                                            <div class="flex-1 min-w-0">
                                                {!! $badgeHtml !!}
                                                <p class="text-xs truncate {{ $notif->is_read ? 'font-normal text-gray-500' : 'font-extrabold text-[#1a3a5c]' }}">{{ $notif->title }}</p>
                                                <p class="text-[11px] text-gray-600 mt-1 leading-relaxed line-clamp-2">
                                                    {!! $renderedNotifContent !!}
                                                </p>
                                                <p class="text-[9px] text-gray-400 mt-1.5 flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                    {{ $notif->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Quick Delete --}}
                                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-200 z-20">
                                        <form action="/notifications/{{ $notif->id }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Notifikasi" class="w-7 h-7 rounded-full bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition shadow-sm border border-red-100 dd-delete-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Hidden form for click action --}}
                                <form id="read-form-{{ $notif->id }}" action="/notifications/{{ $notif->id }}/read" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-xs text-gray-400">
                                Tidak ada notifikasi.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-gray-100">
                        <a href="/notifications" class="block w-full text-center py-2.5 text-xs text-[#1a3a5c] font-bold hover:bg-slate-50 transition bg-slate-50/50">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                </div>
            </div>

            {{-- User Profile --}}
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
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.add("hidden");
    }

    function toggleNotifDropdown(event) {
        event.stopPropagation();
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.toggle("hidden");
        document.getElementById("profileMenu").classList.add("hidden");
    }

    window.addEventListener("click", function() {
        const menu = document.getElementById("profileMenu");
        if (menu) menu.classList.add("hidden");
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.add("hidden");
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
            const notifBtn = document.getElementById('nav-notif-btn');

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

                // Ubah notifikasi button
                if (notifBtn) {
                    notifBtn.classList.replace('bg-white/10', 'bg-slate-100');
                    notifBtn.classList.replace('text-white', 'text-[#1a3a5c]');
                    notifBtn.classList.replace('hover:bg-white/20', 'hover:bg-slate-200/80');
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

                // Kembalikan notifikasi button
                if (notifBtn) {
                    notifBtn.classList.replace('bg-slate-100', 'bg-white/10');
                    notifBtn.classList.replace('text-[#1a3a5c]', 'text-white');
                    notifBtn.classList.replace('hover:bg-slate-200/80', 'hover:bg-white/20');
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

@auth
    <!-- Include compiled JS assets yang berisi Laravel Echo -->
    <script>
        window.laravelReverb = {
            key: "{{ env('VITE_REVERB_APP_KEY', 'z2qmiwap8byabk4uu6vt') }}",
            host: "{{ env('VITE_REVERB_HOST', 'reverb-production-b867.up.railway.app') }}",
            port: "{{ env('VITE_REVERB_PORT', '443') }}",
            scheme: "{{ env('VITE_REVERB_SCHEME', 'https') }}"
        };
    </script>
    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const userId = "{{ auth()->id() }}";
            
            // Define dropdown swipe-to-delete function
            window.initializeDropdownSwipeToDelete = function(card) {
                let startX = 0;
                let diffX = 0;
                let isDragging = false;
                const id = card.getAttribute('data-id');
                const wrapper = card.closest('.dropdown-notif-wrapper');
                if (!wrapper) return;
                
                const underlay = document.getElementById(`dd-underlay-${id}`);
                const underlayContent = document.getElementById(`dd-underlay-content-${id}`);
                const threshold = 140;
                
                card.addEventListener('dragstart', (e) => e.preventDefault());
                
                function handleStart(clientX) {
                    isDragging = true;
                    startX = clientX;
                    card.style.transition = 'none';
                    card.classList.add('dragging');
                    if (underlay) underlay.style.transition = 'none';
                    if (underlayContent) underlayContent.style.transition = 'none';
                }
                
                function handleMove(clientX) {
                    if (!isDragging) return;
                    diffX = clientX - startX;
                    diffX = Math.max(0, diffX);
                    
                    card.style.transform = `translateX(${diffX}px)`;
                    const progress = Math.min(diffX / threshold, 1);
                    
                    if (underlay) {
                        if (diffX >= threshold) {
                            underlay.style.backgroundColor = 'rgba(239, 68, 68, 0.95)';
                            if (underlayContent) {
                                underlayContent.style.opacity = '1';
                                underlayContent.style.transform = 'translateX(0)';
                            }
                        } else {
                            underlay.style.backgroundColor = `rgba(254, 202, 202, ${0.2 + progress * 0.6})`;
                            if (underlayContent) {
                                underlayContent.style.opacity = progress;
                                underlayContent.style.transform = `translateX(${-16 + progress * 16}px)`;
                            }
                        }
                    }
                }
                
                function handleEnd() {
                    if (!isDragging) return;
                    isDragging = false;
                    card.classList.remove('dragging');
                    
                    card.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s';
                    if (underlay) underlay.style.transition = 'background-color 0.4s, opacity 0.4s';
                    if (underlayContent) underlayContent.style.transition = 'all 0.4s';
                    
                    if (diffX >= threshold) {
                        card.style.transform = 'translateX(100%)';
                        card.style.opacity = '0';
                        if (underlay) underlay.style.backgroundColor = '#ef4444';
                        
                        setTimeout(() => {
                            wrapper.style.height = `${wrapper.offsetHeight}px`;
                            wrapper.offsetHeight;
                            wrapper.style.height = '0';
                            wrapper.style.marginBottom = '0';
                            wrapper.style.padding = '0';
                            wrapper.style.opacity = '0';
                            
                            // AJAX request to delete
                            fetch(`/notifications/${id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    _method: 'DELETE'
                                })
                            }).then(response => {
                                if (response.ok) {
                                    setTimeout(() => {
                                        wrapper.remove();
                                        
                                        // Update count badge
                                        const badgeEl = document.querySelector('#nav-notif-btn span');
                                        if (badgeEl) {
                                            let currentCount = parseInt(badgeEl.textContent.trim()) || 0;
                                            if (currentCount > 1) {
                                                badgeEl.textContent = currentCount - 1;
                                            } else {
                                                badgeEl.remove();
                                            }
                                        }
                                        
                                        // If empty, show placeholder
                                        const remaining = document.querySelectorAll('.dropdown-notif-wrapper');
                                        if (remaining.length === 0) {
                                            const container = document.querySelector('#notifMenu .max-h-72');
                                            if (container) {
                                                container.innerHTML = `<div class="px-4 py-8 text-center text-xs text-gray-400">Tidak ada notifikasi.</div>`;
                                            }
                                        }
                                    }, 400);
                                } else {
                                    card.style.transform = 'translateX(0px)';
                                    card.style.opacity = '1';
                                    if (underlay) underlay.style.backgroundColor = 'rgba(254, 202, 202, 0.2)';
                                }
                            });
                        }, 300);
                    } else {
                        card.style.transform = 'translateX(0px)';
                        if (underlay) underlay.style.backgroundColor = 'rgba(254, 202, 202, 0.2)';
                        if (underlayContent) {
                            underlayContent.style.opacity = '0';
                            underlayContent.style.transform = 'translateX(-16px)';
                        }
                    }
                    diffX = 0;
                }
                
                let didMove = false;
                
                card.addEventListener('mousedown', (e) => {
                    if (e.target.closest('.dd-delete-btn') || e.target.closest('form')) return;
                    didMove = false;
                    handleStart(e.clientX);
                });
                
                window.addEventListener('mousemove', (e) => {
                    if (isDragging) {
                        handleMove(e.clientX);
                        if (Math.abs(diffX) > 8) {
                            didMove = true;
                        }
                    }
                });
                
                window.addEventListener('mouseup', () => {
                    handleEnd();
                });
                
                card.addEventListener('touchstart', (e) => {
                    if (e.target.closest('.dd-delete-btn') || e.target.closest('form')) return;
                    didMove = false;
                    handleStart(e.touches[0].clientX);
                }, { passive: true });
                
                card.addEventListener('touchmove', (e) => {
                    if (isDragging) {
                        handleMove(e.touches[0].clientX);
                        if (Math.abs(diffX) > 8) {
                            didMove = true;
                        }
                    }
                }, { passive: true });
                
                card.addEventListener('touchend', () => {
                    handleEnd();
                });

                // Programmatic click handler
                card.addEventListener('click', (e) => {
                    if (e.target.closest('.dd-delete-btn') || e.target.closest('form')) return;
                    if (didMove) return;
                    
                    const form = document.getElementById(`read-form-${id}`);
                    if (form) {
                        form.submit();
                    }
                });
            };

            // Initialize all existing dropdown items
            document.querySelectorAll('.dropdown-notif-card').forEach(card => {
                window.initializeDropdownSwipeToDelete(card);
            });
            
            // Listen to public user-notifications.[userId] channel
            window.Echo.channel('user-notifications.' + userId)
                .listen('.NotificationSent', (e) => {
                    console.log("Real-time Notification Received:", e.notification);
                    
                    const notif = e.notification;
                    
                    // 1. Update the unread badge count
                    const badgeEl = document.querySelector('#nav-notif-btn span');
                    if (badgeEl) {
                        let currentCount = parseInt(badgeEl.textContent.trim()) || 0;
                        badgeEl.textContent = currentCount + 1;
                        badgeEl.classList.remove('hidden');
                    } else {
                        // If no badge exists, create it on the notification button
                        const notifBtn = document.getElementById('nav-notif-btn');
                        if (notifBtn) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-[#e84b7a] text-[9px] font-extrabold text-white animate-pulse';
                            newBadge.textContent = '1';
                            notifBtn.appendChild(newBadge);
                        }
                    }
                    
                    // 2. Prepend the new notification item to the dropdown list
                    const dropdownContainer = document.querySelector('.max-h-72.overflow-y-auto');
                    if (dropdownContainer) {
                        // Remove "Tidak ada notifikasi" placeholder if it exists
                        const emptyPlaceholder = dropdownContainer.querySelector('.py-8.text-center');
                        if (emptyPlaceholder) {
                            emptyPlaceholder.remove();
                        }
                        
                        // Render profile photo
                        let photoHtml = '';
                        if (notif.sender) {
                            const isUrl = notif.sender.profile_photo && notif.sender.profile_photo.startsWith('http');
                            const imgUrl = notif.sender.profile_photo 
                                ? (isUrl ? notif.sender.profile_photo : '/storage/' + notif.sender.profile_photo)
                                : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(notif.sender.name);
                            photoHtml = `<img src="${imgUrl}" class="w-9 h-9 rounded-full object-cover border border-slate-100 flex-shrink-0 shadow-sm">`;
                        } else {
                            photoHtml = `
                                <div class="w-9 h-9 rounded-full bg-[#e8edf2] flex items-center justify-center text-[#1a3a5c] flex-shrink-0 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#1a3a5c]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                    </svg>
                                </div>
                            `;
                        }
                        
                        // Parse mentions, quotes, and sender name on-the-fly
                        let escapedContent = notif.content
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                            
                        let renderedContent = escapedContent;
                        
                        // Highlight quotes (book titles)
                        renderedContent = renderedContent.replace(/&quot;([^&]+)&quot;/g, '<span class="font-bold text-[#1a3a5c]">&ldquo;$1&rdquo;</span>');
                        renderedContent = renderedContent.replace(/"([^"]+)"/g, '<span class="font-bold text-[#1a3a5c]">&ldquo;$1&rdquo;</span>');
                        
                        // Highlight sender name
                        if (notif.sender) {
                            const senderName = notif.sender.name;
                            renderedContent = renderedContent.replace(new RegExp(senderName, 'g'), `<span class="font-bold text-slate-800">${senderName}</span>`);
                            const firstName = senderName.split(' ')[0];
                            renderedContent = renderedContent.replace(new RegExp(firstName, 'g'), `<span class="font-bold text-slate-800">${firstName}</span>`);
                        }

                        const users = @json(\App\Models\User::select('name')->get()->pluck('name'));
                        for (const name of users) {
                            if (renderedContent.includes('@' + name)) {
                                renderedContent = renderedContent.replace('@' + name, `<span class="text-blue-500 font-semibold">@${name}</span>`);
                            }
                        }
                        
                        // Generate badge dynamically
                        let badgeHtml = '';
                        if (notif.title.includes('Peminjaman') || notif.title.includes('Pinjam') || notif.title.includes('Borrow')) {
                            badgeHtml = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 text-amber-600 border border-amber-100/50 mb-1.5"><i data-lucide="book-open" class="w-2.5 h-2.5"></i> Peminjaman Buku</span>';
                        } else if (notif.title.includes('Komentar') || notif.title.includes('Balasan') || notif.title.includes('Reply') || notif.title.includes('Comment')) {
                            badgeHtml = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-50 text-blue-600 border border-blue-100/50 mb-1.5"><i data-lucide="message-square" class="w-2.5 h-2.5"></i> Diskusi</span>';
                        }
                        
                        const newNotifHtml = `
                            <div class="dropdown-notif-wrapper relative overflow-hidden bg-slate-50/50" data-id="${notif.id}" style="transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                                {{-- Underlay Revealed on Swipe --}}
                                <div class="absolute inset-0 bg-[#fecaca]/40 flex items-center pl-6 text-[#7f1d1d] transition-all duration-300" id="dd-underlay-${notif.id}">
                                    <div class="flex items-center gap-2 transform -translate-x-4 opacity-0 transition-all duration-300" id="dd-underlay-content-${notif.id}">
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm border border-red-100">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-red-600 animate-pulse"></i>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-[10px] text-[#7f1d1d]">Tahan & Geser...</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card Content --}}
                                <div class="dropdown-notif-card border-b border-gray-50 last:border-b-0 relative group transition-all duration-200 border-l-2 bg-white border-transparent hover:bg-slate-50 hover:border-[#1a3a5c] cursor-pointer select-none"
                                     style="touch-action: pan-y; transform: translateX(0px);"
                                     data-id="${notif.id}">
                                    <div class="w-full text-left block pl-4 pr-10 py-3.5 outline-none dropdown-notif-clickable">
                                        <div class="flex gap-3 items-start">
                                            <span class="w-1.5 h-1.5 mt-2 rounded-full flex-shrink-0 bg-[#1a3a5c]"></span>
                                            
                                            ${photoHtml}
                                            
                                            <div class="flex-1 min-w-0">
                                                ${badgeHtml}
                                                <p class="text-xs truncate font-extrabold text-[#1a3a5c]">${notif.title}</p>
                                                <p class="text-[11px] text-gray-600 mt-1 leading-relaxed line-clamp-2">${renderedContent}</p>
                                                <p class="text-[9px] text-gray-400 mt-1.5 flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                    Baru saja
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="absolute right-2.5 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-200 z-20">
                                        <form action="/notifications/${notif.id}" method="POST" class="m-0">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" title="Hapus Notifikasi" class="w-7 h-7 rounded-full bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition shadow-sm border border-red-100 dd-delete-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Hidden form for click action --}}
                                <form id="read-form-${notif.id}" action="/notifications/${notif.id}/read" method="POST" class="hidden">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'}">
                                </form>
                            </div>
                        `;
                        
                        dropdownContainer.insertAdjacentHTML('afterbegin', newNotifHtml);
                        
                        // Initialize swipe-to-delete for the newly prepended card
                        const insertedWrapper = dropdownContainer.firstElementChild;
                        const insertedCard = insertedWrapper.querySelector('.dropdown-notif-card');
                        if (insertedCard && typeof window.initializeDropdownSwipeToDelete === 'function') {
                            window.initializeDropdownSwipeToDelete(insertedCard);
                        }
                        
                        // If items count exceeds 10, remove the last one
                        const items = dropdownContainer.querySelectorAll('.dropdown-notif-wrapper');
                        if (items.length > 10) {
                            items[items.length - 1].remove();
                        }
                    }
                    
                    // 3. Prepend to notifications index page list if they are currently on it
                    const indexListContainer = document.getElementById('notifications-index-list');
                    if (indexListContainer) {
                        const emptyIndexPlaceholder = indexListContainer.querySelector('.py-12.text-center');
                        if (emptyIndexPlaceholder) {
                            emptyIndexPlaceholder.remove();
                        }
                        
                        let photoHtmlLarge = '';
                        if (notif.sender) {
                            const isUrl = notif.sender.profile_photo && notif.sender.profile_photo.startsWith('http');
                            const imgUrl = notif.sender.profile_photo 
                                ? (isUrl ? notif.sender.profile_photo : '/storage/' + notif.sender.profile_photo)
                                : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(notif.sender.name);
                            photoHtmlLarge = `<img src="${imgUrl}" class="w-12 h-12 rounded-full object-cover border border-slate-100 flex-shrink-0 shadow-sm">`;
                        } else {
                            photoHtmlLarge = `
                                <div class="w-12 h-12 rounded-full bg-[#e8edf2] flex items-center justify-center text-[#1a3a5c] flex-shrink-0 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-[#1a3a5c]">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                    </svg>
                                </div>
                            `;
                        }
                        
                        // Parse mentions on-the-fly for the index page as well
                        let escapedContent = notif.content
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                            
                        const users = @json(\App\Models\User::select('name')->get()->pluck('name'));
                        let renderedContent = escapedContent;
                        for (const name of users) {
                            if (renderedContent.includes('@' + name)) {
                                renderedContent = renderedContent.replace('@' + name, `<span class="text-blue-500 font-semibold">@${name}</span>`);
                            }
                        }
                        
                        const newIndexItemHtml = `
                            <div class="notification-wrapper relative overflow-hidden rounded-2xl mb-4 bg-slate-100/50 animate-fade-in" data-id="${notif.id}" style="transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                                <div class="absolute inset-0 bg-[#fecaca]/40 flex items-center pl-6 text-[#7f1d1d] transition-all duration-300 rounded-2xl" id="underlay-${notif.id}">
                                    <div class="flex items-center gap-3 transform -translate-x-4 opacity-0 transition-all duration-300" id="underlay-content-${notif.id}">
                                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm border border-red-100">
                                            <i data-lucide="trash-2" class="w-5 h-5 text-red-600 animate-pulse"></i>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-sm text-[#7f1d1d]">Tahan & Geser Terus...</p>
                                            <p class="text-xs text-[#991b1b]/80">Lepas di ujung untuk menghapus</p>
                                        </div>
                                    </div>
                                </div>

                                 <div class="notification-card bg-white border border-gray-100 rounded-2xl p-5 pr-16 shadow-sm hover:shadow-md hover:bg-gray-50/80 hover:border-gray-200 transition-all duration-300 relative flex items-start gap-4 cursor-grab active:cursor-grabbing select-none"
                                      style="touch-action: pan-y; transform: translateX(0px); border-left: 4px solid #1a3a5c !important;"
                                      data-id="${notif.id}">
                                    
                                    <div class="absolute bottom-5 right-5 opacity-70 hover:opacity-100 transition-opacity duration-200 z-10">
                                        <form action="/notifications/${notif.id}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?');" class="m-0">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition shadow-sm border border-red-100" title="Hapus Notifikasi">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <span class="w-3 h-3 mt-3.5 rounded-full flex-shrink-0 bg-[#e84b7a]"></span>
                                    
                                    ${photoHtmlLarge}
                                    
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start gap-4">
                                            <h4 class="text-lg font-bold text-[#1a3a5c] leading-snug font-extrabold">
                                                ${notif.title}
                                            </h4>
                                            <span class="text-xs text-gray-400 font-medium whitespace-nowrap">Baru saja</span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mt-1.5 leading-relaxed">
                                            ${renderedContent}
                                        </p>
                                        
                                        ${notif.link ? `
                                            <div class="mt-4 flex gap-3">
                                                <form action="/notifications/${notif.id}/read" method="POST" class="m-0">
                                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'}">
                                                    <button type="submit" class="bg-[#1a3a5c] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#122b45] transition flex items-center gap-1.5">
                                                        Buka Halaman <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        indexListContainer.insertAdjacentHTML('afterbegin', newIndexItemHtml);
                        
                        const insertedCard = indexListContainer.firstElementChild.querySelector('.notification-card');
                        if (insertedCard && typeof window.initializeSwipeToDelete === 'function') {
                            window.initializeSwipeToDelete(insertedCard);
                        }
                        
                        // Re-render lucide icons if window.lucide is available
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }
                });
        });
    </script>
@endauth