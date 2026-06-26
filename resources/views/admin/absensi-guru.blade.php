@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Absensi Guru</span>
    </x-slot>

<div class="space-y-7">

    {{-- ── WELCOME STRIP ── --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-7 shadow-xl"
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
            <div class="flex gap-3 flex-wrap">
                <div class="bg-white/15 rounded-xl px-5 py-3 text-center min-w-[80px]">
                    <p class="text-white text-2xl font-black">{{ $absensi->where('status', 'hadir')->count() }}</p>
                    <p class="text-blue-300 text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Hadir</p>
                </div>
                <div class="bg-white/10 rounded-xl px-5 py-3 text-center min-w-[80px]">
                    <p class="text-white/70 text-2xl font-black">{{ $semuaGuru->count() - $absensi->count() }}</p>
                    <p class="text-blue-300/70 text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Belum</p>
                </div>
                <div class="bg-white/10 rounded-xl px-5 py-3 text-center min-w-[80px]">
                    <p class="text-white text-2xl font-black">{{ $semuaGuru->count() }}</p>
                    <p class="text-blue-300/70 text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Total</p>
                </div>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- ── FILTER ── --}}
    <form method="GET" action="{{ route('admin.absensi-guru') }}"
          class="bg-white rounded-2xl border border-slate-200 p-5 grid grid-cols-1 sm:grid-cols-4 gap-4
                 hover:border-slate-300 transition-all duration-200 shadow-sm">
        <div>
            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
            <input type="date" name="tanggal"
                   value="{{ request('tanggal', $tanggal->format('Y-m-d')) }}"
                   class="app-input">
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
        <div class="flex items-end gap-3 sm:col-span-2">
            <button type="submit"
                    class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm
                           transition duration-200 shadow-sm flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                Terapkan
            </button>
            <a href="{{ route('admin.absensi-guru') }}"
               class="px-5 py-2.5 border border-slate-200 hover:border-slate-400 text-slate-600
                      font-semibold text-sm rounded-xl transition duration-200 flex items-center gap-1.5">
                Reset
            </a>
        </div>
    </form>

    {{-- Export Bulanan --}}
    <form method="GET" action="{{ route('admin.absensi-guru.export') }}" class="bg-emerald-50/50 rounded-2xl border border-emerald-100 p-5 flex flex-col sm:flex-row items-end gap-4 shadow-sm">
        <div>
            <label class="block text-xs font-black text-emerald-800 uppercase tracking-wider mb-2">Export Rekap Bulanan (CSV)</label>
            <input type="month" name="bulan" class="app-input border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500/20 bg-white" value="{{ date('Y-m') }}" required>
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Download CSV
        </button>
    </form>

    {{-- ── STATUS PER TANGGAL ── --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
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
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-3.5 px-6 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-left">Guru</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Waktu Datang</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Waktu Pulang</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($semuaGuru as $guru)
                    @php $record = $absensi->get($guru->id); @endphp
                    <tr class="hover:bg-slate-50/60 transition duration-150 group">
                        <td class="py-3.5 px-6 text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full text-white flex items-center justify-center font-black text-sm flex-shrink-0
                                            {{ $record ? 'bg-[#1e3a6e]' : 'bg-slate-300' }}">
                                    {{ strtoupper(substr($guru->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $guru->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $guru->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            @if($record && $record->waktu_datang)
                            <div class="flex items-center justify-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0"></span>
                                <span class="text-sm font-semibold text-slate-700">
                                    {{ Carbon::parse($record->waktu_datang)->format('H:i') }}
                                    <span class="font-normal text-slate-400 text-xs">WITA</span>
                                </span>
                            </div>
                            @else
                            <span class="text-slate-300 text-sm">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            @if($record && $record->waktu_pulang)
                            <div class="flex items-center justify-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-400 flex-shrink-0"></span>
                                <span class="text-sm font-semibold text-slate-700">
                                    {{ Carbon::parse($record->waktu_pulang)->format('H:i') }}
                                    <span class="font-normal text-slate-400 text-xs">WITA</span>
                                </span>
                            </div>
                            @else
                            <span class="text-slate-300 text-sm">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            @if($record)
                                @php $cls = match($record->status) {
                                    'hadir' => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                                    'izin'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'sakit' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    default => 'bg-red-50 text-red-600 border-red-100'
                                }; @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[.7rem] font-bold border capitalize {{ $cls }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ match($record->status) { 'hadir'=>'bg-[#1e3a6e]', 'izin'=>'bg-amber-500', 'sakit'=>'bg-slate-400', default=>'bg-red-500' } }}"></span>
                                    {{ $record->status }}
                                    @if($record->status_pengajuan === 'pending') (Pending) @endif
                                    @if($record->status_pengajuan === 'rejected') (Ditolak) @endif
                                </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[.7rem] font-bold
                                         bg-red-50 text-red-500 border border-red-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                Belum Absen
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── RIWAYAT ── --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Riwayat Absensi Guru</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $riwayat->total() }} record ditemukan</p>
            </div>
            <span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600">
                Hal {{ $riwayat->currentPage() }} / {{ $riwayat->lastPage() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-3.5 px-6 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Tanggal</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-left">Guru</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Datang</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Pulang</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($riwayat as $r)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="py-3.5 px-6 text-center">
                            <p class="text-sm font-semibold text-slate-700 whitespace-nowrap">
                                {{ Carbon::parse($r->tanggal)->format('d M Y') }}
                            </p>
                            <p class="text-xs text-slate-400">{{ Carbon::parse($r->tanggal)->translatedFormat('l') }}</p>
                        </td>
                        <td class="py-3.5 px-5 text-left">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                                    {{ strtoupper(substr($r->user->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-semibold text-slate-800">{{ $r->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-600 text-center">
                            {{ $r->waktu_datang ? Carbon::parse($r->waktu_datang)->format('H:i').' WITA' : '—' }}
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-600 text-center">
                            {{ $r->waktu_pulang ? Carbon::parse($r->waktu_pulang)->format('H:i').' WITA' : '—' }}
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            @php $sc = match($r->status) {
                                'hadir' => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                                'izin'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                'sakit' => 'bg-slate-100 text-slate-600 border-slate-200',
                                default => 'bg-red-50 text-red-600 border-red-100',
                            }; @endphp
                            <span class="inline-block px-2.5 py-1 rounded-full text-[.7rem] font-bold border capitalize {{ $sc }}">
                                {{ ucfirst($r->status) }}
                                @if($r->status_pengajuan === 'pending') (Pending) @endif
                                @if($r->status_pengajuan === 'rejected') (Ditolak) @endif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-slate-400 text-sm font-medium">Belum ada data absensi.</p>
                            </div>
                        </td>
                    </tr>
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
