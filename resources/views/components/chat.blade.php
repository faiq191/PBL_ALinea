@auth
<x-chat.toggle-button />

<!-- Roblox Dark Chat Container -> Refactored to Alinea Light theme -->
<div id="roblox-chat-panel" class="hidden fixed bottom-24 right-6 z-50 w-[92vw] sm:w-[700px] max-w-[700px] h-[520px] bg-white rounded-3xl border border-gray-200 shadow-2xl flex overflow-hidden animate-scale-up text-slate-800 font-sans select-none">
    <x-chat.active-chat />
    <x-chat.sidebar />
</div>

<x-chat.modals />

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

{{-- Scripts --}}
<x-chat.scripts.media-pickers />
<x-chat.scripts.leaflet-map />
<x-chat.scripts.api-and-render />
<x-chat.scripts.echo-listener />
@endauth
