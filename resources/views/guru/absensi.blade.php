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
            @if($absensiHariIni && in_array($absensiHariIni->status, ['sakit', 'izin']))
                <span class="bg-amber-500/20 border border-amber-500/30 text-amber-100 text-xs px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5">
                    @if($absensiHariIni->status === 'sakit')
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Status: Sakit
                    @else
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Status: Izin
                    @endif
                    
                    @if($absensiHariIni->status_pengajuan === 'pending')
                        (Menunggu Konfirmasi)
                    @elseif($absensiHariIni->status_pengajuan === 'ditolak')
                        (Ditolak)
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
                $labelBatalkanGuru = $jenisMasaAktif === 'izin' ? 'Batalkan Izin & Hadir' : 'Batalkan Sakit & Hadir';
            @endphp
            <form method="POST" action="{{ route('guru.absensi.datang') }}" class="mt-auto" id="form-datang">
                @csrf
                <input type="hidden" name="jenis_absen" value="hadir">
                <input type="hidden" name="latitude"  id="lat-datang">
                <input type="hidden" name="longitude" id="lng-datang">
                <button type="button" id="btn-datang" onclick="confirmDatang('{{ $jenisMasaAktif ?? 'none' }}')"
                        class="w-full {{ $sedangMasaSakitIzin ? 'bg-orange-600 hover:bg-orange-700' : 'bg-[#1e3a6e] hover:bg-[#162d57]' }} text-white font-bold py-3.5 rounded-xl text-sm transition duration-200 shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 hidden" id="spin-datang" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    @if($sedangMasaSakitIzin)
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
                <div class="w-8 h-1 rounded-full bg-slate-300 mb-4"></div>
                <h3 class="text-lg font-black text-slate-800">Absen Pulang</h3>
                <p class="text-sm text-slate-500 mt-1">Catat kehadiran saat jam sekolah usai.</p>
            </div>
            <form method="POST" action="{{ route('guru.absensi.pulang') }}" class="mt-auto" id="form-pulang">
                @csrf
                <input type="hidden" name="latitude"  id="lat-pulang">
                <input type="hidden" name="longitude" id="lng-pulang">
                <button type="button" id="btn-pulang" onclick="submitAbsen('pulang')"
                        {{ ($absensiHariIni && ($absensiHariIni->waktu_pulang || in_array($absensiHariIni->status, ['sakit', 'izin']))) || !$absensiHariIni || !$absensiHariIni->waktu_datang ? 'disabled' : '' }}
                        class="w-full border border-[#1e3a6e] text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-bold py-3.5 rounded-xl text-sm transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:border-slate-300 disabled:text-slate-400 disabled:hover:bg-transparent disabled:hover:text-slate-400 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 hidden" id="spin-pulang" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    @if($absensiHariIni && in_array($absensiHariIni->status, ['sakit', 'izin']))
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Sedang {{ ucfirst($absensiHariIni->status) }}
                    @elseif($absensiHariIni && $absensiHariIni->waktu_pulang)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Sudah Absen Pulang
                    @elseif(!$absensiHariIni || !$absensiHariIni->waktu_datang)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Absen Datang Dulu
                    @else
                        Hadir — Pulang Sekolah
                    @endif
                </button>
            </form>
        </div>

    </div>
    {{-- Opsi Sakit & Izin --}}
    @php
        $disableSakitIzin = false;
        $statusSakitIzin  = '';
        if ($absensiHariIni && $absensiHariIni->status !== 'alpha') {
            $disableSakitIzin = true;
            if ($absensiHariIni->status === 'hadir') {
                $statusSakitIzin = 'Anda sudah absen hadir hari ini.';
            } elseif ($absensiHariIni->status_pengajuan === 'pending') {
                $statusSakitIzin = 'Menunggu Konfirmasi Admin';
            } elseif (in_array($absensiHariIni->status, ['sakit', 'izin'])) {
                $statusSakitIzin = 'Sedang dalam masa ' . ucfirst($absensiHariIni->status);
            }
        } elseif ($sedangMasaSakitIzin && !$absensiHariIni) {
            $disableSakitIzin = true;
            $statusSakitIzin  = 'Sedang dalam masa Izin (multi-hari)';
        }
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 relative">
        @if($disableSakitIzin)
            <div class="absolute inset-0 z-10 bg-white/60 backdrop-blur-[2px] rounded-xl flex items-center justify-center">
                <span class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-xl flex items-center gap-2">
                    @if($absensiHariIni && $absensiHariIni->status_pengajuan === 'pending')
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @endif
                    {{ $statusSakitIzin }}
                </span>
            </div>
        @endif
        <button type="button" x-data @click="$dispatch('open-modal-sakit')" class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between hover:border-slate-300 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="text-left">
                    <h4 class="font-bold text-slate-800">Absen Sakit</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Lapor tidak enak badan hari ini</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
        <button type="button" x-data @click="$dispatch('open-modal-izin')" class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between hover:border-slate-300 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div class="text-left">
                    <h4 class="font-bold text-slate-800">Pengajuan Izin</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Izin acara keluarga, dll s/d tanggal tertentu</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    @push('modals')
    {{-- Modal Sakit --}}
    <div x-data="{ open: false }" @open-modal-sakit.window="open = true" @keydown.escape.window="open = false" class="relative z-[100]">
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
                    <h3 class="font-bold text-slate-800">Absen Sakit Hari Ini</h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            <form action="{{ route('guru.absensi.datang') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <input type="hidden" name="jenis_absen" value="sakit">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" placeholder="Sakit apa?" required></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">File Surat Keterangan Dokter</label>
                        <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" required>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm">Kirim</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    {{-- Modal Izin --}}
    <div x-data="{ open: false }" @open-modal-izin.window="open = true" @keydown.escape.window="open = false" class="relative z-[100]">
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
                    <h3 class="font-bold text-slate-800">Pengajuan Izin</h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            <form action="{{ route('guru.absensi.datang') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <input type="hidden" name="jenis_absen" value="izin">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Selesai Izin</label>
                        <input type="date" name="tanggal_selesai" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" required min="{{ date('Y-m-d') }}">
                        <p class="text-[0.65rem] text-slate-400 mt-1">Sistem akan mencatat Anda Izin sejak hari ini hingga tanggal tersebut.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alasan/Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" placeholder="Izin ada keperluan apa?" required></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">File Surat Izin</label>
                        <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" required>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm">Kirim</button>
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
                        {{ Carbon::parse($absen->tanggal)->format('d M Y') }}
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($riwayat as $absen)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="py-3.5 px-5 font-semibold text-slate-700 text-sm">{{ Carbon::parse($absen->tanggal)->format('d M Y') }}</td>
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
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat kehadiran.</td></tr>
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
        text: '{{ session("popup_notification")["text"] }}',
        confirmButtonColor: '#1e3a6e'
    });
});
@endif

/* ── GPS State ── */
let gpsLat = null;
let gpsLng = null;
let gpsReady = false;

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
        if (msg.includes('Fake GPS')) {
            gpsErrorTitle = 'Peringatan Keamanan!';
            gpsErrorMsg   = 'Sistem mendeteksi indikasi penggunaan Fake GPS atau Lokasi Palsu di perangkat Anda. Harap matikan aplikasi tersebut untuk dapat melakukan absensi.';
        } else if (msg.includes('ditolak')) {
            gpsErrorTitle = 'Izin Ditolak';
            gpsErrorMsg   = 'Anda belum mengizinkan akses lokasi. Jika menekan peringatan lokasi tidak memunculkan notifikasi izin, harap ubah izin situs secara manual di pengaturan browser Anda (Izinkan Lokasi).';
        } else {
            gpsErrorTitle = 'GPS Gagal';
            gpsErrorMsg   = msg;
        }
    }
}

function requestGPS() {
    const el   = document.getElementById('gps-status');
    const spin = document.getElementById('gps-spinner');
    const txt  = document.getElementById('gps-text');
    
    // Set UI to loading
    el.className = 'bg-white/20 hover:bg-white/30 transition text-white text-xs px-4 py-2 rounded-lg font-semibold flex items-center gap-2 cursor-pointer shadow-sm';
    txt.textContent = 'Mendeteksi lokasi GPS...';
    spin.classList.remove('hidden');

    if (!navigator.geolocation) {
        updateGpsStatus(false, '❌ Browser tidak mendukung GPS');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            const acc = pos.coords.accuracy;
            
            // --- ANTI FAKE GPS HEURISTIC ---
            const isRoundAccuracy = Number.isInteger(acc) && (acc % 10 === 0 || acc === 65);
            const isMissingAltitude = (pos.coords.altitude === null || pos.coords.altitude === 0);
            
            if (isRoundAccuracy && isMissingAltitude) {
                updateGpsStatus(false, 'Terdeteksi penggunaan Aplikasi Fake GPS / Lokasi Palsu!');
                return;
            }

            gpsLat   = pos.coords.latitude;
            gpsLng   = pos.coords.longitude;
            gpsReady = true;
            updateGpsStatus(true, 'Lokasi asli terdeteksi (akurasi ±' + Math.round(acc) + 'm)');
        },
        function(err) {
            const msg = err.code === 1
                ? 'Izin lokasi ditolak. Tekan untuk mengizinkan.'
                : 'GPS tidak tersedia: ' + err.message;
            updateGpsStatus(false, msg);
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
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
