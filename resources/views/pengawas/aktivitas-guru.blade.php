@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Aktivitas Mengajar</span>
    </x-slot>

<div class="space-y-6">

    {{-- Filter --}}
    @php
        $hasFilter = request()->hasAny(['tanggal','guru_id']);
    @endphp
    <div x-data="{ showFilter: {{ $hasFilter ? 'true' : 'false' }} }" class="app-card p-6">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter Jurnal Mengajar</h2>
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
            <form method="GET" action="{{ route('pengawas.aktivitas-guru') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="app-label">Tanggal</label>
                    <input type="date" name="tanggal" class="app-input" value="{{ request('tanggal') }}">
                </div>
                <div>
                    <label class="app-label">Filter Guru</label>
                    <select name="guru_id" class="app-input">
                        <option value="">— Semua Guru —</option>
                        @foreach($semuaGuru as $g)
                        <option value="{{ $g->id }}" {{ request('guru_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3 pt-2">
                    <button type="submit"
                            class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm
                                   transition duration-200 shadow-sm flex items-center gap-2 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                        Terapkan
                    </button>
                    @if($hasFilter)
                    <a href="{{ route('pengawas.aktivitas-guru') }}"
                       class="px-5 py-2.5 border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm rounded-xl transition duration-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="app-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Jurnal Aktivitas Mengajar</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $aktivitas->total() }} sesi ditemukan</p>
            </div>
        </div>
        {{-- Mobile: Card List --}}
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($aktivitas as $item)
            <div class="p-4">
                <div class="flex items-start justify-between gap-2 mb-1.5">
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800 text-sm">{{ $item->user->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $item->mata_pelajaran }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <span class="font-semibold">{{ $item->kelas }}</span>
                            &nbsp;·&nbsp; Jam {{ $item->jam_ke }}
                            &nbsp;·&nbsp; {{ Carbon::parse($item->jam_mulai)->format('H:i') }}@if($item->jam_selesai)–{{ Carbon::parse($item->jam_selesai)->format('H:i') }}@endif
                        </p>
                    </div>
                    <span class="shrink-0 text-xs font-semibold text-slate-400">{{ Carbon::parse($item->tanggal)->format('d M Y') }}</span>
                </div>
            </div>
            @empty
            <div class="py-10 text-center text-slate-400 text-sm">Tidak ada data aktivitas mengajar.</div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full app-tbl text-center">
                <thead><tr>
                    <th class="text-center">Tanggal</th>
                    <th class="text-center">Guru</th>
                    <th class="text-center">Mata Pelajaran</th>
                    <th class="text-center">Kelas</th>
                    <th class="text-center">Jam ke-</th>
                    <th class="text-center">Waktu</th>
                </tr></thead>
                <tbody>
                    @forelse($aktivitas as $item)
                    <tr>
                        <td class="font-semibold whitespace-nowrap text-center">{{ Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td class="font-semibold text-slate-800 text-center">{{ $item->user->name }}</td>
                        <td class="text-center">{{ $item->mata_pelajaran }}</td>
                        <td class="text-center"><span class="app-badge b-blue">{{ $item->kelas }}</span></td>
                        <td class="text-center font-bold">{{ $item->jam_ke }}</td>
                        <td class="whitespace-nowrap text-center">
                            {{ Carbon::parse($item->jam_mulai)->format('H:i') }}
                            @if($item->jam_selesai) – {{ Carbon::parse($item->jam_selesai)->format('H:i') }} @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-10 text-slate-400">Tidak ada data aktivitas mengajar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($aktivitas->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $aktivitas->links() }}</div>
        @endif
    </div>

</div>
</x-app-layout>
