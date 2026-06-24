@php use Carbon\Carbon; @endphp
<x-pengawas-layout pageTitle="Dashboard Pengawas">

    <div class="space-y-6">

        {{-- Header Banner --}}
        <div
            class="animate-up bg-gradient-to-r from-[#1e3a6e] to-[#2d5099] rounded-2xl p-7 text-white relative overflow-hidden shadow-lg">
            <div class="relative z-10">
                <p class="text-blue-200 text-sm font-semibold">Selamat datang,</p>
                <h2 class="text-2xl font-black mt-0.5">{{ Auth::user()->name }}</h2>
                <p class="text-blue-200 text-sm mt-1">
                    {{ Carbon::now()->translatedFormat('l, d F Y') }} &nbsp;·&nbsp; Panel Pengawas
                </p>
            </div>
            <div class="absolute -right-8 -top-8 w-48 h-48 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -bottom-10 w-32 h-32 rounded-full bg-white/5"></div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['label' => 'Total Guru', 'value' => $stats['total_guru'], 'sub' => 'terdaftar', 'color' => 'text-[#1e3a6e]'],
                    ['label' => 'Guru Hadir', 'value' => $stats['guru_hadir'], 'sub' => 'hari ini', 'color' => 'text-[#1e3a6e]'],
                    ['label' => 'Total Siswa', 'value' => $stats['total_siswa'], 'sub' => 'terdaftar', 'color' => 'text-violet-600'],
                    ['label' => 'Sesi Mengajar', 'value' => $stats['sesi_mengajar'], 'sub' => 'hari ini', 'color' => 'text-amber-600'],
                ];
            @endphp
            @foreach($cards as $i => $c)
                <div class="pw-stat animate-up delay-{{ $i + 1 }}">
                    <div class="flex items-start justify-end mb-3">
                        <span
                            class="text-[.68rem] font-semibold text-slate-400 uppercase tracking-wide">{{ $c['sub'] }}</span>
                    </div>
                    <p class="text-3xl font-black {{ $c['color'] }}">{{ $c['value'] }}</p>
                    <p class="text-sm text-slate-500 font-medium mt-0.5">{{ $c['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Status Kehadiran Guru Hari Ini --}}
        <div class="grid grid-cols-3 gap-4">
            @php
                $statusCards = [
                    ['label' => 'Izin', 'value' => $stats['guru_izin'], 'cls' => 'b-amber'],
                    ['label' => 'Sakit', 'value' => $stats['guru_sakit'], 'cls' => 'b-blue'],
                    ['label' => 'Alpha', 'value' => $stats['guru_alpha'], 'cls' => 'b-red'],
                ];
            @endphp
            @foreach($statusCards as $sc)
                <div class="pw-card p-4 flex items-center gap-4">
                    <div>
                        <p class="text-2xl font-black text-slate-800">{{ $sc['value'] }}</p>
                        <span class="pw-badge {{ $sc['cls'] }} text-xs">{{ $sc['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 2 kolom: Hadir & Belum Absen --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Guru Hadir Hari Ini --}}
            <div class="pw-card overflow-hidden animate-up delay-2">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Guru Hadir Hari Ini</h3>
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
                            <span class="pw-badge b-blue">Hadir</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada guru yang absen.</p>
                    @endforelse
                </div>
            </div>

            {{-- Guru Belum Absen --}}
            <div class="pw-card overflow-hidden animate-up delay-3">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Guru Belum Absen</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Sampai saat ini</p>
                    </div>
                    <span class="pw-badge b-red">{{ $belumAbsen->count() }} guru</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($belumAbsen as $g)
                        <div class="px-5 py-3.5 flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($g->name, 0, 1)) }}
                            </div>
                            <p class="font-semibold text-slate-700 text-sm truncate flex-1">{{ $g->name }}</p>
                            <span class="pw-badge b-slate">Belum</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-[#1e3a6e] font-semibold text-sm">Semua guru sudah absen!</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Aktivitas Mengajar Hari Ini --}}
        <div class="pw-card overflow-hidden animate-up delay-4">
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
            <div class="overflow-x-auto">
                <table class="w-full pw-tbl">
                    <thead>
                        <tr>
                            <th>Guru</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th class="text-center">Jam ke-</th>
                            <th>Waktu</th>
                            <th>Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aktivitasHariIni as $a)
                            <tr>
                                <td class="font-semibold text-slate-800">{{ $a->user->name }}</td>
                                <td>{{ $a->mata_pelajaran }}</td>
                                <td>{{ $a->kelas }}</td>
                                <td class="text-center">
                                    <span
                                        class="inline-flex w-7 h-7 rounded-full bg-[#1e3a6e] text-white items-center justify-center font-bold text-xs">
                                        {{ $a->jam_ke }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap">{{ Carbon::parse($a->jam_mulai)->format('H:i') }}
                                    @if($a->jam_selesai)–{{ Carbon::parse($a->jam_selesai)->format('H:i') }}@endif
                                </td>
                                <td>
                                    @php $mc = match ($a->metode) {
                                        'daring' => 'b-purple', 'diskusi' => 'b-blue', 'praktik' => 'b-blue', 'ceramah' => 'b-blue', default => 'b-slate'
                                    }; @endphp
                                    <span class="pw-badge {{ $mc }} capitalize">{{ $a->metode }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-400">Belum ada aktivitas mengajar hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-pengawas-layout>
