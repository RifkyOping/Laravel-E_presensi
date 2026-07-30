@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Absensi Murid</span>
    </x-slot>
<div class="space-y-6">

    {{-- ── WELCOME STRIP ── --}}
    <div id="welcome-strip" class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">Monitoring Kehadiran</p>
                <h1 class="text-white text-2xl font-black leading-tight">Absensi Murid</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    Pantau kehadiran datang & pulang semua murid ·
                    {{ $tanggal->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex flex-row overflow-x-auto sm:overflow-visible flex-nowrap gap-2 sm:gap-3 w-full sm:w-auto mt-3 sm:mt-0 pb-1 sm:pb-0 snap-x">
                <div class="bg-white/15 rounded-xl px-4 py-2.5 text-center min-w-[75px] snap-start flex-shrink-0">
                    <p class="text-white text-xl font-black">{{ $stats['hadir'] }}</p>
                    <p class="text-blue-300 text-[0.6rem] sm:text-[0.65rem] font-semibold uppercase tracking-wider mt-0.5">Hadir</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 py-2.5 text-center min-w-[75px] snap-start flex-shrink-0">
                    <p class="text-white/90 text-xl font-black">{{ $stats['izin'] }}</p>
                    <p class="text-blue-300/80 text-[0.6rem] sm:text-[0.65rem] font-semibold uppercase tracking-wider mt-0.5">Izin</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 py-2.5 text-center min-w-[75px] snap-start flex-shrink-0">
                    <p class="text-white/90 text-xl font-black">{{ $stats['sakit'] }}</p>
                    <p class="text-blue-300/80 text-[0.6rem] sm:text-[0.65rem] font-semibold uppercase tracking-wider mt-0.5">Sakit</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 py-2.5 text-center min-w-[75px] snap-start flex-shrink-0">
                    <p class="text-white/70 text-xl font-black">{{ $stats['belum'] }}</p>
                    <p class="text-blue-300/70 text-[0.6rem] sm:text-[0.65rem] font-semibold uppercase tracking-wider mt-0.5">Belum</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 py-2.5 text-center min-w-[75px] snap-start flex-shrink-0">
                    <p class="text-white text-xl font-black">{{ $stats['total'] }}</p>
                    <p class="text-blue-300/70 text-[0.6rem] sm:text-[0.65rem] font-semibold uppercase tracking-wider mt-0.5">Total</p>
                </div>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- Filter Tanggal & Nama --}}
    @php
        $hasFilter = request()->hasAny(['tanggal','search']) && (request('tanggal') != \Carbon\Carbon::today()->format('Y-m-d') || request('search'));
    @endphp
    <div x-data="{ showFilter: {{ $hasFilter ? 'true' : 'false' }} }" class="app-card p-5">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter Pencarian Murid</h2>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk menyesuaikan pencarian</p>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100" style="display: none;">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="app-label">Tanggal</label>
                    <input type="date" id="filter-tanggal" class="app-input" value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}" onchange="fetchData()">
                </div>
                <div>
                    <label class="app-label">Cari Nama Murid</label>
                    <input type="text" id="filter-search" class="app-input" placeholder="Ketik nama murid..."
                           value="{{ request('search') }}" onkeyup="debounceFetch()">
                </div>
            </div>
        </div>
    </div>

    {{-- Export Rekap --}}
    <div x-data="{ showDownload: false }" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <button type="button" @click="showDownload = !showDownload" class="w-full text-left px-6 py-4 flex items-center justify-between group focus:outline-none hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 text-[#1e3a6e] flex items-center justify-center border border-blue-100 group-hover:bg-blue-100 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-700">Download Rekap Absensi</h3>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk mendownload laporan absensi format Excel</p>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showDownload }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
        <div x-show="showDownload" x-transition class="p-6 bg-slate-50 border-t border-slate-100" style="display: none;">
            <form method="GET" action="{{ route('admin.absensi-siswa.export') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4 w-full">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="app-input bg-white w-full" value="{{ date('Y-m-01') }}" required>
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="app-input bg-white w-full" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Pemisah Kolom</label>
                    <select name="delimiter" class="app-input bg-white w-full h-[42px]">
                        <option value=";">Excel ID (;)</option>
                        <option value=",">Excel EN (,)</option>
                    </select>
                </div>
                <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 h-[42px] rounded-xl text-sm transition duration-200 shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Download Rekap
                </button>
            </form>
        </div>
    </div>

    {{-- ── REKAP CHART ── --}}
    <div id="chart-container" class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Rekap Kehadiran Murid</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $tanggal->translatedFormat('l, d F Y') }}</p>
            </div>
            @php $pctSiswa = $stats['total'] > 0 ? round(($stats['hadir'] / $stats['total']) * 100) : 0; @endphp
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-semibold">Tingkat Kehadiran</p>
                    <p class="text-lg font-black text-[#1e3a6e]">{{ $pctSiswa }}%</p>
                </div>
                <div class="w-14 h-14 relative flex items-center justify-center">
                    <svg class="w-14 h-14 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#1e3a6e" stroke-width="3"
                                stroke-dasharray="{{ $pctSiswa }} {{ 100 - $pctSiswa }}" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute text-[.6rem] font-black text-[#1e3a6e]">{{ $pctSiswa }}%</span>
                </div>
            </div>
        </div>
        <div class="px-6 py-3 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#1e3a6e] to-[#2d5099] rounded-full transition-all duration-700"
                     style="width: {{ $pctSiswa }}%"></div>
            </div>
        </div>
    </div>

    {{-- 2 Kolom: Murid Hadir vs Belum Absen --}}
    <div id="lists-container" class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Murid Hadir --}}
        <div class="app-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Sudah Tercatat</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $tanggal->translatedFormat('l, d F Y') }}</p>
                </div>
                <span class="app-badge b-blue">{{ $siswaHadir->count() }} murid</span>
            </div>
            <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
                @forelse($siswaHadir as $s)
                @php $rec = $absensi->get($s->id); @endphp
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($s->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $s->name }}</p>
                        <p class="text-xs text-slate-400">
                            Datang: {{ $rec && $rec->waktu_datang ? Carbon::parse($rec->waktu_datang)->format('H:i').' WITA' : '—' }}
                            @if($rec && $rec->waktu_pulang) · Pulang: {{ Carbon::parse($rec->waktu_pulang)->format('H:i') }} WITA @endif
                        </p>
                    </div>
                    @php $cls = match($rec?->status) {
                        'hadir'=>'b-blue','izin'=>'b-amber','sakit'=>'b-slate',default=>'b-red'
                    }; @endphp
                    <span class="app-badge {{ $cls }} capitalize">
                        {{ $rec?->status ?? '—' }}
                        @if($rec && $rec->status_pengajuan === 'pending') (Pending) @endif
                        @if($rec && $rec->status_pengajuan === 'rejected') (Ditolak) @endif
                    </span>
                </div>
                @empty
                <p class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada murid yang absen hari ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Murid Belum Absen --}}
        <div class="app-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Belum Tercatat</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Sampai saat ini</p>
                </div>
                <span class="app-badge b-red">{{ $siswaBelum->count() }} murid</span>
            </div>
            <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
                @forelse($siswaBelum as $s)
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($s->name, 0, 1)) }}
                    </div>
                    <p class="font-semibold text-slate-700 text-sm truncate flex-1">{{ $s->name }}</p>
                    <span class="app-badge b-slate">Belum</span>
                </div>
                @empty
                <p class="px-5 py-8 text-center text-[#1e3a6e] font-semibold text-sm">Semua murid sudah absen!</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Riwayat Absensi Murid --}}
    <div id="riwayat-container" class="app-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Riwayat Absensi Murid</h3>
            <p class="text-xs text-slate-400 mt-0.5">{{ $riwayat->total() }} record ditemukan</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full app-tbl">
                <thead><tr>
                    <th class="text-center">Tanggal</th><th class="text-left">Nama</th><th class="text-center">Waktu Datang</th>
                    <th class="text-center">Waktu Pulang</th><th class="text-center">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr>
                        <td class="text-center font-semibold whitespace-nowrap">{{ Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</td>
                        <td class="text-left">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($r->user->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800">{{ $r->user->name }}</span>
                            </div>
                        </td>
                        <td class="text-center">{{ $r->waktu_datang ? Carbon::parse($r->waktu_datang)->format('H:i').' WITA' : '—' }}</td>
                        <td class="text-center">{{ $r->waktu_pulang ? Carbon::parse($r->waktu_pulang)->format('H:i').' WITA' : '—' }}</td>
                        <td class="text-center">
                            @php $cls = match($r->status) {
                                'hadir'=>'b-blue','izin'=>'b-amber','sakit'=>'b-slate',default=>'b-red'
                            }; @endphp
                            <span class="app-badge {{ $cls }} capitalize">
                                {{ $r->status }}
                                @if($r->status_pengajuan === 'pending') (Pending) @endif
                                @if($r->status_pengajuan === 'rejected') (Ditolak) @endif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-8 text-slate-400">Belum ada riwayat absensi murid.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $riwayat->links() }}</div>
        @endif
    </div>

</div>
</x-app-layout>

<script>
    let searchTimeout;
    function debounceFetch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchData, 400);
    }

    function fetchData() {
        const tanggal = document.getElementById('filter-tanggal').value;
        const search = document.getElementById('filter-search').value;
        const url = new URL(window.location.href);
        
        if (tanggal) url.searchParams.set('tanggal', tanggal);
        
        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const els = ['welcome-strip', 'chart-container', 'lists-container', 'riwayat-container'];
            els.forEach(id => {
                const newEl = doc.getElementById(id);
                if (newEl) document.getElementById(id).innerHTML = newEl.innerHTML;
            });
            
            window.history.pushState({}, '', url);
        });
    }

    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('#riwayat-container .pagination a, #riwayat-container nav a');
        if (paginationLink) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            
            const tanggal = document.getElementById('filter-tanggal').value;
            const search = document.getElementById('filter-search').value;
            
            if (tanggal) url.searchParams.set('tanggal', tanggal);
            if (search) url.searchParams.set('search', search);
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const riwayat = doc.getElementById('riwayat-container');
                if (riwayat) document.getElementById('riwayat-container').innerHTML = riwayat.innerHTML;
                window.history.pushState({}, '', url);
            });
        }
    });
</script>
