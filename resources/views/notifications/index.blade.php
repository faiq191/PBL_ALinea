<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi Anda - Alinea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen flex flex-col justify-between">
    <x-header />

    <div class="max-w-4xl mx-auto pt-24 px-6 w-full mb-12 flex-1">
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            
            <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-100">
                <div>
                    <h2 class="text-3xl font-bold text-[#1a3a5c]">Notifikasi</h2>
                    <p class="text-gray-400 mt-1">Pantau aktivitas terbaru di akun Anda</p>
                </div>
                
                @if(auth()->user()->unreadCustomNotificationsCount() > 0)
                    <form method="POST" action="{{ route('notifications.readAll') }}">
                        @csrf
                        <button type="submit" class="bg-[#1a3a5c] text-white px-5 py-2.5 rounded-xl flex items-center gap-2 font-bold hover:bg-[#122b45] transition shadow-md text-sm">
                            <i data-lucide="check-check" class="w-4 h-4"></i>
                            Tandai Semua Dibaca
                        </button>
                    </form>
                @endif
            </div>

            <div id="notifications-index-list" class="space-y-4">
                @forelse ($notifications as $notif)
                    <div class="notification-wrapper relative overflow-hidden rounded-2xl mb-4 bg-slate-100/50" data-id="{{ $notif->id }}" style="transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                        
                        {{-- Underlay Revealed on Swipe --}}
                        <div class="absolute inset-0 bg-[#fecaca]/40 flex items-center pl-6 text-[#7f1d1d] transition-all duration-300 rounded-2xl" id="underlay-{{ $notif->id }}">
                            <div class="flex items-center gap-3 transform -translate-x-4 opacity-0 transition-all duration-300" id="underlay-content-{{ $notif->id }}">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm border border-red-100">
                                    <i data-lucide="trash-2" class="w-5 h-5 text-red-600 animate-pulse"></i>
                                </div>
                                <div>
                                    <p class="font-extrabold text-sm text-[#7f1d1d]">Tahan & Geser Terus...</p>
                                    <p class="text-xs text-[#991b1b]/80">Lepas di ujung untuk menghapus</p>
                                </div>
                            </div>
                        </div>

                        {{-- Card Content --}}
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
                                $badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-600 border border-amber-100/50 mb-2"><i data-lucide="book-open" class="w-3 h-3"></i> Peminjaman Buku</span>';
                            } elseif (\Illuminate\Support\Str::contains($notif->title, ['Komentar', 'Balasan', 'Reply', 'Comment'])) {
                                $badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-600 border border-blue-100/50 mb-2"><i data-lucide="message-square" class="w-3 h-3"></i> Diskusi</span>';
                            }
                        @endphp
                        <div class="notification-card border border-gray-100 rounded-2xl p-5 pr-16 shadow-sm hover:shadow-md hover:bg-gray-50/80 transition-all duration-300 relative flex items-start gap-4 cursor-grab active:cursor-grabbing select-none bg-white hover:border-gray-200"
                             style="touch-action: pan-y; transform: translateX(0px); border-left: 4px solid {{ $notif->is_read ? '#e2e8f0' : '#1a3a5c' }} !important;"
                             data-id="{{ $notif->id }}">
                            
                            {{-- Fallback Delete Button --}}
                            <div class="absolute bottom-5 right-5 opacity-70 hover:opacity-100 transition-opacity duration-200 z-10">
                                <form action="/notifications/{{ $notif->id }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition shadow-sm border border-red-100" title="Hapus Notifikasi">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>

                            <span class="w-3 h-3 mt-3.5 rounded-full flex-shrink-0 {{ $notif->is_read ? 'bg-transparent' : 'bg-[#1a3a5c]' }}"></span>
                            
                            {{-- Profile Photo --}}
                            @if($notif->sender)
                                <img src="{{ $notif->sender->profile_photo
                                    ? (\Illuminate\Support\Str::startsWith($notif->sender->profile_photo, 'http') ? $notif->sender->profile_photo : asset('storage/' . $notif->sender->profile_photo))
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($notif->sender->name) }}"
                                    class="w-12 h-12 rounded-full object-cover border-2 border-slate-100 shadow-sm flex-shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-full bg-[#e8edf2] flex items-center justify-center text-[#1a3a5c] shadow-sm flex-shrink-0">
                                    <i data-lucide="bell" class="w-5 h-5 text-[#1a3a5c]"></i>
                                </div>
                            @endif

                            <div class="flex-1">
                                <div class="flex justify-between items-start gap-4">
                                    <div>
                                        {!! $badgeHtml !!}
                                        <h4 class="text-lg font-bold text-[#1a3a5c] leading-snug {{ $notif->is_read ? 'font-semibold' : 'font-extrabold' }}">
                                            {{ $notif->title }}
                                        </h4>
                                    </div>
                                    <span class="text-xs text-gray-400 font-medium whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                                    {!! $renderedNotifContent !!}
                                </p>
                                
                                @if($notif->link)
                                    <div class="mt-4 flex gap-3">
                                        <form action="/notifications/{{ $notif->id }}/read" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="bg-[#1a3a5c] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#122b45] transition flex items-center gap-1.5 shadow-sm">
                                                Buka Halaman <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                        @if(!$notif->is_read)
                                            <form action="/notifications/{{ $notif->id }}/read" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="redirect" value="false">
                                                <button type="submit" class="border border-gray-200 text-gray-500 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-50 transition shadow-sm">
                                                    Tandai Dibaca
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @elseif(!$notif->is_read)
                                    <div class="mt-4">
                                        <form action="/notifications/{{ $notif->id }}/read" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="bg-[#1a3a5c] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-[#122b45] transition shadow-sm">
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="w-16 h-16 rounded-full bg-[#f8fafc] flex items-center justify-center mx-auto mb-4 border border-slate-100">
                            <i data-lucide="bell-off" class="w-8 h-8 text-slate-300"></i>
                        </div>
                        <p class="text-slate-400 font-semibold text-base">Tidak ada notifikasi baru untuk Anda.</p>
                        <p class="text-slate-300 text-xs mt-1">Kami akan mengabari Anda jika terjadi aktivitas terbaru.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="mt-8 border-t border-gray-100 pt-6">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>
    </div>

    <x-footer />
    <style>
        .notification-card.dragging {
            user-select: none;
            cursor: grabbing !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border-color: #fca5a5 !important;
            border-left-color: #fca5a5 !important;
            background-color: #fff5f5 !important; /* Soft premium reddish-pink background when held */
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            window.initializeSwipeToDelete = function(card) {
                let startX = 0;
                let diffX = 0;
                let isDragging = false;
                const id = card.getAttribute('data-id');
                const wrapper = card.closest('.notification-wrapper');
                if (!wrapper) return;
                
                const underlay = document.getElementById(`underlay-${id}`);
                const underlayContent = document.getElementById(`underlay-content-${id}`);
                const threshold = 200; // Slide threshold in pixels to trigger delete
                
                // Disable browser default drag-and-drop actions
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
                    diffX = Math.max(0, diffX); // Only allow swiping to the right
                    
                    card.style.transform = `translateX(${diffX}px)`;
                    
                    const progress = Math.min(diffX / threshold, 1);
                    
                    if (underlay) {
                        if (diffX >= threshold) {
                            underlay.style.backgroundColor = 'rgba(239, 68, 68, 0.95)'; // Deep red when past threshold
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
                        // Triggers deletion
                        card.style.transform = 'translateX(100%)';
                        card.style.opacity = '0';
                        if (underlay) underlay.style.backgroundColor = '#ef4444'; // Turns vibrant red on release
                        
                        setTimeout(() => {
                            wrapper.style.height = `${wrapper.offsetHeight}px`;
                            // Force reflow
                            wrapper.offsetHeight;
                            wrapper.style.height = '0';
                            wrapper.style.marginBottom = '0';
                            wrapper.style.padding = '0';
                            wrapper.style.opacity = '0';
                            wrapper.style.border = 'none';
                            
                            // Send Ajax DELETE request
                            fetch(`/notifications/${id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    _method: 'DELETE'
                                })
                            }).then(response => {
                                if (response.ok) {
                                    setTimeout(() => {
                                        wrapper.remove();
                                        // If list is empty, reload page to display empty placeholder
                                        const remaining = document.querySelectorAll('.notification-wrapper');
                                        if (remaining.length === 0) {
                                            location.reload();
                                        }
                                    }, 400);
                                } else {
                                    // Spring back if delete request failed
                                    card.style.transform = 'translateX(0px)';
                                    card.style.opacity = '1';
                                    if (underlay) underlay.style.backgroundColor = 'rgba(254, 202, 202, 0.2)';
                                }
                            });
                        }, 300);
                    } else {
                        // Spring back if threshold not reached
                        card.style.transform = 'translateX(0px)';
                        if (underlay) underlay.style.backgroundColor = 'rgba(254, 202, 202, 0.2)';
                        if (underlayContent) {
                            underlayContent.style.opacity = '0';
                            underlayContent.style.transform = 'translateX(-16px)';
                        }
                    }
                    diffX = 0;
                }
                
                // MOUSE DRAG EVENT LISTENERS
                card.addEventListener('mousedown', (e) => {
                    if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) return;
                    handleStart(e.clientX);
                });
                
                window.addEventListener('mousemove', (e) => {
                    handleMove(e.clientX);
                });
                
                window.addEventListener('mouseup', () => {
                    handleEnd();
                });
                
                // TOUCH SCREEN LISTENERS (MOBILE COMPATIBLE)
                card.addEventListener('touchstart', (e) => {
                    if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) return;
                    handleStart(e.touches[0].clientX);
                }, { passive: true });
                
                card.addEventListener('touchmove', (e) => {
                    handleMove(e.touches[0].clientX);
                }, { passive: true });
                
                card.addEventListener('touchend', () => {
                    handleEnd();
                });
            };

            // Initialize all existing cards
            document.querySelectorAll('.notification-card').forEach(card => {
                window.initializeSwipeToDelete(card);
            });
        });
    </script>
    <script>lucide.createIcons();</script>
</body>
</html>
