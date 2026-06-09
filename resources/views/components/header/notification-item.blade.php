@props(['notif'])

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
        $firstName = e(\Illuminate\Support\Str::before($notif->sender->name, ' '));
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
