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
                    <p class="text-white/90 text-xl sm:text-2xl font-black">{{ $absensi->where('status', 'alpa')->count() }}</p>
                    <p class="text-blue-300/80 text-[0.6rem] sm:text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Alpa</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 sm:px-5 py-3 text-center min-w-[80px] snap-start flex-shrink-0">
                    <p class="text-white/90 text-xl sm:text-2xl font-black">{{ $absensi->where('status', 'cuti')->count() }}</p>
                    <p class="text-blue-300/80 text-[0.6rem] sm:text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Cuti</p>
                </div>
                <div class="bg-white/10 rounded-xl px-4 sm:px-5 py-3 text-center min-w-[80px] snap-start flex-shrink-0">
                    <p class="text-white/90 text-xl sm:text-2xl font-black">{{ $absensi->where('status', 'tugas')->count() }}</p>
                    <p class="text-blue-300/80 text-[0.6rem] sm:text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Tugas</p>
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
        $hasFilter = request()->hasAny(['tanggal','guru_id','nip']) && (request('tanggal') != \Carbon\Carbon::today()->format('Y-m-d') || request('guru_id') || request('nip'));
    @endphp
    <div x-data="{ 
        showFilter: localStorage.getItem('filter_admin_absensi_guru') === 'true' || {{ $hasFilter ? 'true' : 'false' }} 
    }" 
    x-init="$watch('showFilter', val => localStorage.setItem('filter_admin_absensi_guru', val))"
    class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-slate-300 transition-all duration-200 shadow-sm">
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
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
                    <input type="date" id="filter-tanggal"
                           value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}"
                           class="app-input" onchange="fetchData()">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Filter NIP</label>
                    <input type="text" id="filter-nip" placeholder="Cari NIP..."
                           value="{{ request('nip') }}"
                           class="app-input" onchange="fetchData()" onkeyup="if(event.key === 'Enter') fetchData()">
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
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Aksi</th>
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
                                    'hadir'  => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                                    'izin'   => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'sakit'  => 'bg-slate-100 text-slate-600 border-slate-200',
                                    'alpa'   => 'bg-red-50 text-red-600 border-red-100',
                                    'cuti'   => 'bg-purple-50 text-purple-700 border-purple-100',
                                    'tugas'  => 'bg-cyan-50 text-cyan-700 border-cyan-100',
                                    default  => 'bg-slate-50 text-slate-500 border-slate-200',
                                }; @endphp
                                @php $dot = match($record->status) {
                                    'hadir'  => 'bg-[#1e3a6e]',
                                    'izin'   => 'bg-amber-500',
                                    'sakit'  => 'bg-slate-400',
                                    'alpa'   => 'bg-red-500',
                                    'cuti'   => 'bg-purple-500',
                                    'tugas'  => 'bg-cyan-500',
                                    default  => 'bg-slate-400',
                                }; @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center gap-1 md:gap-1.5 px-1.5 md:px-3 py-0.5 md:py-1 rounded-full text-[0.55rem] md:text-[.7rem] font-bold border capitalize {{ $cls }}">
                                        <span class="w-1 h-1 md:w-1.5 md:h-1.5 rounded-full {{ $dot }}"></span>
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
                                @if($record->kategori === 'tepat waktu' || $record->kategori === 'tepat_waktu')
                                    <span class="text-[0.55rem] md:text-[0.7rem] text-emerald-500 font-bold capitalize">{{ str_replace('_', ' ', $record->kategori) }}</span>
                                @else
                                    <span class="text-[0.55rem] md:text-[0.7rem] text-red-500 font-bold capitalize">{{ str_replace('_', ' ', $record->kategori) }}</span>
                                @endif
                            @else
                                <span class="text-[0.55rem] md:text-[0.7rem] text-slate-300 font-bold">—</span>
                            @endif
                        </td>
                        @if(!$record)
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            <button type="button"
                                onclick="openCreateGuru({{ $guru->id }}, '{{ addslashes($guru->name) }}', '{{ $tanggal->format('Y-m-d') }}')"
                        class="inline-flex items-center gap-1 px-2 md:px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#1e3a6e] text-[0.6rem] md:text-xs font-bold border border-blue-100 transition-colors">
                                <svg class="w-3 h-3 md:w-3.5 md:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Absen Manual
                            </button>
                        </td>
                        @else
                        <td></td>
                        @endif
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
                        <th class="py-2 md:py-3.5 px-2 md:px-5 font-black text-slate-400 uppercase tracking-wider text-center">Aksi</th>
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
                                'hadir'  => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                                'izin'   => 'bg-amber-50 text-amber-700 border-amber-100',
                                'sakit'  => 'bg-slate-100 text-slate-600 border-slate-200',
                                'alpa'   => 'bg-red-50 text-red-600 border-red-100',
                                'cuti'   => 'bg-purple-50 text-purple-700 border-purple-100',
                                'tugas'  => 'bg-cyan-50 text-cyan-700 border-cyan-100',
                                default  => 'bg-slate-50 text-slate-500 border-slate-200',
                            }; @endphp
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-block px-1.5 md:px-2.5 py-0.5 md:py-1 rounded-full text-[0.55rem] md:text-[.7rem] font-bold border capitalize {{ $sc }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </div>
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            @if($r->kategori)
                                @if($r->kategori === 'tepat waktu' || $r->kategori === 'tepat_waktu')
                                    <span class="text-[0.55rem] md:text-[0.7rem] text-emerald-500 font-bold capitalize">{{ str_replace('_', ' ', $r->kategori) }}</span>
                                @else
                                    <span class="text-[0.55rem] md:text-[0.7rem] text-red-500 font-bold capitalize">{{ str_replace('_', ' ', $r->kategori) }}</span>
                                @endif
                            @else
                                <span class="text-[0.55rem] md:text-[0.7rem] text-slate-300 font-bold">—</span>
                            @endif
                        </td>
                        <td class="py-2 md:py-3.5 px-2 md:px-5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button"
                                    onclick="openEditGuru({{ $r->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-[#1e3a6e] text-xs font-bold border border-blue-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.absensi-guru.destroy', $r->id) }}" id="form-hapus-guru-{{ $r->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="hapusAbsensiGuru({{ $r->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold border border-red-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 md:py-12 text-center">
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

{{-- ── MODAL EDIT ABSENSI GURU ── --}}
<div id="modal-edit-guru"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background:rgba(15,23,42,.55);backdrop-filter:blur(6px);display:none">
    <div class="bg-white rounded-3xl shadow-[0_32px_64px_rgba(0,0,0,.22)] w-full max-w-md
                transform transition-all duration-300 scale-95 opacity-0"
         id="modal-edit-guru-card">

        {{-- Gradient Header --}}
        <div class="px-6 py-5 rounded-t-3xl flex items-center justify-between"
             style="background:linear-gradient(135deg,#1e3a6e 0%,#2d5099 100%)">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                    <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-black text-sm leading-tight">Edit Absensi Guru</h3>
                    <p id="modal-guru-nama" class="text-blue-200 text-xs mt-0.5 font-medium"></p>
                </div>
            </div>
            <button onclick="closeEditGuru()"
                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form id="form-edit-guru" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-guru-id" value="">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Waktu Datang</label>
                    <div class="flex gap-1.5">
                        <input type="time" id="edit-guru-datang" name="waktu_datang" class="app-input flex-1 min-w-0 rounded-xl">
                        <button type="button" onclick="document.getElementById('edit-guru-datang').value=''"
                            title="Kosongkan"
                            class="flex-shrink-0 w-8 h-[42px] flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all text-xs">
                            ✕
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Waktu Pulang</label>
                    <div class="flex gap-1.5">
                        <input type="time" id="edit-guru-pulang" name="waktu_pulang" class="app-input flex-1 min-w-0 rounded-xl">
                        <button type="button" onclick="document.getElementById('edit-guru-pulang').value=''"
                            title="Kosongkan"
                            class="flex-shrink-0 w-8 h-[42px] flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all text-xs">
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Status Kehadiran</label>
                <select id="edit-guru-status" name="status" class="app-input w-full rounded-xl">
                    <option value="">— Kosong / Belum Absen —</option>
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpa">Alpa</option>
                    <option value="cuti">Cuti</option>
                    <option value="tugas">Tugas</option>
                </select>
            </div>

            <div>
                <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Kategori</label>
                <select id="edit-guru-kategori" name="kategori" class="app-input w-full rounded-xl">
                    <option value="">— Tidak Ada —</option>
                    <option value="tepat waktu">Tepat Waktu</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="pulang lebih awal">Pulang Lebih Awal</option>
                    <option value="terlambat dan pulang lebih awal">Terlambat &amp; Pulang Lebih Awal</option>
                    <option value="lupa absen pulang">Lupa Absen Pulang</option>
                    <option value="terlambat dan lupa absen pulang">Terlambat &amp; Lupa Absen Pulang</option>
                    <option value="bolos">Bolos</option>
                    <option value="alpa">Alpa</option>
                </select>
            </div>

            <div>
                <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Keterangan</label>
                <textarea id="edit-guru-keterangan" name="keterangan" rows="2"
                    class="app-input w-full resize-none rounded-xl" placeholder="(opsional)"></textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeEditGuru()"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-white text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                    style="background:linear-gradient(135deg,#1e3a6e 0%,#2d5099 100%)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>

{{-- ── MODAL BUAT ABSENSI GURU (MANUAL) ── --}}
<div id="modal-create-guru"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background:rgba(15,23,42,.55);backdrop-filter:blur(6px);display:none">
    <div class="bg-white rounded-3xl shadow-[0_32px_64px_rgba(0,0,0,.22)] w-full max-w-md
                transform transition-all duration-300 scale-95 opacity-0"
         id="modal-create-guru-card">

        {{-- Gradient Header --}}
        <div class="px-6 py-5 rounded-t-3xl flex items-center justify-between"
             style="background:linear-gradient(135deg,#1e3a6e 0%,#2d5099 100%)">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-black text-sm leading-tight">Absen Manual Guru</h3>
                    <p id="modal-create-guru-nama" class="text-blue-200 text-xs mt-0.5 font-medium"></p>
                </div>
            </div>
            <button onclick="closeCreateGuru()"
                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form id="form-create-guru" method="POST" action="{{ route('admin.absensi-guru.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="create-guru-user-id" name="user_id" value="">
            <input type="hidden" id="create-guru-tanggal" name="tanggal" value="">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Waktu Datang</label>
                    <input type="time" id="create-guru-datang" name="waktu_datang" class="app-input w-full rounded-xl">
                </div>
                <div>
                    <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Waktu Pulang</label>
                    <input type="time" id="create-guru-pulang" name="waktu_pulang" class="app-input w-full rounded-xl">
                </div>
            </div>

            <div>
                <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Status Kehadiran <span class="text-red-400">*</span></label>
                <select id="create-guru-status" name="status" class="app-input w-full rounded-xl" required>
                    <option value="">— Pilih Status —</option>
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpa">Alpa</option>
                    <option value="cuti">Cuti</option>
                    <option value="tugas">Tugas</option>
                </select>
            </div>

            <div>
                <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1.5">Keterangan</label>
                <textarea id="create-guru-keterangan" name="keterangan" rows="2"
                    class="app-input w-full resize-none rounded-xl" placeholder="(opsional)"></textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeCreateGuru()"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-white text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                    style="background:linear-gradient(135deg,#1e3a6e 0%,#2d5099 100%)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Absensi
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function fetchData() {
        const tanggal = document.getElementById('filter-tanggal').value;
        const guruId = document.getElementById('filter-guru').value;
        const nip = document.getElementById('filter-nip').value;
        const url = new URL(window.location.href);
        
        if(tanggal) url.searchParams.set('tanggal', tanggal);
        else url.searchParams.delete('tanggal');
        
        if(guruId) url.searchParams.set('guru_id', guruId);
        else url.searchParams.delete('guru_id');
        
        if(nip) url.searchParams.set('nip', nip);
        else url.searchParams.delete('nip');

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

    // ── Modal Create Guru (Absen Manual) ──
    function openCreateGuru(userId, nama, tanggal) {
        document.getElementById('create-guru-user-id').value = userId;
        document.getElementById('create-guru-tanggal').value = tanggal;
        document.getElementById('modal-create-guru-nama').textContent = nama + ' · ' + tanggal;
        document.getElementById('create-guru-datang').value = '';
        document.getElementById('create-guru-pulang').value = '';
        document.getElementById('create-guru-status').value = '';
        document.getElementById('create-guru-keterangan').value = '';
        const overlay = document.getElementById('modal-create-guru');
        const card = document.getElementById('modal-create-guru-card');
        overlay.style.display = 'flex';
        requestAnimationFrame(() => {
            card.classList.remove('scale-95','opacity-0');
            card.classList.add('scale-100','opacity-100');
        });
        document.body.style.overflow = 'hidden';
    }

    function closeCreateGuru() {
        const overlay = document.getElementById('modal-create-guru');
        const card = document.getElementById('modal-create-guru-card');
        card.classList.remove('scale-100','opacity-100');
        card.classList.add('scale-95','opacity-0');
        setTimeout(() => { overlay.style.display = 'none'; }, 250);
        document.body.style.overflow = '';
    }

    document.getElementById('modal-create-guru').addEventListener('click', function(e) {
        if (e.target === this) closeCreateGuru();
    });

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

    // ── Modal Edit Guru ──
    function openEditGuru(id) {
        fetch(`/admin/absensi-guru/${id}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('edit-guru-id').value = data.id;
            document.getElementById('modal-guru-nama').textContent = data.nama + ' · ' + data.tanggal;
            document.getElementById('edit-guru-datang').value   = data.waktu_datang  ?? '';
            document.getElementById('edit-guru-pulang').value   = data.waktu_pulang  ?? '';
            document.getElementById('edit-guru-status').value   = data.status        ?? 'hadir';
            document.getElementById('edit-guru-kategori').value = data.kategori      ?? '';
            document.getElementById('edit-guru-keterangan').value = data.keterangan  ?? '';

            document.getElementById('form-edit-guru').action = `/admin/absensi-guru/${data.id}`;
            const overlay = document.getElementById('modal-edit-guru');
            const card = document.getElementById('modal-edit-guru-card');
            overlay.style.display = 'flex';
            requestAnimationFrame(() => {
                card.classList.remove('scale-95','opacity-0');
                card.classList.add('scale-100','opacity-100');
            });
            document.body.style.overflow = 'hidden';
        })
        .catch(() => Swal.fire('Error', 'Gagal memuat data absensi.', 'error'));
    }

    function closeEditGuru() {
        const overlay = document.getElementById('modal-edit-guru');
        const card = document.getElementById('modal-edit-guru-card');
        card.classList.remove('scale-100','opacity-100');
        card.classList.add('scale-95','opacity-0');
        setTimeout(() => { overlay.style.display = 'none'; }, 250);
        document.body.style.overflow = '';
    }

    document.getElementById('modal-edit-guru').addEventListener('click', function(e) {
        if (e.target === this) closeEditGuru();
    });

    // ── Hapus Absensi Guru ──
    function hapusAbsensiGuru(id) {
        Swal.fire({
            title: 'Hapus Absensi?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonText: '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                popup: 'swal-popup-custom',
                title: 'swal-title-custom',
                htmlContainer: 'swal-text-custom',
                confirmButton: 'swal-btn-danger',
                cancelButton: 'swal-btn-cancel',
                actions: 'swal-actions-custom',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-hapus-guru-' + id).submit();
            }
        });
    }

    // ── Notifikasi session ──
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil Disimpan!',
        text: '{{ session('success') }}',
        timer: 3500,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: false,
        buttonsStyling: false,
        customClass: {
            popup: 'swal-popup-custom swal-success-popup',
            title: 'swal-title-custom',
            htmlContainer: 'swal-text-custom',
            timerProgressBar: 'swal-progress-bar',
        },
    });
    @endif
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        text: '{{ session('error') }}',
        buttonsStyling: false,
        customClass: {
            popup: 'swal-popup-custom',
            title: 'swal-title-custom',
            htmlContainer: 'swal-text-custom',
            confirmButton: 'swal-btn-primary',
        },
    });
    @endif
</script>
