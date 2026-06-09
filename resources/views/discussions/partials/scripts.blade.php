        <!-- Include compiled JS assets yang berisi Laravel Echo -->
    <script>
        window.laravelReverb = {
            key: "{{ config('broadcasting.connections.reverb.key') ?? 'z2qmiwap8byabk4uu6vt' }}",
            host: "{{ config('broadcasting.connections.reverb.options.host') ?? '127.0.0.1' }}",
            port: "{{ config('broadcasting.connections.reverb.options.port') ?? '8080' }}",
            scheme: "{{ config('broadcasting.connections.reverb.options.scheme') ?? 'http' }}"
        };
    </script>
    @vite(['resources/js/app.js'])

    <script>
        function formatCommentContent(content) {
            // 1. Escape HTML first to prevent XSS (since we inject with innerHTML)
            let escaped = content
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");

            // 2. Parse Mention
            const usernames = Array.from(document.querySelectorAll('.comment-username'))
                .map(el => el.textContent.trim())
                .filter((v, i, a) => a.indexOf(v) === i)
                .sort((a, b) => b.length - a.length);
            
            let mentionMatched = false;
            for (const username of usernames) {
                const escapedName = username.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                const pattern = new RegExp('^@' + escapedName + '(\\s|$)');
                if (pattern.test(escaped)) {
                    escaped = escaped.replace(pattern, (match, p1) => {
                        return `<span class="text-blue-500 font-semibold text-xs mr-1">@${username}</span>` + p1;
                    });
                    mentionMatched = true;
                    break;
                }
            }
            if (!mentionMatched) {
                escaped = escaped.replace(/^@([a-zA-Z0-9_]+)/, '<span class="text-blue-500 font-semibold text-xs mr-1">@$1</span>');
            }

            // 3. GIF placeholders
            const gifRegex = /(https?:\/\/[^\s<>\"]+?\.(?:gif)(?:[?#][^\s<>\"]*)?|https?:\/\/(?:www\.)?media\.tenor\.com\/[^\s<>\"]+|https?:\/\/(?:www\.)?tenor\.com\/view\/[^\s<>\"]+)/ig;
            const gifPlaceholders = [];
            escaped = escaped.replace(gifRegex, (match) => {
                const placeholder = `___GIF_PLACEHOLDER_${gifPlaceholders.length}___`;
                const decodedUrl = match.replace(/&amp;/g, '&');
                gifPlaceholders.push({ placeholder, url: decodedUrl });
                return placeholder;
            });

            // 4. Match any other URLs and replace with clickable warning links (blue highlight)
            const urlRegex = /(https?:\/\/[^\s<>\"]+)/ig;
            escaped = escaped.replace(urlRegex, (match) => {
                const decodedUrl = match.replace(/&amp;/g, '&');
                const safeUrlForJS = decodedUrl.replace(/'/g, "\\'");
                return `<a href="${decodedUrl}" target="_blank" rel="noopener noreferrer" ` +
                       `class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 underline font-medium bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 text-xs transition duration-200" ` +
                       `title="Peringatan: Tautan eksternal dari pengguna lain. Harap berhati-hati saat membuka tautan dari luar." ` +
                       `onclick="return confirm('Peringatan Keamanan: Tautan ini berasal dari luar ALinea. Membuka tautan eksternal dari orang asing berpotensi bahaya (phishing, malware, dll).\\n\\nApakah Anda yakin ingin membuka: ${safeUrlForJS}?')">` +
                       `<span>${decodedUrl}</span>` +
                       `<i data-lucide="external-link" class="w-3 h-3 text-blue-500"></i>` +
                       `</a>`;
            });

            // 5. Restore GIFs
            gifPlaceholders.forEach(item => {
                const imgHtml = `<div class="relative max-w-xs overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition mt-2">` +
                               `<img src="${item.url}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('${item.url}')">` +
                               `</div>`;
                escaped = escaped.replace(item.placeholder, imgHtml);
            });

            return escaped;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            const discussionId = "{{ $discussion->id }}";
            
            let activeAnchor = (window.location.hash && window.location.hash.startsWith('#comment-')) 
                ? window.location.hash.substring(1) 
                : null;

            const scrollToAnchor = () => {
                const isReload = performance.getEntriesByType('navigation')[0]?.type === 'reload';
                if (isReload) {
                    window.scrollTo(0, 0);
                    document.documentElement.classList.remove('hash-loading');
                    return;
                }
                
                if (activeAnchor) {
                    const target = document.getElementById(activeAnchor);
                    if (target) {
                        // Instant scroll to keep target centered
                        target.scrollIntoView({ behavior: 'auto', block: 'center' });
                        
                        // Reveal page content once we are locked on target element
                        document.documentElement.classList.remove('hash-loading');
                        
                        // Strip the hash from the URL bar immediately so that subsequent manual refreshes go to the top
                        if (window.location.hash) {
                            history.replaceState(null, null, window.location.pathname + window.location.search);
                        }
                        
                        // Highlight comment box with a beautiful premium animation
                        const cardInner = target.querySelector('.bg-white.border');
                        if (cardInner && !cardInner.classList.contains('ring-2')) {
                            cardInner.classList.add('ring-2', 'ring-[#1a3a5c]/40', 'scale-[1.01]', 'duration-700', 'transition-all');
                            setTimeout(() => {
                                cardInner.classList.remove('ring-2', 'ring-[#1a3a5c]/40', 'scale-[1.01]');
                            }, 2500);
                        }
                        
                        // Reset scroll restoration
                        setTimeout(() => {
                            if ('scrollRestoration' in history) {
                                history.scrollRestoration = 'auto';
                            }
                        }, 200);
                    }
                } else {
                    document.documentElement.classList.remove('hash-loading');
                }
            };

            // Run immediately
            scrollToAnchor();
            // Run on window load (after all assets and layout settle height)
            window.addEventListener('load', scrollToAnchor);
            // Run in multiple passes to cancel out Alpine.js dynamic height layout shifts
            setTimeout(scrollToAnchor, 100);
            setTimeout(scrollToAnchor, 300);
            setTimeout(scrollToAnchor, 600);
            
            // Safety fallback to ensure page is always visible
            setTimeout(() => {
                document.documentElement.classList.remove('hash-loading');
            }, 850);

            // AJAX Comment & Reply Submit Handler (100% SPA experience, no page reload or jumps to top!)
            document.addEventListener('submit', async (e) => {
                const form = e.target;
                if (form.action && (form.action.includes('/comment') || form.action.includes('/comments'))) {
                    e.preventDefault();
                    
                    // 1. Snapshot old comment IDs IMMEDIATELY before fetch or WebSockets can run!
                    const oldIds = Array.from(document.querySelectorAll('[id^="comment-"]')).map(el => el.id);
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;
                    
                    try {
                        const formData = new FormData(form);
                        const csrfToken = form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        
                        // Set headers including Laravel Socket ID to exclude sender from Echo broadcast
                        const headers = {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        };
                        if (window.Echo && window.Echo.socketId()) {
                            headers['X-Socket-ID'] = window.Echo.socketId();
                        }

                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: headers
                        });
                        
                        if (response.ok) {
                            const html = await response.text();
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            
                            // 2. Replace comments container
                            const newComments = doc.querySelector('.space-y-6');
                            const currentComments = document.querySelector('.space-y-6');
                            if (newComments && currentComments) {
                                currentComments.innerHTML = newComments.innerHTML;
                                if (window.lucide) {
                                    window.lucide.createIcons();
                                }
                                // Re-init Alpine for newly injected x-data elements
                                if (window.Alpine) {
                                    currentComments.querySelectorAll('[x-data]').forEach(el => {
                                        try { window.Alpine.initTree(el); } catch(e) {}
                                    });
                                }
                                // Re-bind all attachment forms after DOM replace
                                currentComments.querySelectorAll('.attachment-form').forEach(form => {
                                    if (!form.dataset.bound) {
                                        initAttachmentFormBindings(form);
                                        form.dataset.bound = 'true';
                                    }
                                });
                            }
                            
                            // Clear textarea & attachments
                            const textarea = form.querySelector('textarea');
                            if (textarea) textarea.value = '';
                            clearAttachment(form);
                            
                            // 3. Find the newly added comment ID from the fetched response doc compared to our pre-submit snapshot
                            const newIds = Array.from(doc.querySelectorAll('[id^="comment-"]')).map(el => el.id);
                            const addedId = newIds.find(id => !oldIds.includes(id));
                            
                            if (addedId) {
                                window.location.hash = addedId;
                                const target = document.getElementById(addedId);
                                if (target) {
                                    setTimeout(() => {
                                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        
                                        const cardInner = target.querySelector('.bg-white.border');
                                        if (cardInner) {
                                            cardInner.classList.add('ring-2', 'ring-[#1a3a5c]/40', 'scale-[1.01]', 'duration-700', 'transition-all');
                                            setTimeout(() => {
                                                cardInner.classList.remove('ring-2', 'ring-[#1a3a5c]/40', 'scale-[1.01]');
                                            }, 2500);
                                        }
                                    }, 120);
                                }
                            }
                        } else {
                            form.submit();
                        }
                    } catch (err) {
                        console.error(err);
                        form.submit();
                    } finally {
                        if (submitBtn) submitBtn.disabled = false;
                    }
                }
            });
            
            // Dengar channel public discussion.[id]
            window.Echo.channel('discussion.' + discussionId)
                .listen('.CommentSent', (e) => {
                    console.log("Komentar baru masuk:", e.comment);
                    
                    const comment = e.comment;
                    const commentUser = comment.user;
                    
                    // Tentukan avatar (foto profil atau inisial)
                    let avatarHtml = '';
                    if (commentUser.profile_photo) {
                        const isUrl = commentUser.profile_photo.startsWith('http');
                        const imgUrl = isUrl ? commentUser.profile_photo : '/storage/' + commentUser.profile_photo;
                        avatarHtml = `<img src="${imgUrl}" class="w-10 h-10 shrink-0 rounded-full object-cover shadow-sm z-10">`;
                    } else {
                        const initial = commentUser.name.substring(0, 1);
                        avatarHtml = `<div class="w-10 h-10 shrink-0 rounded-full bg-[#e8edf2] flex items-center justify-center font-bold text-[#1a3a5c] z-10">${initial}</div>`;
                    }
                    
                    if (comment.parent_id) {
                        // Jika ini adalah balasan (subcomment)
                        const repliesContainer = document.getElementById('replies-' + comment.parent_id);
                        if (repliesContainer) {
                            // Tampilkan container jika sebelumnya kosong/hidden
                            repliesContainer.classList.remove('hidden');
                            
                            // Tentukan avatar berukuran mini (w-8 h-8) untuk reply
                            let replyAvatarHtml = '';
                            if (commentUser.profile_photo) {
                                const isUrl = commentUser.profile_photo.startsWith('http');
                                const imgUrl = isUrl ? commentUser.profile_photo : '/storage/' + commentUser.profile_photo;
                                replyAvatarHtml = `<img src="${imgUrl}" class="w-8 h-8 shrink-0 rounded-full object-cover shadow-sm">`;
                            } else {
                                const initial = commentUser.name.substring(0, 1);
                                replyAvatarHtml = `<div class="w-8 h-8 shrink-0 rounded-full bg-[#d0e4f5] flex items-center justify-center font-bold text-[#1a3a5c] text-xs">${initial}</div>`;
                            }
                            
                            const newReplyHtml = `
                                <div id="comment-${comment.id}" class="relative group animate-fade-in">
                                    <div class="absolute -left-6 top-5 w-6 h-4 border-b-2 border-l-2 border-gray-100 rounded-bl-xl"></div>
                                    <div class="flex gap-3 relative">
                                        ${replyAvatarHtml}
                                        <div class="flex-1 bg-white border border-gray-100 rounded-2xl rounded-tl-none p-3 shadow-sm">
                                            <div class="flex justify-between items-start mb-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-sm text-[#1a3a5c] comment-username">${commentUser.name}</span>
                                                    <span class="text-[10px] text-gray-400">Baru saja</span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${formatCommentContent(comment.content)}</p>
                                            ${renderAttachmentHtml(comment.attachment_type, comment.attachment_path, comment.attachment_name)}
                                            
                                            ${isAuthenticated ? `
                                                <button onclick="
                                                    const rootCommentEl = document.getElementById('comment-${comment.parent_id}');
                                                    if (rootCommentEl) {
                                                        const alpineData = Alpine.$data(rootCommentEl);
                                                        alpineData.replying = true;
                                                        alpineData.editing = false;
                                                        setTimeout(() => {
                                                            const textarea = document.getElementById('reply-textarea-${comment.parent_id}');
                                                            if (textarea) {
                                                                textarea.value = '@${commentUser.name} ';
                                                                textarea.focus();
                                                            }
                                                        }, 50);
                                                    }
                                                " class="text-[10px] font-bold text-gray-400 hover:text-[#1a3a5c] mt-1.5 flex items-center gap-1 transition">
                                                    <i data-lucide="reply" class="w-3 h-3"></i> Balas
                                                </button>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                            repliesContainer.insertAdjacentHTML('beforeend', newReplyHtml);
                        }
                    } else {
                        // Jika ini adalah komentar utama (root)
                        const newCommentHtml = `
                            <div x-data="{ replying: false, editing: false }" id="comment-${comment.id}" class="flex gap-4 group animate-fade-in">
                                ${avatarHtml}
                                <div class="flex-1 relative">
                                    <div class="bg-gray-50 rounded-2xl rounded-tl-none p-4 mb-2">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-sm text-[#1a3a5c] comment-username">${commentUser.name}</span>
                                                <span class="text-[10px] text-gray-400">Baru saja</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${formatCommentContent(comment.content)}</p>
                                        ${renderAttachmentHtml(comment.attachment_type, comment.attachment_path, comment.attachment_name)}
                                    </div>
                                    
                                    <div id="replies-${comment.id}" class="relative ml-2 pl-6 border-l-2 border-gray-100 space-y-4 pt-2 hidden"></div>
                                </div>
                            </div>
                        `;
                        
                        const commentContainer = document.querySelector('.space-y-6');
                        if (commentContainer) {
                            const emptyPlaceholder = commentContainer.querySelector('.text-center.py-8');
                            if (emptyPlaceholder) {
                                emptyPlaceholder.remove();
                            }
                            commentContainer.insertAdjacentHTML('beforeend', newCommentHtml);
                        }
                    }
                    if (window.lucide) window.lucide.createIcons();
                })
                .listen('.CommentUpdated', (e) => {
                    console.log("Komentar di-update:", e.comment);
                    const comment = e.comment;
                    
                    // Cari element pembungkus komentar/reply berdasarkan ID
                    const commentEl = document.getElementById('comment-' + comment.id);
                    if (commentEl) {
                        if (comment.content.startsWith('_deleted_')) {
                            // 1. Tampilkan teks "Pesan ini telah dihapus..."
                            const contentEl = commentEl.querySelector('p[x-show="!editing"]') || commentEl.querySelector('p[x-show="!editingReply"]') || commentEl.querySelector('.italic');
                            if (contentEl) {
                                if (comment.content === '_deleted_by_admin_') {
                                    contentEl.outerHTML = `
                                        <p class="text-xs text-red-400 italic flex items-center gap-1.5 py-1 bg-red-50/50 px-3 py-1 rounded-lg border border-red-100/50 w-fit">
                                            <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-red-500"></i> ${comment.parent_id ? 'Balasan' : 'Pesan'} ini telah dihapus oleh moderator/admin
                                        </p>
                                    `;
                                } else {
                                    contentEl.outerHTML = `
                                        <p class="text-xs text-gray-400 italic flex items-center gap-1.5 py-1">
                                            <i data-lucide="ban" class="w-3.5 h-3.5"></i> ${comment.parent_id ? 'Balasan' : 'Pesan'} ini telah dihapus oleh pengguna
                                        </p>
                                    `;
                                }
                                lucide.createIcons();
                            }
                            
                            // 2. Sembunyikan tombol edit/hapus
                            const actionsEl = commentEl.querySelector('.opacity-0');
                            if (actionsEl) {
                                actionsEl.remove();
                            }
                            
                            // 3. Sembunyikan tombol balas (Reply button)
                            const replyBtn = commentEl.querySelector('button[class*="hover:text-[#1a3a5c]"]');
                            if (replyBtn) {
                                replyBtn.remove();
                            }
                        } else {
                            // Proses edit komentar normal
                            const contentEl = commentEl.querySelector('p[x-show="!editing"]') || commentEl.querySelector('p[x-show="!editingReply"]');
                            if (contentEl) {
                                if (comment.parent_id) {
                                    contentEl.innerHTML = formatCommentContent(comment.content).replace(/\n/g, '<br>');
                                } else {
                                    contentEl.innerHTML = formatCommentContent(comment.content).replace(/\n/g, '<br>');
                                }
                            }
                            
                            // Update waktu menjadi "Baru saja" dan tambahkan penanda "(disunting)" agar sinkron instan di semua user!
                            const metaEl = commentEl.querySelector('.text-\\[10px\\]') || commentEl.querySelector('.text-gray-400');
                            if (metaEl) {
                                metaEl.innerHTML = `
                                    <span>Baru saja</span>
                                    <span class="text-gray-300">•</span>
                                    <span class="italic text-[9px] text-gray-400">(disunting)</span>
                                `;
                            }
                            
                            // Juga, update isi textarea di form edit agar sinkron jika di-edit lagi!
                            const textareaEl = commentEl.querySelector('textarea[name="content"]');
                            if (textareaEl) {
                                textareaEl.value = comment.content;
                            }
                        }
                    }
                    if (window.lucide) window.lucide.createIcons();
                })
                .listen('.CommentDeleted', (e) => {
                    console.log("Komentar dihapus:", e.commentId);
                    const commentEl = document.getElementById('comment-' + e.commentId);
                    if (commentEl) {
                        commentEl.remove();
                    }
                });

            // Bind all attachment forms existing on load
            document.querySelectorAll('.attachment-form').forEach(form => {
                form.dataset.bound = 'true';
                initAttachmentFormBindings(form);
            });

            // MutationObserver: auto-bind any new .attachment-form inserted into DOM
            // Handles Alpine x-show reply forms & WebSocket-injected content
            const _attachObserver = new MutationObserver((mutations) => {
                mutations.forEach(mutation => {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType !== 1) return;
                        // Check if the node itself is a form, or contains forms
                        const forms = node.matches && node.matches('.attachment-form')
                            ? [node]
                            : Array.from(node.querySelectorAll ? node.querySelectorAll('.attachment-form') : []);
                        forms.forEach(form => {
                            if (!form.dataset.bound) {
                                form.dataset.bound = 'true';
                                initAttachmentFormBindings(form);
                            }
                        });
                    });

                    // Handle Alpine x-show: style attribute toggled on/near .attachment-form
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                        const el = mutation.target;
                        if (!el || el.nodeType !== 1) return;
                        // Check if el is, or contains, an .attachment-form that is now visible
                        const forms = el.matches && el.matches('.attachment-form')
                            ? [el]
                            : Array.from(el.querySelectorAll ? el.querySelectorAll('.attachment-form') : []);
                        forms.forEach(form => {
                            if (!form.dataset.bound && form.style.display !== 'none') {
                                form.dataset.bound = 'true';
                                initAttachmentFormBindings(form);
                            }
                        });
                    }
                });
            });
            _attachObserver.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['style'] });

            // Fallback: re-scan on any Balas button click (handles Alpine x-show reveal)
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    // Match Balas button by checking for lucide-reply icon or data attribute
                    const hasReplyIcon = btn.querySelector('[data-lucide="reply"]');
                    const textContent = (btn.textContent || '').replace(/\s+/g, ' ').trim();
                    if (hasReplyIcon || textContent === 'Balas' || textContent.includes('Balas')) {
                        setTimeout(() => {
                            document.querySelectorAll('.attachment-form').forEach(form => {
                                if (!form.dataset.bound) {
                                    form.dataset.bound = 'true';
                                    initAttachmentFormBindings(form);
                                }
                            });
                        }, 150);
                    }
                }
            });
        });

        // ==========================================
        // ATTACHMENTS & MODALS JS ENGINE
        // ==========================================
        
        function renderAttachmentHtml(type, path, name) {
            if (!path) return '';
            
            let html = '<div class="mt-3">';
            if (type === 'image') {
                const fullUrl = path.startsWith('http') ? path : '/storage/' + path;
                html += `
                    <div class="relative max-w-sm overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                        <img src="${fullUrl}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('${fullUrl}')">
                    </div>
                `;
            } else if (type === 'file') {
                const fullUrl = '/storage/' + path;
                html += `
                    <a href="${fullUrl}" download class="inline-flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl p-3 hover:bg-gray-100 transition max-w-xs">
                        <div class="w-10 h-10 bg-[#e8edf2] rounded-xl flex items-center justify-center text-[#1a3a5c]">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-[#1a3a5c] truncate">${name || 'Berkas'}</p>
                            <p class="text-[10px] text-gray-400">Klik untuk unduh</p>
                        </div>
                        <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                    </a>
                `;
            } else if (type === 'giphy' || type === 'tenor') {
                html += `
                    <div class="relative max-w-xs overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                        <img src="${path}" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox('${path}')">
                    </div>
                `;
            } else if (type === 'gmaps') {
                const locName = name || 'Lokasi Terbagikan';
                html += `
                    <div data-coords="${path}" data-name="${locName}" onclick="viewSharedLocation(this)" class="inline-flex items-center gap-3 bg-[#e8edf2] border border-[#d0e4f5] rounded-2xl p-3 hover:bg-[#d8e4f5] transition max-w-xs cursor-pointer">
                        <div class="w-10 h-10 bg-[#1a3a5c] rounded-xl flex items-center justify-center text-white">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-[#1a3a5c] truncate">${locName}</p>
                            <p class="text-[10px] text-gray-500">Lihat Peta</p>
                        </div>
                        <i data-lucide="map" class="w-4 h-4 text-[#1a3a5c]"></i>
                    </div>
                `;
            }
            html += '</div>';
            return html;
        }

        if (typeof TENOR_API_KEY === 'undefined') {
            var TENOR_API_KEY = 'LIVDSRZULELA';
        }

        const TENOR_CATEGORIES = [
            { name: 'Favorit', query: 'love', overlayClass: 'bg-[#ff4b5c]/60 hover:bg-[#ff4b5c]/50' },
            { name: 'Tren Baru', query: 'trending', overlayClass: 'bg-black/40 hover:bg-black/30' },
            { name: 'Keren', query: 'awesome', overlayClass: 'bg-black/40 hover:bg-black/30' },
            { name: 'Bercanda', query: 'jk', overlayClass: 'bg-black/40 hover:bg-black/30' },
            { name: 'Semoga Sukses', query: 'good luck', overlayClass: 'bg-black/40 hover:bg-black/30' },
            { name: 'Tos', query: 'high five', overlayClass: 'bg-black/40 hover:bg-black/30' }
        ];

        window.tenorCategoryCache = window.tenorCategoryCache || {};

        function getFavoritedGifs() {
            try {
                const favs = localStorage.getItem('alinea_tenor_favorites');
                return favs ? JSON.parse(favs) : [];
            } catch (e) {
                return [];
            }
        }

        function toggleFavoriteGif(gifUrl) {
            try {
                let favs = getFavoritedGifs();
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

        function renderFavoritesList(formEl) {
            const popover = formEl.querySelector('.tenor-popover');
            if (!popover) return;

            const resultsContainer = popover.querySelector('.tenor-popover-results');
            const clearBtn = popover.querySelector('.btn-clear-search');
            if (clearBtn) clearBtn.classList.remove('hidden');

            resultsContainer.innerHTML = '';
            const favs = getFavoritedGifs();
            
            if (favs.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="col-span-2 flex flex-col items-center justify-center py-8 px-4 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-400 mb-2 fill-yellow-400 animate-pulse" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <p class="text-xs font-bold text-[#1a3a5c]">Favorit Kosong</p>
                        <p class="text-[10px] text-gray-400 mt-1 max-w-[200px] leading-relaxed">Belum ada GIF favorit. Klik bintang pada GIF apa saja untuk menyimpannya di sini.</p>
                    </div>
                `;
                return;
            }

            favs.forEach(gifUrl => {
                const container = document.createElement('div');
                container.className = 'relative group w-full h-24 rounded-lg overflow-hidden border border-gray-100 shadow-sm bg-gray-50';

                const img = document.createElement('img');
                img.src = gifUrl;
                img.className = 'w-full h-full object-cover cursor-pointer hover:scale-105 transition duration-200';
                img.onclick = () => {
                    selectGifForForm(formEl, gifUrl);
                };

                const favBtn = document.createElement('button');
                favBtn.type = 'button';
                favBtn.className = 'absolute top-1 right-1 p-1 rounded-full bg-black/40 hover:bg-black/60 transition z-10 text-white';
                favBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`;
                
                favBtn.onclick = (e) => {
                    e.stopPropagation();
                    toggleFavoriteGif(gifUrl);
                    container.remove();
                    if (resultsContainer.children.length === 0) {
                        resultsContainer.innerHTML = `
                            <div class="col-span-2 flex flex-col items-center justify-center py-8 px-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-400 mb-2 fill-yellow-400 animate-pulse" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <p class="text-xs font-bold text-[#1a3a5c]">Favorit Kosong</p>
                                <p class="text-[10px] text-gray-400 mt-1 max-w-[200px] leading-relaxed">Belum ada GIF favorit. Klik bintang pada GIF apa saja untuk menyimpannya di sini.</p>
                            </div>
                        `;
                    }
                };

                container.appendChild(img);
                container.appendChild(favBtn);
                resultsContainer.appendChild(container);
            });
        }

        function toggleTenorPopover(formEl) {
            console.log("toggleTenorPopover toggled for form:", formEl);
            // Close any other open tenor popovers first
            document.querySelectorAll('.tenor-popover').forEach(pop => {
                if (pop !== formEl.querySelector('.tenor-popover')) {
                    pop.classList.add('hidden');
                }
            });

            const popover = formEl.querySelector('.tenor-popover');
            if (!popover) {
                console.log("Popover element not found inside form!");
                return;
            }

            if (popover.classList.contains('hidden')) {
                console.log("Popover was hidden, showing it now");
                popover.classList.remove('hidden');
                const searchInput = popover.querySelector('.tenor-popover-search');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
                
                // Show categories by default when opened
                renderTenorCategories(formEl);
            } else {
                console.log("Popover was visible, hiding it now");
                popover.classList.add('hidden');
            }
        }

        function renderTenorCategories(formEl) {
            const popover = formEl.querySelector('.tenor-popover');
            if (!popover) return;

            const resultsContainer = popover.querySelector('.tenor-popover-results');
            const clearBtn = popover.querySelector('.btn-clear-search');
            if (clearBtn) clearBtn.classList.add('hidden');

            resultsContainer.innerHTML = '';
            TENOR_CATEGORIES.forEach(cat => {
                const card = document.createElement('div');
                card.className = 'relative h-20 rounded-xl overflow-hidden cursor-pointer hover:scale-[1.02] active:scale-[0.98] transition duration-200 shadow-sm border border-gray-200 bg-gray-50';
                
                const img = document.createElement('img');
                img.className = 'absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300';
                
                if (window.tenorCategoryCache[cat.name]) {
                    img.src = window.tenorCategoryCache[cat.name];
                    img.classList.remove('opacity-0');
                } else {
                    fetch(`https://g.tenor.com/v1/search?key=${TENOR_API_KEY}&q=${encodeURIComponent(cat.query)}&limit=1`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.results && data.results[0] && data.results[0].media && data.results[0].media[0]) {
                                const mediaObj = data.results[0].media[0];
                                const previewUrl = (mediaObj.tinygif && mediaObj.tinygif.url) ? mediaObj.tinygif.url : mediaObj.gif.url;
                                window.tenorCategoryCache[cat.name] = previewUrl;
                                img.src = previewUrl;
                                img.classList.remove('opacity-0');
                            }
                        })
                        .catch(err => console.error("Error loading category image:", err));
                }
                
                const overlay = document.createElement('div');
                overlay.className = `absolute inset-0 flex items-center justify-center font-bold text-white text-xs tracking-wide transition-colors duration-200 ${cat.overlayClass}`;
                
                if (cat.name === 'Tren Baru') {
                    overlay.innerHTML = `<span class="flex items-center gap-1"><i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Tren Baru</span>`;
                } else {
                    overlay.textContent = cat.name;
                }
                
                card.appendChild(img);
                card.appendChild(overlay);
                
                card.onclick = () => {
                    const searchInput = popover.querySelector('.tenor-popover-search');
                    if (searchInput) {
                        searchInput.value = cat.name;
                        if (clearBtn) clearBtn.classList.remove('hidden');
                    }
                    if (cat.name === 'Favorit') {
                        renderFavoritesList(formEl);
                    } else {
                        fetchTenorForForm(formEl, 'search', cat.query);
                    }
                };
                
                resultsContainer.appendChild(card);
            });
            if (window.lucide) window.lucide.createIcons();
        }

        async function fetchTenorForForm(formEl, type, query = '') {
            const popover = formEl.querySelector('.tenor-popover');
            if (!popover) return;

            const resultsContainer = popover.querySelector('.tenor-popover-results');
            const clearBtn = popover.querySelector('.btn-clear-search');
            
            // Show clear button if there's a search active
            if (clearBtn) {
                if (query || type === 'search') {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            resultsContainer.innerHTML = '<div class="col-span-2 flex justify-center py-6"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-[#1a3a5c]"></div></div>';
            
            let url = `https://g.tenor.com/v1/trending?key=${TENOR_API_KEY}&limit=12`;
            if (type === 'search' && query) {
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
                            container.className = 'relative group w-full h-24 rounded-lg overflow-hidden border border-gray-100 shadow-sm bg-gray-50';

                            const img = document.createElement('img');
                            img.src = previewUrl;
                            img.className = 'w-full h-full object-cover cursor-pointer hover:scale-105 transition duration-200';
                            img.onclick = () => {
                                selectGifForForm(formEl, gifUrl);
                            };

                            const favBtn = document.createElement('button');
                            favBtn.type = 'button';
                            const isFav = getFavoritedGifs().includes(gifUrl);
                            favBtn.className = 'absolute top-1 right-1 p-1 rounded-full bg-black/40 hover:bg-black/60 transition z-10 text-white';
                            favBtn.innerHTML = isFav 
                                ? `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>`
                                : `<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-200 hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.25.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 9.42c-.783-.57-.384-1.81.587-1.81H8.48a1 1 0 00.95-.69L11.05 2.92z"/></svg>`;
                            
                            favBtn.onclick = (e) => {
                                e.stopPropagation();
                                const added = toggleFavoriteGif(gifUrl);
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
                    resultsContainer.innerHTML = '<p class="col-span-2 text-center text-gray-400 py-6 text-[11px]">GIF tidak ditemukan.</p>';
                }
            } catch (err) {
                console.error(err);
                resultsContainer.innerHTML = '<p class="col-span-2 text-center text-red-500 py-6 text-[11px]">Gagal memuat GIF.</p>';
            }
        }

        function selectGifForForm(formEl, gifUrl) {
            clearAttachment(formEl);
            formEl.querySelector('.attachment-type-input').value = 'tenor';
            formEl.querySelector('.attachment-url-input').value = gifUrl;
            renderPreview(formEl, 'tenor', gifUrl);
            
            // Close the popover
            const popover = formEl.querySelector('.tenor-popover');
            if (popover) popover.classList.add('hidden');
        }

        // Close tenor popovers & emoji pickers when clicking outside
        document.addEventListener('click', (e) => {
            // Don't close if clicking inside a popover/picker or on attachment buttons
            const isInsidePopover = e.target.closest('.tenor-popover') || e.target.closest('.emoji-picker-panel');
            const isAttachmentBtn = e.target.closest('.btn-attach-tenor') || e.target.closest('.btn-attach-emoji');
            if (isInsidePopover || isAttachmentBtn || !document.body.contains(e.target)) return;

            document.querySelectorAll('.tenor-popover').forEach(pop => {
                pop.classList.add('hidden');
            });
            document.querySelectorAll('.emoji-picker-panel').forEach(panel => {
                panel.remove();
            });
        });

        function insertTextAtCursor(textarea, text) {
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const val = textarea.value;
            textarea.value = val.substring(0, start) + text + val.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + text.length;
            textarea.focus();
            
            // Trigger input event to update limits/listeners
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
        }

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
                emojis: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐽','🐸','🐵','🙈','🙉','🙊','🐒','🐔','🐧','🐦','🐤','🐣','🐥','🦆','🦅','🦉','🦇','🐺','🐗','🐴','🦄','🐝','🪱','🐛','🦋','🐌','🐞','🐜','🪰','🪲','🦗','🕷️','🕸️','Scorpion','🐢','🐍','🦎','🐙','🦑','🦞','🦀','🐡','🐠','🐟','🐬','🐳','🐋','🦈','🐊','🐆','🐅']
            },
            {
                category: 'Makanan & Minuman',
                icon: '🍎',
                emojis: ['🍎','🍊','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🥑','🥦','🥬','🥒','🌶️','🫑','🌽','🥕','🍞','🥐','🥞','🧇','🧀','🍖','🍗','🥩','🥓','🍔','🍟','🌭','🥪','🌮','🌯',' Salad','🍿','🍳','🥤','🧋','☕','🍵','🍺','🍻','🍷']
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

        function toggleEmojiPicker(buttonEl, formEl) {
            // Close any other open emoji pickers first
            document.querySelectorAll('.emoji-picker-panel').forEach(panel => {
                if (panel !== formEl.querySelector('.emoji-picker-panel')) {
                    panel.remove();
                }
            });
            // Also close tenor popover
            const tenorPopover = formEl.querySelector('.tenor-popover');
            if (tenorPopover) tenorPopover.classList.add('hidden');

            let picker = formEl.querySelector('.emoji-picker-panel');
            if (picker) {
                picker.remove();
                return;
            }

            picker = document.createElement('div');
            picker.className = 'emoji-picker-panel absolute bottom-14 left-4 right-4 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 flex flex-col max-h-[280px] overflow-hidden animate-scale-up';
            picker.onclick = (e) => e.stopPropagation();

            picker.innerHTML = `
                <div class="p-2 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <span class="text-xs font-bold text-[#1a3a5c] flex items-center gap-1">
                        <i data-lucide="smile" class="w-3.5 h-3.5 text-[#1a3a5c]"></i> Pilih Emoji
                    </span>
                    <input type="text" placeholder="Cari emoji..." class="emoji-search-input bg-white text-[11px] text-gray-800 border border-gray-200 rounded-xl px-2.5 py-0.5 outline-none focus:border-[#1a3a5c] w-32 transition">
                </div>
                <div class="emoji-categories flex gap-1 border-b border-gray-100 p-1 overflow-x-auto bg-gray-50/30">
                    ${EMOJI_DATA.map((cat, idx) => `
                        <button type="button" class="category-tab-btn p-1 hover:bg-gray-100 rounded-lg text-sm transition" data-idx="${idx}" title="${cat.category}">
                            ${cat.icon}
                        </button>
                    `).join('')}
                </div>
                <div class="emoji-grid flex-1 overflow-y-auto p-2 grid grid-cols-8 gap-1 bg-white max-h-[170px]">
                    <!-- Emojis will go here -->
                </div>
            `;

            formEl.appendChild(picker);
            if (window.lucide) window.lucide.createIcons();

            const searchInput = picker.querySelector('.emoji-search-input');
            const grid = picker.querySelector('.emoji-grid');
            const textarea = formEl.querySelector('textarea');

            const renderEmojis = (filteredList) => {
                grid.innerHTML = filteredList.map(emoji => `
                    <button type="button" class="emoji-btn hover:scale-125 hover:bg-gray-50 rounded-lg p-1 text-lg transition duration-100 flex items-center justify-center">
                        ${emoji}
                    </button>
                `).join('');
                
                grid.querySelectorAll('.emoji-btn').forEach(btn => {
                    btn.onclick = () => {
                        insertTextAtCursor(textarea, btn.textContent.trim());
                    };
                });
            };

            const showCategory = (idx) => {
                picker.querySelectorAll('.category-tab-btn').forEach(btn => {
                    btn.classList.toggle('bg-gray-100', parseInt(btn.dataset.idx) === idx);
                });
                renderEmojis(EMOJI_DATA[idx].emojis);
            };

            picker.querySelectorAll('.category-tab-btn').forEach(btn => {
                btn.onclick = () => {
                    showCategory(parseInt(btn.dataset.idx));
                    if (searchInput) searchInput.value = '';
                };
            });

            if (searchInput) {
                searchInput.oninput = (e) => {
                    const val = e.target.value.trim().toLowerCase();
                    if (!val) {
                        showCategory(0);
                        return;
                    }
                    
                    let matchedEmojis = [];
                    EMOJI_DATA.forEach(cat => {
                        if (cat.category.toLowerCase().includes(val)) {
                            matchedEmojis = matchedEmojis.concat(cat.emojis);
                        }
                    });
                    
                    const keywordDict = {
                        'love': ['❤️','😍','🥰','😘','💖','💕','❤️‍🔥'],
                        'smile': ['😀','😃','😄','😁','😆','😊','🙂'],
                        'laugh': ['😂','🤣','😆','😅'],
                        'sad': ['😢','😭','😞','😔','🥺'],
                        'angry': ['😠','😡','🤬','😤'],
                        'heart': ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','💖','💕'],
                        'thumbs': ['👍','👎'],
                        'yes': ['👍','👌','✔️'],
                        'no': ['👎','❌'],
                        'fire': ['🔥','❤️‍🔥'],
                        'star': ['⭐','🌟','✨','💫'],
                        'ok': ['👌','👍'],
                        'clap': ['👏'],
                        'cool': ['😎']
                    };
                    
                    Object.keys(keywordDict).forEach(key => {
                        if (key.includes(val)) {
                            matchedEmojis = matchedEmojis.concat(keywordDict[key]);
                        }
                    });
                    
                    matchedEmojis = Array.from(new Set(matchedEmojis));
                    
                    if (matchedEmojis.length > 0) {
                        renderEmojis(matchedEmojis);
                    } else {
                        grid.innerHTML = '<span class="col-span-8 text-[10px] text-gray-400 text-center py-4">Tidak ada hasil</span>';
                    }
                };
            }

            showCategory(0);
        }

        // --- MAPS (LEAFLET) MODAL CONTROLLER ---
        let leafletMap = null;
        let leafletMarker = null;
        let currentTileLayer = null;
        let selectedLat = -6.2088;
        let selectedLng = 106.8456;
        let selectedLocationName = "Jakarta, Indonesia";

        // MAP_LAYERS is created lazily inside openMapModal to avoid
        // calling L.tileLayer() at script parse time (which crashes if L is not ready)
        let _mapLayersCache = null;
        function getMapLayers() {
            if (!_mapLayersCache) {
                _mapLayersCache = {
                    streets: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }),
                    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
                    }),
                    dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                    })
                };
            }
            return _mapLayersCache;
        }

        function switchMapLayer(layerName) {
            if (!leafletMap) return;
            
            // Remove current active layer
            if (currentTileLayer) {
                leafletMap.removeLayer(currentTileLayer);
            }
            
            // Add new layer
            currentTileLayer = getMapLayers()[layerName];
            currentTileLayer.addTo(leafletMap);
            
            // Update active button state style
            ['streets', 'satellite', 'dark'].forEach(name => {
                const btn = document.getElementById(`layer-btn-${name}`);
                if (btn) {
                    if (name === layerName) {
                        btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white shadow-sm";
                    } else {
                        btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200";
                    }
                }
            });
        }

        function openMapModal(formEl) {
            window.activeAttachmentForm = formEl;
            document.getElementById('map-modal').classList.remove('hidden');
            document.getElementById('map-search-input').value = '';
            document.getElementById('map-search-results').classList.add('hidden');
            document.getElementById('map-search-results').innerHTML = '';
            
            setTimeout(() => {
                if (!leafletMap) {
                    leafletMap = L.map('leaflet-map').setView([selectedLat, selectedLng], 13);
                    
                    // Set default layer (streets)
                    currentTileLayer = getMapLayers().streets;
                    currentTileLayer.addTo(leafletMap);
                    
                    leafletMarker = L.marker([selectedLat, selectedLng], { draggable: true }).addTo(leafletMap);
                    
                    const updateCoords = (lat, lng) => {
                        selectedLat = lat;
                        selectedLng = lng;
                        document.getElementById('selected-coords-text').textContent = `Koordinat: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                        
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                            .then(res => res.json())
                            .then(data => {
                                selectedLocationName = data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                                document.getElementById('selected-coords-text').textContent = `Lokasi: ${selectedLocationName}`;
                                leafletMarker.bindPopup(`<b>Lokasi Terpilih</b><br><span class="text-xs text-gray-600">${selectedLocationName}</span>`).openPopup();
                            })
                            .catch(() => {
                                selectedLocationName = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            });
                    };
                    
                    leafletMarker.on('dragend', function(e) {
                        const position = leafletMarker.getLatLng();
                        updateCoords(position.lat, position.lng);
                    });
                    
                    leafletMap.on('click', function(e) {
                        leafletMarker.setLatLng(e.latlng);
                        updateCoords(e.latlng.lat, e.latlng.lng);
                    });
                } else {
                    leafletMap.invalidateSize();
                }
            }, 200);
        }

        function closeMapModal() {
            document.getElementById('map-modal').classList.add('hidden');
        }

        async function getCurrentLocation() {
            const locBtn = document.querySelector('button[onclick="getCurrentLocation()"]');
            if (locBtn) locBtn.disabled = true;

            if (!navigator.geolocation) {
                alert('Geolokasi tidak didukung oleh browser Anda.');
                if (locBtn) locBtn.disabled = false;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    selectedLat = lat;
                    selectedLng = lon;

                    leafletMap.setView([lat, lon], 17);
                    leafletMarker.setLatLng([lat, lon]);

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(res => res.json())
                        .then(data => {
                            selectedLocationName = data.display_name || `${lat.toFixed(4)}, ${lon.toFixed(4)}`;
                            document.getElementById('selected-coords-text').textContent = `Lokasi: ${selectedLocationName}`;
                            leafletMarker.bindPopup(`<b>Lokasi Saya</b><br><span class="text-xs text-gray-600">${selectedLocationName}</span>`).openPopup();
                        })
                        .catch(() => {
                            selectedLocationName = `${lat.toFixed(4)}, ${lon.toFixed(4)}`;
                            document.getElementById('selected-coords-text').textContent = `Lokasi: ${selectedLocationName}`;
                        });
                    
                    if (locBtn) locBtn.disabled = false;
                },
                (error) => {
                    console.error(error);
                    let errMsg = 'Gagal mendapatkan lokasi Anda saat ini.';
                    if (error.code === error.PERMISSION_DENIED) {
                        errMsg += '\n\nIzin akses lokasi ditolak oleh browser. Silakan aktifkan izin lokasi untuk situs ini di pengaturan browser Anda.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        errMsg += '\n\nLayanan lokasi perangkat tidak tersedia. Jika Anda menggunakan Windows, silakan aktifkan dengan cara:\n1. Buka Settings > Privacy & security > Location.\n2. Aktifkan/Nyalakan "Location services".\n3. Pastikan izin akses lokasi untuk browser Anda juga sudah dicentang/diaktifkan.';
                    } else if (error.code === error.TIMEOUT) {
                        errMsg += '\n\nWaktu permintaan habis. Coba ulangi beberapa saat lagi.';
                    }
                    alert(errMsg);
                    if (locBtn) locBtn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        }

        async function searchLocation() {
            const query = document.getElementById('map-search-input').value.trim();
            if (!query) return;
            
            const searchBtn = document.querySelector('#map-modal button[onclick="searchLocation()"]');
            const resultsContainer = document.getElementById('map-search-results');
            searchBtn.disabled = true;
            
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`);
                const data = await res.json();
                
                resultsContainer.innerHTML = '';
                if (data && data.length > 0) {
                    resultsContainer.classList.remove('hidden');
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-gray-50 cursor-pointer transition text-xs text-gray-700 flex items-start gap-2';
                        div.innerHTML = `
                            <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="truncate">${item.display_name}</span>
                        `;
                        div.onclick = () => {
                            const lat = parseFloat(item.lat);
                            const lon = parseFloat(item.lon);
                            selectedLat = lat;
                            selectedLng = lon;
                            selectedLocationName = item.display_name;
                            
                            leafletMap.setView([lat, lon], 17);
                            leafletMarker.setLatLng([lat, lon]);
                            document.getElementById('selected-coords-text').textContent = `Lokasi: ${selectedLocationName}`;
                            leafletMarker.bindPopup(`<b>${item.type || 'Lokasi'}</b><br><span class="text-xs text-gray-600">${selectedLocationName}</span>`).openPopup();
                            
                            resultsContainer.classList.add('hidden');
                            document.getElementById('map-search-input').value = item.display_name;
                        };
                        resultsContainer.appendChild(div);
                    });
                } else {
                    resultsContainer.innerHTML = '<p class="p-3 text-xs text-gray-400 text-center">Lokasi tidak ditemukan.</p>';
                    resultsContainer.classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
                alert('Gagal mencari lokasi.');
            } finally {
                searchBtn.disabled = false;
            }
        }

        // Close search results dropdown on click outside
        document.addEventListener('click', (e) => {
            const results = document.getElementById('map-search-results');
            if (results && !results.contains(e.target) && e.target.id !== 'map-search-input') {
                results.classList.add('hidden');
            }
        });

        document.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && e.target && e.target.id === 'map-search-input') {
                searchLocation();
            }
        });

        function confirmShareLocation() {
            const form = window.activeAttachmentForm;
            if (form) {
                clearAttachment(form);
                form.querySelector('.attachment-type-input').value = 'gmaps';
                form.querySelector('.attachment-url-input').value = `${selectedLat},${selectedLng}|${selectedLocationName}`;
                renderPreview(form, 'gmaps', selectedLocationName);
            }
            closeMapModal();
        }

        // --- VIEW MAP MODAL CONTROLLER ---
        let viewLeafletMap = null;
        let viewLeafletMarker = null;
        let currentViewTileLayer = null;
        let viewSelectedLat = -6.2088;
        let viewSelectedLng = 106.8456;

        // VIEW_MAP_LAYERS is also created lazily
        let _viewMapLayersCache = null;
        function getViewMapLayers() {
            if (!_viewMapLayersCache) {
                _viewMapLayersCache = {
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
            return _viewMapLayersCache;
        }

        function switchViewMapLayer(layerName) {
            if (!viewLeafletMap) return;
            if (currentViewTileLayer) {
                viewLeafletMap.removeLayer(currentViewTileLayer);
            }
            currentViewTileLayer = getViewMapLayers()[layerName];
            currentViewTileLayer.addTo(viewLeafletMap);
            
            ['streets', 'satellite', 'dark'].forEach(name => {
                const btn = document.getElementById(`view-layer-btn-${name}`);
                if (btn) {
                    if (name === layerName) {
                        btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-[#1a3a5c] text-white shadow-sm";
                    } else {
                        btn.className = "px-2.5 py-1 text-[10px] font-bold rounded-lg transition bg-gray-100 text-gray-700 hover:bg-gray-200";
                    }
                }
            });
        }

        function viewSharedLocation(elOrCoordsStr, locationName) {
            let coordsStr = '';
            let locName = '';
            
            if (typeof elOrCoordsStr === 'object' && elOrCoordsStr !== null) {
                coordsStr = elOrCoordsStr.getAttribute('data-coords') || '';
                locName = elOrCoordsStr.getAttribute('data-name') || 'Lokasi Terbagikan';
            } else {
                coordsStr = elOrCoordsStr || '';
                locName = locationName || 'Lokasi Terbagikan';
            }

            const parts = coordsStr.split('|')[0].split(',');
            if (parts.length < 2) return;
            
            const lat = parseFloat(parts[0]);
            const lng = parseFloat(parts[1]);
            viewSelectedLat = lat;
            viewSelectedLng = lng;

            document.getElementById('view-map-title').textContent = locName;
            document.getElementById('view-map-coords-text').textContent = `Koordinat: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            document.getElementById('view-map-gmaps-link').href = `https://www.google.com/maps?q=${lat},${lng}`;
            
            document.getElementById('view-map-modal').classList.remove('hidden');

            setTimeout(() => {
                if (!viewLeafletMap) {
                    viewLeafletMap = L.map('view-leaflet-map').setView([lat, lng], 17);
                    currentViewTileLayer = getViewMapLayers().streets;
                    currentViewTileLayer.addTo(viewLeafletMap);
                    
                    viewLeafletMarker = L.marker([lat, lng]).addTo(viewLeafletMap);
                } else {
                    viewLeafletMap.setView([lat, lng], 17);
                    viewLeafletMarker.setLatLng([lat, lng]);
                    viewLeafletMap.invalidateSize();
                }
                viewLeafletMarker.bindPopup(`<b>${locName}</b>`).openPopup();
                
                // Reset layer buttons to streets
                switchViewMapLayer('streets');
            }, 200);
        }

        function closeViewMapModal() {
            document.getElementById('view-map-modal').classList.add('hidden');
        }

        // Close modals on Escape key press
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMapModal();
                closeViewMapModal();
                closeLightbox();
            }
        });

        // --- ATTACHMENT BINDINGS ---
        // ============================================================
        // PRIMARY ATTACHMENT BUTTON HANDLER - Event Delegation
        // Handles ALL .btn-attach-* clicks regardless of binding state.
        // This fires for both the main form and dynamically-shown reply forms.
        // ============================================================
        document.addEventListener('click', function(e) {
            // 1. Handle tenor popover internal controls
            if (e.target.closest('.tenor-popover')) {
                const closeBtn = e.target.closest('.btn-close-tenor');
                const clearBtn = e.target.closest('.btn-clear-search');
                const gifItem  = e.target.closest('[data-gif-url]');
                if (closeBtn) {
                    e.stopPropagation();
                    closeBtn.closest('.tenor-popover').classList.add('hidden');
                }
                if (clearBtn) {
                    e.stopPropagation();
                    const popover = clearBtn.closest('.tenor-popover');
                    const searchInput = popover ? popover.querySelector('.tenor-popover-search') : null;
                    if (searchInput) searchInput.value = '';
                    clearBtn.classList.add('hidden');
                    const form = popover ? popover.closest('.attachment-form') : null;
                    if (form) renderTenorCategories(form);
                }
                if (gifItem) {
                    // handled inline by Tenor render
                }
                return; // Stop further processing
            }
            if (e.target.closest('.emoji-picker-panel')) return;

            // 2. Handle attachment toolbar buttons
            const imageBtn = e.target.closest('.btn-attach-image');
            const tenorBtn = e.target.closest('.btn-attach-tenor');
            const gmapsBtn = e.target.closest('.btn-attach-gmaps');
            const emojiBtn  = e.target.closest('.btn-attach-emoji');

            const btn = imageBtn || tenorBtn || gmapsBtn || emojiBtn;
            if (!btn) return;

            console.log('[Attach] Button clicked:', btn.className, '| Target:', e.target.tagName);
            e.stopPropagation();
            const form = btn.closest('.attachment-form');
            console.log('[Attach] Form found:', form ? 'YES (class=' + form.className + ')' : 'NULL');
            if (!form) return;

            if (imageBtn) {
                const fi = form.querySelector('.attachment-file-input');
                if (fi) { fi.setAttribute('accept', 'image/*'); fi.click(); }
            } else if (tenorBtn) {
                toggleTenorPopover(form);
            } else if (gmapsBtn) {
                openMapModal(form);
            } else if (emojiBtn) {
                toggleEmojiPicker(emojiBtn, form);
            }
        });

        // Delegated oninput for tenor search box
        document.addEventListener('input', function(e) {
            if (!e.target.classList.contains('tenor-popover-search')) return;
            const popover = e.target.closest('.tenor-popover');
            if (!popover) return;
            const form = popover.closest('.attachment-form');
            if (!form) return;
            const clearBtn = popover.querySelector('.btn-clear-search');
            const query = e.target.value.trim();
            if (clearBtn) clearBtn.classList.toggle('hidden', !query);
            clearTimeout(popover._searchTimeout);
            popover._searchTimeout = setTimeout(() => {
                if (query) {
                    fetchTenorForForm(form, 'search', query);
                } else {
                    renderTenorCategories(form);
                }
            }, 400);
        });

        // Delegated change handler for file inputs (backup in case onchange not bound)
        document.addEventListener('change', function(e) {
            if (!e.target.classList.contains('attachment-file-input')) return;
            const form = e.target.closest('.attachment-form');
            if (!form) return;
            const file = e.target.files[0];
            if (!file) return;
            const typeInput = form.querySelector('.attachment-type-input');
            const urlInput  = form.querySelector('.attachment-url-input');
            if (typeInput) typeInput.value = '';
            if (urlInput)  urlInput.value  = '';
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (evt) => renderPreview(form, 'image', evt.target.result);
                reader.readAsDataURL(file);
            }
        });

        function initAttachmentFormBindings(form) {
            const fileInput = form.querySelector('.attachment-file-input');
            const typeInput = form.querySelector('.attachment-type-input');
            const urlInput = form.querySelector('.attachment-url-input');
            
            // File input and paste/GIF detection are bound per-form
            // (Button clicks are handled by the delegated listener above)
            
            if (fileInput) {
                fileInput.onchange = (e) => {
                    const file = e.target.files[0];
                    if (!file) return;
                    if (typeInput) typeInput.value = '';
                    if (urlInput) urlInput.value = '';
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (evt) => renderPreview(form, 'image', evt.target.result);
                        reader.readAsDataURL(file);
                    }
                };
            }
            
            const textarea = form.querySelector('textarea');
            if (textarea) {
                // Auto-detect pasted/typed GIF link and convert to attachment preview
                const checkGif = () => {
                    const content = textarea.value;
                    const gifRegex = /(https?:\/\/[^\s<>\"]+?\.(?:gif)(?:[?#][^\s<>\"]*)?|https?:\/\/(?:www\.)?media\.tenor\.com\/[^\s<>\"]+)/i;
                    const match = content.match(gifRegex);
                    if (match) {
                        const gifUrl = match[1];
                        textarea.value = content.replace(gifUrl, '').trim();
                        selectGifForForm(form, gifUrl);
                    }
                };
                
                textarea.addEventListener('input', checkGif);
                textarea.addEventListener('change', checkGif);

                textarea.addEventListener('paste', (e) => {
                    const items = (e.clipboardData || e.originalEvent.clipboardData).items;
                    for (let index in items) {
                        const item = items[index];
                        if (item.kind === 'file' && item.type.startsWith('image/')) {
                            const blob = item.getAsFile();
                            const file = new File([blob], "screenshot_" + Date.now() + ".png", { type: blob.type });
                            
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            fileInput.files = dataTransfer.files;
                            
                            if (typeInput) typeInput.value = '';
                            if (urlInput) urlInput.value = '';
                            
                            const reader = new FileReader();
                            reader.onload = (evt) => {
                                renderPreview(form, 'image', evt.target.result);
                            };
                            reader.readAsDataURL(file);
                            
                            e.preventDefault();
                            break;
                        }
                    }
                    setTimeout(checkGif, 10);
                });
            }
        }

        function renderPreview(form, type, value) {
            const previewBox = form.querySelector('.attachment-preview-box');
            if (!previewBox) return;
            
            previewBox.classList.remove('hidden');
            previewBox.innerHTML = '';
            
            let previewHtml = '';
            if (type === 'image' || type === 'giphy' || type === 'tenor') {
                previewHtml = `
                    <div class="flex items-center gap-3">
                        <img src="${value}" class="w-12 h-12 object-cover rounded-xl border border-gray-200">
                        <div>
                            <p class="text-xs font-bold text-[#1a3a5c]">${type === 'image' ? 'Unggahan Gambar' : 'GIF Terpilih'}</p>
                            <p class="text-[10px] text-gray-400">Siap dikirim</p>
                        </div>
                    </div>
                `;
            } else if (type === 'file') {
                previewHtml = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center text-[#1a3a5c]">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-[#1a3a5c] truncate max-w-[180px]">${value}</p>
                            <p class="text-[10px] text-gray-400">Berkas unggahan</p>
                        </div>
                    </div>
                `;
            } else if (type === 'gmaps') {
                previewHtml = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#e8edf2] rounded-xl flex items-center justify-center text-[#1a3a5c]">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#1a3a5c] truncate max-w-[180px]">${value}</p>
                            <p class="text-[10px] text-gray-400">Lokasi terbagikan</p>
                        </div>
                    </div>
                `;
            }
            
            previewHtml += `
                <button type="button" class="btn-clear-attachment p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            `;
            
            previewBox.innerHTML = previewHtml;
            if (window.lucide) window.lucide.createIcons();
            
            previewBox.querySelector('.btn-clear-attachment').onclick = () => {
                clearAttachment(form);
            };
        }

        function clearAttachment(form) {
            const fileInput = form.querySelector('.attachment-file-input');
            if (fileInput) fileInput.value = '';
            
            const typeInput = form.querySelector('.attachment-type-input');
            if (typeInput) typeInput.value = '';
            
            const urlInput = form.querySelector('.attachment-url-input');
            if (urlInput) urlInput.value = '';
            
            const previewBox = form.querySelector('.attachment-preview-box');
            if (previewBox) {
                previewBox.classList.add('hidden');
                previewBox.innerHTML = '';
            }
        }

        // --- LIGHTBOX CONTROLLER ---
        function openLightbox(url) {
            const lb = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            img.src = url;
            lb.classList.remove('hidden');
            lb.classList.remove('pointer-events-none');
            setTimeout(() => {
                lb.classList.add('opacity-100');
                img.classList.remove('scale-95');
                img.classList.add('scale-100');
            }, 10);
        }

        function closeLightbox() {
            const lb = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            lb.classList.remove('opacity-100');
            img.classList.remove('scale-100');
            img.classList.add('scale-95');
            setTimeout(() => {
                lb.classList.add('hidden');
                lb.classList.add('pointer-events-none');
            }, 300);
        }
    </script>

