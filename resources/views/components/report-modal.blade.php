<!-- Global Report Modal (Bahasa Indonesia sesuai KBBI) -->
<div id="report-modal" class="fixed inset-0 bg-black/60 z-[9999] hidden flex items-center justify-center p-4" onclick="if(event.target === this) closeReportModal()">
    <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl border border-gray-100 overflow-hidden flex flex-col transform scale-95 transition-all duration-300 relative animate-scale-up" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-lg font-bold text-[#1a3a5c] flex items-center gap-2">
                <i data-lucide="flag" class="w-5 h-5 text-rose-500"></i> Laporkan Pengguna
            </h3>
            <button type="button" onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-xl transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <!-- Form -->
        <form action="/reports/user" method="POST" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="reported_id" id="report-reported-id">
            <input type="hidden" name="reported_type" id="report-reported-type">
            <input type="hidden" name="discussion_id" id="report-discussion-id">
            <input type="hidden" name="comment_id" id="report-comment-id">

            <!-- Target User Display -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Pengguna Terlapor</label>
                <div class="bg-[#e8edf2] border border-[#d0e4f5] text-[#1a3a5c] rounded-xl px-4 py-3 text-sm font-bold flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-[#1a3a5c]"></i>
                    <span id="report-username-display">-</span>
                </div>
            </div>

            <!-- Evidence Attachment Info -->
            <div id="report-evidence-container" class="hidden">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Bukti Lampiran</label>
                <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-3.5 text-xs flex items-start gap-2.5">
                    <i data-lucide="info" class="w-4 h-4 shrink-0 mt-0.5 text-amber-600"></i>
                    <span id="report-evidence-text">Konten yang dilaporkan akan otomatis dilampirkan dalam laporan ini.</span>
                </div>
            </div>

            <!-- Reason Input -->
            <div>
                <label for="report-reason" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 ml-1">Alasan Pelaporan</label>
                <textarea 
                    id="report-reason" 
                    name="reason" 
                    required 
                    rows="4" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm outline-none focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-100 transition resize-none text-[#1a3a5c]" 
                    placeholder="Tuliskan alasan pelaporan secara rinci, jelas, dan santun..."></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
                <button type="button" onclick="closeReportModal()" class="px-4 py-2.5 rounded-xl font-bold text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">Batal</button>
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
    function openReportModal(type, targetId, reportedUserId, reportedUserName) {
        // Validation check for guests
        @guest
            window.location.href = '/login';
            return;
        @endguest

        // Reset fields
        document.getElementById('report-reported-id').value = reportedUserId;
        document.getElementById('report-reported-type').value = type;
        document.getElementById('report-username-display').textContent = reportedUserName;
        document.getElementById('report-reason').value = '';

        const discussionIdInput = document.getElementById('report-discussion-id');
        const commentIdInput = document.getElementById('report-comment-id');
        const evidenceContainer = document.getElementById('report-evidence-container');
        const evidenceText = document.getElementById('report-evidence-text');

        discussionIdInput.value = '';
        commentIdInput.value = '';

        if (type === 'discussion') {
            discussionIdInput.value = targetId;
            evidenceContainer.classList.remove('hidden');
            evidenceText.textContent = 'Postingan diskusi oleh "' + reportedUserName + '" akan otomatis dilampirkan sebagai bukti laporan.';
        } else if (type === 'comment') {
            commentIdInput.value = targetId;
            evidenceContainer.classList.remove('hidden');
            evidenceText.textContent = 'Komentar/balasan oleh "' + reportedUserName + '" akan otomatis dilampirkan sebagai bukti laporan.';
        } else {
            evidenceContainer.classList.add('hidden');
        }

        const modal = document.getElementById('report-modal');
        modal.classList.remove('hidden');
        
        // Re-trigger lucide icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function closeReportModal() {
        const modal = document.getElementById('report-modal');
        modal.classList.add('hidden');
    }
</script>
