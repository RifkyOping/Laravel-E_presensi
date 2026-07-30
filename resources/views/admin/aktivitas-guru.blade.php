@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Aktivitas Mengajar</span>
    </x-slot>

    <div class="space-y-7">

        {{-- ── WELCOME STRIP ── --}}
        <div id="welcome-strip" class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-7 shadow-xl"
            style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">Monitoring Jurnal
                        Mengajar</p>
                    <h1 class="text-white text-2xl font-black leading-tight">Aktivitas Mengajar Guru</h1>
                    <p class="text-blue-300/80 text-sm mt-1">
                        Daftar lengkap sesi & jurnal mengajar seluruh guru
                        @if(request('tanggal')) · {{ Carbon::parse(request('tanggal'))->translatedFormat('d F Y') }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <div class="bg-white/15 rounded-xl px-5 py-3 text-center min-w-[80px]">
                        <p class="text-white text-2xl font-black">{{ $aktivitas->total() }}</p>
                        <p class="text-blue-300 text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Sesi</p>
                    </div>
                    <div class="bg-white/10 rounded-xl px-5 py-3 text-center min-w-[80px]">
                        <p class="text-white text-2xl font-black">
                            {{ $aktivitas->unique('user_id')->count() }}
                        </p>
                        <p class="text-blue-300/70 text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Guru</p>
                    </div>
                </div>
            </div>
            <div
                class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none">
            </div>
            <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
        </div>

        {{-- ── FILTER ── --}}
        @php
            $hasFilter = request()->hasAny(['tanggal', 'guru_id']);
        @endphp
        <div x-data="{ showFilter: {{ $hasFilter ? 'true' : 'false' }} }"
            class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-slate-300 transition-all duration-200 shadow-sm">
            <button type="button" @click="showFilter = !showFilter"
                class="w-full text-left flex items-center justify-between group focus:outline-none">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                        <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-700">Filter Jurnal Mengajar</h2>
                        <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk memfilter berdasarkan tanggal
                            atau nama guru</p>
                    </div>
                </div>
                <div
                    class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4 text-slate-500 transition-transform duration-300"
                        :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>

            <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100" style="display: none;">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
                        <input type="date" id="filter-tanggal" value="{{ request('tanggal') }}" class="app-input" onchange="fetchData()">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Filter
                            Guru</label>
                        <select id="filter-guru" class="app-input" onchange="fetchData()">
                            <option value="">— Semua Guru —</option>
                            @foreach($semuaGuru as $g)
                                <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}
                                </option>
                            @endforeach
                        </select>
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
                        <h3 class="text-sm font-black text-slate-700">Download Rekap Aktivitas Mengajar</h3>
                        <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk mendownload laporan aktivitas format Excel</p>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showDownload }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>
            <div x-show="showDownload" x-transition class="p-6 bg-slate-50 border-t border-slate-100" style="display: none;">
                <form method="GET" action="{{ route('admin.aktivitas-guru.export') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4 w-full">
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

        {{-- ── TABEL AKTIVITAS ── --}}
        <div id="tabel-container" class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Jurnal Aktivitas Mengajar</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $aktivitas->total() }} sesi ditemukan</p>
                </div>
                <span class="text-[0.65rem] md:text-xs font-bold px-2 md:px-3 py-1 md:py-1.5 rounded-lg bg-slate-100 text-slate-600">
                    Hal {{ $aktivitas->currentPage() }} / {{ $aktivitas->lastPage() }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs md:text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70">
                            <th
                                class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">
                                Nama</th>
                            <th
                                class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">
                                Mata Pelajaran</th>
                            <th
                                class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">
                                Kelas</th>
                            <th
                                class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">
                                Jam ke-</th>
                            <th
                                class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">
                                Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($aktivitas as $item)
                            <tr class="hover:bg-slate-50/60 transition duration-150">
                                {{-- Guru --}}
                                <td class="py-2 md:py-3.5 px-2 md:px-5 text-left">
                                    <div class="flex items-center gap-1.5 md:gap-2.5">
                                        <div
                                            class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-[0.6rem] md:text-xs flex-shrink-0">
                                            {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-[0.65rem] md:text-sm font-semibold text-slate-800 max-w-[4rem] sm:max-w-[7rem] md:max-w-none truncate md:overflow-visible md:whitespace-normal">
                                            {{ $item->user->name }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Mata Pelajaran --}}
                                <td class="py-2 md:py-3.5 px-2 md:px-5 text-center text-[0.65rem] md:text-sm text-slate-600 max-w-[4.5rem] sm:max-w-[7rem] md:max-w-none truncate md:overflow-visible md:whitespace-normal">
                                    {{ $item->mata_pelajaran }}
                                </td>

                                {{-- Kelas --}}
                                <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                                    <span class="inline-block px-1.5 md:px-2.5 py-0.5 md:py-1 rounded-full text-[0.55rem] md:text-[.7rem] font-bold
                                             bg-blue-50 text-[#1e3a6e] border border-blue-100 whitespace-nowrap">
                                        {{ $item->kelas }}
                                    </span>
                                </td>

                                {{-- Jam ke- --}}
                                <td class="py-2 md:py-3.5 px-2 md:px-5 text-center font-bold text-slate-700 text-[0.65rem] md:text-sm">
                                    {{ $item->jam_ke }}
                                </td>

                                {{-- Waktu --}}
                                <td class="py-2 md:py-3.5 px-2 md:px-5 text-center text-[0.55rem] md:text-sm text-slate-600 whitespace-nowrap">
                                    <span class="font-semibold text-slate-700">
                                        {{ Carbon::parse($item->jam_mulai)->format('H:i') }}
                                    </span>
                                    @if($item->jam_selesai)
                                        <span class="text-slate-400"> –
                                            {{ Carbon::parse($item->jam_selesai)->format('H:i') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 md:py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div
                                            class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 md:w-8 md:h-8 text-slate-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-400 text-xs md:text-sm font-medium">Belum ada data aktivitas mengajar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($aktivitas->hasPages())
                <div class="px-3 md:px-6 py-2 md:py-4 border-t border-slate-100">{{ $aktivitas->links() }}</div>
            @endif
        </div>

    </div>
</x-app-layout>

<script>
    function fetchData() {
        const tanggal = document.getElementById('filter-tanggal').value;
        const guruId = document.getElementById('filter-guru').value;
        const url = new URL(window.location.href);
        
        if (tanggal) url.searchParams.set('tanggal', tanggal);
        
        if (guruId) url.searchParams.set('guru_id', guruId);
        else url.searchParams.delete('guru_id');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const els = ['welcome-strip', 'tabel-container'];
            els.forEach(id => {
                const newEl = doc.getElementById(id);
                if (newEl) document.getElementById(id).innerHTML = newEl.innerHTML;
            });
            
            window.history.pushState({}, '', url);
        });
    }

    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('#tabel-container .pagination a, #tabel-container nav a');
        if (paginationLink) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            
            const tanggal = document.getElementById('filter-tanggal').value;
            const guruId = document.getElementById('filter-guru').value;
            
            if (tanggal) url.searchParams.set('tanggal', tanggal);
            if (guruId) url.searchParams.set('guru_id', guruId);
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const tabel = doc.getElementById('tabel-container');
                if (tabel) document.getElementById('tabel-container').innerHTML = tabel.innerHTML;
                window.history.pushState({}, '', url);
            });
        }
    });
</script>