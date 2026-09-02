@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Absensi Sekolah</span>
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

    {{-- Banner tanggal + GPS status --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-5 py-5 sm:px-8 sm:py-6 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 text-left">
            <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">E-Presensi Murid — Hari Ini</p>
            <p class="text-white text-2xl font-black">{{ Carbon::now()->translatedFormat('l, d F Y') }}</p>
            <p class="text-blue-300/70 text-sm mt-1">
                Radius absensi: <strong class="text-white">{{ $setting->radius_meter }} m</strong>
                dari {{ $setting->nama_sekolah }}
            </p>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>

        {{-- GPS Status --}}
        <div class="relative z-10 mt-4 flex justify-center">
            <button type="button" onclick="requestGPS()" id="gps-status" class="bg-white/20 hover:bg-white/30 transition text-white text-xs px-4 py-2 rounded-lg font-semibold flex items-center gap-2 cursor-pointer shadow-sm">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="gps-spinner">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span id="gps-text">Mendeteksi lokasi GPS...</span>
            </button>
        </div>

        {{-- Status absen hari ini --}}
        @if($absensiHariIni)
        <div class="relative z-10 mt-3 flex justify-center gap-3 flex-wrap">
            @if($absensiHariIni->status_pengajuan === 'rejected' && !$absensiHariIni->waktu_datang)
                <span class="bg-red-500/30 border border-red-400/40 text-red-100 text-xs px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Pengajuan {{ ucfirst($absensiHariIni->status) }} Ditolak &mdash; Silakan Absen Datang
                </span>
            @elseif(in_array($absensiHariIni->status, ['sakit', 'izin']) && $absensiHariIni->status_pengajuan !== 'rejected')
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
                    @else
                        (Disetujui)
                    @endif
                </span>
            @else
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
        </div>
        @endif
    </div>

    {{-- Alert jika pengajuan ditolak dan belum absen --}}
    @php
        $isRejectedToday = $absensiHariIni && $absensiHariIni->status_pengajuan === 'rejected' && !$absensiHariIni->waktu_datang;
    @endphp
    @if($isRejectedToday)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 text-amber-800 text-sm shadow-sm">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="flex-1">
            <p class="font-bold text-amber-900">Pengajuan Izin/Sakit Anda Ditolak</p>
            @if($absensiHariIni->alasan_ditolak)
                <p class="text-xs text-amber-800 mt-1"><strong>Alasan Penolakan:</strong> {{ $absensiHariIni->alasan_ditolak }}</p>
            @endif
            <p class="text-xs text-amber-700 mt-1 font-medium">Anda masih dapat melakukan absen datang sekolah biasa dengan menekan tombol <strong>Hadir &mdash; Datang Sekolah</strong> di bawah ini.</p>
        </div>
    </div>
    @endif

    {{-- Tombol Absen --}}
    @php
        $sudahDatang = $absensiHariIni && $absensiHariIni->waktu_datang;
        $sudahPulang = $absensiHariIni && $absensiHariIni->waktu_pulang;
        $bisaPulang  = $sudahDatang && !$sudahPulang;

        // Status aktif sakit/izin hari ini (hanya jika approved)
        $isPending = $absensiHariIni && $absensiHariIni->status_pengajuan === 'pending';
        $isApprovedSakitIzin = $absensiHariIni && in_array($absensiHariIni->status, ['sakit', 'izin']) && $absensiHariIni->status_pengajuan === 'approved';

        // Apakah sedang sakit/izin disetujui (digunakan untuk disable card)
        $isSakitIzin = $isApprovedSakitIzin;

        // Teks overlay pada card sakit/izin
        $disableSakitIzin = false;
        $statusSakitIzin  = '';
        if ($isPending) {
            $disableSakitIzin = true;
            $statusSakitIzin = 'Menunggu Konfirmasi';
        } elseif ($isApprovedSakitIzin) {
            $disableSakitIzin = true;
            $statusSakitIzin = 'Sedang dalam masa ' . ucfirst($absensiHariIni->status);
        } elseif (isset($sedangMasaSakitIzin) && $sedangMasaSakitIzin && !$absensiHariIni) {
            // Izin multi-hari dari hari sebelumnya
            $disableSakitIzin = true;
            $statusSakitIzin  = 'Sedang dalam masa Izin (multi-hari)';
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Datang --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col gap-5
                    {{ $sudahDatang ? 'opacity-70' : 'hover:border-[#1e3a6e]/40 hover:shadow-md' }}
                    transition-all duration-200">
            <div>
                <div class="w-8 h-1 rounded-full {{ $sudahDatang ? 'bg-green-400' : 'bg-[#1e3a6e]' }} mb-4"></div>
                <h3 class="text-lg font-black text-slate-800">Absen Datang</h3>
                @if($sudahDatang)
                    <p class="text-sm text-green-600 font-semibold mt-1 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Tercatat pukul {{ Carbon::parse($absensiHariIni->waktu_datang)->format('H:i') }} WITA
                    </p>
                @else
                    <p class="text-sm text-slate-500 mt-1">GPS Anda harus berada dalam radius {{ $setting->radius_meter }}m dari sekolah.</p>
                @endif
            </div>
            <form method="POST" action="{{ route('absensi.datang') }}" class="mt-auto" id="form-datang">
                @csrf
                <input type="hidden" name="jenis_absen" value="hadir">
                <input type="hidden" name="latitude"  id="lat-datang">
                <input type="hidden" name="longitude" id="lng-datang">
                <input type="hidden" name="accuracy"  id="acc-datang">
                <input type="hidden" name="timestamp" id="ts-datang">
                @php
                    $labelBatalkan = $jenisMasaAktif === 'izin' ? 'Batalkan Izin & Hadir' : 'Batalkan Sakit & Hadir';
                @endphp
                <button type="button" id="btn-datang"
                        onclick="confirmDatang('{{ ($sedangMasaSakitIzin && !$isRejectedToday) ? ($jenisMasaAktif ?? 'none') : 'none' }}')"
                        {{ $sudahDatang ? 'disabled' : '' }}
                        class="w-full {{ ($sedangMasaSakitIzin && !$isRejectedToday && !$sudahDatang) ? 'bg-orange-600 hover:bg-orange-700' : 'bg-[#1e3a6e] hover:bg-[#162d57]' }} text-white font-bold py-3.5 rounded-xl text-sm
                                transition duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 hidden" id="spin-datang" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    @if($sedangMasaSakitIzin && !$isRejectedToday && !$sudahDatang)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $labelBatalkan }}
                    @elseif($sudahDatang)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Sudah Absen Datang
                    @else
                        Hadir — Datang Sekolah
                    @endif
                </button>
            </form>
        </div>

        {{-- Pulang --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col gap-5
                    {{ $sudahPulang ? 'opacity-70' : ($bisaPulang ? 'hover:border-[#1e3a6e]/40 hover:shadow-md' : 'opacity-60') }}
                    transition-all duration-200">
            <div>
                <div class="w-8 h-1 rounded-full {{ $sudahPulang ? 'bg-green-400' : 'bg-slate-300' }} mb-4"></div>
                <h3 class="text-lg font-black text-slate-800">Absen Pulang</h3>
                @if($sudahPulang)
                    <p class="text-sm text-green-600 font-semibold mt-1 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Tercatat pukul {{ Carbon::parse($absensiHariIni->waktu_pulang)->format('H:i') }} WITA
                    </p>
                @else
                    <p class="text-sm text-slate-500 mt-1">GPS Anda harus berada dalam radius {{ $setting->radius_meter }}m dari sekolah.</p>
                @endif
            </div>
            <form method="POST" action="{{ route('absensi.pulang') }}" class="mt-auto" id="form-pulang">
                @csrf
                <input type="hidden" name="latitude"  id="lat-pulang">
                <input type="hidden" name="longitude" id="lng-pulang">
                <input type="hidden" name="accuracy"  id="acc-pulang">
                <input type="hidden" name="timestamp" id="ts-pulang">
                <button type="button" id="btn-pulang"
                        onclick="submitAbsen('pulang')"
                        {{ ($sudahPulang || $isSakitIzin) ? 'disabled' : '' }}
                        class="w-full border border-[#1e3a6e] text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white
                               font-bold py-3.5 rounded-xl text-sm transition duration-200
                               disabled:opacity-50 disabled:cursor-not-allowed disabled:border-slate-300 disabled:text-slate-400
                               disabled:hover:bg-transparent disabled:hover:text-slate-400 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 hidden" id="spin-pulang" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    @if($absensiHariIni && in_array($absensiHariIni->status, ['sakit', 'izin']) && $absensiHariIni->status_pengajuan === 'approved')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Sedang {{ ucfirst($absensiHariIni->status) }}
                    @elseif($sudahPulang)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Sudah Absen Pulang
                    @else
                        Hadir — Pulang Sekolah
                    @endif
                </button>
            </form>
        </div>

    </div>

    {{-- Opsi Pengajuan Sakit/Izin --}}
    <div class="mt-4 relative">
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
        <button type="button" x-data @click="$dispatch('open-modal-pengajuan')" class="w-full bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between hover:border-slate-300 hover:shadow-md transition">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div class="text-left">
                    <h4 class="font-bold text-slate-800">Pengajuan Sakit/Izin</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Ajukan surat izin atau keterangan sakit</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    {{-- QR Code Section --}}
    @if(Auth::user()->nomor_induk)
    <div x-data="{ showFullScreenQR: false }" class="mt-4 relative bg-white rounded-xl border border-slate-200 p-5 lg:p-6 flex flex-col md:flex-row gap-6 md:gap-8 items-center md:items-start shadow-sm transition hover:shadow-md hover:border-slate-300">
        {{-- QR Code --}}
        <div class="flex flex-col items-center flex-shrink-0">
            <div @click="showFullScreenQR = true" class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200 mb-4 cursor-pointer hover:shadow-md transition-shadow group relative" id="qr-code-container" title="Klik untuk memperbesar">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->style('round')->margin(1)->generate(Auth::user()->nomor_induk) !!}
                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl flex items-center justify-center pointer-events-none">
                    <div class="bg-white/90 text-slate-800 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm backdrop-blur-sm flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        Perbesar
                    </div>
                </div>
            </div>
            <div class="flex gap-2 w-full">
                <button onclick="downloadPDFQR()" class="w-full justify-center bg-[#1e3a6e] hover:bg-[#162d57] text-white text-[11px] font-bold py-2.5 px-3 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download
                </button>
            </div>
        </div>
        
        {{-- Instructions --}}
        <div class="flex-1 text-center md:text-left w-full">
            <h3 class="text-base font-bold text-slate-800 mb-1">QR Code Absensi Offline</h3>
            <p class="text-xs text-slate-500 mb-3 leading-relaxed">Gunakan QR Code ini untuk melakukan absensi saat Anda tidak memiliki koneksi internet atau terkendala GPS.</p>
            
            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-[0.7rem] leading-relaxed border border-blue-100 shadow-sm text-left">
                <p class="font-bold mb-1.5 flex items-center gap-1.5 text-blue-900">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Cara Penggunaan:
                </p>
                <ol class="list-decimal pl-5 space-y-1 text-blue-800/90 font-medium">
                    <li>Simpan gambar QR Code ini ke galeri HP Anda, atau cetak di kertas.</li>
                    <li>Saat di sekolah, tunjukkan QR Code ini ke guru yang bertugas piket atau mengajar.</li>
                    <li>Guru akan melakukan scan menggunakan sistem ini untuk mencatat kehadiran Anda.</li>
                </ol>
            </div>
        </div>
        
        <!-- Fullscreen QR Modal -->
        <template x-teleport="body">
            <div x-show="showFullScreenQR" 
                 style="display: none; z-index: 99999;" 
                 class="fixed inset-0 bg-white flex flex-col items-center justify-center p-6"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                 
                <button @click="showFullScreenQR = false" class="absolute top-6 right-6 text-slate-800 bg-slate-100 hover:bg-slate-200 p-3 rounded-full transition-colors shadow-sm cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <div class="flex flex-col items-center justify-center w-full max-w-lg mx-auto" @click.outside="showFullScreenQR = false">
                    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-2xl border border-slate-100 w-full flex justify-center items-center">
                        <div class="w-full h-full flex justify-center items-center [&>svg]:w-full [&>svg]:h-auto [&>svg]:max-w-[400px]">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(400)->style('round')->margin(1)->generate(Auth::user()->nomor_induk) !!}
                        </div>
                    </div>
                    <h2 class="mt-8 text-2xl sm:text-4xl font-black text-slate-800 text-center">{{ Auth::user()->name }}</h2>
                    <p class="mt-2 text-lg sm:text-2xl font-medium text-slate-500 text-center">NISN: {{ Auth::user()->nomor_induk }}</p>
                </div>
            </div>
        </template>
    </div>
    @endif

    @push('modals')
    {{-- Modal Pengajuan Sakit/Izin --}}
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
                    <h3 class="font-bold text-slate-800">Pengajuan Sakit/Izin</h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            <form action="{{ route('absensi.datang') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Wali Kelas</label>
                        @php
                            $lastGuruId = \App\Models\AbsensiSiswa::where('user_id', Auth::id())
                                ->whereNotNull('guru_id')
                                ->latest('created_at')
                                ->value('guru_id');
                        @endphp
                        <select name="guru_id" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm bg-white" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($semuaGuru as $guru)
                                <option value="{{ $guru->id }}" {{ (old('guru_id', $lastGuruId) == $guru->id) ? 'selected' : '' }}>{{ $guru->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Judul Pengajuan</label>
                        <select name="jenis_absen" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm bg-white" required>
                            <option value="">-- Pilih Sakit/Izin --</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 text-sm" placeholder="Keterangan pengajuan..." required></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">File Surat Keterangan / Izin</label>
                        <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200" required>
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

    {{-- Riwayat dari database --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm">Riwayat Kehadiran</h3>
            <p class="text-xs text-slate-400 mt-0.5">30 hari terakhir.</p>
        </div>

        {{-- Mobile: Card List --}}
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($riwayat as $r)
            <div class="px-4 py-3.5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold text-slate-700 text-sm leading-tight">
                        {{ Carbon::parse($r->tanggal)->translatedFormat('d F Y') }}
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ Carbon::parse($r->tanggal)->translatedFormat('l') }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        @if($r->waktu_datang) <span class="font-semibold">Datang:</span> {{ Carbon::parse($r->waktu_datang)->format('H:i') }} @endif
                        @if($r->waktu_pulang) &nbsp;·&nbsp; <span class="font-semibold">Pulang:</span> {{ Carbon::parse($r->waktu_pulang)->format('H:i') }} @endif
                    </p>
                </div>
                <div class="shrink-0 text-right">
                    @php
                        $cls = match($r->status ?? 'hadir') {
                            'hadir' => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                            'izin'  => 'bg-amber-50 text-amber-700 border-amber-100',
                            'sakit' => 'bg-slate-50 text-slate-600 border-slate-200',
                            default => 'bg-red-50 text-red-600 border-red-100',
                        };
                    @endphp
                    <span class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border {{ $cls }} capitalize">
                        {{ $r->status ?? 'hadir' }}
                    </span>
                    @if($r->kategori && $r->status === 'hadir')
                        @php
                            $katCls = match($r->kategori) {
                                'tepat_waktu' => 'text-emerald-600',
                                'terlambat' => 'text-orange-600',
                                'bolos' => 'text-red-600',
                                default => 'text-slate-500'
                            };
                        @endphp
                        <p class="text-[0.65rem] font-bold mt-1 {{ $katCls }}">{{ ucwords(str_replace('_', ' ', $r->kategori)) }}</p>
                    @endif
                    @if($r->status_pengajuan === 'pending')
                        <p class="text-[0.6rem] text-slate-400 font-semibold mt-0.5">Pending</p>
                    @elseif($r->status_pengajuan === 'rejected')
                        <p class="text-[0.6rem] text-red-500 font-semibold mt-0.5">Ditolak</p>
                        @if($r->alasan_ditolak)
                            <p class="text-[0.6rem] text-red-500 mt-0.5 max-w-[150px] truncate" title="{{ $r->alasan_ditolak }}">Alasan: {{ $r->alasan_ditolak }}</p>
                        @endif
                    @endif
                </div>
            </div>
            @empty
            <div class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat absensi.</div>
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
                    @forelse($riwayat as $r)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="py-3.5 px-5 font-semibold text-slate-700 text-sm">
                            {{ Carbon::parse($r->tanggal)->translatedFormat('d F Y') }}
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-500">
                            {{ Carbon::parse($r->tanggal)->translatedFormat('l') }}
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-600 text-center">
                            {{ $r->waktu_datang ? Carbon::parse($r->waktu_datang)->format('H:i').' WITA' : '—' }}
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-600 text-center">
                            {{ $r->waktu_pulang ? Carbon::parse($r->waktu_pulang)->format('H:i').' WITA' : '—' }}
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            @php
                                $cls = match($r->status ?? 'hadir') {
                                    'hadir' => 'bg-blue-50 text-[#1e3a6e] border-blue-100',
                                    'izin'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'sakit' => 'bg-slate-50 text-slate-600 border-slate-200',
                                    default => 'bg-red-50 text-red-600 border-red-100',
                                };
                            @endphp
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border {{ $cls }} capitalize">
                                    {{ $r->status ?? 'hadir' }}
                                </span>
                                @if($r->kategori && $r->status === 'hadir')
                                    @php
                                        $katCls = match($r->kategori) {
                                            'tepat_waktu' => 'text-emerald-600',
                                            'terlambat' => 'text-orange-600',
                                            'bolos' => 'text-red-600',
                                            default => 'text-slate-500'
                                        };
                                    @endphp
                                    <span class="text-[0.65rem] font-bold {{ $katCls }}">{{ ucwords(str_replace('_', ' ', $r->kategori)) }}</span>
                                @endif
                                @if($r->status_pengajuan === 'pending')
                                    <span class="text-[0.6rem] text-slate-400 font-semibold flex items-center gap-1">
                                        <svg class="w-3 h-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pending
                                    </span>
                                @elseif($r->status_pengajuan === 'rejected')
                                    <span class="text-[0.6rem] text-red-500 font-semibold">Ditolak</span>
                                    @if($r->alasan_ditolak)
                                        <span class="text-[0.6rem] text-red-500 max-w-[150px] truncate" title="{{ $r->alasan_ditolak }}">Alasan: {{ $r->alasan_ditolak }}</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat absensi.</td>
                    </tr>
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

/* ── QR Code PDF Download ── */
function downloadPDFQR() {
    const svg = document.querySelector('#qr-code-container svg');
    if (!svg) return alert('QR Code tidak ditemukan');
    
    // Convert SVG to Data URI for perfect rendering in canvas
    const serializer = new XMLSerializer();
    let source = serializer.serializeToString(svg);
    if(!source.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)){
        source = source.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
    }
    const svgDataUri = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(source);
    
    // Create temporary wrapper in DOM for accurate styling
    const wrapper = document.createElement('div');
    wrapper.style.position = 'absolute';
    wrapper.style.left = '-9999px';
    wrapper.style.top = '0';
    wrapper.innerHTML = `
        <div id="pdf-wrapper" style="width: 559px; height: 793px; background: #ffffff; padding-top: 130px; box-sizing: border-box;">
            <div id="pdf-content" style="width: 420px; margin: 0 auto; padding: 40px; text-align: center; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #ffffff; border: 2px dashed #cbd5e1; border-radius: 24px; box-sizing: border-box;">
                <p style="margin: 0 0 25px 0; color: #1e3a6e; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                    QR Code Absensi
                </p>
                <div style="margin-bottom: 25px;">
                    <img src="${svgDataUri}" style="width: 200px; height: 200px; margin: 0 auto; display: block;" />
                </div>
                <h2 style="margin: 0 0 8px 0; color: #1e293b; font-size: 24px; font-weight: 800;">{{ Auth::user()->name }}</h2>
                <p style="margin: 0 0 15px 0; color: #64748b; font-size: 16px; font-weight: 600;">NISN: {{ Auth::user()->nomor_induk }}</p>
                
                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0;" />
                
                <p style="margin: 0; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    E-Presensi {{ \App\Models\SchoolSetting::first()->nama_sekolah ?? '' }}
                </p>
            </div>
        </div>
    `;
    document.body.appendChild(wrapper);
    
    const opt = {
        margin:       0,
        filename:     'QR_Code_Absensi_{{ Auth::user()->nomor_induk }}.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
        jsPDF:        { unit: 'mm', format: 'a5', orientation: 'portrait' }
    };
    
    // Generate and cleanup
    html2pdf().set(opt).from(wrapper.querySelector('#pdf-wrapper')).save().then(() => {
        document.body.removeChild(wrapper);
    });
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</x-app-layout>