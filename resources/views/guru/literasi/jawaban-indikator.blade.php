<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Jawaban Indikator E-Book') }}
        </h2>
    </x-slot>

    <div class="space-y-6 animate-up">

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
                showFilter: localStorage.getItem('filter_guru_jawaban_indikator') === 'true' || {{ request('kelas_id') ? 'true' : 'false' }} 
            }" 
            x-init="$watch('showFilter', val => localStorage.setItem('filter_guru_jawaban_indikator', val))"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                            <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-700">Filter Pencarian</h2>
                            <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk menyesuaikan tingkat, jurusan, dan rombel</p>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100" style="display: none;">
                    <form method="GET" action="{{ route('guru.literasi.jawaban-indikator') }}" class="flex flex-row items-end gap-2 sm:gap-3 w-full">
                        <div class="flex-1 min-w-0">
                            <label class="block text-[10px] sm:hidden font-black text-slate-500 uppercase tracking-wider mb-1">Kelas</label>
                            <label class="hidden sm:block text-xs font-bold text-gray-700 mb-1.5">Pilih Kelas</label>
                            <select name="kelas_id" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-xs sm:text-sm h-[38px] sm:h-[42px] px-2 sm:px-4 py-1.5 sm:py-2.5">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->tingkat }} {{ $k->jurusan }} {{ $k->rombel }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow-sm transition flex items-center justify-center gap-1.5 h-[38px] sm:h-[42px]">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span class="hidden sm:inline">Terapkan</span>
                            </button>
                            @if(request('kelas_id'))
                                <a href="{{ route('guru.literasi.jawaban-indikator') }}" class="px-3 sm:px-5 py-2 sm:py-2.5 border border-gray-300 text-gray-700 rounded-xl text-xs sm:text-sm font-bold hover:bg-gray-50 transition flex items-center justify-center h-[38px] sm:h-[42px] gap-1.5">
                                    <svg class="w-3.5 h-3.5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span class="hidden sm:inline">Reset</span>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

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
                                <form action="{{ route('guru.literasi.jawaban-indikator.nilai') }}" method="POST">
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
</x-app-layout>
