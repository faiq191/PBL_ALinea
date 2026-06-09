<script>
    document.addEventListener('DOMContentLoaded', () => {
        const userId = "{{ auth()->id() }}";
        
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
