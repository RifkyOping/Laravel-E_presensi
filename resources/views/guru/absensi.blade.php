@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Absensi Sekolah Guru</span>
    </x-slot>

<div class="space-y-6">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 font-semibold px-5 py-3.5 rounded-xl text-sm mb-6">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-sm mb-6">
        <div class="flex items-center gap-3 font-semibold mb-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Gagal Mengirim Pengajuan
        </div>
        <ul class="list-disc ml-8 text-red-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Tanggal --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-5 py-5 sm:px-8 sm:py-6 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10">
            <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">E-Presensi Guru — Hari Ini</p>
            <p class="text-white text-2xl font-black">{{ Carbon::now()->translatedFormat('l, d F Y') }}</p>
            <p class="text-blue-300/70 text-sm mt-1">
                Radius absensi: <strong class="text-white">{{ $setting->radius_meter }} m</strong>
                dari {{ $setting->nama_sekolah }}
            </p>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        {{-- GPS Status --}}
        <div class="relative z-10 mt-3 flex justify-center gap-3 flex-wrap">
            @if($absensiHariIni && $absensiHariIni->status_pengajuan === 'rejected' && !$absensiHariIni->waktu_datang && !$absensiHariIni->waktu_pulang)
                <span class="bg-red-500/30 border border-red-400/40 text-red-100 text-xs px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Pengajuan {{ ucfirst($absensiHariIni->status) }} Ditolak &mdash; Silakan Lakukan Absensi
                </span>
            @elseif($absensiHariIni && in_array($absensiHariIni->status, ['cuti', 'tugas', 'sakit', 'izin']) && $absensiHariIni->status_pengajuan !== 'rejected')
                <span class="bg-amber-500/20 border border-amber-500/30 text-amber-100 text-xs px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Status: {{ ucfirst($absensiHariIni->status) }}
                    @if($absensiHariIni->status_pengajuan === 'pending')
                        (Menunggu Konfirmasi)
                    @else
                        (Disetujui)
                    @endif
                </span>
            @elseif($absensiHariIni)
                @if($absensiHariIni->waktu_datang)
                <span class="bg-white/20 text-white text-xs px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Datang: {{ Carbon::parse($absensiHariIni->waktu_datang)->format('H:i') }} WITA
                </span>
                @endif
                @if($absensiHariIni->waktu_pulang)
                <span class="bg-white/20 text-white text-xs px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Pulang: {{ Carbon::parse($absensiHariIni->waktu_pulang)->format('H:i') }} WITA
                </span>
                @endif
            @endif

            <button type="button" onclick="requestGPS()" id="gps-status" class="bg-white/20 hover:bg-white/30 transition text-white text-xs px-4 py-2 rounded-lg font-semibold flex items-center gap-2 cursor-pointer shadow-sm">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="gps-spinner">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span id="gps-text">Mendeteksi lokasi GPS...</span>
            </button>
        </div>
    </div>

    {{-- Alert jika pengajuan cuti/tugas ditolak dan belum absen --}}
    @php
        $isRejectedTodayGuru = $absensiHariIni && $absensiHariIni->status_pengajuan === 'rejected' && !$absensiHariIni->waktu_datang && !$absensiHariIni->waktu_pulang;
    @endphp
    @if($isRejectedTodayGuru)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 text-amber-800 text-sm shadow-sm">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="flex-1">
            <p class="font-bold text-amber-900">Pengajuan Cuti/Tugas Anda Ditolak</p>
            @if($absensiHariIni->alasan_ditolak)
                <p class="text-xs text-amber-800 mt-1"><strong>Alasan Penolakan:</strong> {{ $absensiHariIni->alasan_ditolak }}</p>
            @endif
            <p class="text-xs text-amber-700 mt-1 font-medium">Anda masih dapat melakukan absensi sekolah biasa hari ini melalui tombol kehadiran di bawah.</p>
        </div>
    </div>
    @endif

    {{-- Status Hari Ini --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="font-bold text-slate-800 mb-4">Status Absensi Anda Hari Ini</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Datang --}}
            <div class="flex items-center gap-4 bg-slate-50 rounded-xl p-4 border
                {{ $absensiHariIni && $absensiHariIni->waktu_datang ? 'border-[#1e3a6e]/30 bg-blue-50/50' : 'border-slate-200' }}">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0
                    {{ $absensiHariIni && $absensiHariIni->waktu_datang ? 'bg-[#1e3a6e] text-white' : 'bg-slate-200 text-slate-500' }}">
                    IN
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Waktu Datang</p>
                    @if($absensiHariIni && $absensiHariIni->waktu_datang)
                    <p class="text-xl font-black text-[#1e3a6e]">{{ Carbon::parse($absensiHariIni->waktu_datang)->format('H:i') }} <span class="text-sm font-semibold">WITA</span></p>
                    @if($absensiHariIni->kategori && str_contains(strtolower($absensiHariIni->kategori), 'terlambat'))
                        <span class="text-[0.65rem] font-bold text-red-500 uppercase tracking-wider mt-0.5 block">Terlambat</span>
                    @endif
                    @else
                    <p class="text-base font-semibold text-slate-400">Belum absen</p>
                    @endif
                </div>
            </div>
            {{-- Pulang --}}
            <div class="flex items-center gap-4 bg-slate-50 rounded-xl p-4 border
                {{ $absensiHariIni && $absensiHariIni->waktu_pulang ? 'border-[#1e3a6e]/30 bg-blue-50/50' : 'border-slate-200' }}">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0
                    {{ $absensiHariIni && $absensiHariIni->waktu_pulang ? 'bg-[#1e3a6e] text-white' : 'bg-slate-200 text-slate-500' }}">
                    OUT
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Waktu Pulang</p>
                    @if($absensiHariIni && $absensiHariIni->waktu_pulang)
                    <p class="text-xl font-black text-[#1e3a6e]">{{ Carbon::parse($absensiHariIni->waktu_pulang)->format('H:i') }} <span class="text-sm font-semibold">WITA</span></p>
                    @if($absensiHariIni->kategori && str_contains(strtolower($absensiHariIni->kategori), 'pulang lebih awal'))
                        <span class="text-[0.65rem] font-bold text-red-500 uppercase tracking-wider mt-0.5 block">Pulang Lebih Awal</span>
                    @endif
                    @else
                    <p class="text-base font-semibold text-slate-400">Belum absen</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Absen --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Datang --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col gap-5 hover:border-[#1e3a6e]/40 hover:shadow-md transition-all duration-200">
            <div>
                <div class="w-8 h-1 rounded-full bg-[#1e3a6e] mb-4"></div>
                <h3 class="text-lg font-black text-slate-800">Absen Datang</h3>
                <p class="text-sm text-slate-500 mt-1">Catat kehadiran saat tiba di sekolah.</p>
            </div>
            @if($absensiHariIni && $absensiHariIni->waktu_datang)
            <div class="mt-auto flex items-center gap-3 bg-blue-50 border border-blue-100 text-[#1e3a6e] font-bold px-5 py-3.5 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Sudah tercatat pukul {{ Carbon::parse($absensiHariIni->waktu_datang)->format('H:i') }} WITA
            </div>
            @else
            @php
                $labelBatalkanGuru = $jenisMasaAktif === 'cuti' ? 'Batalkan Cuti & Hadir' : 'Batalkan Tugas & Hadir';
            @endphp
            <form method="POST" action="{{ route('guru.absensi.datang') }}" class="mt-auto" id="form-datang">
                @csrf
                <input type="hidden" name="jenis_absen" value="hadir">
                <input type="hidden" name="latitude"  id="lat-datang">
                <input type="hidden" name="longitude" id="lng-datang">
                <input type="hidden" name="accuracy"  id="acc-datang">
                <input type="hidden" name="timestamp" id="ts-datang">
                <button type="button" id="btn-datang" onclick="confirmDatang('{{ ($sedangMasaCutiTugas && !$isRejectedTodayGuru) ? ($jenisMasaAktif ?? 'none') : 'none' }}')"
                        class="w-full {{ ($sedangMasaCutiTugas && !$isRejectedTodayGuru) ? 'bg-orange-600 hover:bg-orange-700' : 'bg-[#1e3a6e] hover:bg-[#162d57]' }} text-white font-bold py-3.5 rounded-xl text-sm transition duration-200 shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 hidden" id="spin-datang" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    @if($sedangMasaCutiTugas && !$isRejectedTodayGuru)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $labelBatalkanGuru }}
                    @else
                        Hadir — Datang Sekolah
                    @endif
                </button>
            </form>
            @endif
        </div>

        {{-- Pulang --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col gap-5 hover:border-[#1e3a6e]/40 hover:shadow-md transition-all duration-200">
            <div>
                <div class="w-8 h-1 rounded-full {{ $absensiHariIni && $absensiHariIni->waktu_pulang ? 'bg-green-500' : 'bg-[#1e3a6e]' }} mb-4"></div>
                <h3 class="text-lg font-black text-slate-800">Absen Pulang</h3>
                <p class="text-sm text-slate-500 mt-1">Catat kehadiran saat jam sekolah usai.</p>
            </div>
            @if($absensiHariIni && $absensiHariIni->waktu_pulang)
            <div class="mt-auto flex items-center gap-3 bg-blue-50 border border-blue-100 text-[#1e3a6e] font-bold px-5 py-3.5 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Sudah tercatat pukul {{ Carbon::parse($absensiHariIni->waktu_pulang)->format('H:i') }} WITA
            </div>
            @else
            @php
                $isCutiTugasApproved = $absensiHariIni && in_array($absensiHariIni->status, ['cuti', 'tugas', 'sakit', 'izin']) && $absensiHariIni->status_pengajuan === 'approved';
            @endphp
            <form method="POST" action="{{ route('guru.absensi.pulang') }}" class="mt-auto" id="form-pulang">
                @csrf
                <input type="hidden" name="latitude"  id="lat-pulang">
                <input type="hidden" name="longitude" id="lng-pulang">
                <input type="hidden" name="accuracy"  id="acc-pulang">
                <input type="hidden" name="timestamp" id="ts-pulang">
                <button type="button" id="btn-pulang" onclick="submitAbsen('pulang')"
                        {{ $isCutiTugasApproved ? 'disabled' : '' }}
                        class="w-full border border-[#1e3a6e] text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-bold py-3.5 rounded-xl text-sm transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:border-slate-300 disabled:text-slate-400 disabled:hover:bg-transparent disabled:hover:text-slate-400 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 hidden" id="spin-pulang" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    @if($isCutiTugasApproved)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Sedang {{ ucfirst($absensiHariIni->status) }}
                    @else
                        Hadir — Pulang Sekolah
                    @endif
                </button>
            </form>
            @endif
        </div>

    </div>
    {{-- Opsi Cuti & Tugas --}}
    @php
        $disableCutiTugas = false;
        $statusCutiTugas  = '';
        if ($absensiHariIni && $absensiHariIni->status_pengajuan === 'pending') {
            $disableCutiTugas = true;
            $statusCutiTugas = 'Menunggu Konfirmasi Admin';
        } elseif ($absensiHariIni && in_array($absensiHariIni->status, ['cuti', 'tugas'])) {
            $disableCutiTugas = true;
            $statusCutiTugas = 'Sedang dalam masa ' . ucfirst($absensiHariIni->status);
        } elseif ($sedangMasaCutiTugas && !$absensiHariIni) {
            $disableCutiTugas = true;
            $statusCutiTugas  = 'Sedang dalam masa ' . ucfirst($jenisMasaAktif);
        }
    @endphp
    <div class="grid grid-cols-1 gap-4 mt-4 relative">
        @if($disableCutiTugas)
            <div class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[2px] rounded-xl flex items-center justify-center">
                <span class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-xl flex items-center gap-2">
                    @if($absensiHariIni && $absensiHariIni->status_pengajuan === 'pending')
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @endif
                    {{ $statusCutiTugas }}
                </span>
            </div>
        @endif
        <button type="button" x-data @click="$dispatch('open-modal-pengajuan')" class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between hover:border-slate-300 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <div class="text-left">
                    <h4 class="font-bold text-slate-800">Pengajuan Cuti / Tugas</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Lapor cuti atau tugas dinas luar (DL)</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    @push('modals')
    {{-- Modal Pengajuan --}}
    <div x-data="{ open: false }" @open-modal-pengajuan.window="open = true" @keydown.escape.window="open = false" class="relative z-[100]">
        <!-- Backdrop -->
        <div x-show="open" style="display: none;" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
             
        <!-- Modal Panel -->
        <div x-show="open" style="display: none;" class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-8"
                 class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden pointer-events-auto" @click.stop>
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Pengajuan Cuti / Tugas</h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            <form action="{{ route('guru.absensi.datang') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Pengajuan</label>
                        <select name="jenis_absen" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" required>
                            <option value="" disabled selected>Pilih jenis pengajuan...</option>
                            <option value="cuti">Cuti (Sakit, Melahirkan, dll)</option>
                            <option value="tugas">Tugas Luar / Dinas (DL)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Judul Pengajuan</label>
                        <input type="text" name="judul_pengajuan" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" placeholder="Contoh: Cuti Sakit / Rapat MGMP" required>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tgl Mulai</label>
                            <input type="date" name="tanggal_mulai" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tgl Selesai</label>
                            <input type="date" name="tanggal_selesai" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keterangan / Alasan</label>
                        <textarea name="keterangan" rows="2" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" placeholder="Tuliskan keterangan detail di sini..." required></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">File Lampiran (Surat)</label>
                        <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm">Kirim Pengajuan</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    @endpush

    {{-- Riwayat --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm">Riwayat Kehadiran</h3>
            <p class="text-xs text-slate-400 mt-0.5">30 hari terakhir catatan datang dan pulang Anda.</p>
        </div>

        {{-- Mobile: Card List --}}
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($riwayat as $absen)
            <div class="px-4 py-3.5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold text-slate-700 text-sm leading-tight">
                        {{ Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ Carbon::parse($absen->tanggal)->translatedFormat('l') }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        @if($absen->waktu_datang)<span class="font-semibold">Datang:</span> {{ Carbon::parse($absen->waktu_datang)->format('H:i') }}@endif
                        @if($absen->waktu_pulang) &nbsp;·&nbsp; <span class="font-semibold">Pulang:</span> {{ Carbon::parse($absen->waktu_pulang)->format('H:i') }}@endif
                    </p>
                </div>
                <div class="shrink-0 text-right">
                    @php $sc = match($absen->status ?? 'hadir') {
                        'hadir' => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                        'izin'  => 'bg-amber-50 text-amber-700 border-amber-100',
                        'sakit' => 'bg-slate-100 text-slate-600 border-slate-200',
                        default => 'bg-red-50 text-red-600 border-red-100',
                    }; @endphp
                    <span class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border capitalize {{ $sc }}">
                        {{ ucfirst($absen->status ?? 'hadir') }}
                    </span>
                    @if($absen->status_pengajuan === 'pending')
                        <p class="text-[0.6rem] text-slate-400 font-semibold mt-0.5">Pending</p>
                    @elseif($absen->status_pengajuan === 'rejected')
                        <p class="text-[0.6rem] text-red-500 font-semibold mt-0.5">Ditolak</p>
                    @endif
                    @if($absen->kategori)
                        @if($absen->kategori === 'tepat waktu')
                            <p class="text-[0.6rem] text-emerald-500 font-bold mt-0.5 capitalize">{{ $absen->kategori }}</p>
                        @else
                            <p class="text-[0.6rem] text-red-500 font-bold mt-0.5 capitalize">{{ $absen->kategori }}</p>
                        @endif
                    @endif
                </div>
            </div>
            @empty
            <div class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat kehadiran.</div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Hari</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Waktu Datang</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Waktu Pulang</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($riwayat as $absen)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="py-3.5 px-5 font-semibold text-slate-700 text-sm">{{ Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</td>
                        <td class="py-3.5 px-5 text-sm text-slate-500">{{ Carbon::parse($absen->tanggal)->translatedFormat('l') }}</td>
                        <td class="py-3.5 px-5 text-sm text-slate-600 text-center">{{ $absen->waktu_datang ? Carbon::parse($absen->waktu_datang)->format('H:i').' WITA' : '—' }}</td>
                        <td class="py-3.5 px-5 text-sm text-slate-600 text-center">{{ $absen->waktu_pulang ? Carbon::parse($absen->waktu_pulang)->format('H:i').' WITA' : '—' }}</td>
                        <td class="py-3.5 px-5 text-center">
                            @php $sc = match($absen->status ?? 'hadir') {
                                'hadir' => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                                'izin'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                'sakit' => 'bg-slate-100 text-slate-600 border-slate-200',
                                default => 'bg-red-50 text-red-600 border-red-100',
                            }; @endphp
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border capitalize {{ $sc }}">{{ ucfirst($absen->status ?? 'hadir') }}</span>
                                @if($absen->status_pengajuan === 'pending')
                                    <span class="text-[0.6rem] text-slate-400 font-semibold">Pending</span>
                                @elseif($absen->status_pengajuan === 'rejected')
                                    <span class="text-[0.6rem] text-red-500 font-semibold">Ditolak</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            @if($absen->kategori)
                                @if($absen->kategori === 'tepat waktu')
                                    <span class="text-[0.7rem] text-emerald-500 font-bold capitalize">{{ $absen->kategori }}</span>
                                @else
                                    <span class="text-[0.7rem] text-red-500 font-bold capitalize">{{ $absen->kategori }}</span>
                                @endif
                            @else
                                <span class="text-[0.7rem] text-slate-300 font-bold">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat kehadiran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
@if(session('popup_notification'))
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: '{{ session("popup_notification")["icon"] }}',
        title: '{{ session("popup_notification")["title"] }}',
        html: '{!! session("popup_notification")["text"] !!}',
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#1e3a6e',
        customClass: {
            popup: 'rounded-2xl shadow-2xl border border-slate-100',
            title: 'text-xl font-black text-slate-800',
            htmlContainer: 'text-sm text-slate-500 font-medium',
            confirmButton: 'font-bold rounded-xl px-8 py-2.5 shadow-sm hover:shadow-md transition-all'
        },
        buttonsStyling: true
    });
});
@endif

/* ── GPS State & Jitter Detection ── */
let gpsLat = null;
let gpsLng = null;
let gpsAcc = null;
let gpsTimestamp = null;
let gpsReady = false;
let watchId = null;
let gpsSamples = [];
const REQUIRED_SAMPLES = 3;

let gpsErrorTitle = 'GPS Belum Siap';
let gpsErrorMsg = 'Sistem masih memuat lokasi GPS Anda. Pastikan izin lokasi aktif dan tunggu sebentar...';

function updateGpsStatus(ok, msg) {
    const el   = document.getElementById('gps-status');
    const spin = document.getElementById('gps-spinner');
    const txt  = document.getElementById('gps-text');
    txt.textContent = msg;
    spin.classList.add('hidden');
    el.className = 'bg-white/20 hover:bg-white/30 transition text-white text-xs px-4 py-2 rounded-lg font-semibold flex items-center gap-2 cursor-pointer shadow-sm';
    el.classList.add(ok ? '!bg-green-500/80' : '!bg-red-500/80');

    if (!ok) {
        if (msg.includes('Fake GPS') || msg.includes('Palsu') || msg.includes('Jitter')) {
            gpsErrorTitle = 'Peringatan Keamanan!';
            gpsErrorMsg   = 'Sistem mendeteksi indikasi penggunaan Fake GPS / Lokasi Palsu (sinyal GPS statis tanpa getaran alami satelit). Harap matikan aplikasi Fake GPS untuk absensi.';
        } else if (msg.includes('ditolak')) {
            gpsErrorTitle = 'Izin Ditolak';
            gpsErrorMsg   = 'Anda belum mengizinkan akses lokasi. Jika menekan peringatan lokasi tidak memunculkan notifikasi izin, harap ubah izin situs secara manual di pengaturan browser Anda (Izinkan Lokasi).';
        } else {
            gpsErrorTitle = 'GPS Gagal';
            gpsErrorMsg   = msg;
        }
    }
}

/* ── Ambil GPS otomatis dengan Deteksi Jitter ── */
function requestGPS() {
    const el   = document.getElementById('gps-status');
    const spin = document.getElementById('gps-spinner');
    const txt  = document.getElementById('gps-text');
    
    // Reset state
    gpsReady = false;
    gpsSamples = [];
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }

    // Set UI to loading
    el.className = 'bg-white/20 hover:bg-white/30 transition text-white text-xs px-4 py-2 rounded-lg font-semibold flex items-center gap-2 cursor-pointer shadow-sm';
    txt.textContent = 'Memindai sinyal satelit GPS...';
    spin.classList.remove('hidden');

    if (!navigator.geolocation) {
        updateGpsStatus(false, '❌ Browser tidak mendukung GPS');
        return;
    }

    let sampleTimeout = null;

    function evaluateSamples(isTimeout = false) {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        if (sampleTimeout) clearTimeout(sampleTimeout);

        if (gpsSamples.length === 0) {
            updateGpsStatus(false, 'Gagal memperoleh koordinat GPS yang akurat.');
            return;
        }

        const latestPos = gpsSamples[gpsSamples.length - 1];
        const acc = latestPos.coords.accuracy;

        // 1. Basic Heuristics
        const isRoundAccuracy = Number.isInteger(acc) && (acc % 10 === 0 || acc === 65);
        const isMissingAltitude = (latestPos.coords.altitude === null || latestPos.coords.altitude === 0);
        const isTooPerfectAccuracy = acc < 5;

        if ((isRoundAccuracy && isMissingAltitude) || isTooPerfectAccuracy) {
            updateGpsStatus(false, 'Terdeteksi penggunaan Aplikasi Fake GPS / Lokasi Palsu!');
            return;
        }

        // 2. Deteksi Jitter (Getaran Alami Satelit GPS Fisik)
        if (gpsSamples.length >= 3) {
            const lats = gpsSamples.map(s => s.coords.latitude);
            const lngs = gpsSamples.map(s => s.coords.longitude);
            const accs = gpsSamples.map(s => s.coords.accuracy);

            const latDiff = Math.max(...lats) - Math.min(...lats);
            const lngDiff = Math.max(...lngs) - Math.min(...lngs);
            const accDiff = Math.max(...accs) - Math.min(...accs);

            const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
            const timeSpan = (gpsSamples[gpsSamples.length - 1].timestamp - gpsSamples[0].timestamp);

            // Pada perangkat HP asli, satelit fisik selalu menghasilkan getaran mikro desimal atau fluktuasi akurasi.
            // Aplikasi Mock/Fake GPS di HP menginjeksi angka statis 100% kaku tanpa jitter sama sekali.
            if (isMobile && timeSpan >= 1000 && latDiff === 0 && lngDiff === 0 && accDiff === 0) {
                updateGpsStatus(false, 'Terdeteksi Lokasi Palsu (Sinyal GPS Statis Tanpa Jitter Satelit)!');
                return;
            }
        }

        gpsLat   = latestPos.coords.latitude;
        gpsLng   = latestPos.coords.longitude;
        gpsAcc   = latestPos.coords.accuracy;
        gpsTimestamp = latestPos.timestamp;
        gpsReady = true;
        updateGpsStatus(true, 'Lokasi terverifikasi (akurasi ±' + Math.round(acc) + 'm)');
    }

    // Timeout pengaman (maksimal 15 detik untuk menyelesaikan sampling)
    sampleTimeout = setTimeout(function() {
        if (!gpsReady && gpsSamples.length > 0) {
            evaluateSamples(true);
        }
    }, 15000);

    watchId = navigator.geolocation.watchPosition(
        function(pos) {
            gpsSamples.push(pos);
            const count = gpsSamples.length;

            if (count < REQUIRED_SAMPLES) {
                txt.textContent = 'Menguji keaslian sinyal GPS... (' + count + '/' + REQUIRED_SAMPLES + ')';
            } else {
                evaluateSamples();
            }
        },
        function(err) {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            if (sampleTimeout) clearTimeout(sampleTimeout);

            const msg = err.code === 1
                ? 'Izin lokasi ditolak. Tekan untuk mengizinkan.'
                : 'GPS tidak tersedia: ' + err.message;
            updateGpsStatus(false, msg);
        },
        { enableHighAccuracy: true, timeout: 30000, maximumAge: 0 }
    );
}

window.addEventListener('load', requestGPS);

/* ── Submit dengan Konfirmasi (Sakit/Izin) ── */
function confirmDatang(jenis) {
    if (jenis === 'izin' || jenis === 'sakit') {
        const label = jenis === 'izin' ? 'Izin' : 'Sakit';
        Swal.fire({
            title: 'Batalkan ' + label + '?',
            text: 'Anda tercatat sedang dalam masa ' + label + '. Apakah Anda yakin ingin membatalkannya dan melakukan Absen Hadir hari ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Tetap Hadir',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-slate-100',
                title: 'text-lg font-black text-slate-800',
                confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm',
                cancelButton: 'font-bold rounded-xl px-6 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border-none shadow-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                submitAbsen('datang');
            }
        });
    } else {
        submitAbsen('datang');
    }
}

/* ── Submit dengan GPS ── */
function submitAbsen(type) {
    if (!gpsReady) {
        Swal.fire({
            icon: gpsErrorTitle.includes('Peringatan') ? 'error' : 'warning',
            title: gpsErrorTitle,
            text: gpsErrorMsg,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#1e3a6e'
        });
        return;
    }

    // Isi hidden input
    document.getElementById('lat-' + type).value = gpsLat;
    document.getElementById('lng-' + type).value = gpsLng;
    document.getElementById('acc-' + type).value = gpsAcc;
    document.getElementById('ts-' + type).value = gpsTimestamp;

    // Tampilkan loading
    const btn  = document.getElementById('btn-' + type);
    const spin = document.getElementById('spin-' + type);
    btn.disabled = true;
    spin.classList.remove('hidden');
    spin.classList.add('animate-spin');

    // Submit form
    document.getElementById('form-' + type).submit();
}
</script>
</x-app-layout>
