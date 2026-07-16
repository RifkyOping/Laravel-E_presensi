@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Rekap Absensi Kelas</span>
    </x-slot>

<div class="space-y-7">

    {{-- ── WELCOME STRIP ── --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">Monitoring Absensi Siswa Per Kelas</p>
                <h1 class="text-white text-2xl font-black leading-tight">Rekap Absensi Kelas</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    Data absensi siswa yang diinput oleh guru per sesi mengajar
                    @if(request('tanggal')) · {{ Carbon::parse(request('tanggal'))->translatedFormat('d F Y') }} @else · {{ $tanggal->translatedFormat('d F Y') }} @endif
                </p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <div class="bg-white/15 rounded-xl px-5 py-3 text-center min-w-[80px]">
                    <p class="text-white text-2xl font-black">{{ $jadwals->total() }}</p>
                    <p class="text-blue-300 text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Sesi</p>
                </div>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- ── FILTER ── --}}
    @php $hasFilter = request()->hasAny(['tanggal','guru_id','kelas']); @endphp
    <div x-data="{ showFilter: {{ $hasFilter ? 'true' : 'false' }} }" class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter Rekap Absensi Kelas</h2>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Filter berdasarkan tanggal, guru, atau kelas</p>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100" style="display: none;">
            <form method="GET" action="{{ route('admin.rekap-absensi-kelas') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}" class="app-input">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Filter Guru</label>
                    <select name="guru_id" class="app-input">
                        <option value="">— Semua Guru —</option>
                        @foreach($semuaGuru as $g)
                        <option value="{{ $g->id }}" {{ request('guru_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Kelas</label>
                    <input type="text" name="kelas" value="{{ request('kelas') }}" placeholder="misal: X RPL 1" class="app-input">
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                        Terapkan
                    </button>
                    @if($hasFilter)
                    <a href="{{ route('admin.rekap-absensi-kelas') }}" class="btn-outline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── TABEL REKAP ── --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Sesi Kelas yang Sudah Diabsen</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $jadwals->total() }} sesi ditemukan · Klik "Detail" untuk melihat daftar siswa</p>
            </div>
            <span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600">
                Hal {{ $jadwals->currentPage() }} / {{ $jadwals->lastPage() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Guru / Mata Pelajaran</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Jam ke-</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Hadir</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Alpa</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Sakit</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Izin</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Total</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($jadwals as $jadwal)
                    <tr class="hover:bg-slate-50/60 transition duration-150">

                        {{-- Guru & Mapel --}}
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                                    {{ strtoupper(substr($jadwal->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 leading-tight">{{ $jadwal->user->name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $jadwal->mata_pelajaran }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Kelas --}}
                        <td class="py-4 px-5">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[.7rem] font-bold bg-blue-50 text-[#1e3a6e] border border-blue-100 whitespace-nowrap">
                                {{ $jadwal->kelas }}
                            </span>
                        </td>

                        {{-- Jam ke- --}}
                        <td class="py-4 px-5 text-center font-bold text-slate-700 text-sm">
                            {{ $jadwal->jam_ke }}
                        </td>

                        {{-- Statistik --}}
                        <td class="py-4 px-5 text-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-50 text-emerald-700 font-black text-sm border border-emerald-100">
                                {{ $jadwal->total_hadir }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-red-50 text-red-700 font-black text-sm border border-red-100">
                                {{ $jadwal->total_alpa }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-50 text-blue-700 font-black text-sm border border-blue-100">
                                {{ $jadwal->total_sakit }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-amber-50 text-amber-700 font-black text-sm border border-amber-100">
                                {{ $jadwal->total_izin }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center font-black text-slate-600 text-sm">
                            {{ $jadwal->total_siswa }}
                        </td>

                        {{-- Aksi --}}
                        <td class="py-4 px-5 text-center">
                            <a href="{{ route('admin.rekap-absensi-kelas.detail', ['jadwal' => $jadwal->id, 'tanggal' => $tanggal->format('Y-m-d')]) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1e3a6e] hover:underline bg-[#1e3a6e]/5 hover:bg-[#1e3a6e]/10 transition px-3 py-1.5 rounded-full">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <p class="text-slate-400 text-sm font-medium">Belum ada data absensi kelas pada tanggal ini.</p>
                                <p class="text-slate-300 text-xs">Coba ubah filter atau pilih tanggal yang berbeda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jadwals->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $jadwals->links() }}</div>
        @endif
    </div>

</div>
</x-app-layout>
