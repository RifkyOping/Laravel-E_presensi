<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Jawaban Indikator') }}
        </h2>
    </x-slot>

    <div class="-mt-4 sm:mt-0 space-y-2 md:space-y-6 animate-up">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <style>
                .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
            </style>

            <div x-data="{ 
                showFilter: window.innerWidth >= 768 || localStorage.getItem('filter_guru_jawaban_indikator') === 'true' || {{ request('kelas_id') ? 'true' : 'false' }} 
            }" 
            x-init="$watch('showFilter', val => localStorage.setItem('filter_guru_jawaban_indikator', val))"
            @resize.window="if (window.innerWidth >= 768) showFilter = true"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 sm:p-6">
            
            {{-- Desktop Header (Hidden on Mobile) --}}
            <div class="hidden md:block">
                <h2 class="text-sm font-black text-slate-700 flex items-center gap-2 mb-2">
                    <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                        <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                    </div>
                    Filter Pencarian
                </h2>
                <p class="text-sm text-slate-500 mb-5 ml-7">Pilih kelas untuk menyesuaikan tingkat, jurusan, dan rombel.</p>
            </div>

            {{-- Mobile Button (Visible only on Mobile) --}}
            <button @click="showFilter = !showFilter" type="button" 
                    class="md:hidden w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-[#1e3a6e] text-white rounded-xl font-bold shadow-[0_4px_12px_rgba(30,58,110,0.2)] hover:bg-[#162d57] active:scale-[0.98] transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <span x-text="showFilter ? 'Tutup Filter' : 'Tampilkan Filter'" class="text-sm"></span>
                <svg class="w-4 h-4 transition-transform duration-300" :class="showFilter ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

                <div x-show="showFilter" x-transition class="mt-4 pt-4 sm:mt-5 sm:pt-5 border-t border-slate-100" style="display: none;">
                    <form id="filter-form" method="GET" action="{{ route('piket.literasi.jawaban-indikator') }}" class="flex flex-row items-end gap-2 sm:gap-3 w-full">
                        <div class="flex-1 min-w-0">
                            <label class="block text-[10px] sm:hidden font-black text-slate-500 uppercase tracking-wider mb-1">Kelas</label>
                            <label class="hidden sm:block text-xs font-bold text-gray-700 mb-1.5">Pilih Kelas</label>
                            <select id="filter_kelas_id" name="kelas_id" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-xs sm:text-sm h-[38px] sm:h-[42px] px-2 sm:px-4 py-1.5 sm:py-2.5">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->tingkat }} {{ $k->jurusan }} {{ $k->rombel }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-center gap-2 shrink-0">
                            <!-- Loading indicator -->
                            <div id="loading-indicator" class="flex-shrink-0 flex items-center justify-center h-[38px] sm:h-[42px] px-3 hidden">
                                <svg class="animate-spin h-5 w-5 text-[#1e3a6e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="data-container">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($groupedJawabans as $key => $items)
                @php
                    $first = $items->first();
                    $siswa = $first->user;
                    $isDinilai = $items->whereNotNull('nilai_guru')->count() > 0;
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border {{ $isDinilai ? 'border-green-200' : 'border-gray-200' }} overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b {{ $isDinilai ? 'border-green-100 bg-green-50/50' : 'border-gray-100 bg-gray-50/50' }} flex justify-between items-start gap-2">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base line-clamp-1" title="{{ $siswa->name }}">{{ $siswa->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $siswa->siswaProfile?->kelas }} {{ $siswa->siswaProfile?->jurusan }} {{ $siswa->siswaProfile?->rombel }}
                            </p>
                            <p class="text-[0.65rem] text-gray-400 font-medium">No. Induk: {{ $siswa->nomor_induk ?? '-' }}</p>
                        </div>
                        @if($isDinilai)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-[0.65rem] font-black uppercase tracking-wider flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Dinilai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-[0.65rem] font-black uppercase tracking-wider flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Menunggu
                            </span>
                        @endif
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="mb-4 text-xs font-semibold text-slate-500 bg-slate-50 px-3 py-2 rounded-lg border border-slate-100 flex items-center justify-between">
                            <span>Tipe Buku:</span>
                            <span class="text-[#1e3a6e] uppercase tracking-wider font-black">{{ $first->jenis_buku }}</span>
                        </div>
                        
                        <details class="group flex-1 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="cursor-pointer text-sm font-bold text-[#1e3a6e] bg-[#1e3a6e]/5 hover:bg-[#1e3a6e]/10 px-4 py-2.5 rounded-xl transition flex justify-between items-center outline-none select-none">
                                <span>{{ $isDinilai ? 'Edit Penilaian' : 'Beri Penilaian' }}</span>
                                <svg class="w-4 h-4 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </summary>
                            
                            <div class="mt-4 space-y-4">
                                <form action="{{ route('piket.literasi.jawaban-indikator.nilai') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $siswa->id }}">
                                    <input type="hidden" name="buku_id" value="{{ $first->buku_id }}">
                                    <input type="hidden" name="jenis_buku" value="{{ $first->jenis_buku }}">
                                    
                                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                        @foreach($items as $jawaban)
                                            <div class="bg-blue-50/30 p-3 rounded-xl border border-blue-100">
                                                <p class="font-semibold text-gray-700 text-xs mb-1.5 leading-relaxed">Q: {{ $jawaban->indikator?->pertanyaan }}</p>
                                                <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-gray-600 text-xs mb-3 italic">
                                                    "{{ $jawaban->jawaban }}"
                                                </div>
                                                
                                                <div class="space-y-2">
                                                    <div>
                                                        <select name="nilai_guru[{{ $jawaban->indikator_id }}]" required
                                                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-xs py-2">
                                                            <option value="">Pilih Nilai</option>
                                                            <option value="4" {{ $jawaban->nilai_guru == 4 ? 'selected' : '' }}>Sangat Baik (4)</option>
                                                            <option value="3" {{ $jawaban->nilai_guru == 3 ? 'selected' : '' }}>Baik (3)</option>
                                                            <option value="2" {{ $jawaban->nilai_guru == 2 ? 'selected' : '' }}>Cukup (2)</option>
                                                            <option value="1" {{ $jawaban->nilai_guru == 1 ? 'selected' : '' }}>Kurang (1)</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <input type="text" name="catatan_guru[{{ $jawaban->indikator_id }}]" value="{{ $jawaban->catatan_guru }}"
                                                               placeholder="Catatan/alasan (opsional)..."
                                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-xs py-2">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <button type="submit" class="w-full bg-[#1e3a6e] hover:bg-[#162d57] text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm transition">
                                            Simpan Penilaian
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white p-12 text-center rounded-2xl shadow-sm border border-gray-100">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-gray-500 font-medium">Belum ada jawaban indikator dari murid.</p>
                </div>
            @endforelse
            </div>

            <div class="mt-6">
                {{ $jawabans->links() }}
            </div>
        </div>

    </div>

    <script>
        // AJAX Filter Logic
        document.addEventListener('DOMContentLoaded', function() {
            const kelasInput = document.getElementById('filter_kelas_id');
            const form = document.getElementById('filter-form');
            const container = document.getElementById('data-container');
            const loading = document.getElementById('loading-indicator');

            function fetchFilteredData() {
                const kelasId = kelasInput.value;
                
                if (loading) loading.classList.remove('hidden');

                const url = new URL(form.action);
                if (kelasId) url.searchParams.set('kelas_id', kelasId);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('data-container');
                    
                    if(newContent) {
                        container.innerHTML = newContent.innerHTML;
                    }
                    
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                })
                .finally(() => {
                    if (loading) loading.classList.add('hidden');
                });
            }

            if(kelasInput) kelasInput.addEventListener('change', fetchFilteredData);
        });
    </script>
</x-app-layout>

