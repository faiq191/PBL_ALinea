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
</script>
