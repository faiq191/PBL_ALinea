<script>
    // Global variable references
    if (typeof TENOR_API_KEY === 'undefined') {
        var TENOR_API_KEY = 'LIVDSRZULELA';
    }
    const onlineUsers = new Set();
    let chatActiveUserId = null;
    let chatMap = null;
    let chatMapMarker = null;
    let chatViewMap = null;
    let chatViewMapMarker = null;
    let chatMapSelectedCoords = [-7.9839, 112.6214]; // Default Malang
    let chatMapLayers = null;
    let currentChatTileLayer = null;
    let currentChatViewTileLayer = null;
    
    function toggleChatPanel() {
        const panel = document.getElementById('roblox-chat-panel');
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            loadChatUsers();
            markActiveChatAsRead();
        } else {
            panel.classList.add('hidden');
        }
    }

    window.openChatWithUser = function(id, name, avatar = '') {
        const panel = document.getElementById('roblox-chat-panel');
        if (panel && panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
        }
        if (!avatar) {
            avatar = '{{ asset("Gambar/default_avatar.png") }}';
        }
        selectChatUser(id, name, avatar);
    };
    function selectChatAttachment(type, url, name) {
        document.getElementById('chat-attachment-type').value = type;
        document.getElementById('chat-attachment-url').value = url;
        document.getElementById('chat-attachment-name').value = name;

        const preview = document.getElementById('chat-preview-box');
        preview.classList.remove('hidden');
        
        let previewContent = '';
        if (type === 'image') {
            previewContent = `<img src="${url}" class="w-8 h-8 object-cover rounded-md border border-gray-200">`;
        } else if (type === 'tenor') {
            previewContent = `<img src="${url}" class="w-8 h-8 object-cover rounded-md border border-gray-200">`;
        } else if (type === 'gmaps') {
            previewContent = `<span class="flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Lokasi terpilih</span>`;
        }

        preview.innerHTML = `
            <div class="flex items-center gap-2">
                ${previewContent}
                <div>
                    <p class="font-extrabold text-[10px] text-[#1a3a5c]">${name}</p>
                    <p class="text-[8px] text-slate-500">Lampiran siap dikirim</p>
                </div>
            </div>
            <button type="button" onclick="clearChatAttachment()" class="text-slate-400 hover:text-red-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        `;
    }

    function clearChatAttachment() {
        document.getElementById('chat-attachment-type').value = '';
        document.getElementById('chat-attachment-url').value = '';
        document.getElementById('chat-attachment-name').value = '';
        document.getElementById('chat-file-input').value = '';

        const preview = document.getElementById('chat-preview-box');
        preview.classList.add('hidden');
        preview.innerHTML = '';
    }

    function handleChatImageUpload(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                selectChatAttachment('image', e.target.result, file.name);
            };
            reader.readAsDataURL(file);
        }
    }
    // --- LOAD MESSAGES & USERS CONTROLLER ---
    function updateHeaderStatus() {
        const statusEl = document.getElementById('chat-header-status');
        if (!statusEl) return;
        if (chatActiveUserId && onlineUsers.has(parseInt(chatActiveUserId))) {
            statusEl.textContent = 'Online';
            statusEl.classList.remove('text-slate-400');
            statusEl.classList.add('text-green-500');
        } else {
            statusEl.textContent = 'Offline';
            statusEl.classList.remove('text-green-500');
            statusEl.classList.add('text-slate-400');
        }
    }

    async function loadChatUsers(query = '') {
        const list = document.getElementById('chat-users-list');
        list.innerHTML = '<div class="py-12 text-center"><span class="text-xs text-slate-400 font-medium">Memuat obrolan...</span></div>';

        try {
            const res = await fetch(`/chat/users?q=${encodeURIComponent(query)}`);
            const users = await res.json();
            list.innerHTML = '';

            let globalUnread = 0;

            if (users.length > 0) {
                users.forEach(u => {
                    globalUnread += u.unread_count;

                    const isOnline = onlineUsers.has(parseInt(u.id));

                    const row = document.createElement('div');
                    row.className = `p-3.5 flex items-center justify-between cursor-pointer hover:bg-[#1a3a5c]/5 transition duration-150 relative ${parseInt(chatActiveUserId) === parseInt(u.id) ? 'bg-[#1a3a5c]/10' : ''}`;
                    row.onclick = () => selectChatUser(u.id, u.name, u.profile_photo);

                    row.innerHTML = `
                        <div class="flex items-center gap-3 min-w-0 flex-grow pr-3">
                            <div class="relative shrink-0">
                                <img class="w-9 h-9 rounded-full object-cover border border-gray-150" src="${u.profile_photo}">
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border border-white ${isOnline ? 'bg-green-500' : 'bg-slate-300'}"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-extrabold text-[#1a3a5c] truncate">${u.name}</h4>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5">${u.latest_message || 'Belum ada obrolan'}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1 shrink-0">
                            <span class="text-[8px] text-slate-400 font-bold">${u.latest_message_time_human || ''}</span>
                            ${u.unread_count > 0 ? `<span class="bg-[#1a3a5c] text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full min-w-[18px] text-center border border-white">${u.unread_count}</span>` : ''}
                        </div>
                    `;
                    list.appendChild(row);
                });
            } else {
                list.innerHTML = '<div class="py-12 text-center text-xs text-slate-500 font-medium">Tidak ada pengguna ditemukan.</div>';
            }

            // Update Global badge
            const badge = document.getElementById('chat-global-badge');
            if (globalUnread > 0) {
                badge.textContent = globalUnread;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        } catch (e) {
            list.innerHTML = '<div class="py-12 text-center text-xs text-red-500 font-medium">Gagal memuat obrolan.</div>';
        }
    }

    async function selectChatUser(id, name, avatar) {
        chatActiveUserId = id;
        document.getElementById('chat-receiver-id').value = id;
        
        // Toggle view
        document.getElementById('chat-empty-state').classList.add('hidden');
        const view = document.getElementById('chat-conversation-view');
        view.classList.remove('hidden');

        document.getElementById('chat-header-avatar').src = avatar;
        document.getElementById('chat-header-name').textContent = name;
        document.getElementById('chat-header-profile-link').href = `/users/${id}`;

        // Update active header online/offline indicator
        updateHeaderStatus();

        // Clear preview & form
        clearChatAttachment();
        const textInput = document.getElementById('chat-message-text');
        if (textInput) {
            textInput.value = '';
            textInput.style.height = '36px';
        }

        // Load history messages
        await fetchChatMessages(id);
        
        // Highlight active user row
        loadChatUsers();
    }

    function closeActiveChat() {
        chatActiveUserId = null;
        document.getElementById('chat-receiver-id').value = '';
        document.getElementById('chat-conversation-view').classList.add('hidden');
        document.getElementById('chat-empty-state').classList.remove('hidden');
        loadChatUsers();
    }

    async function fetchChatMessages(userId) {
        const area = document.getElementById('chat-messages-area');
        area.innerHTML = '<div class="flex-grow flex items-center justify-center py-12"><span class="text-xs text-slate-400 animate-pulse">Memuat pesan...</span></div>';

        try {
            const res = await fetch(`/chat/messages/${userId}`);
            const data = await res.json();
            area.innerHTML = '';

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    appendMessageToDOM(msg, false);
                });
                scrollToBottom();
            } else {
                area.innerHTML = '<div class="flex-grow flex items-center justify-center py-12 text-xs text-slate-500">Kirim pesan pertama Anda untuk memulai obrolan.</div>';
            }
        } catch (e) {
            area.innerHTML = '<div class="flex-grow flex items-center justify-center py-12 text-xs text-red-500">Gagal memuat pesan.</div>';
        }
    }

    function appendMessageToDOM(msg, isRealtime = false) {
        const area = document.getElementById('chat-messages-area');
        
        // Check if there is an empty state placeholder
        const emptyState = area.querySelector('.py-12');
        if (emptyState) emptyState.remove();

        const isMe = parseInt(msg.sender_id) === parseInt('{{ auth()->id() }}');
        
        const wrapper = document.createElement('div');
        wrapper.className = `flex gap-3 max-w-[85%] ${isMe ? 'self-end flex-row-reverse' : 'self-start'}`;

        let attachmentHtml = '';
        if (msg.attachment_path) {
            if (msg.attachment_type === 'image' || msg.attachment_type === 'tenor') {
                attachmentHtml = `
                    <div class="relative overflow-hidden rounded-xl border border-gray-150 bg-slate-50 mt-1 cursor-pointer max-w-[200px]" onclick="openChatLightbox('${msg.attachment_path}')">
                        <img src="${msg.attachment_path}" class="w-full max-h-36 object-cover rounded-xl hover:opacity-90 transition">
                    </div>
                `;
            } else if (msg.attachment_type === 'gmaps') {
                attachmentHtml = `
                    <div onclick="openChatViewMap('${msg.attachment_path}', '${msg.attachment_name || 'Lokasi Terbagikan'}')" class="mt-1 flex items-center gap-2 bg-slate-50 border border-gray-100 rounded-xl p-2 hover:bg-slate-100 transition cursor-pointer max-w-[200px]">
                        <div class="w-8 h-8 bg-[#1a3a5c] rounded-lg flex items-center justify-center text-white shrink-0 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-extrabold text-[#1a3a5c] truncate">${msg.attachment_name || 'Lokasi'}</p>
                            <p class="text-[8px] text-slate-500">Lihat Peta</p>
                        </div>
                    </div>
                `;
            } else {
                attachmentHtml = `
                    <a href="${msg.attachment_path}" target="_blank" class="mt-1 flex items-center gap-2 bg-slate-50 border border-gray-100 rounded-xl p-2 hover:bg-slate-100 transition cursor-pointer max-w-[200px] text-left decoration-none block">
                        <div class="w-8 h-8 bg-[#1a3a5c] rounded-lg flex items-center justify-center text-white shrink-0 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-extrabold text-[#1a3a5c] truncate">${msg.attachment_name || 'Berkas'}</p>
                            <p class="text-[8px] text-slate-500">Unduh Lampiran</p>
                        </div>
                    </a>
                `;
            }
        }

        const avatarUrl = !isMe && document.getElementById('chat-header-avatar').src 
            ? document.getElementById('chat-header-avatar').src 
            : '{{ auth()->user()->profile_photo ? (str_starts_with(auth()->user()->profile_photo, "http") ? auth()->user()->profile_photo : asset("storage/" . auth()->user()->profile_photo)) : asset("Gambar/default_avatar.png") }}';

        wrapper.innerHTML = `
            ${!isMe ? `<a href="/users/${msg.sender_id}" title="Lihat Profil"><img src="${avatarUrl}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-gray-150 mt-0.5 hover:opacity-80 transition duration-150"></a>` : ''}
            <div class="flex flex-col ${isMe ? 'items-end' : 'items-start'}">
                <div class="px-3.5 py-2 rounded-2xl text-[11px] leading-relaxed break-words break-all max-w-full ${isMe ? 'bg-[#1a3a5c] text-white rounded-tr-none shadow-sm' : 'bg-white text-slate-800 rounded-tl-none border border-gray-200 shadow-sm'}">
                    ${msg.message ? escapeChatHtml(msg.message) : ''}
                    ${attachmentHtml}
                </div>
                <div class="flex items-center gap-1 mt-1 justify-end">
                    <span class="text-[7px] text-slate-400 font-bold block">${msg.created_at_formatted}</span>
                    ${isMe ? `
                        <span class="chat-status-ticks shrink-0" data-msg-id="${msg.id}">
                            ${msg.is_read ? `
                                <svg class="w-3.5 h-3.5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M2 12l5.25 5 2.625-3M8 12l5.25 5L22 7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            ` : `
                                <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            `}
                        </span>
                    ` : ''}
                </div>
            </div>
        `;

        area.appendChild(wrapper);
        if (isRealtime) {
            scrollToBottom();
        }
    }

    function scrollToBottom() {
        const area = document.getElementById('chat-messages-area');
        area.scrollTop = area.scrollHeight;
    }

    function escapeChatHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    async function sendChatMessage(e) {
        e.preventDefault();
        const textInput = document.getElementById('chat-message-text');
        const text = textInput.value.trim();

        const fileInput = document.getElementById('chat-file-input');
        const attType = document.getElementById('chat-attachment-type').value;
        const attUrl = document.getElementById('chat-attachment-url').value;
        const attName = document.getElementById('chat-attachment-name').value;

        if (!text && !attType && !fileInput.files[0]) return;

        if (text.length > 256) {
            alert('Pesan maksimal 256 karakter.');
            return;
        }

        // Build FormData
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('receiver_id', chatActiveUserId);
        if (text) formData.append('message', text);
        if (attType) formData.append('attachment_type', attType);
        if (attUrl) formData.append('attachment_url', attUrl);
        if (attName) formData.append('attachment_name', attName);

        if (fileInput.files[0]) {
            formData.append('attachment', fileInput.files[0]);
        }

        // Clear input bar
        textInput.value = '';
        textInput.style.height = '36px';
        clearChatAttachment();

        try {
            const res = await fetch('/chat/send', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.status === 'success') {
                appendMessageToDOM(data.message, true);
                loadChatUsers();
            }
        } catch (e) {
            console.error("Gagal mengirim pesan chat:", e);
        }
    }

    async function markActiveChatAsRead() {
        if (!chatActiveUserId) return;
        try {
            await fetch(`/chat/read/${chatActiveUserId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        } catch (e) {}
    }

    // --- LIGHTBOX CONTROLS ---
    function openChatLightbox(url) {
        const lb = document.getElementById('chat-lightbox');
        const img = document.getElementById('chat-lightbox-img');
        img.src = url;
        lb.classList.remove('hidden');
        lb.classList.remove('pointer-events-none');
        setTimeout(() => {
            lb.classList.add('opacity-100');
            img.classList.remove('scale-95');
            img.classList.add('scale-100');
        }, 10);
    }

    function closeChatLightbox() {
        const lb = document.getElementById('chat-lightbox');
        const img = document.getElementById('chat-lightbox-img');
        lb.classList.remove('opacity-100');
        img.classList.remove('scale-100');
        img.classList.add('scale-95');
        setTimeout(() => {
            lb.classList.add('hidden');
            lb.classList.add('pointer-events-none');
        }, 300);
    }

    function isChatActivelyVisible() {
        const panel = document.getElementById('roblox-chat-panel');
        if (!panel || panel.classList.contains('hidden')) return false;
        const emptyState = document.getElementById('chat-empty-state');
        if (!emptyState || !emptyState.classList.contains('hidden')) return false;
        return true;
    }

    // Listen click outside to close popovers and search results
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#chat-input-form') && document.body.contains(e.target)) {
            document.querySelectorAll('.chat-emoji-picker-panel').forEach(p => p.remove());
            document.querySelectorAll('.chat-tenor-popover').forEach(p => p.remove());
        }
        
        const results = document.getElementById('chat-map-search-results');
        if (results && !results.contains(e.target) && e.target.id !== 'chat-map-search-input') {
            results.classList.add('hidden');
        }
    });
</script>
