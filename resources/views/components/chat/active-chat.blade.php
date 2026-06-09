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
