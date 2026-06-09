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
