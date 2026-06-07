@auth
<!-- Floating Chat Toggle Button -->
<button id="chat-toggle-btn" onclick="toggleChatPanel()" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-[#1e2022] hover:bg-[#2b2d30] border border-slate-700 text-white rounded-full flex items-center justify-center shadow-2xl transition hover:scale-105 active:scale-95 group">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-white group-hover:rotate-6 transition duration-200">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM21.375 9.75a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM16.5 18.75h-2.25a.75.75 0 0 1-.75-.75v-1.5a.75.75 0 0 1 .75-.75h2.25a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-.75.75ZM6.75 18.75H4.5a.75.75 0 0 1-.75-.75v-1.5a.75.75 0 0 1 .75-.75h2.25a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-.75.75Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
    </svg>
    <!-- Total Unread Badge -->
    <span id="chat-global-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full border border-[#1e2022] min-w-[20px] text-center">
        0
    </span>
</button>

<!-- Roblox Dark Chat Container -->
<div id="roblox-chat-panel" class="hidden fixed bottom-24 right-6 z-50 w-[92vw] sm:w-[700px] max-w-[700px] h-[520px] bg-[#1e2022] rounded-3xl border border-slate-800 shadow-2xl flex overflow-hidden animate-scale-up text-white font-sans select-none">
    
    <!-- LEFT: Active Chat Panel -->
    <div id="active-chat-panel" class="flex-1 bg-[#2b2d30] flex flex-col relative">
        <!-- Empty State (No conversation selected) -->
        <div id="chat-empty-state" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-[#2b2d30]">
            <div class="w-16 h-16 rounded-2xl bg-[#1e2022] flex items-center justify-center text-slate-400 mb-4 border border-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.083.214.125.44.125.667v4.618a2.25 2.25 0 0 1-1.022 1.884l-4.516 2.97a2.25 2.25 0 0 1-2.428 0l-4.516-2.97a2.25 2.25 0 0 1-1.022-1.884V9.178c0-.227.042-.453.125-.667m13.75 0a4.912 4.912 0 0 0-3.59-3.59m3.59 3.59a4.907 4.907 0 0 1-3.59 3.59m0-7.18a4.91 4.91 0 0 0-3.59 3.59m0 0a4.91 4.91 0 0 1-3.59-3.59m0 0a4.907 4.907 0 0 0-3.59 3.59m0 0a4.912 4.912 0 0 1-3.59-3.59m15.54 7.18v4.618a2.25 2.25 0 0 1-1.022 1.884l-4.516 2.97a2.25 2.25 0 0 1-2.428 0l-4.516-2.97a2.25 2.25 0 0 1-1.022-1.884V9.178" />
                </svg>
            </div>
            <h4 class="text-sm font-bold text-slate-200">Perpustakaan Komunitas Alinea</h4>
            <p class="text-xs text-slate-400 mt-2 max-w-[240px]">Pilih pembaca atau bagikan lokasi & buku terdekat lu lewat obrolan.</p>
        </div>

        <!-- Conversation View (Active) -->
        <div id="chat-conversation-view" class="hidden flex-1 flex flex-col h-full bg-[#2b2d30] overflow-hidden">
            <!-- Chat Header -->
            <div class="px-5 py-4 bg-[#1e2022] border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img id="chat-header-avatar" class="w-8 h-8 rounded-full object-cover border border-slate-700" src="" alt="Avatar">
                    <div>
                        <h4 id="chat-header-name" class="text-xs font-extrabold text-white leading-none">Username</h4>
                        <span class="text-[9px] text-[#00e676] font-bold mt-1 block">Online</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-slate-400 hover:text-white transition p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                    <button onclick="closeActiveChat()" class="text-slate-400 hover:text-white transition p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Scrollable Message Area -->
            <div id="chat-messages-area" class="flex-1 overflow-y-auto p-4 space-y-4 flex flex-col bg-[#2b2d30] scrollbar-thin">
                <!-- Messages render dynamically -->
            </div>

            <!-- Preview Box (For selected attachment) -->
            <div id="chat-preview-box" class="hidden px-4 py-2 border-t border-slate-800 bg-[#1e2022] flex items-center justify-between text-xs text-slate-300">
                <!-- Attachment preview -->
            </div>

            <!-- Input & Toolbar Footer -->
            <form id="chat-input-form" onsubmit="sendChatMessage(event)" class="p-3 bg-[#1e2022] border-t border-slate-800 flex flex-col gap-2 relative">
                @csrf
                <input type="hidden" name="receiver_id" id="chat-receiver-id">
                <input type="hidden" name="attachment_type" id="chat-attachment-type">
                <input type="hidden" name="attachment_url" id="chat-attachment-url">
                <input type="hidden" name="attachment_name" id="chat-attachment-name">

                <!-- TextInput Row -->
                <div class="flex items-center gap-2">
                    <input type="text" id="chat-message-text" placeholder="Ketik pesan..." class="flex-grow bg-[#393b3d] text-white rounded-xl px-4 py-2.5 text-xs outline-none border border-transparent focus:border-slate-500 transition duration-200">
                    <button type="submit" class="w-8 h-8 rounded-xl bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center transition shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </button>
                </div>

                <!-- Attachment Buttons Row -->
                <div class="flex items-center gap-3 px-1 mt-0.5">
                    <!-- Image upload -->
                    <button type="button" onclick="document.getElementById('chat-file-input').click()" class="text-slate-400 hover:text-white transition p-1" title="Unggah Gambar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </button>
                    <input type="file" id="chat-file-input" onchange="handleChatImageUpload(this)" class="hidden" accept="image/*">

                    <!-- Emoji Picker -->
                    <button type="button" onclick="toggleChatEmojiPicker(this)" class="text-slate-400 hover:text-white transition p-1" title="Emoji">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                        </svg>
                    </button>

                    <!-- Tenor GIF -->
                    <button type="button" onclick="toggleChatTenorPopover(this)" class="text-slate-400 hover:text-white transition p-1" title="Tenor GIF">
                        <span class="text-[10px] font-extrabold border-2 border-slate-400 rounded px-1 py-0.5 leading-none select-none tracking-tight">GIF</span>
                    </button>

                    <!-- Share Location Leaflet -->
                    <button type="button" onclick="openChatMapModal()" class="text-slate-400 hover:text-[#00e676] transition p-1" title="Bagikan Lokasi">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RIGHT: Chat List Panel -->
    <div class="w-[280px] bg-[#181a1b] flex flex-col border-l border-slate-800">
        <!-- Chat Title & Search Header -->
        <div class="p-4 bg-[#1e2022] border-b border-slate-800 flex flex-col gap-2.5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold tracking-tight">Chat</h3>
                <!-- Close panel widget button -->
                <button onclick="toggleChatPanel()" class="text-slate-400 hover:text-white transition p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Friend Search Input -->
            <div class="relative flex items-center">
                <input type="text" id="chat-search-input" oninput="loadChatUsers(this.value)" placeholder="Cari teman..." class="w-full bg-[#2b2d30] text-white rounded-xl pl-8 pr-3 py-1.5 text-xs outline-none border border-transparent focus:border-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 absolute left-3 text-slate-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" />
                </svg>
            </div>
        </div>

        <!-- Chat List Rows -->
        <div id="chat-users-list" class="flex-grow overflow-y-auto divide-y divide-slate-800/40 scrollbar-thin">
            <!-- Loading indicator or user rows -->
        </div>
    </div>
</div>

<!-- Chat Lightbox Modal -->
<div id="chat-lightbox" class="fixed inset-0 bg-black/90 z-[99999] hidden flex items-center justify-center opacity-0 transition-opacity duration-300 pointer-events-none" onclick="closeChatLightbox()">
    <button class="absolute top-6 right-6 text-white/70 hover:text-white transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <img id="chat-lightbox-img" class="max-w-[90%] max-h-[90%] object-contain rounded-lg shadow-2xl transform scale-95 transition-transform duration-300">
</div>

<!-- Chat Share Map Modal -->
<div id="chat-map-modal" class="fixed inset-0 bg-black/60 z-[99999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeChatMapModal()">
    <div class="bg-[#1e2022] text-white rounded-3xl w-full max-w-xl shadow-2xl border border-slate-800 overflow-hidden flex flex-col max-h-[80vh] animate-scale-up">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between bg-[#181a1b]">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Bagikan Lokasi
            </h3>
            <button onclick="closeChatMapModal()" class="text-slate-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-3 bg-[#1e2022] border-b border-slate-800 flex flex-col gap-2 relative">
            <div class="flex gap-2">
                <input type="text" id="chat-map-search-input" class="flex-1 bg-[#2b2d30] border border-slate-700 text-white rounded-xl px-3.5 py-2 text-xs outline-none focus:border-slate-500 transition" placeholder="Cari nama lokasi atau alamat...">
                <button onclick="searchChatLocation()" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-500 transition flex items-center gap-1">
                    Cari
                </button>
                <button onclick="getCurrentChatLocation()" class="bg-[#2b2d30] text-slate-300 border border-slate-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-700 transition flex items-center gap-1" title="Gunakan Lokasi Saat Ini">
                    Lokasi Saya
                </button>
            </div>
            <!-- Search Results Suggestion List -->
            <div id="chat-map-search-results" class="hidden absolute top-full left-3 right-3 bg-[#2b2d30] border border-slate-700 rounded-xl shadow-xl z-[1000] max-h-40 overflow-y-auto divide-y divide-slate-800 mt-1"></div>
        </div>
        <div id="chat-leaflet-map-container" class="flex-grow min-h-[280px] relative bg-[#2b2d30]">
            <div id="chat-leaflet-map" class="absolute inset-0"></div>
        </div>
        <div class="p-3 border-t border-slate-800 flex items-center justify-between bg-[#181a1b]">
            <p class="text-[10px] text-slate-400 max-w-[240px] truncate" id="chat-selected-coords-text">Koordinat: -6.2088, 106.8456</p>
            <div class="flex gap-2 shrink-0">
                <button type="button" onclick="closeChatMapModal()" class="px-4 py-2 rounded-xl font-bold text-xs text-slate-400 hover:bg-[#2b2d30] transition">Batal</button>
                <button onclick="confirmChatShareLocation()" class="bg-blue-600 text-white px-5 py-2 rounded-xl font-bold text-xs hover:bg-blue-500 transition shadow-sm">Bagikan Lokasi Ini</button>
            </div>
        </div>
    </div>
</div>

<!-- Chat View Map Modal -->
<div id="chat-view-map-modal" class="fixed inset-0 bg-black/60 z-[99999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeChatViewMapModal()">
    <div class="bg-[#1e2022] text-white rounded-3xl w-full max-w-xl shadow-2xl border border-slate-800 overflow-hidden flex flex-col max-h-[80vh] animate-scale-up">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between bg-[#181a1b]">
            <h3 class="text-sm font-bold flex items-center gap-2 truncate max-w-[80%]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span id="chat-view-map-title">Lokasi Terbagikan</span>
            </h3>
            <button onclick="closeChatViewMapModal()" class="text-slate-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div id="chat-view-leaflet-map-container" class="flex-grow min-h-[300px] relative bg-[#2b2d30]">
            <div id="chat-view-leaflet-map" class="absolute inset-0"></div>
        </div>
        
        <div class="p-3 border-t border-slate-800 flex items-center justify-between bg-[#181a1b]">
            <p class="text-[10px] text-slate-400 max-w-[200px] truncate" id="chat-view-map-coords-text">Koordinat: -6.2088, 106.8456</p>
            <div class="flex gap-2 shrink-0">
                <button type="button" onclick="closeChatViewMapModal()" class="px-4 py-2 rounded-xl font-bold text-xs text-slate-400 hover:bg-[#2b2d30] transition">Tutup</button>
                <a id="chat-view-map-gmaps-link" href="#" target="_blank" class="bg-blue-600 text-white px-5 py-2 rounded-xl font-bold text-xs hover:bg-blue-500 transition shadow-sm flex items-center gap-1.5">
                    Buka di Google Maps
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chat Emoji & GIF Pickers style helper -->
<style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #393b3d;
        border-radius: 10px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #4f5255;
    }
</style>

<script>
    // Ensure Leaflet assets are loaded globally
    if (!document.getElementById('leaflet-css-chat')) {
        const link = document.createElement('link');
        link.id = 'leaflet-css-chat';
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
    }
    
    // Global variable references
    let chatActiveUserId = null;
    let chatMap = null;
    let chatMapMarker = null;
    let chatViewMap = null;
    let chatViewMapMarker = null;
    let chatMapSelectedCoords = [-7.9839, 112.6214]; // Default Malang
    
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

    function toggleChatEmojiPicker(buttonEl) {
        // Close existing
        document.querySelectorAll('.chat-emoji-picker-panel').forEach(p => p.remove());
        const formEl = document.getElementById('chat-input-form');
        
        let picker = formEl.querySelector('.chat-emoji-picker-panel');
        if (picker) {
            picker.remove();
            return;
        }

        picker = document.createElement('div');
        picker.className = 'chat-emoji-picker-panel absolute bottom-28 left-4 right-4 bg-[#1e2022] border border-slate-800 rounded-2xl shadow-2xl z-[1000] flex flex-col max-h-[220px] overflow-hidden animate-scale-up';
        picker.onclick = (e) => e.stopPropagation();

        picker.innerHTML = `
            <div class="p-2 border-b border-slate-800 flex items-center justify-between bg-[#181a1b]">
                <span class="text-[10px] font-bold text-slate-300">Pilih Emoji</span>
                <input type="text" placeholder="Cari..." class="chat-emoji-search bg-[#2b2d30] border border-slate-700 text-white rounded-lg px-2 py-0.5 text-[10px] outline-none w-28 focus:border-slate-500">
            </div>
            <div class="chat-emoji-categories flex gap-1 border-b border-slate-800 p-1 overflow-x-auto bg-[#181a1b]/55 scrollbar-thin">
                ${EMOJI_DATA.map((cat, idx) => `
                    <button type="button" class="chat-category-btn p-1 hover:bg-[#2b2d30] rounded-lg text-xs transition ${idx === 0 ? 'bg-[#2b2d30]' : ''}" data-idx="${idx}">
                        ${cat.icon}
                    </button>
                `).join('')}
            </div>
            <div class="chat-emoji-grid flex-1 overflow-y-auto p-2 grid grid-cols-7 gap-1 bg-[#1e2022] max-h-[120px] scrollbar-thin">
            </div>
        `;

        formEl.appendChild(picker);
        const grid = picker.querySelector('.chat-emoji-grid');
        const input = picker.querySelector('.chat-emoji-search');
        const textarea = document.getElementById('chat-message-text');

        const render = (list) => {
            grid.innerHTML = list.map(emoji => `
                <button type="button" class="chat-emoji-btn hover:scale-125 p-1 text-sm transition flex items-center justify-center">
                    ${emoji}
                </button>
            `).join('');

            grid.querySelectorAll('.chat-emoji-btn').forEach(btn => {
                btn.onclick = () => {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const val = textarea.value;
                    textarea.value = val.substring(0, start) + btn.textContent.trim() + val.substring(end);
                    textarea.focus();
                };
            });
        };

        const showCategory = (idx) => {
            picker.querySelectorAll('.chat-category-btn').forEach(btn => {
                btn.classList.toggle('bg-[#2b2d30]', parseInt(btn.dataset.idx) === idx);
            });
            render(EMOJI_DATA[idx].emojis);
        };

        picker.querySelectorAll('.chat-category-btn').forEach(btn => {
            btn.onclick = () => showCategory(parseInt(btn.dataset.idx));
        });

        if (input) {
            input.oninput = (e) => {
                const val = e.target.value.toLowerCase().trim();
                if (!val) {
                    showCategory(0);
                    return;
                }
                const filtered = [];
                EMOJI_DATA.forEach(cat => {
                    cat.emojis.forEach(emo => {
                        if (emo.includes(val) || cat.category.toLowerCase().includes(val)) {
                            filtered.push(emo);
                        }
                    });
                });
                render(filtered);
            };
        }

        showCategory(0);
    }

    // Tenor GIF Integration
    function toggleChatTenorPopover(btn) {
        document.querySelectorAll('.chat-tenor-popover').forEach(p => p.remove());
        const formEl = document.getElementById('chat-input-form');

        const pop = document.createElement('div');
        pop.className = 'chat-tenor-popover absolute bottom-28 left-4 right-4 bg-[#1e2022] border border-slate-800 rounded-2xl shadow-2xl z-[1000] flex flex-col h-[260px] overflow-hidden animate-scale-up';
        pop.onclick = (e) => e.stopPropagation();

        pop.innerHTML = `
            <div class="p-2 border-b border-slate-800 flex items-center justify-between bg-[#181a1b]">
                <span class="text-[10px] font-bold text-slate-300">Pilih GIF (Tenor)</span>
                <input type="text" placeholder="Cari GIF..." class="chat-tenor-search bg-[#2b2d30] border border-slate-700 text-white rounded-lg px-2 py-0.5 text-[10px] outline-none w-32 focus:border-slate-500">
            </div>
            <div class="chat-tenor-results flex-grow overflow-y-auto p-2 grid grid-cols-2 gap-1.5 scrollbar-thin">
                <div class="col-span-2 flex justify-center py-6"><span class="text-xs text-slate-400">Memuat tren...</span></div>
            </div>
        `;

        formEl.appendChild(pop);
        const results = pop.querySelector('.chat-tenor-results');
        const input = pop.querySelector('.chat-tenor-search');

        const fetchGifs = async (query = '') => {
            results.innerHTML = '<div class="col-span-2 flex justify-center py-6"><span class="text-xs text-slate-400">Mencari...</span></div>';
            let url = `https://g.tenor.com/v1/trending?key=${TENOR_API_KEY}&limit=10`;
            if (query) {
                url = `https://g.tenor.com/v1/search?key=${TENOR_API_KEY}&q=${encodeURIComponent(query)}&limit=10`;
            }
            try {
                const res = await fetch(url);
                const data = await res.json();
                results.innerHTML = '';
                if (data.results && data.results.length > 0) {
                    data.results.forEach(item => {
                        if (item.media && item.media[0]) {
                            const gifUrl = item.media[0].gif.url;
                            const previewUrl = item.media[0].tinygif?.url || gifUrl;
                            const img = document.createElement('img');
                            img.src = previewUrl;
                            img.className = 'w-full h-16 object-cover cursor-pointer rounded-lg hover:scale-105 transition duration-150 border border-slate-850';
                            img.onclick = () => {
                                selectChatAttachment('tenor', gifUrl, 'Tenor GIF');
                                pop.remove();
                            };
                            results.appendChild(img);
                        }
                    });
                } else {
                    results.innerHTML = '<span class="col-span-2 text-center py-4 text-xs text-slate-500">Tidak ada hasil.</span>';
                }
            } catch (err) {
                results.innerHTML = '<span class="col-span-2 text-center py-4 text-xs text-red-500">Gagal memuat.</span>';
            }
        };

        if (input) {
            let timeout = null;
            input.oninput = (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    fetchGifs(e.target.value.trim());
                }, 400);
            };
        }

        fetchGifs();
    }

    function selectChatAttachment(type, url, name) {
        document.getElementById('chat-attachment-type').value = type;
        document.getElementById('chat-attachment-url').value = url;
        document.getElementById('chat-attachment-name').value = name;

        const preview = document.getElementById('chat-preview-box');
        preview.classList.remove('hidden');
        
        let previewContent = '';
        if (type === 'image') {
            previewContent = `<img src="${url}" class="w-8 h-8 object-cover rounded-md border border-slate-700">`;
        } else if (type === 'tenor') {
            previewContent = `<img src="${url}" class="w-8 h-8 object-cover rounded-md border border-slate-700">`;
        } else if (type === 'gmaps') {
            previewContent = `<span class="flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Lokasi terpilih</span>`;
        }

        preview.innerHTML = `
            <div class="flex items-center gap-2">
                ${previewContent}
                <div>
                    <p class="font-extrabold text-[10px] text-white">${name}</p>
                    <p class="text-[8px] text-slate-400">Lampiran siap dikirim</p>
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

    // --- LEAFLET MAPS INTEGRATION FOR CHAT ---
    function openChatMapModal() {
        document.getElementById('chat-map-modal').classList.remove('hidden');
        
        // Ensure Leaflet JS is fully initialized before mapping
        if (typeof L === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => {
                setTimeout(initChatMap, 300);
            };
            document.head.appendChild(script);
        } else {
            setTimeout(initChatMap, 100);
        }
    }

    function initChatMap() {
        if (chatMap) {
            chatMap.invalidateSize();
            return;
        }

        chatMap = L.map('chat-leaflet-map', {
            zoomControl: false
        }).setView(chatMapSelectedCoords, 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(chatMap);

        L.control.zoom({ position: 'bottomright' }).addTo(chatMap);

        chatMapMarker = L.marker(chatMapSelectedCoords, { draggable: true }).addTo(chatMap);

        const updateCoords = (lat, lng) => {
            chatMapSelectedCoords = [lat, lng];
            document.getElementById('chat-selected-coords-text').textContent = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        };

        chatMapMarker.on('dragend', function (e) {
            const pos = chatMapMarker.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        chatMap.on('click', function (e) {
            chatMapMarker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });

        updateCoords(chatMapSelectedCoords[0], chatMapSelectedCoords[1]);
    }

    function closeChatMapModal() {
        document.getElementById('chat-map-modal').classList.add('hidden');
    }

    async function searchChatLocation() {
        const query = document.getElementById('chat-map-search-input').value.trim();
        if (!query) return;

        const resultsContainer = document.getElementById('chat-map-search-results');
        resultsContainer.innerHTML = '<div class="p-2.5 text-center text-slate-400 text-xs">Mencari...</div>';
        resultsContainer.classList.remove('hidden');

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`);
            const data = await res.json();
            
            resultsContainer.innerHTML = '';
            if (data.length > 0) {
                data.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'p-2.5 hover:bg-[#181a1b] cursor-pointer text-xs transition text-white border-b border-slate-800';
                    row.textContent = item.display_name;
                    row.onclick = () => {
                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lon);
                        chatMapSelectedCoords = [lat, lng];
                        chatMapMarker.setLatLng(chatMapSelectedCoords);
                        chatMap.setView(chatMapSelectedCoords, 15);
                        document.getElementById('chat-selected-coords-text').textContent = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        resultsContainer.classList.add('hidden');
                    };
                    resultsContainer.appendChild(row);
                });
            } else {
                resultsContainer.innerHTML = '<div class="p-2.5 text-center text-slate-400 text-xs">Lokasi tidak ditemukan.</div>';
            }
        } catch (e) {
            resultsContainer.innerHTML = '<div class="p-2.5 text-center text-red-500 text-xs">Gagal melakukan pencarian.</div>';
        }
    }

    function getCurrentChatLocation() {
        if (!navigator.geolocation) {
            alert("Geolocation tidak didukung oleh browser Anda.");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                chatMapSelectedCoords = [lat, lng];
                chatMapMarker.setLatLng(chatMapSelectedCoords);
                chatMap.setView(chatMapSelectedCoords, 15);
                document.getElementById('chat-selected-coords-text').textContent = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            },
            (err) => {
                alert("Gagal mendapatkan lokasi. Pastikan izin GPS aktif.");
            },
            { enableHighAccuracy: true }
        );
    }

    function confirmChatShareLocation() {
        const coordsStr = `${chatMapSelectedCoords[0].toFixed(6)},${chatMapSelectedCoords[1].toFixed(6)}`;
        selectChatAttachment('gmaps', coordsStr, 'Lokasi Terbagikan');
        closeChatMapModal();
    }

    // --- VIEW LOCATION MAP ---
    function openChatViewMap(coordsStr, locationName) {
        document.getElementById('chat-view-map-modal').classList.remove('hidden');
        document.getElementById('chat-view-map-title').textContent = locationName;
        document.getElementById('chat-view-map-coords-text').textContent = `Koordinat: ${coordsStr}`;
        document.getElementById('chat-view-map-gmaps-link').href = `https://www.google.com/maps/search/?api=1&query=${coordsStr}`;

        const parts = coordsStr.split(',');
        const lat = parseFloat(parts[0]);
        const lng = parseFloat(parts[1]);

        setTimeout(() => {
            if (chatViewMap) {
                chatViewMap.setView([lat, lng], 15);
                chatViewMapMarker.setLatLng([lat, lng]);
                chatViewMap.invalidateSize();
                return;
            }

            chatViewMap = L.map('chat-view-leaflet-map', {
                zoomControl: false
            }).setView([lat, lng], 15);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(chatViewMap);

            chatViewMapMarker = L.marker([lat, lng]).addTo(chatViewMap);
            L.control.zoom({ position: 'bottomright' }).addTo(chatViewMap);
        }, 200);
    }

    function closeChatViewMapModal() {
        document.getElementById('chat-view-map-modal').classList.add('hidden');
    }

    // --- LOAD MESSAGES & USERS CONTROLLER ---
    async function loadChatUsers(query = '') {
        const list = document.getElementById('chat-users-list');
        list.innerHTML = '<div class="py-12 text-center"><span class="text-xs text-slate-400">Memuat obrolan...</span></div>';

        try {
            const res = await fetch(`/chat/users?q=${encodeURIComponent(query)}`);
            const users = await res.json();
            list.innerHTML = '';

            let globalUnread = 0;

            if (users.length > 0) {
                users.forEach(u => {
                    globalUnread += u.unread_count;

                    const row = document.createElement('div');
                    row.className = `p-3.5 flex items-center justify-between cursor-pointer hover:bg-[#2b2d30]/50 transition duration-150 relative ${chatActiveUserId === u.id ? 'bg-[#2b2d30]' : ''}`;
                    row.onclick = () => selectChatUser(u.id, u.name, u.profile_photo);

                    row.innerHTML = `
                        <div class="flex items-center gap-3 min-w-0 flex-grow pr-3">
                            <img class="w-10 h-10 rounded-full object-cover border border-slate-800" src="${u.profile_photo}">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold text-white truncate">${u.name}</h4>
                                <p class="text-[10px] text-slate-400 truncate mt-1">${u.latest_message || 'Belum ada obrolan'}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                            <span class="text-[8px] text-slate-500 font-bold">${u.latest_message_time_human || ''}</span>
                            ${u.unread_count > 0 ? `<span class="bg-red-500 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full min-w-[18px] text-center border border-[#181a1b]">${u.unread_count}</span>` : ''}
                        </div>
                    `;
                    list.appendChild(row);
                });
            } else {
                list.innerHTML = '<div class="py-12 text-center text-xs text-slate-500">Tidak ada pengguna ditemukan.</div>';
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
            list.innerHTML = '<div class="py-12 text-center text-xs text-red-500">Gagal memuat obrolan.</div>';
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

        // Clear preview & form
        clearChatAttachment();
        document.getElementById('chat-message-text').value = '';

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
                    <div class="relative overflow-hidden rounded-xl border border-slate-700 bg-black/20 mt-1 cursor-pointer max-w-[200px]" onclick="openChatLightbox('${msg.attachment_path}')">
                        <img src="${msg.attachment_path}" class="w-full max-h-36 object-cover rounded-xl hover:opacity-90 transition">
                    </div>
                `;
            } else if (msg.attachment_type === 'gmaps') {
                attachmentHtml = `
                    <div onclick="openChatViewMap('${msg.attachment_path}', '${msg.attachment_name || 'Lokasi Terbagikan'}')" class="mt-1 flex items-center gap-2 bg-[#181a1b] border border-slate-800 rounded-xl p-2 hover:bg-[#1e2022] transition cursor-pointer max-w-[200px]">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-extrabold text-white truncate">${msg.attachment_name || 'Lokasi'}</p>
                            <p class="text-[8px] text-slate-400">Lihat Peta</p>
                        </div>
                    </div>
                `;
            }
        }

        const avatarUrl = !isMe && document.getElementById('chat-header-avatar').src 
            ? document.getElementById('chat-header-avatar').src 
            : '{{ auth()->user()->profile_photo ? (str_starts_with(auth()->user()->profile_photo, "http") ? auth()->user()->profile_photo : asset("storage/" . auth()->user()->profile_photo)) : asset("Gambar/default_avatar.png") }}';

        wrapper.innerHTML = `
            ${!isMe ? `<img src="${avatarUrl}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-slate-800 mt-0.5">` : ''}
            <div class="flex flex-col ${isMe ? 'items-end' : 'items-start'}">
                <div class="px-3.5 py-2 rounded-2xl text-[11px] leading-relaxed break-words max-w-full ${isMe ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-[#1e2022] text-slate-200 rounded-tl-none border border-slate-800'}">
                    ${msg.message ? escapeChatHtml(msg.message) : ''}
                    ${attachmentHtml}
                </div>
                <span class="text-[7px] text-slate-500 mt-1 font-bold block">${msg.created_at_formatted}</span>
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

    // Listen click outside to close popovers
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#chat-input-form')) {
            document.querySelectorAll('.chat-emoji-picker-panel').forEach(p => p.remove());
            document.querySelectorAll('.chat-tenor-popover').forEach(p => p.remove());
        }
    });

    // Real-time Echo listener configuration
    document.addEventListener('DOMContentLoaded', () => {
        // Load initial users
        loadChatUsers();

        // Check if Echo is loaded
        if (window.Echo) {
            const userId = '{{ auth()->id() }}';
            window.Echo.private('chat.' + userId)
                .listen('.MessageSent', (e) => {
                    const msg = e.message;
                    
                    // If message is from the currently active chat user
                    if (chatActiveUserId && parseInt(msg.sender_id) === parseInt(chatActiveUserId)) {
                        appendMessageToDOM(msg, true);
                        markActiveChatAsRead();
                    } else {
                        // Play message alert sound or flash widget
                        if (typeof window.playChatAlertSound === 'function') {
                            window.playChatAlertSound();
                        }
                    }
                    
                    // Reload chat list to update last message & unread badge
                    loadChatUsers();
                });
        }
    });
</script>
@endauth
