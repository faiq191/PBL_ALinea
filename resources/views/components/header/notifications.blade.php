@props(['isHome'])

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
                <x-header.notification-item :notif="$notif" />
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
