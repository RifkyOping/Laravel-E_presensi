@php use Carbon\Carbon; @endphp
<x-pengawas-layout pageTitle="Absensi Guru" pageSubtitle="Monitoring kehadiran guru">

<div class="space-y-6">

    {{-- Filter --}}
    <div x-data="{ showFilter: {{ request('guru_id') || request('status') || request('tanggal', $tanggal->format('Y-m-d')) != $tanggal->format('Y-m-d') ? 'true' : 'false' }} }" class="pw-card p-6">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter Data Absensi</h2>
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
            <form method="GET" action="{{ route('pengawas.absensi-guru') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="pw-label">Tanggal</label>
                    <input type="date" name="tanggal" class="pw-input"
                           value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="pw-label">Filter Guru</label>
                    <select name="guru_id" class="pw-input">
                        <option value="">— Semua Guru —</option>
                        @foreach($semuaGuru as $g)
                        <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected':'' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="pw-label">Status</label>
                    <select name="status" class="pw-input">
                        <option value="">— Semua Status —</option>
                        @foreach(['hadir','izin','sakit','alpha'] as $s)
                        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3 flex gap-3 pt-2">
                    <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold text-sm rounded-xl transition shadow-md shadow-[#1e3a6e]/20 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('pengawas.absensi-guru') }}"
                       class="px-6 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm rounded-xl transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Status Kehadiran Guru --}}
    <div class="pw-card overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Status Kehadiran Guru</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $tanggal->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    Hadir: {{ $absensi->where('status', 'hadir')->count() }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
                    Belum: {{ $semuaGuru->count() - $absensi->count() }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    Total: {{ $semuaGuru->count() }} Guru
                </span>
            </div>
        </div>

        {{-- Mobile: Card List --}}
        <div class="block sm:hidden divide-y divide-slate-100">
            @foreach($semuaGuru as $i => $guru)
            @php $record = $absensi->get($guru->id); @endphp
            <div class="px-4 py-3 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0
                    {{ $record ? 'bg-[#1e3a6e] text-white' : 'bg-slate-200 text-slate-500' }}">
                    {{ strtoupper(substr($guru->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 text-sm truncate">{{ $guru->name }}</p>
                    @if($record && $record->waktu_datang)
                    <p class="text-xs text-slate-400 mt-0.5">
                        Datang: {{ Carbon::parse($record->waktu_datang)->format('H:i') }} WITA
                        @if($record->waktu_pulang) &nbsp;·&nbsp; Pulang: {{ Carbon::parse($record->waktu_pulang)->format('H:i') }} WITA @endif
                    </p>
                    @else
                    <p class="text-xs text-slate-400 mt-0.5">Belum tercatat</p>
                    @endif
                </div>
                @if($record)
                    @php $cls = match($record->status) { 'hadir'=>'b-blue','izin'=>'b-amber','sakit'=>'b-slate',default=>'b-red' }; @endphp
                    <span class="pw-badge {{ $cls }} capitalize shrink-0">
                        {{ $record->status }}
                        @if($record->status_pengajuan==='pending') (P) @endif
                    </span>
                @else
                    <span class="pw-badge b-slate shrink-0">Belum</span>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full pw-tbl">
                <thead>
                    <tr>
                        <th class="w-8 text-center">No.</th>
                        <th class="text-left">Nama Guru</th>
                        <th class="text-center">Waktu Datang</th>
                        <th class="text-center">Waktu Pulang</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semuaGuru as $i => $guru)
                    @php $record = $absensi->get($guru->id); @endphp
                    <tr>
                        <td class="text-center text-slate-400 font-medium text-xs">{{ $i + 1 }}</td>
                        <td class="text-left">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($guru->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800 text-sm">{{ $guru->name }}</span>
                            </div>
                        </td>
                        <td class="text-center text-sm">
                            @if($record && $record->waktu_datang)
                                <span class="font-semibold text-slate-700">{{ Carbon::parse($record->waktu_datang)->format('H:i') }}</span>
                                <span class="text-slate-400 text-xs"> WITA</span>
                            @else <span class="text-slate-300">—</span> @endif
                        </td>
                        <td class="text-center text-sm">
                            @if($record && $record->waktu_pulang)
                                <span class="font-semibold text-slate-700">{{ Carbon::parse($record->waktu_pulang)->format('H:i') }}</span>
                                <span class="text-slate-400 text-xs"> WITA</span>
                            @else <span class="text-slate-300">—</span> @endif
                        </td>
                        <td class="text-center">
                            @if($record)
                                @php $cls = match($record->status) { 'hadir'=>'b-blue','izin'=>'b-amber','sakit'=>'b-slate',default=>'b-red' }; @endphp
                                <span class="pw-badge {{ $cls }} capitalize">
                                    {{ $record->status }}
                                    @if($record->status_pengajuan === 'pending') (Pending) @endif
                                    @if($record->status_pengajuan === 'rejected') (Ditolak) @endif
                                </span>
                            @else
                                <span class="pw-badge b-slate">Belum Absen</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat Absensi Guru --}}
    <div class="pw-card overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Riwayat Absensi Guru</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $riwayat->total() }} record ditemukan</p>
            </div>
            @if(request('tanggal') || request('guru_id') || request('status'))
                <div class="flex items-center gap-2 flex-wrap">
                    @if(request('tanggal'))
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full border border-blue-200">
                            {{ Carbon::parse(request('tanggal'))->format('d M Y') }}
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="px-2.5 py-1 bg-purple-50 text-purple-700 text-xs font-semibold rounded-full border border-purple-200 capitalize">
                            {{ request('status') }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Mobile: Card List --}}
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($riwayat as $i => $r)
            <div class="px-4 py-3 flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <div class="w-7 h-7 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">{{ strtoupper(substr($r->user->name, 0, 1)) }}</div>
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $r->user->name }}</p>
                    </div>
                    <p class="text-xs text-slate-400">
                        {{ Carbon::parse($r->tanggal)->format('d M Y') }} ({{ Carbon::parse($r->tanggal)->translatedFormat('l') }})
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if($r->waktu_datang)Datang: {{ Carbon::parse($r->waktu_datang)->format('H:i') }}@endif
                        @if($r->waktu_pulang) &nbsp;·&nbsp; Pulang: {{ Carbon::parse($r->waktu_pulang)->format('H:i') }}@endif
                    </p>
                </div>
                @php $cls = match($r->status) { 'hadir'=>'b-blue','izin'=>'b-amber','sakit'=>'b-slate',default=>'b-red' }; @endphp
                <span class="pw-badge {{ $cls }} capitalize shrink-0">
                    {{ $r->status }}
                    @if($r->status_pengajuan==='pending') (P) @endif
                </span>
            </div>
            @empty
            <div class="py-10 text-center text-slate-400 text-sm">Tidak ada data absensi ditemukan.</div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full pw-tbl">
                <thead>
                    <tr>
                        <th class="w-8 text-center">No.</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-left">Nama Guru</th>
                        <th class="text-center">Datang</th>
                        <th class="text-center">Pulang</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $i => $r)
                    <tr>
                        <td class="text-center text-slate-400 font-medium text-xs">{{ $riwayat->firstItem() + $i }}</td>
                        <td class="whitespace-nowrap text-center">
                            <span class="font-semibold text-slate-700 text-sm">{{ Carbon::parse($r->tanggal)->format('d M Y') }}</span><br>
                            <span class="text-[.7rem] text-slate-400">{{ Carbon::parse($r->tanggal)->translatedFormat('l') }}</span>
                        </td>
                        <td class="text-left">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">{{ strtoupper(substr($r->user->name, 0, 1)) }}</div>
                                <span class="font-semibold text-slate-800 text-sm">{{ $r->user->name }}</span>
                            </div>
                        </td>
                        <td class="text-center text-sm">
                            @if($r->waktu_datang)<span class="font-semibold text-slate-700">{{ Carbon::parse($r->waktu_datang)->format('H:i') }}</span><span class="text-slate-400 text-xs"> WITA</span>
                            @else <span class="text-slate-300">—</span> @endif
                        </td>
                        <td class="text-center text-sm">
                            @if($r->waktu_pulang)<span class="font-semibold text-slate-700">{{ Carbon::parse($r->waktu_pulang)->format('H:i') }}</span><span class="text-slate-400 text-xs"> WITA</span>
                            @else <span class="text-slate-300">—</span> @endif
                        </td>
                        <td class="text-center">
                            @php $cls = match($r->status) { 'hadir'=>'b-blue','izin'=>'b-amber','sakit'=>'b-slate',default=>'b-red' }; @endphp
                            <span class="pw-badge {{ $cls }} capitalize">
                                {{ $r->status }}
                                @if($r->status_pengajuan === 'pending') (Pending) @endif
                                @if($r->status_pengajuan === 'rejected') (Ditolak) @endif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-10"><div class="flex flex-col items-center gap-2 text-slate-400"><svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg><p class="text-sm font-medium">Tidak ada data absensi ditemukan.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayat->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $riwayat->links() }}</div>
        @endif
    </div>

</div>
</x-pengawas-layout>
