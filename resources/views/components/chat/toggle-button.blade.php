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
