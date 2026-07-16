@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Detail Absensi Kelas</span>
    </x-slot>

<div class="space-y-6">

    {{-- Back Button --}}
    <div>
        <a href="{{ route('admin.rekap-absensi-kelas') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#1e3a6e] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Rekap Absensi Kelas
        </a>
    </div>

    {{-- Info Jadwal --}}
    <div class="bg-[#1e3a6e] rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-white/60 text-xs font-bold uppercase tracking-wider mb-1">Detail Absensi Kelas</p>
                <h2 class="text-2xl font-black">{{ $jadwal->mata_pelajaran }}</h2>
                <p class="text-white/75 text-sm mt-2 font-medium">
                    Kelas {{ $jadwal->kelas }} &mdash; Jam ke-{{ $jadwal->jam_ke }}
                    ({{ Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WITA)
                </p>
                <p class="text-white/60 text-xs mt-1.5 font-semibold flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Guru: {{ $jadwal->user->name }}
                </p>
            </div>
            <div class="flex-shrink-0 text-right">
                <p class="text-white/60 text-xs font-bold uppercase tracking-wider mb-1">Tanggal</p>
                <p class="text-white font-black text-xl">{{ $tanggal->translatedFormat('d F Y') }}</p>
                <span class="inline-flex items-center gap-1.5 bg-emerald-400/20 border border-emerald-400/40 text-emerald-300 font-bold text-xs px-3 py-1 rounded-full mt-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Sudah Diabsen
                </span>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    @php
        $countHadir = $absensi->where('status', 'hadir')->count();
        $countAlpa  = $absensi->where('status', 'alpa')->count();
        $countSakit = $absensi->where('status', 'sakit')->count();
        $countIzin  = $absensi->where('status', 'izin')->count();
        $total      = $absensi->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-emerald-100 p-4 text-center shadow-sm">
            <p class="text-3xl font-black text-emerald-600">{{ $countHadir }}</p>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Hadir</p>
            @if($total > 0)
            <p class="text-[10px] text-emerald-400 font-semibold mt-0.5">{{ round($countHadir / $total * 100) }}%</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-red-100 p-4 text-center shadow-sm">
            <p class="text-3xl font-black text-red-600">{{ $countAlpa }}</p>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Alpa</p>
            @if($total > 0)
            <p class="text-[10px] text-red-400 font-semibold mt-0.5">{{ round($countAlpa / $total * 100) }}%</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-blue-100 p-4 text-center shadow-sm">
            <p class="text-3xl font-black text-blue-600">{{ $countSakit }}</p>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Sakit</p>
            @if($total > 0)
            <p class="text-[10px] text-blue-400 font-semibold mt-0.5">{{ round($countSakit / $total * 100) }}%</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-amber-100 p-4 text-center shadow-sm">
            <p class="text-3xl font-black text-amber-600">{{ $countIzin }}</p>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-1">Izin</p>
            @if($total > 0)
            <p class="text-[10px] text-amber-400 font-semibold mt-0.5">{{ round($countIzin / $total * 100) }}%</p>
            @endif
        </div>
    </div>

    {{-- Daftar Siswa --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Daftar Siswa — {{ $total }} Orang
                </h3>
                <p class="text-sm text-slate-400 mt-0.5">Rekap kehadiran tiap siswa pada sesi ini</p>
            </div>
        </div>

        @if($absensi->isEmpty())
            <div class="py-14 text-center">
                <p class="text-slate-400 text-sm font-medium">Tidak ada data absensi ditemukan untuk sesi ini.</p>
            </div>
        @else
        <div class="divide-y divide-slate-100">
            @foreach($absensi as $i => $item)
            @php
                $badgeClass = match($item->status) {
                    'hadir'  => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                    'alpa'   => 'text-red-700 bg-red-50 border-red-200',
                    'sakit'  => 'text-blue-700 bg-blue-50 border-blue-200',
                    'izin'   => 'text-amber-700 bg-amber-50 border-amber-200',
                    default  => 'text-slate-700 bg-slate-50 border-slate-200',
                };
                $badgeIcon = match($item->status) {
                    'hadir'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>',
                    'alpa'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>',
                    'sakit'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'izin'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                    default  => '',
                };
                $badgeLabel = ucfirst($item->status);
            @endphp
            <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition">
                <div class="w-8 h-8 rounded-full bg-[#1e3a6e]/10 text-[#1e3a6e] flex items-center justify-center font-black text-xs flex-shrink-0">
                    {{ $i + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 truncate">{{ $item->siswa->name ?? '-' }}</p>
                    <p class="text-xs text-slate-400 font-medium">NIS: {{ $item->siswa->siswaProfile?->nis ?? '-' }}</p>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="inline-flex items-center gap-1.5 {{ $badgeClass }} border font-bold text-xs px-3 py-1.5 rounded-full flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $badgeIcon !!}</svg>
                        {{ $badgeLabel }}
                    </span>
                    @if($item->keterangan)
                        <p class="text-[10px] text-slate-400 italic">{{ $item->keterangan }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
</x-app-layout>
