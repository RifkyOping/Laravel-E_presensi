@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard</span>
    </x-slot>

<div class="space-y-7">

    {{-- ── WELCOME STRIP ── --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-blue-300 text-sm font-semibold mb-1">Selamat datang kembali,</p>
                <h1 class="text-white text-2xl font-black leading-tight">{{ Auth::user()->name }}</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    {{ Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex flex-col items-start sm:items-end gap-1 shrink-0">
                <span class="text-white/40 text-xs uppercase tracking-widest font-bold">Panel Admin</span>
                <span class="text-white text-sm font-semibold">E-Presensi SMKN 1 Majene</span>
            </div>
        </div>
        {{-- Decorative --}}
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
        $stats_display = [
            ['label' => 'Total Siswa',       'value' => $stats['total_siswa'],   'sub' => 'terdaftar',  'accent' => '#1e3a6e', 'light' => '#eef2ff'],
            ['label' => 'Total Guru',         'value' => $stats['total_guru'],    'sub' => 'terdaftar',  'accent' => '#1e3a6e', 'light' => '#eef2ff'],
            ['label' => 'Guru Hadir',         'value' => $stats['guru_hadir'],    'sub' => 'hari ini',   'accent' => '#1e6e3a', 'light' => '#f0fdf4'],
            ['label' => 'Sesi Mengajar',      'value' => $stats['guru_mengajar'], 'sub' => 'hari ini',   'accent' => '#1e3a6e', 'light' => '#eff6ff'],
            ['label' => 'Total Mapel',        'value' => $stats['total_mapel'],   'sub' => 'tersedia',   'accent' => '#6d28d9', 'light' => '#f5f3ff'],
            ['label' => 'Mapel Aktif',        'value' => $stats['mapel_aktif'],   'sub' => 'aktif',      'accent' => '#92400e', 'light' => '#fffbeb'],
        ];
        @endphp

        @foreach($stats_display as $s)
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
            <div class="w-8 h-1 rounded-full" style="background: {{ $s['accent'] }}"></div>
            <p class="text-3xl font-black" style="color: {{ $s['accent'] }}">{{ $s['value'] }}</p>
            <div>
                <p class="text-[.8rem] font-semibold text-slate-700 leading-tight">{{ $s['label'] }}</p>
                <p class="text-[.68rem] text-slate-400 uppercase tracking-wide font-semibold">{{ $s['sub'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── QUICK ACCESS ── --}}
    <div>
        <p class="text-[.7rem] font-black uppercase tracking-widest text-slate-400 mb-3">Akses Cepat</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @php
            $menus = [
                [
                    'href'  => route('admin.users'),
                    'title' => 'Pengguna',
                    'desc'  => 'Kelola akun siswa, guru, dan pengawas',
                    'stat'  => ($stats['total_siswa'] + $stats['total_guru']) . ' akun',
                    'color' => '#1e3a6e',
                ],
                [
                    'href'  => route('admin.mata-pelajaran.index'),
                    'title' => 'Mata Pelajaran',
                    'desc'  => 'Kelola daftar & status mata pelajaran',
                    'stat'  => $stats['mapel_aktif'] . ' aktif',
                    'color' => '#1e3a6e',
                ],
                [
                    'href'  => route('admin.ebook.index'),
                    'title' => 'E-Book Literasi',
                    'desc'  => 'Upload & kelola koleksi e-book untuk siswa',
                    'stat'  => \App\Models\EBook::count() . ' buku',
                    'color' => '#1e3a6e',
                ],
                [
                    'href'  => route('admin.absensi-guru'),
                    'title' => 'Absensi Guru',
                    'desc'  => 'Pantau kehadiran datang & pulang guru',
                    'stat'  => $stats['guru_hadir'] . ' hadir hari ini',
                    'color' => '#1e6e3a',
                ],
                [
                    'href'  => route('admin.aktivitas-guru'),
                    'title' => 'Aktivitas Mengajar',
                    'desc'  => 'Monitor jurnal dan sesi mengajar guru',
                    'stat'  => $stats['guru_mengajar'] . ' sesi hari ini',
                    'color' => '#1e3a6e',
                ],
            ];
            @endphp

            @foreach($menus as $m)
            <a href="{{ $m['href'] }}"
               class="group bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40">
                <div class="flex items-start justify-between">
                    <h3 class="text-sm font-bold text-slate-800 group-hover:text-[#1e3a6e] transition-colors">
                        {{ $m['title'] }}
                    </h3>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-[#1e3a6e] transition-colors flex-shrink-0 mt-0.5"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed flex-1">{{ $m['desc'] }}</p>
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-[.7rem] font-bold uppercase tracking-wide" style="color: {{ $m['color'] }}80">
                        {{ $m['stat'] }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── 2 PANEL: Guru Hadir & Sesi Mengajar ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Guru Hadir --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Absensi Guru Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ Carbon::today()->translatedFormat('d F Y') }}</p>
                </div>
                <a href="{{ route('admin.absensi-guru') }}"
                   class="text-xs font-semibold text-[#1e3a6e] hover:underline">
                    Lihat semua →
                </a>
            </div>

            {{-- Progress bar --}}
            @php
                $pct = $stats['total_guru'] > 0
                    ? round(($stats['guru_hadir'] / $stats['total_guru']) * 100)
                    : 0;
            @endphp
            <div class="px-6 py-3 border-b border-slate-50 bg-slate-50/50">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="font-semibold text-slate-500">Tingkat Kehadiran</span>
                    <span class="font-bold text-[#1e3a6e]">{{ $pct }}%</span>
                </div>
                <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-[#1e3a6e] rounded-full transition-all duration-700"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>

            <div class="divide-y divide-slate-50">
                @forelse($guruHadir as $a)
                <div class="px-6 py-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-sm flex-shrink-0">
                        {{ strtoupper(substr($a->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $a->user->name }}</p>
                        <p class="text-xs text-slate-400">
                            {{ Carbon::parse($a->waktu_datang)->format('H:i') }} WITA
                            @if($a->waktu_pulang) · Pulang {{ Carbon::parse($a->waktu_pulang)->format('H:i') }} @endif
                        </p>
                    </div>
                    @php $cls = match($a->status) {
                        'hadir'=>'bg-blue-50 text-[#1e3a6e] border-blue-100',
                        'izin'=>'bg-amber-50 text-amber-700 border-amber-100',
                        'sakit'=>'bg-slate-100 text-slate-600 border-slate-200',
                        default=>'bg-red-50 text-red-600 border-red-100'
                    }; @endphp
                    <span class="text-[.68rem] font-bold px-2.5 py-1 rounded-full whitespace-nowrap {{ $cls }} border capitalize">
                        {{ $a->status }}
                        @if($a->status_pengajuan === 'pending') (Pending) @endif
                        @if($a->status_pengajuan === 'rejected') (Ditolak) @endif
                    </span>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-slate-400 text-sm">Belum ada guru yang absen hari ini.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Sesi Mengajar --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Sesi Mengajar Hari Ini</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ Carbon::today()->translatedFormat('d F Y') }}</p>
                </div>
                <a href="{{ route('admin.aktivitas-guru') }}"
                   class="text-xs font-semibold text-[#1e3a6e] hover:underline">
                    Lihat semua →
                </a>
            </div>

            <div class="divide-y divide-slate-50">
                @forelse($aktivitasHariIni as $a)
                <div class="px-6 py-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-100 text-[#1e3a6e] flex items-center justify-center font-black text-sm flex-shrink-0 border border-slate-200">
                        {{ $a->jam_ke }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $a->user->name }}</p>
                        <p class="text-xs text-slate-400 truncate">
                            {{ $a->mata_pelajaran }} &middot; {{ $a->kelas }}
                        </p>
                    </div>
                    <span class="text-[.68rem] font-bold text-slate-400 whitespace-nowrap">
                        {{ Carbon::parse($a->jam_mulai)->format('H:i') }}
                    </span>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-slate-400 text-sm">Belum ada aktivitas mengajar hari ini.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
</x-app-layout>
