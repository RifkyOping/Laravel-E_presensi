@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard Pengawas</span>
    </x-slot>

    <div class="space-y-6">

        {{-- Header Banner --}}
        <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-5 py-5 sm:px-8 sm:py-7 shadow-xl"
             style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-blue-300 text-sm font-semibold mb-1">Selamat datang,</p>
                    <h1 class="text-white text-2xl font-black leading-tight">{{ Auth::user()->name }}</h1>
                    <p class="text-blue-300/80 text-sm mt-1">
                        {{ Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
                <div class="flex flex-col items-start sm:items-end gap-1 shrink-0">
                    <span class="text-white/40 text-xs uppercase tracking-widest font-bold">Pengawas</span>
                    <span class="text-white text-sm font-semibold">E-Presensi UPTD SMKN 1 Majene</span>
                </div>
            </div>
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
            <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['label' => 'Total Guru', 'value' => $stats['total_guru'], 'sub' => 'terdaftar', 'color' => 'text-[#1e3a6e]'],
                    ['label' => 'Guru Hadir', 'value' => $stats['guru_hadir'], 'sub' => 'hari ini', 'color' => 'text-[#1e3a6e]'],
                    ['label' => 'Total Siswa', 'value' => $stats['total_siswa'], 'sub' => 'terdaftar', 'color' => 'text-violet-600'],
                    ['label' => 'Siswa Hadir', 'value' => $stats['siswa_hadir'], 'sub' => 'hari ini', 'color' => 'text-violet-600'],
                ];
            @endphp
            @foreach($cards as $i => $c)
                <div class="stat-card animate-up delay-{{ $i + 1 }}">
                    <div class="flex items-start justify-end mb-3">
                        <span
                            class="text-[.68rem] font-semibold text-slate-400 uppercase tracking-wide">{{ $c['sub'] }}</span>
                    </div>
                    <p class="text-3xl font-black {{ $c['color'] }}">{{ $c['value'] }}</p>
                    <p class="text-sm text-slate-500 font-medium mt-0.5">{{ $c['label'] }}</p>
                </div>
            @endforeach
        </div>



        {{-- 2 kolom: Hadir & Belum Absen --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Guru Hadir Hari Ini --}}
            <div class="app-card overflow-hidden animate-up delay-2">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Absensi Guru Terbaru</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ Carbon::today()->translatedFormat('d F Y') }}</p>
                    </div>
                    <a href="{{ route('pengawas.absensi-guru') }}"
                        class="text-xs font-semibold text-[#1e3a6e] hover:underline">
                        Lihat Semua →
                    </a>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($guruHadirHariIni as $a)
                        <div class="px-5 py-3.5 flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($a->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 text-sm truncate">{{ $a->user->name }}</p>
                                <p class="text-xs text-slate-400">
                                    Datang: {{ Carbon::parse($a->waktu_datang)->format('H:i') }} WITA
                                    @if($a->waktu_pulang) · Pulang: {{ Carbon::parse($a->waktu_pulang)->format('H:i') }}
                                    WITA @endif
                                </p>
                            </div>
                            @php $cls = match($a->status) {
                                'hadir'=>'b-blue','izin'=>'b-amber','sakit'=>'b-slate',default=>'b-red'
                            }; @endphp
                            <span class="app-badge {{ $cls }} capitalize">
                                {{ $a->status }}
                                @if($a->status_pengajuan === 'pending') (Pending) @endif
                                @if($a->status_pengajuan === 'rejected') (Ditolak) @endif
                            </span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada guru yang absen.</p>
                    @endforelse
                </div>
            </div>

            {{-- Guru Belum Absen --}}
            <div class="app-card overflow-hidden animate-up delay-3">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Guru Belum Absen</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Sampai saat ini</p>
                    </div>
                    <span class="app-badge b-red">{{ $belumAbsen->count() }} guru</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($belumAbsen as $g)
                        <div class="px-5 py-3.5 flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($g->name, 0, 1)) }}
                            </div>
                            <p class="font-semibold text-slate-700 text-sm truncate flex-1">{{ $g->name }}</p>
                            <span class="app-badge b-slate">Belum</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-[#1e3a6e] font-semibold text-sm">Semua guru sudah absen!</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Aktivitas Mengajar Hari Ini --}}
        <div class="app-card overflow-hidden animate-up delay-4">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Aktivitas Mengajar Hari Ini</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ Carbon::today()->translatedFormat('d F Y') }}</p>
                </div>
                <a href="{{ route('pengawas.aktivitas-guru') }}"
                    class="text-xs font-semibold text-[#1e3a6e] hover:underline">
                    Lihat Semua →
                </a>
            </div>
            {{-- Mobile: Card List --}}
            <div class="block sm:hidden divide-y divide-slate-100">
                @forelse($aktivitasHariIni as $a)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm">{{ $a->user->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $a->mata_pelajaran }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                <span class="font-semibold">{{ $a->kelas }}</span>
                                &nbsp;·&nbsp; Jam {{ $a->jam_ke }}
                                &nbsp;·&nbsp; {{ Carbon::parse($a->jam_mulai)->format('H:i') }}@if($a->jam_selesai)–{{ Carbon::parse($a->jam_selesai)->format('H:i') }}@endif
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada aktivitas mengajar hari ini.</p>
                @endforelse
            </div>

            {{-- Desktop: Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full app-tbl text-center">
                    <thead>
                        <tr>
                            <th class="text-center">Guru</th>
                            <th class="text-center">Mata Pelajaran</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Jam ke-</th>
                            <th class="text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aktivitasHariIni as $a)
                            <tr>
                                <td class="font-semibold text-slate-800 text-center">{{ $a->user->name }}</td>
                                <td class="text-center">{{ $a->mata_pelajaran }}</td>
                                <td class="text-center">{{ $a->kelas }}</td>
                                <td class="text-center">
                                    {{ $a->jam_ke }}
                                </td>
                                <td class="whitespace-nowrap text-center">{{ Carbon::parse($a->jam_mulai)->format('H:i') }}
                                    @if($a->jam_selesai)–{{ Carbon::parse($a->jam_selesai)->format('H:i') }}@endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-slate-400">Belum ada aktivitas mengajar hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
