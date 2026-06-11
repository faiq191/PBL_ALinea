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
    <div class="bg-white text-slate-800 rounded-3xl w-full max-w-xl shadow-2xl border border-gray-150 flex flex-col max-h-[80vh] animate-scale-up" style="border-radius:1.5rem;">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-[#1a3a5c] flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1a3a5c]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Bagikan Lokasi
            </h3>
            <button onclick="closeChatMapModal()" class="text-slate-400 hover:text-slate-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-3 bg-white border-b border-gray-100 flex flex-col gap-2">
            <div class="flex gap-2">
                <input type="text" id="chat-map-search-input"
                    class="flex-1 bg-slate-100 border border-gray-200 text-slate-850 rounded-xl px-3.5 py-2 text-xs outline-none focus:border-[#1a3a5c]/40 focus:bg-white transition"
                    placeholder="Cari nama lokasi atau alamat..."
                    onkeydown="if(event.key==='Enter'){event.preventDefault();searchChatLocation();}">
                <button id="chat-map-search-btn" onclick="event.preventDefault(); event.stopPropagation(); searchChatLocation();" class="bg-[#1a3a5c] hover:bg-[#122b45] text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1">
                    Cari
                </button>
                <button onclick="getCurrentChatLocation()" class="bg-slate-100 text-slate-655 border border-gray-200 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-200 transition flex items-center gap-1" title="Gunakan Lokasi Saat Ini">
                    Lokasi Saya
                </button>
            </div>
            <!-- Search Results: inline (bukan absolute) agar tidak di-clip overflow-hidden modal -->
            <div id="chat-map-search-results" class="hidden bg-white border border-gray-200 rounded-xl shadow-inner max-h-40 overflow-y-auto divide-y divide-gray-100"></div>
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
