<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Piket Absen Sholat Murid</span>
    </x-slot>

    <div class="max-w-7xl space-y-6">
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl text-sm font-medium flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-sm font-medium flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <div x-data="{ showFilter: {{ request('kelas_id') ? 'false' : 'true' }} }" class="bg-white rounded-xl border border-slate-200 p-6">
            <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                        <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-700">Filter Kelas & Tanggal</h2>
                        <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk memilih kelas dan tanggal pencatatan</p>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>
            
            <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100" style="display: none;">
                <form method="GET" action="{{ route('piket.sholat.index') }}" class="flex flex-row items-end gap-2 sm:gap-4 w-full">
                    <div class="flex-1 min-w-0">
                        <label class="block text-[10px] sm:text-xs font-black text-slate-500 uppercase tracking-wider mb-1 sm:mb-2 truncate">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}" 
                               class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-2 sm:px-4 py-2 sm:py-2.5 text-slate-800 font-medium focus:outline-none transition text-xs sm:text-sm bg-white">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-[10px] sm:text-xs font-black text-slate-500 uppercase tracking-wider mb-1 sm:mb-2 truncate">Kelas</label>
                        <select name="kelas_id" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-2 sm:px-4 py-2 sm:py-2.5 text-slate-800 font-medium focus:outline-none transition text-xs sm:text-sm bg-white">
                            <option value="">Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    {{ $k->tingkat }} {{ $k->jurusan }} {{ $k->rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-bold transition shadow-sm h-[34px] sm:h-[42px] flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            @if($selectedKelas)
                <div x-data="{ showDownload: false }" class="border-b border-slate-100">
                    <button type="button" @click="showDownload = !showDownload" class="w-full text-left px-6 py-4 flex items-center justify-between group focus:outline-none bg-blue-50/40 hover:bg-blue-100/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-[#1e3a6e] flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#1e3a6e]">Download Rekap Absensi</h3>
                                <p class="text-[0.65rem] text-slate-500 font-medium">Klik untuk mendownload laporan absensi format Excel</p>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center bg-white group-hover:bg-blue-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showDownload }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>
                    <div x-show="showDownload" x-transition class="px-6 py-5 bg-white border-t border-slate-100" style="display: none;">
                        <form method="GET" action="{{ route('piket.sholat.export') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4 w-full">
                            <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                            <div class="flex-1">
                                <label class="block text-xs font-black text-[#1e3a6e] uppercase tracking-wider mb-2">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="app-input border-blue-200 focus:border-[#1e3a6e] focus:ring-[#1e3a6e]/20 bg-white w-full h-10 rounded-xl" value="{{ date('Y-m-01') }}" required>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-black text-[#1e3a6e] uppercase tracking-wider mb-2">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="app-input border-blue-200 focus:border-[#1e3a6e] focus:ring-[#1e3a6e]/20 bg-white w-full h-10 rounded-xl" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="w-full sm:w-auto">
                                <label class="block text-xs font-black text-[#1e3a6e] uppercase tracking-wider mb-2">Pemisah Kolom</label>
                                <select name="delimiter" class="app-input border-blue-200 focus:border-[#1e3a6e] focus:ring-[#1e3a6e]/20 bg-white w-full h-10 rounded-xl">
                                    <option value=";">Excel ID (;)</option>
                                    <option value=",">Excel EN (,)</option>
                                </select>
                            </div>
                            <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 h-10 rounded-xl text-sm transition duration-200 shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Download Rekap
                            </button>
                        </form>
                    </div>
                </div>

                @if($siswas->isEmpty())
                    <div class="p-10 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h4 class="text-slate-800 font-bold mb-1">Tidak ada murid</h4>
                        <p class="text-sm text-slate-500">Belum ada data murid untuk kelas {{ $selectedKelas ? $selectedKelas->tingkat.' '.$selectedKelas->jurusan.' '.$selectedKelas->rombel : '' }}.</p>
                    </div>
                @else
                    <div class="p-0 overflow-x-auto">
                        <form method="POST" action="{{ route('piket.sholat.store') }}">
                            @csrf
                            <input type="hidden" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}">
                            <div class="p-4 sm:p-5 bg-white border-b border-slate-100 flex flex-row items-center justify-between gap-3 sm:gap-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-slate-800 text-sm sm:text-base">Daftar Absensi Sholat</h3>
                                    <p class="text-[11px] sm:text-sm text-slate-500 mt-0.5 truncate sm:whitespace-normal">Tandai status sholat siswa. {{ $absensi->isNotEmpty() ? 'Data sudah disimpan.' : '' }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" onclick="sholatSemua()" id="btn-tandai-semua" class="inline-flex items-center justify-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] sm:text-sm px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl hover:bg-emerald-100 transition shadow-sm {{ $absensi->isNotEmpty() ? 'hidden' : '' }}">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="hidden sm:inline">Tandai Semua</span>
                                        <span class="sm:hidden">Semua Hadir</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Hidden Inputs untuk Sinkronisasi Mobile & Desktop --}}
                            @foreach($siswas as $s)
                                @php $currentStatus = isset($absensi[$s->id]) ? $absensi[$s->id]->status : 'tidak_sholat'; @endphp
                                <input type="hidden" name="status[{{ $s->id }}]" id="status-hidden-{{ $s->id }}" value="{{ $currentStatus }}">
                            @endforeach

                            {{-- Mobile View --}}
                            <div class="block sm:hidden bg-white border-b border-slate-100">
                                <div class="flex flex-row items-center gap-2 px-4 py-3.5 bg-slate-50 border-b border-slate-100 text-[10px] sm:text-xs font-black text-slate-500 uppercase tracking-wider text-center">
                                    <div class="flex-1 text-left min-w-0">Nama</div>
                                    <div class="w-12 flex-shrink-0 leading-tight">Sholat</div>
                                    <div class="w-12 flex-shrink-0 leading-tight">Udzur</div>
                                    <div class="w-12 flex-shrink-0 leading-tight">Tidak</div>
                                </div>
                                <div class="divide-y divide-slate-100">
                                    @foreach($siswas as $idx => $s)
                                        @php 
                                            $currentStatus = isset($absensi[$s->id]) ? $absensi[$s->id]->status : 'tidak_sholat';
                                        @endphp
                                        <div class="flex flex-row items-center gap-2 px-4 py-3.5 hover:bg-slate-50/50 transition">
                                            <div class="flex-1 min-w-0 pr-2">
                                                <div class="font-bold text-slate-800 text-[13px] sm:text-sm leading-tight truncate">{{ $s->name }}</div>
                                            </div>
                                            <div class="w-12 flex-shrink-0 flex justify-center">
                                                <input type="radio" name="status_mobile[{{ $s->id }}]" value="sholat" onchange="syncStatus({{ $s->id }}, 'sholat')" {{ $currentStatus === 'sholat' ? 'checked' : '' }} {{ $absensi->isNotEmpty() ? 'disabled' : '' }} class="absensi-radio w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                            </div>
                                            <div class="w-12 flex-shrink-0 flex justify-center">
                                                <input type="radio" name="status_mobile[{{ $s->id }}]" value="udzur" onchange="syncStatus({{ $s->id }}, 'udzur')" {{ $currentStatus === 'udzur' ? 'checked' : '' }} {{ $absensi->isNotEmpty() ? 'disabled' : '' }} class="absensi-radio w-5 h-5 text-amber-500 focus:ring-amber-500 border-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                            </div>
                                            <div class="w-12 flex-shrink-0 flex justify-center">
                                                <input type="radio" name="status_mobile[{{ $s->id }}]" value="tidak_sholat" onchange="syncStatus({{ $s->id }}, 'tidak_sholat')" {{ $currentStatus === 'tidak_sholat' ? 'checked' : '' }} {{ $absensi->isNotEmpty() ? 'disabled' : '' }} class="absensi-radio w-5 h-5 text-red-500 focus:ring-red-500 border-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Desktop View --}}
                            <div class="hidden sm:block">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100">
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider w-16">No</th>
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Nama</th>
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Status Absensi Sholat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($siswas as $idx => $s)
                                        @php 
                                            $currentStatus = isset($absensi[$s->id]) ? $absensi[$s->id]->status : 'tidak_sholat';
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-slate-600">{{ $idx + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-800">{{ $s->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $s->nomor_induk }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-4">
                                                    <label class="flex items-center gap-2 cursor-pointer group">
                                                        <input type="radio" name="status_desktop[{{ $s->id }}]" value="sholat" onchange="syncStatus({{ $s->id }}, 'sholat')" {{ $currentStatus === 'sholat' ? 'checked' : '' }} {{ $absensi->isNotEmpty() ? 'disabled' : '' }} class="absensi-radio w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <span class="text-sm font-semibold text-slate-600 group-hover:text-emerald-700 transition">Sholat</span>
                                                    </label>
                                                    <label class="flex items-center gap-2 cursor-pointer group">
                                                        <input type="radio" name="status_desktop[{{ $s->id }}]" value="udzur" onchange="syncStatus({{ $s->id }}, 'udzur')" {{ $currentStatus === 'udzur' ? 'checked' : '' }} {{ $absensi->isNotEmpty() ? 'disabled' : '' }} class="absensi-radio w-4 h-4 text-amber-500 focus:ring-amber-500 border-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <span class="text-sm font-semibold text-slate-600 group-hover:text-amber-600 transition">Udzur (Haid/Sakit)</span>
                                                    </label>
                                                    <label class="flex items-center gap-2 cursor-pointer group">
                                                        <input type="radio" name="status_desktop[{{ $s->id }}]" value="tidak_sholat" onchange="syncStatus({{ $s->id }}, 'tidak_sholat')" {{ $currentStatus === 'tidak_sholat' ? 'checked' : '' }} {{ $absensi->isNotEmpty() ? 'disabled' : '' }} class="absensi-radio w-4 h-4 text-red-500 focus:ring-red-500 border-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <span class="text-sm font-semibold text-slate-600 group-hover:text-red-600 transition">Tidak Sholat</span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                            
                            <div class="p-5 bg-white border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3 sticky bottom-0 z-10 shadow-[0_-5px_15px_-3px_rgba(0,0,0,0.05)]">
                                <button type="{{ $absensi->isNotEmpty() ? 'button' : 'submit' }}" {!! $absensi->isNotEmpty() ? 'onclick="enableEditMode(event, this)"' : '' !!} class="w-full sm:w-auto {{ $absensi->isNotEmpty() ? 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-[#1e3a6e] text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-bold text-sm transition duration-200 shadow-sm' : 'bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm flex items-center justify-center gap-2' }}">
                                    @if($absensi->isNotEmpty())
                                        <svg class="edit-icon w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span class="btn-text">Edit Absensi</span>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="btn-text">Simpan Absensi</span>
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                <div class="p-12 text-center flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"/></svg>
                    <h4 class="text-slate-800 font-bold text-lg mb-1">Silakan Pilih Kelas</h4>
                    <p class="text-slate-500 text-sm max-w-sm">Pilih kelas pada form filter di atas untuk mulai mendata absensi sholat siswa.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function sholatSemua() {
            document.querySelectorAll('input[id^="status-hidden-"]').forEach(hidden => {
                let id = hidden.id.replace('status-hidden-', '');
                // Pastikan tidak disabled
                let sampleRadio = document.querySelector(`input[name="status_mobile[${id}]"][value="sholat"]`);
                if(sampleRadio && !sampleRadio.disabled) {
                    syncStatus(id, 'sholat');
                }
            });
        }

        function syncStatus(id, value) {
            let hidden = document.getElementById('status-hidden-' + id);
            if(hidden) hidden.value = value;
            
            // Sync radio mobile
            document.querySelectorAll(`input[name="status_mobile[${id}]"][value="${value}"]`).forEach(r => r.checked = true);
            // Sync radio desktop
            document.querySelectorAll(`input[name="status_desktop[${id}]"][value="${value}"]`).forEach(r => r.checked = true);
        }

        function enableEditMode(e, btn) {
            if(e) e.preventDefault();
            
            // Enable all radio buttons
            document.querySelectorAll('.absensi-radio').forEach(radio => {
                radio.disabled = false;
            });
            
            // Show tandai semua button
            const btnTandai = document.getElementById('btn-tandai-semua');
            if(btnTandai) btnTandai.classList.remove('hidden');
            
            // Change button to submit state
            setTimeout(() => {
                btn.type = 'submit';
                btn.removeAttribute('onclick');
            }, 50);
            
            btn.className = 'w-full sm:w-auto bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm flex items-center justify-center gap-2';
            
            // Change text
            const btnText = btn.querySelector('.btn-text');
            if (btnText) btnText.textContent = 'Simpan Perubahan';
            
            // Change icon
            const editIcon = btn.querySelector('.edit-icon');
            if (editIcon) {
                editIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
                editIcon.classList.remove('edit-icon');
            }
        }
    </script>
</x-app-layout>
