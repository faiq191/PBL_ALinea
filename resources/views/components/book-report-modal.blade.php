<!-- Global Book Report Modal (Bahasa Indonesia sesuai KBBI) -->
<div id="book-report-modal" class="fixed inset-0 bg-black/60 z-[9999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeBookReportModal()">
    <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl border border-gray-100 overflow-hidden flex flex-col transform scale-95 transition-all duration-300 relative animate-scale-up" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-lg font-bold text-[#1a3a5c] flex items-center gap-2">
                <i data-lucide="flag" class="w-5 h-5 text-rose-500"></i> Laporkan Buku
            </h3>
            <button type="button" onclick="closeBookReportModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-xl transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <!-- Form -->
        <form action="/reports/book" method="POST" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="book_id" id="book-report-id">

            <!-- Target Book Display -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Buku yang Dilaporkan</label>
                <div class="bg-[#e8edf2] border border-[#d0e4f5] text-[#1a3a5c] rounded-xl px-4 py-3 text-sm font-bold flex items-center gap-2">
                    <i data-lucide="book-open" class="w-4 h-4 text-[#1a3a5c]"></i>
                    <span id="book-report-title-display">-</span>
                </div>
            </div>

            <!-- Target Book Owner Display -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Pemilik Koleksi Buku</label>
                <div class="bg-slate-50 border border-slate-200 text-gray-700 rounded-xl px-4 py-2.5 text-xs font-semibold flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                    <span id="book-report-owner-display">-</span>
                </div>
            </div>

            <!-- Reason Input -->
            <div>
                <label for="book-report-reason" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Alasan Pelaporan</label>
                <textarea 
                    id="book-report-reason" 
                    name="reason" 
                    required 
                    rows="4" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm outline-none focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-100 transition resize-none text-[#1a3a5c]" 
                    placeholder="Tuliskan alasan pelaporan buku secara rinci, jelas, dan santun..."></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
                <button type="button" onclick="closeBookReportModal()" class="px-4 py-2.5 rounded-xl font-bold text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">Batal</button>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition shadow-md shadow-rose-100 flex items-center gap-1.5 hover:-translate-y-0.5 transform duration-200">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i> Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Session Notification Toasts (Premium design) -->
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="fixed top-24 right-6 lg:right-12 z-[9999] bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-semibold flex items-center gap-3 shadow-xl max-w-sm animate-scale-up">
        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
        </div>
        <p class="flex-1">{{ session('success') }}</p>
        <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
@endif
@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="fixed top-24 right-6 lg:right-12 z-[9999] bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl text-xs font-semibold flex items-center gap-3 shadow-xl max-w-sm animate-scale-up">
        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
        </div>
        <p class="flex-1">{{ session('error') }}</p>
        <button @click="show = false" class="text-rose-400 hover:text-rose-600 transition">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
@endif

<script>
    function openBookReportModal(bookId, bookTitle, ownerId, ownerName) {
        // Validation check for guests
        @guest
            window.location.href = '/login';
            return;
        @endguest

        // Set form values
        document.getElementById('book-report-id').value = bookId;
        document.getElementById('book-report-title-display').textContent = bookTitle;
        document.getElementById('book-report-owner-display').textContent = ownerName ? ownerName : 'Pengunggah Umum';
        document.getElementById('book-report-reason').value = '';

        const modal = document.getElementById('book-report-modal');
        modal.classList.remove('hidden');
        
        // Re-trigger lucide icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function closeBookReportModal() {
        const modal = document.getElementById('book-report-modal');
        modal.classList.add('hidden');
    }
</script>
