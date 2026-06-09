@auth
<!-- Floating Chat Toggle Button -->
<button id="chat-toggle-btn" onclick="toggleChatPanel()" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-gradient-to-tr from-[#1a3a5c] to-[#255283] hover:from-[#122b45] hover:to-[#1a3a5c] border border-[#1a3a5c] text-white rounded-full flex items-center justify-center shadow-2xl transition hover:scale-105 active:scale-95 group">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-white group-hover:rotate-6 transition duration-200">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM21.375 9.75a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM16.5 18.75h-2.25a.75.75 0 0 1-.75-.75v-1.5a.75.75 0 0 1 .75-.75h2.25a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-.75.75ZM6.75 18.75H4.5a.75.75 0 0 1-.75-.75v-1.5a.75.75 0 0 1 .75-.75h2.25a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-.75.75Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
    </svg>
    <!-- Total Unread Badge -->
    <span id="chat-global-badge" class="hidden absolute -top-1 -right-1 bg-[#1a3a5c] text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full border border-white min-w-[20px] text-center">
        0
    </span>
</button>

<!-- Roblox Dark Chat Container -> Refactored to Alinea Light theme -->
<div id="roblox-chat-panel" class="hidden fixed bottom-24 right-6 z-50 w-[92vw] sm:w-[700px] max-w-[700px] h-[520px] bg-white rounded-3xl border border-gray-200 shadow-2xl flex overflow-hidden animate-scale-up text-slate-800 font-sans select-none">
    
    <!-- LEFT: Active Chat Panel -->
    <div id="active-chat-panel" class="flex-1 bg-white flex flex-col relative">
        <!-- Empty State (No conversation selected) -->
        <div id="chat-empty-state" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-white">
            <div class="w-16 h-16 rounded-2xl bg-white flex items-center justify-center mb-4 border border-gray-100 shadow-sm p-1">
                <img src="{{ asset('Gambar/logo_alinea_tanpa_tulisan2.png') }}" class="w-full h-full object-contain rounded-xl" alt="Alinea Favicon">
            </div>
            <h4 class="text-sm font-bold text-[#1a3a5c]">Perpustakaan Komunitas Alinea</h4>
            <p class="text-xs text-slate-500 mt-2 max-w-[240px]">Pilih pembaca atau bagikan lokasi & buku terdekat lu lewat obrolan.</p>
        </div>

        <!-- Conversation View (Active) -->
        <div id="chat-conversation-view" class="hidden flex-1 flex flex-col h-full bg-slate-50 overflow-hidden">
            <!-- Chat Header -->
            <div class="px-5 py-4 bg-white border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img id="chat-header-avatar" class="w-8 h-8 rounded-full object-cover border border-gray-100" src="" alt="Avatar">
                    <div>
                        <h4 id="chat-header-name" class="text-xs font-extrabold text-[#1a3a5c] leading-none">Username</h4>
                        <span id="chat-header-status" class="text-[9px] text-slate-400 font-bold mt-1 block">Offline</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="closeActiveChat()" class="text-slate-400 hover:text-[#1a3a5c] transition p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Scrollable Message Area -->
            <div id="chat-messages-area" class="flex-1 overflow-y-auto p-4 space-y-4 flex flex-col bg-slate-50 scrollbar-thin">
                <!-- Messages render dynamically -->
            </div>

            <!-- Preview Box (For selected attachment) -->
            <div id="chat-preview-box" class="hidden px-4 py-2 border-t border-gray-100 bg-white flex items-center justify-between text-xs text-slate-600">
                <!-- Attachment preview -->
            </div>

            <!-- Input & Toolbar Footer -->
            <form id="chat-input-form" onsubmit="sendChatMessage(event)" class="p-3 bg-white border-t border-gray-100 flex flex-col gap-2 relative">
                @csrf
                <input type="hidden" name="receiver_id" id="chat-receiver-id">
                <input type="hidden" name="attachment_type" id="chat-attachment-type">
                <input type="hidden" name="attachment_url" id="chat-attachment-url">
                <input type="hidden" name="attachment_name" id="chat-attachment-name">

                <!-- TextInput Row -->
                <div class="flex items-center gap-2">
                    <textarea id="chat-message-text" placeholder="Ketik pesan..." maxlength="256" rows="1" class="flex-grow bg-slate-100 border border-gray-200 text-slate-800 placeholder-slate-450 rounded-xl px-4 py-2 text-xs outline-none focus:bg-white focus:border-[#1a3a5c]/40 transition duration-200 resize-none h-[36px] max-h-24 overflow-y-auto leading-relaxed"></textarea>
                    <button type="submit" class="w-8 h-8 rounded-xl bg-[#1a3a5c] hover:bg-[#122b45] text-white flex items-center justify-center transition shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </button>
                </div>

                <!-- Attachment Buttons Row -->
                <div class="flex items-center gap-3 px-1 mt-0.5">
                    <!-- Image upload -->
                    <button type="button" onclick="document.getElementById('chat-file-input').click()" class="text-slate-400 hover:text-[#1a3a5c] transition p-1" title="Unggah Gambar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </button>
                    <input type="file" id="chat-file-input" onchange="handleChatImageUpload(this)" class="hidden" accept="image/*">

                    <!-- Emoji Picker -->
                    <button type="button" onclick="toggleChatEmojiPicker(this)" class="text-slate-400 hover:text-[#1a3a5c] transition p-1" title="Emoji">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                        </svg>
                    </button>

                    <!-- Tenor GIF -->
                    <button type="button" onclick="toggleChatTenorPopover(this)" class="text-slate-400 hover:text-[#1a3a5c] transition p-1" title="Tenor GIF">
                        <span class="text-[9px] font-extrabold border-2 border-slate-300 hover:border-[#1a3a5c] rounded px-1 py-0.5 leading-none select-none tracking-tight">GIF</span>
                    </button>

                    <!-- Share Location Leaflet -->
                    <button type="button" onclick="openChatMapModal()" class="text-slate-400 hover:text-[#1a3a5c] transition p-1" title="Bagikan Lokasi">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 115 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RIGHT: Chat List Panel -->
    <div class="w-[280px] bg-slate-50 flex flex-col border-l border-gray-100">
        <!-- Chat Title & Search Header -->
        <div class="p-4 bg-white border-b border-gray-100 flex flex-col gap-2.5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-[#1a3a5c] tracking-tight">Chat</h3>
                <!-- Close panel widget button -->
                <button onclick="toggleChatPanel()" class="text-slate-400 hover:text-slate-700 transition p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Friend Search Input -->
            <div class="relative flex items-center">
                <input type="text" id="chat-search-input" oninput="loadChatUsers(this.value)" placeholder="Cari teman..." class="w-full bg-slate-100 border border-gray-200 text-slate-800 placeholder-slate-450 rounded-xl pl-8 pr-3 py-1.5 text-xs outline-none focus:bg-white focus:border-[#1a3a5c]/40 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 absolute left-3 text-slate-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" />
                </svg>
            </div>
        </div>

        <!-- Chat List Rows -->
        <div id="chat-users-list" class="flex-grow overflow-y-auto divide-y divide-gray-100 scrollbar-thin bg-white">
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
    <div class="bg-white text-slate-800 rounded-3xl w-full max-w-xl shadow-2xl border border-gray-150 overflow-hidden flex flex-col max-h-[80vh] animate-scale-up">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-[#1a3a5c] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1a3a5c]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Bagikan Lokasi
            </h3>
            <button onclick="closeChatMapModal()" class="text-slate-400 hover:text-slate-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-3 bg-white border-b border-gray-100 flex flex-col gap-2 relative">
            <div class="flex gap-2">
                <input type="text" id="chat-map-search-input" class="flex-1 bg-slate-100 border border-gray-200 text-slate-850 rounded-xl px-3.5 py-2 text-xs outline-none focus:border-[#1a3a5c]/40 focus:bg-white transition" placeholder="Cari nama lokasi atau alamat...">
                <button onclick="searchChatLocation()" class="bg-[#1a3a5c] hover:bg-[#122b45] text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1">
                    Cari
                </button>
                <button onclick="getCurrentChatLocation()" class="bg-slate-100 text-slate-655 border border-gray-200 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-200 transition flex items-center gap-1" title="Gunakan Lokasi Saat Ini">
                    Lokasi Saya
                </button>
            </div>
            <!-- Search Results Suggestion List -->
            <div id="chat-map-search-results" class="hidden absolute top-full left-3 right-3 bg-white border border-gray-200 rounded-xl shadow-xl z-[1000] max-h-40 overflow-y-auto divide-y divide-gray-100 mt-1"></div>
        </div>
        <div id="chat-leaflet-map-container" class="flex-grow min-h-[280px] relative bg-slate-100">
            <div id="chat-leaflet-map" class="absolute inset-0"></div>
            <!-- Layer Selector Floating Control -->
            <div class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur-sm p-1.5 rounded-xl border border-gray-200 shadow-lg flex gap-1">
                <button type="button" onclick="switchChatMapLayer('streets')" id="chat-layer-btn-streets" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white">Jalan</button>
                <button type="button" onclick="switchChatMapLayer('satellite')" id="chat-layer-btn-satellite" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Satelit</button>
                <button type="button" onclick="switchChatMapLayer('dark')" id="chat-layer-btn-dark" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Gelap</button>
            </div>
        </div>
        <div class="p-3 border-t border-gray-100 flex items-center justify-between bg-slate-50">
            <p class="text-[10px] text-slate-500 max-w-[240px] truncate" id="chat-selected-coords-text">Koordinat: -6.2088, 106.8456</p>
            <div class="flex gap-2 shrink-0">
                <button type="button" onclick="closeChatMapModal()" class="px-4 py-2 rounded-xl font-bold text-xs text-slate-500 hover:bg-slate-200 transition">Batal</button>
                <button onclick="confirmChatShareLocation()" class="bg-[#1a3a5c] hover:bg-[#122b45] text-white px-5 py-2 rounded-xl font-bold text-xs transition shadow-sm">Bagikan Lokasi Ini</button>
            </div>
        </div>
    </div>
</div>

<!-- Chat View Map Modal -->
<div id="chat-view-map-modal" class="fixed inset-0 bg-black/60 z-[99999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeChatViewMapModal()">
    <div class="bg-white text-slate-800 rounded-3xl w-full max-w-xl shadow-2xl border border-gray-150 overflow-hidden flex flex-col max-h-[80vh] animate-scale-up">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-[#1a3a5c] flex items-center gap-2 truncate max-w-[80%]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1a3a5c]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span id="chat-view-map-title">Lokasi Terbagikan</span>
            </h3>
            <button onclick="closeChatViewMapModal()" class="text-slate-450 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div id="chat-view-leaflet-map-container" class="flex-grow min-h-[300px] relative bg-slate-100">
            <div id="chat-view-leaflet-map" class="absolute inset-0"></div>
            <!-- Layer Selector Floating Control -->
            <div class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur-sm p-1.5 rounded-xl border border-gray-200 shadow-lg flex gap-1">
                <button type="button" onclick="switchChatViewMapLayer('streets')" id="chat-view-layer-btn-streets" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white">Jalan</button>
                <button type="button" onclick="switchChatViewMapLayer('satellite')" id="chat-view-layer-btn-satellite" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Satelit</button>
                <button type="button" onclick="switchChatViewMapLayer('dark')" id="chat-view-layer-btn-dark" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Gelap</button>
            </div>
        </div>
        
        <div class="p-3 border-t border-gray-100 flex items-center justify-between bg-slate-50">
            <p class="text-[10px] text-slate-500 max-w-[200px] truncate" id="chat-view-map-coords-text">Koordinat: -6.2088, 106.8456</p>
            <div class="flex gap-2 shrink-0">
                <button type="button" onclick="closeChatViewMapModal()" class="px-4 py-2 rounded-xl font-bold text-xs text-slate-500 hover:bg-slate-200 transition">Tutup</button>
                <a id="chat-view-map-gmaps-link" href="#" target="_blank" class="bg-[#1a3a5c] hover:bg-[#122b45] text-white px-5 py-2 rounded-xl font-bold text-xs transition shadow-sm flex items-center gap-1.5">
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
        background: #cbd5e1;
        border-radius: 10px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
    if (typeof EMOJI_DATA === 'undefined') {
        var EMOJI_DATA = [
        {
            category: 'Ekspresi',
            icon: '😀',
            emojis: ['😀','😃','😄','😁','😆','😅','😂','🤣','😊','😇','🙂','🙃','😉','😌','😍','🥰','😘','😗','😙','😚','😋','😛','😝','😜','🤪','🤨','🧐','🤓','😎','🥸','🤩','🥳','😏','😒','😞','😔','😟','😕','🙁','☹️','😣','😖','😫','😩','🥺','😢','😭','😤','😠','😡','🤬','🤯','😳','🥵','🥶','😱','😨','😰','😥','😓','🤔','🫣','🤭','🫢','🫡','🤫','🫠','🤥','😶','😐','😑','😬','🙄','😯','😦','😧','😮','😲','🥱','😴','🤤','😪','😵','😵‍💫','🥴','🤢','🤮','🤧','😷','🤒','🤕','🤑','🤠','😈','👿','👹','👺','💀','☠️','👽','👾','🤖','🎃']
        },
        {
            category: 'Tangan & Tubuh',
            icon: '👋',
            emojis: ['👋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🫰','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏','✍️','💅','🤳','💪','🦾','👂','🦻','👃','🧠','🫀','🫁','🦷','🦴','👀','👁️','👅','👄']
        },
        {
            category: 'Hati & Cinta',
            icon: '❤️',
            emojis: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❤️‍🔥','❤️‍🩹','❣️','💕','💞','💓','💗','💖','💘','💝','💟','💌','💋']
        },
        {
            category: 'Hewan & Alam',
            icon: '🐱',
            emojis: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐽','🐸','🐵','🙈','🙉','🙊','🐒','🐔','🐧','🐦','🐤','🐣','🐥','🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🪱','🐛','🦋','🐌','🐞','🐜','🪰','🪲','🦗','🕷️','🕸️','🦂','🐢','🐍','🦎','🐙','🦑','🦞','🦀','🐡','🐠','🐟','🐬','🐳','🐋','🦈','🐊','🐆','🐅']
        },
        {
            category: 'Makanan & Minuman',
            icon: '🍎',
            emojis: ['🍎','🧡','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🥑','🥦','🥬','🥒','🌶️','🫑','🌽','🥕','🍞','🥐','🥞','🧇','🧀','🍖','🍗','🥩','🥓','🍔','🍟','🌭','🥪','taco','🌯','🥗','🍿','🍳','🥤','🧋','☕','🍵','🍺','🍻','🍷'].map(n => n === 'taco' ? '🌮' : n)
        },
        {
            category: 'Aktivitas',
            icon: '⚽',
            emojis: ['⚽','🏀','🏈','⚾','🥎','🎾','🏐','🎱','🏓','🏸','⛳','🏹','🥊','🥋','🛹','⛸️','⛷️','🏂','🏋️','🤸','⛹️','🤺','🤾','🏌️','🧘','🏄','🏊','🚣','🧗','🚴','🏆','🥇','🥈','🥉','🏅','🎫','🎬','🎨','🎧']
        },
        {
            category: 'Perjalanan & Tempat',
            icon: '🚗',
            emojis: ['🚗','🚕','🚙','🚌','🏎️','🚓','🚒','🚚','🛵','🏍️','🚲','🚨','🛑','✈️','🚀','🛸','🚁','⛵','⚓','🗺️','🧭','🏔️','⛰️','🌋','🗻','🏕️','⛺','🏠','🏡','🏢','🏥','🏦','🏨','🏫','🏰','🏯','🗼','🗽','⛩️','🕋','🪐','🌑','🌕','☀️','⭐','☁️','🌧️','❄️','🔥']
        },
        {
            category: 'Objek & Simbol',
            icon: '💡',
            emojis: ['⌚','📱','💻','⌨️','📷','📺','📻','🕯️','💡','🔦','🧱','🔪','🛡️','🚬','🔮','🧿','💈','🧲','🧪','🧬','🗝️','🔑','🔨','🪛','🔧','🪚','⚙️','⚖️','🔗','⛓️','🩹','🩺','📦','✉️','🏷️','✏️','✒️','📝','💼','📁','📅','🗑️','🔒','🔓','🔔','📣','❓','❔','❗','❕','💯']
        }
    ];
    }

    // Ensure Leaflet assets are loaded globally
    if (!document.getElementById('leaflet-css-chat')) {
        const link = document.createElement('link');
        link.id = 'leaflet-css-chat';
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
    }
    
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
        picker.className = 'chat-emoji-picker-panel absolute bottom-28 left-4 right-4 bg-white border border-gray-200 rounded-2xl shadow-2xl z-[1000] flex flex-col max-h-[220px] overflow-hidden animate-scale-up';
        picker.onclick = (e) => e.stopPropagation();

        picker.innerHTML = `
            <div class="p-2 border-b border-gray-100 flex items-center justify-between bg-slate-50">
                <span class="text-[10px] font-bold text-[#1a3a5c]">Pilih Emoji</span>
                <div class="flex items-center gap-1.5">
                    <input type="text" placeholder="Cari..." class="chat-emoji-search bg-slate-100 border border-gray-200 text-slate-800 placeholder-slate-450 rounded-lg px-2 py-0.5 text-[10px] outline-none w-24 focus:bg-white focus:border-[#1a3a5c]/40">
                    <button type="button" class="btn-close-emoji text-slate-400 hover:text-red-500 transition" title="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="chat-emoji-categories flex gap-1 border-b border-gray-100 p-1 overflow-x-auto bg-slate-50 scrollbar-thin">
                ${EMOJI_DATA.map((cat, idx) => `
                    <button type="button" class="chat-category-btn p-1 hover:bg-slate-200 rounded-lg text-xs transition ${idx === 0 ? 'bg-slate-200' : ''}" data-idx="${idx}">
                        ${cat.icon}
                    </button>
                `).join('')}
            </div>
            <div class="chat-emoji-grid flex-1 overflow-y-auto p-2 grid grid-cols-7 gap-1 bg-white max-h-[120px] scrollbar-thin">
            </div>
        `;

        formEl.appendChild(picker);
        picker.querySelector('.btn-close-emoji').onclick = () => picker.remove();
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
                btn.classList.toggle('bg-slate-200', parseInt(btn.dataset.idx) === idx);
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
    // Tenor GIF Integration
    const CHAT_TENOR_CATEGORIES = [
        { name: 'Favorit', query: 'love', overlayClass: 'bg-[#ff4b5c]/60 hover:bg-[#ff4b5c]/50' },
        { name: 'Tren Baru', query: 'trending', overlayClass: 'bg-black/40 hover:bg-black/30' },
        { name: 'Keren', query: 'awesome', overlayClass: 'bg-black/40 hover:bg-black/30' },
        { name: 'Bercanda', query: 'jk', overlayClass: 'bg-black/40 hover:bg-black/30' },
        { name: 'Semoga Sukses', query: 'good luck', overlayClass: 'bg-black/40 hover:bg-black/30' },
        { name: 'Tos', query: 'high five', overlayClass: 'bg-black/40 hover:bg-black/30' }
    ];

    window.chatTenorCategoryCache = window.chatTenorCategoryCache || {};

    function getChatFavoritedGifs() {
        try {
            const favs = localStorage.getItem('alinea_tenor_favorites');
            return favs ? JSON.parse(favs) : [];
        } catch (e) {
            return [];
        }
    }

    function toggleChatFavoriteGif(gifUrl) {
        try {
            let favs = getChatFavoritedGifs();
            const idx = favs.indexOf(gifUrl);
            if (idx > -1) {
                favs.splice(idx, 1);
            } else {
                favs.push(gifUrl);
            }
            localStorage.setItem('alinea_tenor_favorites', JSON.stringify(favs));
            return idx === -1;
        } catch (e) {
            return false;
        }
    }

    function toggleChatTenorPopover(btn) {
        document.querySelectorAll('.chat-tenor-popover').forEach(p => p.remove());
        const formEl = document.getElementById('chat-input-form');

        const pop = document.createElement('div');
        pop.className = 'chat-tenor-popover absolute bottom-28 left-4 right-4 bg-white border border-gray-200 rounded-2xl shadow-2xl z-[1000] flex flex-col h-[280px] overflow-hidden animate-scale-up';
        pop.onclick = (e) => e.stopPropagation();

        pop.innerHTML = `
            <div class="p-2 border-b border-gray-100 flex items-center justify-between bg-slate-50">
                <span class="text-[10px] font-bold text-[#1a3a5c] flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-[#1a3a5c]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                    Cari GIF Tenor
                </span>
                <button type="button" class="btn-close-tenor text-slate-400 hover:text-slate-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-2 bg-slate-50/50 border-b border-gray-100 relative flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" class="chat-tenor-search w-full bg-white text-slate-800 border border-gray-200 rounded-xl pl-3 pr-8 py-1 text-xs outline-none focus:border-[#1a3a5c]/40 transition" placeholder="Cari di Tenor...">
                    <button type="button" class="btn-clear-search hidden absolute right-2.5 top-1.5 text-slate-400 hover:text-slate-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                </div>
            </div>
            <div class="chat-tenor-results flex-grow overflow-y-auto p-2 grid grid-cols-2 gap-1.5 scrollbar-thin bg-white">
                <div class="col-span-2 flex justify-center py-6"><span class="text-xs text-slate-400">Memuat kategori...</span></div>
            </div>
        `;

        formEl.appendChild(pop);
        const resultsContainer = pop.querySelector('.chat-tenor-results');
        const input = pop.querySelector('.chat-tenor-search');
        const clearBtn = pop.querySelector('.btn-clear-search');
        const closeBtn = pop.querySelector('.btn-close-tenor');

        closeBtn.onclick = () => pop.remove();

        const renderFavoritesList = () => {
            if (clearBtn) clearBtn.classList.remove('hidden');
            resultsContainer.innerHTML = '';
            const favs = getChatFavoritedGifs();
            
            if (favs.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="col-span-2 flex flex-col items-center justify-center py-8 px-4 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-400 mb-2 fill-yellow-400 animate-pulse" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <p class="text-xs font-bold text-[#1a3a5c]">Favorit Kosong</p>
                        <p class="text-[9px] text-slate-450 mt-1 max-w-[200px] leading-relaxed">Belum ada GIF favorit. Klik bintang pada GIF apa saja untuk menyimpannya di sini.</p>
                    </div>
                `;
                return;
            }

            favs.forEach(gifUrl => {
                const container = document.createElement('div');
                container.className = 'relative group w-full h-20 rounded-lg overflow-hidden border border-gray-100 shadow-sm bg-gray-50';

                const img = document.createElement('img');
                img.src = gifUrl;
                img.className = 'w-full h-full object-cover cursor-pointer hover:scale-105 transition duration-200';
                img.onclick = () => {
                    selectChatAttachment('tenor', gifUrl, 'Tenor GIF');
                    pop.remove();
                };

                const favBtn = document.createElement('button');
                favBtn.type = 'button';
                favBtn.className = 'absolute top-1 right-1 p-1 rounded-full bg-black/45 hover:bg-black/60 transition z-10 text-white';
                favBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`;
                
                favBtn.onclick = (e) => {
                    e.stopPropagation();
                    toggleChatFavoriteGif(gifUrl);
                    container.remove();
                    if (resultsContainer.children.length === 0) {
                        renderFavoritesList();
                    }
                };
                
                container.appendChild(img);
                container.appendChild(favBtn);
                resultsContainer.appendChild(container);
            });
        };

        const renderChatTenorCategories = () => {
            if (clearBtn) clearBtn.classList.add('hidden');
            resultsContainer.innerHTML = '';
            CHAT_TENOR_CATEGORIES.forEach(cat => {
                const card = document.createElement('div');
                card.className = 'relative h-16 rounded-xl overflow-hidden cursor-pointer hover:scale-[1.02] active:scale-[0.98] transition duration-200 shadow-sm border border-gray-155 bg-gray-50';
                
                const img = document.createElement('img');
                img.className = 'absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300';
                
                if (window.chatTenorCategoryCache[cat.name]) {
                    img.src = window.chatTenorCategoryCache[cat.name];
                    img.classList.remove('opacity-0');
                } else {
                    fetch(`https://g.tenor.com/v1/search?key=${TENOR_API_KEY}&q=${encodeURIComponent(cat.query)}&limit=1`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.results && data.results[0] && data.results[0].media && data.results[0].media[0]) {
                                const mediaObj = data.results[0].media[0];
                                const previewUrl = (mediaObj.tinygif && mediaObj.tinygif.url) ? mediaObj.tinygif.url : mediaObj.gif.url;
                                window.chatTenorCategoryCache[cat.name] = previewUrl;
                                img.src = previewUrl;
                                img.classList.remove('opacity-0');
                            }
                        })
                        .catch(err => console.error("Error loading category image:", err));
                }
                
                const overlay = document.createElement('div');
                overlay.className = `absolute inset-0 flex items-center justify-center font-bold text-white text-[11px] tracking-wide transition-colors duration-200 ${cat.overlayClass}`;
                overlay.textContent = cat.name;
                
                card.appendChild(img);
                card.appendChild(overlay);
                
                card.onclick = () => {
                    if (input) {
                        input.value = cat.name;
                        if (clearBtn) clearBtn.classList.remove('hidden');
                    }
                    if (cat.name === 'Favorit') {
                        renderFavoritesList();
                    } else {
                        fetchGifs(cat.query);
                    }
                };
                
                resultsContainer.appendChild(card);
            });
        };

        const fetchGifs = async (query = '') => {
            if (clearBtn) {
                if (query) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            resultsContainer.innerHTML = '<div class="col-span-2 flex justify-center py-6"><div class="animate-spin rounded-full h-5 w-5 border-b-2 border-[#1a3a5c]"></div></div>';
            let url = `https://g.tenor.com/v1/trending?key=${TENOR_API_KEY}&limit=12`;
            if (query) {
                url = `https://g.tenor.com/v1/search?key=${TENOR_API_KEY}&q=${encodeURIComponent(query)}&limit=12`;
            }
            try {
                const res = await fetch(url);
                const data = await res.json();
                resultsContainer.innerHTML = '';
                if (data.results && data.results.length > 0) {
                    data.results.forEach(item => {
                        if (item.media && item.media[0]) {
                            const mediaObj = item.media[0];
                            const gifUrl = mediaObj.gif.url;
                            const previewUrl = (mediaObj.tinygif && mediaObj.tinygif.url) ? mediaObj.tinygif.url : gifUrl;
                            
                            const container = document.createElement('div');
                            container.className = 'relative group w-full h-20 rounded-lg overflow-hidden border border-gray-100 shadow-sm bg-gray-50';

                            const img = document.createElement('img');
                            img.src = previewUrl;
                            img.className = 'w-full h-full object-cover cursor-pointer hover:scale-105 transition duration-200';
                            img.onclick = () => {
                                selectChatAttachment('tenor', gifUrl, 'Tenor GIF');
                                pop.remove();
                            };

                            const favBtn = document.createElement('button');
                            favBtn.type = 'button';
                            const isFav = getChatFavoritedGifs().includes(gifUrl);
                            favBtn.className = 'absolute top-1 right-1 p-1 rounded-full bg-black/40 hover:bg-black/60 transition z-10 text-white';
                            favBtn.innerHTML = isFav 
                                ? `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`
                                : `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-200 hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.25.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 9.42c-.783-.57-.384-1.81.587-1.81H8.48a1 1 0 00.95-.69L11.05 2.92z"/></svg>`;
                            
                            favBtn.onclick = (e) => {
                                e.stopPropagation();
                                const added = toggleChatFavoriteGif(gifUrl);
                                if (added) {
                                    favBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`;
                                } else {
                                    favBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-200 hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.25.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 9.42c-.783-.57-.384-1.81.587-1.81H8.48a1 1 0 00.95-.69L11.05 2.92z"/></svg>`;
                                }
                            };

                            container.appendChild(img);
                            container.appendChild(favBtn);
                            resultsContainer.appendChild(container);
                        }
                    });
                } else {
                    resultsContainer.innerHTML = '<span class="col-span-2 text-center py-4 text-xs text-slate-500">Tidak ada hasil.</span>';
                }
            } catch (err) {
                resultsContainer.innerHTML = '<span class="col-span-2 text-center py-4 text-xs text-red-500">Gagal memuat.</span>';
            }
        };

        if (clearBtn) {
            clearBtn.onclick = () => {
                if (input) input.value = '';
                renderChatTenorCategories();
            };
        }

        if (input) {
            let timeout = null;
            input.oninput = (e) => {
                const val = e.target.value.trim();
                clearTimeout(timeout);
                if (!val) {
                    renderChatTenorCategories();
                    return;
                }
                timeout = setTimeout(() => {
                    fetchGifs(val);
                }, 400);
            };
        }

        renderChatTenorCategories();
    }

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

    // Ensure Leaflet JS/CSS are loaded dynamically
    function ensureLeafletLoaded(callback) {
        if (!document.getElementById('leaflet-css-chat')) {
            const link = document.createElement('link');
            link.id = 'leaflet-css-chat';
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
        }
        
        if (typeof L === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => {
                setTimeout(callback, 300);
            };
            document.head.appendChild(script);
        } else {
            setTimeout(callback, 100);
        }
    }

    function getChatMapLayers() {
        if (!chatMapLayers) {
            chatMapLayers = {
                streets: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }),
                satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri'
                }),
                dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO'
                })
            };
        }
        return chatMapLayers;
    }

    function switchChatMapLayer(layerName) {
        if (!chatMap || typeof L === 'undefined') return;
        if (currentChatTileLayer) {
            chatMap.removeLayer(currentChatTileLayer);
        }
        currentChatTileLayer = getChatMapLayers()[layerName];
        currentChatTileLayer.addTo(chatMap);
        
        ['streets', 'satellite', 'dark'].forEach(name => {
            const btn = document.getElementById(`chat-layer-btn-${name}`);
            if (btn) {
                if (name === layerName) {
                    btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white shadow-sm";
                } else {
                    btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200";
                }
            }
        });
    }

    function switchChatViewMapLayer(layerName) {
        if (!chatViewMap || typeof L === 'undefined') return;
        if (currentChatViewTileLayer) {
            chatViewMap.removeLayer(currentChatViewTileLayer);
        }
        currentChatViewTileLayer = getChatMapLayers()[layerName];
        currentChatViewTileLayer.addTo(chatViewMap);
        
        ['streets', 'satellite', 'dark'].forEach(name => {
            const btn = document.getElementById(`chat-view-layer-btn-${name}`);
            if (btn) {
                if (name === layerName) {
                    btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white shadow-sm";
                } else {
                    btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200";
                }
            }
        });
    }

    function openChatMapModal() {
        document.getElementById('chat-map-modal').classList.remove('hidden');
        ensureLeafletLoaded(() => {
            initChatMap();
        });
    }

    function initChatMap() {
        if (chatMap) {
            chatMap.invalidateSize();
            switchChatMapLayer('streets');
            return;
        }

        chatMap = L.map('chat-leaflet-map', {
            zoomControl: false
        }).setView(chatMapSelectedCoords, 13);

        currentChatTileLayer = getChatMapLayers().streets;
        currentChatTileLayer.addTo(chatMap);

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
        switchChatMapLayer('streets');
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
                    row.className = 'p-2.5 hover:bg-gray-50 cursor-pointer text-xs transition text-gray-700 border-b border-gray-100 flex items-start gap-2';
                    row.innerHTML = `
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="truncate">${item.display_name}</span>
                    `;
                    row.onclick = () => {
                        const lat = parseFloat(item.lat);
                        const lng = parseFloat(item.lon);
                        chatMapSelectedCoords = [lat, lng];
                        chatMapMarker.setLatLng(chatMapSelectedCoords);
                        chatMap.setView(chatMapSelectedCoords, 15);
                        document.getElementById('chat-selected-coords-text').textContent = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        document.getElementById('chat-map-search-input').value = item.display_name;
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

        ensureLeafletLoaded(() => {
            if (chatViewMap) {
                chatViewMap.setView([lat, lng], 15);
                chatViewMapMarker.setLatLng([lat, lng]);
                chatViewMap.invalidateSize();
                switchChatViewMapLayer('streets');
                return;
            }

            chatViewMap = L.map('chat-view-leaflet-map', {
                zoomControl: false
            }).setView([lat, lng], 15);

            currentChatViewTileLayer = getChatMapLayers().streets;
            currentChatViewTileLayer.addTo(chatViewMap);

            chatViewMapMarker = L.marker([lat, lng]).addTo(chatViewMap);
            L.control.zoom({ position: 'bottomright' }).addTo(chatViewMap);
            switchChatViewMapLayer('streets');
        });
    }

    function closeChatViewMapModal() {
        document.getElementById('chat-view-map-modal').classList.add('hidden');
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
            ${!isMe ? `<img src="${avatarUrl}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-gray-150 mt-0.5">` : ''}
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

    // Real-time Echo listener configuration
    document.addEventListener('DOMContentLoaded', () => {
        // Load initial users
        loadChatUsers();

        const textInput = document.getElementById('chat-message-text');
        if (textInput) {
            textInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    const form = document.getElementById('chat-input-form');
                    if (form) {
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                        }
                    }
                }
            });
            textInput.addEventListener('input', function() {
                this.style.height = '36px';
                const scrollHeight = this.scrollHeight;
                if (scrollHeight > 36) {
                    this.style.height = Math.min(scrollHeight, 96) + 'px';
                }
            });
        }

        const chatSearchInput = document.getElementById('chat-map-search-input');
        if (chatSearchInput) {
            chatSearchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchChatLocation();
                }
            });
        }

        // Check if Echo is loaded
        if (window.Echo) {
            const userId = '{{ auth()->id() }}';
            
            // Join online presence channel
            window.Echo.join('online')
                .here((users) => {
                    users.forEach(u => onlineUsers.add(parseInt(u.id)));
                    loadChatUsers();
                    updateHeaderStatus();
                })
                .joining((user) => {
                    onlineUsers.add(parseInt(user.id));
                    loadChatUsers();
                    updateHeaderStatus();
                })
                .leaving((user) => {
                    onlineUsers.delete(parseInt(user.id));
                    loadChatUsers();
                    updateHeaderStatus();
                });

            // Listen to private channel
            window.Echo.private('chat.' + userId)
                .listen('.MessageSent', (e) => {
                    const msg = e.message;
                    
                    // If message is from the currently active chat user & panel is open
                    if (chatActiveUserId && parseInt(msg.sender_id) === parseInt(chatActiveUserId) && isChatActivelyVisible()) {
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
                })
                .listen('.MessagesRead', (e) => {
                    // When the other user reads our messages
                    if (chatActiveUserId && parseInt(e.readerId) === parseInt(chatActiveUserId)) {
                        document.querySelectorAll('.chat-status-ticks').forEach(ticks => {
                            ticks.innerHTML = `
                                <svg class="w-3.5 h-3.5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M2 12l5.25 5 2.625-3M8 12l5.25 5L22 7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            `;
                        });
                    }
                });
        }
    });
</script>
@endauth
