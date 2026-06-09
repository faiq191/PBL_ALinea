<script>
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
