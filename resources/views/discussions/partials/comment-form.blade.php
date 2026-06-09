                <form action="/diskusi/{{ $discussion->id }}/comment" method="POST" enctype="multipart/form-data" class="mb-10 attachment-form relative">
                    @csrf
                    <textarea name="content" maxlength="256" rows="3" class="w-full bg-[#e8edf2] border-none rounded-2xl p-4 text-sm text-[#1a3a5c] outline-none focus:ring-2 focus:ring-[#1a3a5c] resize-none @error('content') ring-2 ring-red-500 @enderror" placeholder="Tulis balasanmu di sini... (Bisa paste screenshot/gambar juga!)"></textarea>
                    @error('content')
                        <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror

                    <!-- Preview Box -->
                    <div class="attachment-preview-box hidden mt-3 p-3 bg-gray-50 border border-gray-200 rounded-2xl flex items-center justify-between animate-fade-in"></div>

                    <!-- Inline Tenor Popover Container (Light Theme) -->
                    <div class="tenor-popover hidden absolute bottom-16 left-6 right-6 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 flex flex-col max-h-[450px] overflow-hidden animate-scale-up">
                        <div class="p-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <span class="text-xs font-bold text-[#1a3a5c] flex items-center gap-1.5">
                                <i data-lucide="film" class="w-4 h-4 text-[#1a3a5c]"></i> Cari GIF Tenor
                            </span>
                            <button type="button" class="btn-close-tenor text-gray-400 hover:text-gray-600 transition">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <div class="p-3 bg-gray-50/50 border-b border-gray-100 relative flex items-center gap-2">
                            <div class="relative flex-1">
                                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                                <input type="text" class="tenor-popover-search w-full bg-white text-gray-800 border border-gray-200 rounded-xl pl-9 pr-8 py-2 text-xs outline-none focus:border-[#1a3a5c] focus:ring-1 focus:ring-[#1a3a5c] transition" placeholder="Cari di Tenor...">
                                <button type="button" class="btn-clear-search hidden absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 transition">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="tenor-popover-results flex-1 overflow-y-auto p-3 grid grid-cols-2 gap-2 min-h-[180px] max-h-[320px] bg-white">
                            <!-- Categories or results will be injected here -->
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-1.5">
                            <input type="file" name="attachment" class="hidden attachment-file-input" onchange="(function(fi){var f=fi.closest('.attachment-form');var file=fi.files[0];if(!file)return;var t=f.querySelector('.attachment-type-input');var u=f.querySelector('.attachment-url-input');if(t)t.value='';if(u)u.value='';if(file.type.startsWith('image/')){var r=new FileReader();r.onload=function(e){renderPreview(f,'image',e.target.result)};r.readAsDataURL(file)}})(this)">
                            <input type="hidden" name="attachment_type" class="attachment-type-input">
                            <input type="hidden" name="attachment_url" class="attachment-url-input">
                            
                            <button type="button" onclick="var fi=this.closest('.attachment-form').querySelector('.attachment-file-input');fi.setAttribute('accept','image/*');fi.click(); event.stopPropagation();" class="btn-attach-image p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-100 rounded-xl transition" title="Unggah Gambar">
                                <i data-lucide="image" class="w-5 h-5"></i>
                            </button>
                            <button type="button" onclick="toggleTenorPopover(this.closest('.attachment-form')); event.stopPropagation();" class="btn-attach-tenor p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-100 rounded-xl transition" title="Cari GIF Tenor">
                                <i data-lucide="film" class="w-5 h-5"></i>
                            </button>
                            <button type="button" onclick="openMapModal(this.closest('.attachment-form')); event.stopPropagation();" class="btn-attach-gmaps p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-100 rounded-xl transition" title="Bagikan Lokasi">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </button>
                            <button type="button" onclick="toggleEmojiPicker(this,this.closest('.attachment-form')); event.stopPropagation();" class="btn-attach-emoji p-2 text-gray-400 hover:text-[#1a3a5c] hover:bg-gray-100 rounded-xl transition relative" title="Pilih Emoji">
                                <i data-lucide="smile" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <button type="submit" class="bg-[#1a3a5c] text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-[#122b45] transition">Kirim</button>
                    </div>
                </form>
