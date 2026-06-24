@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Absensi Siswa</span>
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
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Banner tanggal + GPS status --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-6 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 text-center">
            <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">E-Presensi Siswa — Hari Ini</p>
            <p class="text-white text-2xl font-black">{{ Carbon::now()->translatedFormat('l, d F Y') }}</p>
            <p class="text-blue-300/70 text-sm mt-1">
                Radius absensi: <strong class="text-white">{{ $setting->radius_meter }} m</strong>
                dari {{ $setting->nama_sekolah }}
            </p>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>

        {{-- GPS Status --}}
        <div class="relative z-10 mt-4 flex justify-center">
            <div id="gps-status" class="bg-white/20 text-white text-xs px-4 py-2 rounded-lg font-semibold flex items-center gap-2">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="gps-spinner">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span id="gps-text">Mendeteksi lokasi GPS...</span>
            </div>
        </div>

        {{-- Status absen hari ini --}}
        @if($absensiHariIni)
        <div class="relative z-10 mt-3 flex justify-center gap-3 flex-wrap">
            <span class="bg-white/20 text-white text-xs px-3 py-1.5 rounded-lg font-semibold">
                ✅ Datang: {{ Carbon::parse($absensiHariIni->waktu_datang)->format('H:i') }} WITA
            </span>
            @if($absensiHariIni->waktu_pulang)
            <span class="bg-white/20 text-white text-xs px-3 py-1.5 rounded-lg font-semibold">
                🏠 Pulang: {{ Carbon::parse($absensiHariIni->waktu_pulang)->format('H:i') }} WITA
            </span>
            @endif
        </div>
        @endif
    </div>

    {{-- Tombol Absen --}}
    @php
        $sudahDatang = $absensiHariIni && $absensiHariIni->waktu_datang;
        $sudahPulang = $absensiHariIni && $absensiHariIni->waktu_pulang;
        $bisaPulang  = $sudahDatang && !$sudahPulang;
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
                    <p class="text-sm text-green-600 font-semibold mt-1">
                        ✅ Tercatat pukul {{ Carbon::parse($absensiHariIni->waktu_datang)->format('H:i') }} WITA
                    </p>
                @else
                    <p class="text-sm text-slate-500 mt-1">GPS Anda harus berada dalam radius {{ $setting->radius_meter }}m dari sekolah.</p>
                @endif
            </div>
            <form method="POST" action="{{ route('absensi.datang') }}" class="mt-auto" id="form-datang">
                @csrf
                <input type="hidden" name="latitude"  id="lat-datang">
                <input type="hidden" name="longitude" id="lng-datang">
                <button type="button" id="btn-datang"
                        onclick="submitAbsen('datang')"
                        {{ $sudahDatang ? 'disabled' : '' }}
                        class="w-full bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold py-3.5 rounded-xl text-sm
                               transition duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 hidden" id="spin-datang" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ $sudahDatang ? '✅ Sudah Absen Datang' : 'Hadir — Datang Sekolah' }}
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
                    <p class="text-sm text-green-600 font-semibold mt-1">
                        ✅ Tercatat pukul {{ Carbon::parse($absensiHariIni->waktu_pulang)->format('H:i') }} WITA
                    </p>
                @elseif(!$sudahDatang)
                    <p class="text-sm text-slate-400 mt-1">Absen datang terlebih dahulu.</p>
                @else
                    <p class="text-sm text-slate-500 mt-1">GPS Anda harus berada dalam radius {{ $setting->radius_meter }}m dari sekolah.</p>
                @endif
            </div>
            <form method="POST" action="{{ route('absensi.pulang') }}" class="mt-auto" id="form-pulang">
                @csrf
                <input type="hidden" name="latitude"  id="lat-pulang">
                <input type="hidden" name="longitude" id="lng-pulang">
                <button type="button" id="btn-pulang"
                        onclick="submitAbsen('pulang')"
                        {{ ($sudahPulang || !$sudahDatang) ? 'disabled' : '' }}
                        class="w-full border border-[#1e3a6e] text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white
                               font-bold py-3.5 rounded-xl text-sm transition duration-200
                               disabled:opacity-50 disabled:cursor-not-allowed disabled:border-slate-300 disabled:text-slate-400
                               disabled:hover:bg-transparent disabled:hover:text-slate-400 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 hidden" id="spin-pulang" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    @if($sudahPulang)
                        ✅ Sudah Absen Pulang
                    @elseif(!$sudahDatang)
                        ⏳ Absen Datang Dulu
                    @else
                        Hadir — Pulang Sekolah
                    @endif
                </button>
            </form>
        </div>

    </div>

    {{-- Riwayat dari database --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm">Riwayat Kehadiran</h3>
            <p class="text-xs text-slate-400 mt-0.5">30 hari terakhir.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Waktu Datang</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Waktu Pulang</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($riwayat as $r)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="py-3.5 px-5 font-semibold text-slate-700 text-sm">
                            {{ Carbon::parse($r->tanggal)->translatedFormat('d F Y') }}
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-600">
                            {{ $r->waktu_datang ? Carbon::parse($r->waktu_datang)->format('H:i').' WITA' : '—' }}
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-600">
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
                            <span class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border {{ $cls }} capitalize">
                                {{ $r->status ?? 'hadir' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-slate-400 text-sm">Belum ada riwayat absensi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
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
    el.classList.remove('bg-white/20');
    el.classList.add(ok ? 'bg-green-500/30' : 'bg-red-500/30');

    if (!ok) {
        if (msg.includes('Fake GPS')) {
            gpsErrorTitle = 'Peringatan Keamanan!';
            gpsErrorMsg   = 'Sistem mendeteksi indikasi penggunaan Fake GPS atau Lokasi Palsu di perangkat Anda. Harap matikan aplikasi tersebut untuk dapat melakukan absensi.';
        } else if (msg.includes('ditolak')) {
            gpsErrorTitle = 'Izin Ditolak';
            gpsErrorMsg   = 'Anda belum mengizinkan akses lokasi. Harap ubah izin situs di browser Anda menjadi Allow/Izinkan, lalu muat ulang halaman.';
        } else {
            gpsErrorTitle = 'GPS Gagal';
            gpsErrorMsg   = msg;
        }
    }
}

/* ── Ambil GPS otomatis saat halaman dibuka ── */
window.addEventListener('load', function() {
    if (!navigator.geolocation) {
        updateGpsStatus(false, '❌ Browser tidak mendukung GPS');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            const acc = pos.coords.accuracy;
            
            // --- ANTI FAKE GPS HEURISTIC ---
            // 1. Fake GPS sering memberikan akurasi bulat sempurna (10, 20, 50, 100, dll) tanpa desimal
            // 2. Fake GPS sering tidak punya data altitude (null) dan speed (null/0)
            const isRoundAccuracy = Number.isInteger(acc) && (acc % 10 === 0 || acc === 65);
            const isMissingAltitude = (pos.coords.altitude === null || pos.coords.altitude === 0);
            
            // Jika akurasi mencurigakan DAN tidak ada ketinggian, kita tandai sebagai Fake GPS
            if (isRoundAccuracy && isMissingAltitude) {
                updateGpsStatus(false, '⚠️ Terdeteksi penggunaan Aplikasi Fake GPS / Lokasi Palsu!');
                return;
            }

            gpsLat   = pos.coords.latitude;
            gpsLng   = pos.coords.longitude;
            gpsReady = true;
            updateGpsStatus(true, '📍 Lokasi asli terdeteksi (akurasi ±' + Math.round(acc) + 'm)');
        },
        function(err) {
            const msg = err.code === 1
                ? '❌ Izin lokasi ditolak — aktifkan GPS di browser'
                : '❌ GPS tidak tersedia: ' + err.message;
            updateGpsStatus(false, msg);
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
});

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