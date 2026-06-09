    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black/90 z-[9999] hidden flex items-center justify-center opacity-0 transition-opacity duration-300 pointer-events-none" onclick="closeLightbox()">
        <button class="absolute top-6 right-6 text-white/70 hover:text-white transition">
            <i data-lucide="x" class="w-8 h-8"></i>
        </button>
        <img id="lightbox-img" class="max-w-[90%] max-h-[90%] object-contain rounded-lg shadow-2xl transform scale-95 transition-transform duration-300">
    </div>



    <!-- Map Modal -->
    <div id="map-modal" class="fixed inset-0 bg-black/60 z-[999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeMapModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[85vh] animate-scale-up">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-[#1a3a5c] flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5"></i> Bagikan Lokasi
                </h3>
                <button onclick="closeMapModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-4 bg-gray-50 border-b border-gray-100 flex flex-col gap-2 relative">
                <div class="flex gap-2">
                    <input type="text" id="map-search-input" class="flex-1 bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-[#1a3a5c] transition" placeholder="Cari nama lokasi atau alamat...">
                    <button onclick="searchLocation()" class="bg-[#1a3a5c] text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-[#122b45] transition flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="search" class="w-4 h-4"></i> Cari
                    </button>
                    <button onclick="getCurrentLocation()" class="bg-white text-gray-700 border border-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-100 transition flex items-center gap-1.5 shadow-sm" title="Gunakan Lokasi Saat Ini">
                        <i data-lucide="locate" class="w-4 h-4 text-rose-500 animate-pulse"></i> Lokasi Saya
                    </button>
                </div>
                <!-- Search Results Suggestion List -->
                <div id="map-search-results" class="hidden absolute top-full left-4 right-4 bg-white border border-gray-200 rounded-xl shadow-xl z-[1000] max-h-48 overflow-y-auto divide-y divide-gray-100 mt-1">
                </div>
            </div>
            <!-- Info Geolocation Accuracy Box -->
            <div class="px-4 py-2.5 bg-blue-50/80 border-b border-blue-100 flex items-start gap-2 text-[10px] text-blue-700 leading-normal">
                <i data-lucide="info" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                <span><b>Tips Akurasi:</b> Pencarian otomatis di laptop/PC berbasis alamat IP sehingga bisa kurang akurat. Untuk akurasi GPS terbaik, disarankan mengakses via HP/perangkat ber-GPS. Lu juga bisa menggeser pin biru langsung di peta atau mencari alamat manual di kolom pencarian.</span>
            </div>
            <div id="leaflet-map-container" class="flex-1 min-h-[380px] relative">
                <div id="leaflet-map" class="absolute inset-0"></div>
                <!-- Layer Selector Floating Control -->
                <div class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur-sm p-1.5 rounded-xl border border-gray-200 shadow-lg flex gap-1">
                    <button onclick="switchMapLayer('streets')" id="layer-btn-streets" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white">Jalan</button>
                    <button onclick="switchMapLayer('satellite')" id="layer-btn-satellite" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Satelit</button>
                    <button onclick="switchMapLayer('dark')" id="layer-btn-dark" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Gelap</button>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                <p class="text-xs text-gray-500 max-w-[200px] sm:max-w-sm truncate" id="selected-coords-text">Koordinat: -6.2088, 106.8456</p>
                <div class="flex gap-2 shrink-0">
                    <button type="button" onclick="closeMapModal()" class="px-4 py-2.5 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-100 transition">Batal</button>
                    <button onclick="confirmShareLocation()" class="bg-[#1a3a5c] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#122b45] transition shadow-sm">Bagikan Lokasi Ini</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Map Modal -->
    <div id="view-map-modal" class="fixed inset-0 bg-black/60 z-[999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeViewMapModal()">
        <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[85vh] animate-scale-up">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-[#1a3a5c] flex items-center gap-2 truncate max-w-[80%]">
                    <i data-lucide="map-pin" class="w-5 h-5 text-rose-500"></i> <span id="view-map-title">Lokasi Terbagikan</span>
                </h3>
                <button onclick="closeViewMapModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div id="view-leaflet-map-container" class="flex-1 min-h-[400px] relative">
                <div id="view-leaflet-map" class="absolute inset-0"></div>
                <!-- Layer Selector Floating Control -->
                <div class="absolute top-3 right-3 z-[1000] bg-white/95 backdrop-blur-sm p-1.5 rounded-xl border border-gray-200 shadow-lg flex gap-1">
                    <button onclick="switchViewMapLayer('streets')" id="view-layer-btn-streets" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white">Jalan</button>
                    <button onclick="switchViewMapLayer('satellite')" id="view-layer-btn-satellite" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Satelit</button>
                    <button onclick="switchViewMapLayer('dark')" id="view-layer-btn-dark" class="px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200">Gelap</button>
                </div>
            </div>
            
            <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                <p class="text-xs text-gray-500 max-w-[200px] sm:max-w-sm truncate" id="view-map-coords-text">Koordinat: -6.2088, 106.8456</p>
                <div class="flex gap-2 shrink-0">
                    <button type="button" onclick="closeViewMapModal()" class="px-4 py-2.5 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-100 transition">Tutup</button>
                    <a id="view-map-gmaps-link" href="#" target="_blank" class="bg-[#1a3a5c] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#122b45] transition shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>

