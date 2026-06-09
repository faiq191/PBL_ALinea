<script>
    // Ensure Leaflet assets are loaded globally
    if (!document.getElementById('leaflet-css-chat')) {
        const link = document.createElement('link');
        link.id = 'leaflet-css-chat';
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
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
</script>
