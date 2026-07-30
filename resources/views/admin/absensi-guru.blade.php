@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Absensi Guru</span>
    </x-slot>

<div class="space-y-7">

    {{-- ── WELCOME STRIP ── --}}
    <div id="stats-container" class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">Monitoring Kehadiran</p>
                <h1 class="text-white text-2xl font-black leading-tight">Absensi Guru</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    Pantau kehadiran datang & pulang semua guru ·
                    {{ $tanggal->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex flex-row overflow-x-auto sm:overflow-visible flex-nowrap gap-2 sm:gap-3 w-full sm:w-auto mt-3 sm:mt-0 pb-1 sm:pb-0 snap-x">
                <div class="bg-white/15 rounded-xl px-4 sm:px-5 py-3 text-center min-w-[80px] snap-start flex-shrink-0">
                    <p class="text-white text-xl sm:text-2xl font-black">{{ $absensi->where('status', 'hadir')->count() }}</p>
                    <p class="text-blue-300 text-[0.6rem] sm:text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Hadir</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 sm:px-5 py-3 text-center min-w-[80px] snap-start flex-shrink-0">
                    <p class="text-white/90 text-xl sm:text-2xl font-black">{{ $absensi->where('status', 'izin')->count() }}</p>
                    <p class="text-blue-300/80 text-[0.6rem] sm:text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Izin</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 sm:px-5 py-3 text-center min-w-[80px] snap-start flex-shrink-0">
                    <p class="text-white/90 text-xl sm:text-2xl font-black">{{ $absensi->where('status', 'sakit')->count() }}</p>
                    <p class="text-blue-300/80 text-[0.6rem] sm:text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Sakit</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 sm:px-5 py-3 text-center min-w-[80px] snap-start flex-shrink-0">
                    <p class="text-white/70 text-xl sm:text-2xl font-black">{{ $semuaGuru->count() - $absensi->count() }}</p>
                    <p class="text-blue-300/70 text-[0.6rem] sm:text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Belum</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 sm:px-5 py-3 text-center min-w-[80px] snap-start flex-shrink-0">
                    <p class="text-white text-xl sm:text-2xl font-black">{{ $semuaGuru->count() }}</p>
                    <p class="text-blue-300/70 text-[0.6rem] sm:text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Total</p>
                </div>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- ── FILTER ── --}}
    @php
        $hasFilter = request()->hasAny(['tanggal','guru_id']) && (request('tanggal') != \Carbon\Carbon::today()->format('Y-m-d') || request('guru_id'));
    @endphp
    <div x-data="{ showFilter: {{ $hasFilter ? 'true' : 'false' }} }" class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-slate-300 transition-all duration-200 shadow-sm">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter Pencarian Guru</h2>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk memfilter berdasarkan tanggal atau nama guru</p>
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
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
                    <input type="date" id="filter-tanggal"
                           value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}"
                           class="app-input" onchange="fetchData()">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Filter Guru</label>
                    <select id="filter-guru" class="app-input" onchange="fetchData()">
                        <option value="">— Semua Guru —</option>
                        @foreach($listGuru as $g)
                        <option value="{{ $g->id }}" {{ request('guru_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
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
            <form method="GET" action="{{ route('admin.absensi-guru.export') }}" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-4 w-full">
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

    {{-- ── STATUS PER TANGGAL ── --}}
    <div id="rekap-container" class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Rekap Kehadiran</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $tanggal->translatedFormat('l, d F Y') }}</p>
            </div>
            @php $pct = $semuaGuru->count() > 0 ? round(($absensi->where('status', 'hadir')->count() / $semuaGuru->count()) * 100) : 0; @endphp
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-semibold">Tingkat Kehadiran</p>
                    <p class="text-lg font-black text-[#1e3a6e]">{{ $pct }}%</p>
                </div>
                <div class="w-14 h-14 relative flex items-center justify-center">
                    <svg class="w-14 h-14 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#1e3a6e" stroke-width="3"
                                stroke-dasharray="{{ $pct }} {{ 100 - $pct }}" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute text-[.6rem] font-black text-[#1e3a6e]">{{ $pct }}%</span>
                </div>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="px-6 py-3 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-[#1e3a6e] to-[#2d5099] rounded-full transition-all duration-700"
                     style="width: {{ $pct }}%"></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs md:text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-2 md:py-3.5 px-2 md:px-6 font-black text-slate-400 uppercase tracking-wider text-left">Nama</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Datang</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Pulang</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($semuaGuru as $guru)
                    @php $record = $absensi->get($guru->id); @endphp
                    <tr class="hover:bg-slate-50/60 transition duration-150 group">
                        <td class="py-2 md:py-3.5 px-2 md:px-6 text-left">
                            <div class="flex items-center gap-1.5 md:gap-3">
                                <div class="w-6 h-6 md:w-9 md:h-9 rounded-full text-white flex items-center justify-center font-black text-[0.6rem] md:text-sm flex-shrink-0
                                            {{ $record ? 'bg-[#1e3a6e]' : 'bg-slate-300' }}">
                                    {{ strtoupper(substr($guru->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-[0.65rem] md:text-sm max-w-[4.5rem] sm:max-w-[7rem] md:max-w-none truncate md:overflow-visible md:whitespace-normal">{{ $guru->name }}</p>
                                    <p class="text-[0.55rem] md:text-xs text-slate-400 max-w-[4.5rem] sm:max-w-[7rem] md:max-w-none truncate md:overflow-visible md:whitespace-normal">{{ $guru->nomor_induk ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            @if($record && $record->waktu_datang)
                            <div class="flex items-center justify-center gap-1 md:gap-2">
                                <span class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-green-400 flex-shrink-0"></span>
                                <span class="text-[0.65rem] md:text-sm font-semibold text-slate-700">
                                    {{ Carbon::parse($record->waktu_datang)->format('H:i') }}
                                    <span class="font-normal text-slate-400 text-[0.55rem] md:text-xs hidden md:inline">WITA</span>
                                </span>
                            </div>
                            @else
                            <span class="text-slate-300 text-[0.65rem] md:text-sm">—</span>
                            @endif
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            @if($record && $record->waktu_pulang)
                            <div class="flex items-center justify-center gap-1 md:gap-2">
                                <span class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-slate-400 flex-shrink-0"></span>
                                <span class="text-[0.65rem] md:text-sm font-semibold text-slate-700">
                                    {{ Carbon::parse($record->waktu_pulang)->format('H:i') }}
                                    <span class="font-normal text-slate-400 text-[0.55rem] md:text-xs hidden md:inline">WITA</span>
                                </span>
                            </div>
                            @else
                            <span class="text-slate-300 text-[0.65rem] md:text-sm">—</span>
                            @endif
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            @if($record)
                                @php $cls = match($record->status) {
                                    'hadir' => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                                    'izin'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'sakit' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    default => 'bg-red-50 text-red-600 border-red-100'
                                }; @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center gap-1 md:gap-1.5 px-1.5 md:px-3 py-0.5 md:py-1 rounded-full text-[0.55rem] md:text-[.7rem] font-bold border capitalize {{ $cls }}">
                                        <span class="w-1 h-1 md:w-1.5 md:h-1.5 rounded-full {{ match($record->status) { 'hadir'=>'bg-[#1e3a6e]', 'izin'=>'bg-amber-500', 'sakit'=>'bg-slate-400', default=>'bg-red-500' } }}"></span>
                                        {{ $record->status }}
                                    </span>
                                </div>
                            @else
                            <span class="inline-flex items-center gap-1 md:gap-1.5 px-1.5 md:px-3 py-0.5 md:py-1 rounded-full text-[0.55rem] md:text-[.7rem] font-bold
                                         bg-red-50 text-red-500 border border-red-100">
                                <span class="w-1 h-1 md:w-1.5 md:h-1.5 rounded-full bg-red-400"></span>
                                Belum
                            </span>
                            @endif
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            @if($record && $record->kategori)
                                @if($record->kategori === 'tepat waktu')
                                    <span class="text-[0.55rem] md:text-[0.7rem] text-emerald-500 font-bold capitalize">{{ $record->kategori }}</span>
                                @else
                                    <span class="text-[0.55rem] md:text-[0.7rem] text-red-500 font-bold capitalize">{{ $record->kategori }}</span>
                                @endif
                            @else
                                <span class="text-[0.55rem] md:text-[0.7rem] text-slate-300 font-bold">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="riwayat-container" class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Riwayat Absensi Guru</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $riwayat->total() }} record ditemukan</p>
            </div>
            <span class="text-[0.65rem] md:text-xs font-bold px-2 md:px-3 py-1 md:py-1.5 rounded-lg bg-slate-100 text-slate-600">
                Hal {{ $riwayat->currentPage() }} / {{ $riwayat->lastPage() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs md:text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-2 md:py-3.5 px-2 md:px-6 font-black text-slate-400 uppercase tracking-wider text-center w-12 md:w-auto">Tgl</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-left">Nama</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Datang</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Pulang</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($riwayat as $r)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="py-2 md:py-3.5 px-2 md:px-6 text-center">
                            <p class="text-[0.65rem] md:text-sm font-semibold text-slate-700 whitespace-nowrap">
                                {{ Carbon::parse($r->tanggal)->translatedFormat('d M y') }}
                            </p>
                            <p class="text-[0.55rem] md:text-xs text-slate-400">{{ Carbon::parse($r->tanggal)->translatedFormat('l') }}</p>
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-left">
                            <div class="flex items-center gap-1.5 md:gap-2.5">
                                <div class="w-5 h-5 md:w-7 md:h-7 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-[0.55rem] md:text-xs flex-shrink-0">
                                    {{ strtoupper(substr($r->user->name, 0, 1)) }}
                                </div>
                                <span class="text-[0.65rem] md:text-sm font-semibold text-slate-800 max-w-[4.5rem] sm:max-w-[7rem] md:max-w-none truncate md:overflow-visible md:whitespace-normal">{{ $r->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-[0.65rem] md:text-sm text-slate-600 text-center whitespace-nowrap">
                            {{ $r->waktu_datang ? Carbon::parse($r->waktu_datang)->format('H:i') : '—' }}
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-[0.65rem] md:text-sm text-slate-600 text-center whitespace-nowrap">
                            {{ $r->waktu_pulang ? Carbon::parse($r->waktu_pulang)->format('H:i') : '—' }}
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            @php $sc = match($r->status) {
                                'hadir' => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                                'izin'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                'sakit' => 'bg-slate-100 text-slate-600 border-slate-200',
                                default => 'bg-red-50 text-red-600 border-red-100',
                            }; @endphp
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-block px-1.5 md:px-2.5 py-0.5 md:py-1 rounded-full text-[0.55rem] md:text-[.7rem] font-bold border capitalize {{ $sc }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </div>
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            @if($r->kategori)
                                @if($r->kategori === 'tepat waktu')
                                    <span class="text-[0.55rem] md:text-[0.7rem] text-emerald-500 font-bold capitalize">{{ $r->kategori }}</span>
                                @else
                                    <span class="text-[0.55rem] md:text-[0.7rem] text-red-500 font-bold capitalize">{{ $r->kategori }}</span>
                                @endif
                            @else
                                <span class="text-[0.55rem] md:text-[0.7rem] text-slate-300 font-bold">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 md:py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 md:w-10 md:h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-slate-400 text-xs md:text-sm font-medium">Belum ada data absensi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
        <div class="px-3 md:px-6 py-2 md:py-4 border-t border-slate-100">{{ $riwayat->links() }}</div>
        @endif
    </div>

</div>
</x-app-layout>

<script>
    function fetchData() {
        const tanggal = document.getElementById('filter-tanggal').value;
        const guruId = document.getElementById('filter-guru').value;
        const url = new URL(window.location.href);
        if(tanggal) url.searchParams.set('tanggal', tanggal);
        if(guruId) url.searchParams.set('guru_id', guruId);
        else url.searchParams.delete('guru_id');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const stats = doc.getElementById('stats-container');
            const rekap = doc.getElementById('rekap-container');
            const riwayat = doc.getElementById('riwayat-container');
            
            if (stats) document.getElementById('stats-container').innerHTML = stats.innerHTML;
            if (rekap) document.getElementById('rekap-container').innerHTML = rekap.innerHTML;
            if (riwayat) document.getElementById('riwayat-container').innerHTML = riwayat.innerHTML;
            
            window.history.pushState({}, '', url);
        });
    }

    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('#riwayat-container .pagination a, #riwayat-container nav a');
        if (paginationLink) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            
            const tanggal = document.getElementById('filter-tanggal').value;
            const guruId = document.getElementById('filter-guru').value;
            if(tanggal) url.searchParams.set('tanggal', tanggal);
            if(guruId) url.searchParams.set('guru_id', guruId);
            
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
