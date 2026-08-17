@php
use Carbon\Carbon;
use App\Models\JadwalMengajar;
use App\Models\AbsensiKelasSiswa;
use App\Models\AbsensiSholatSiswa;
use App\Models\ProgresEbook;
use App\Models\CatatanLiterasiQuran;
use Illuminate\Support\Str;

$user = Auth::user();
$hariIniStr = Carbon::now()->locale('id')->translatedFormat('l');
$tanggalSekarang = Carbon::now()->format('Y-m-d');
$currentTime = Carbon::now()->format('H:i:s');

$setting = \App\Models\SchoolSetting::get();
$blokAktif = $setting->blok_jadwal_aktif ?? 'A';

// 1. Jadwal Hari Ini
$profile = $user->siswaProfile;
$kelasLengkap = $profile ? trim("{$profile->kelas} {$profile->jurusan} {$profile->rombel}") : trim("{$user->kelas} {$user->jurusan} {$user->rombel}");
if ($blokAktif === 'TEFA') {
    $jadwalHariIni = collect();
} else {
    $jadwalHariIni = JadwalMengajar::with(['user'])
        ->where('hari', $hariIniStr)
        ->where('kelas', $kelasLengkap)
        ->whereIn('tipe_blok', ['Semua', $blokAktif])
        ->orderBy('jam_mulai')
        ->get();
}

// 2. Persentase Kehadiran Kelas (Dihitung dinamis dari tabel absensi_kelas_siswa)
$totalAbsenKelas = AbsensiKelasSiswa::where('siswa_id', $user->id)->count();
$totalHadirKelas = AbsensiKelasSiswa::where('siswa_id', $user->id)->where('status', 'hadir')->count();
$totalIzinKelas  = AbsensiKelasSiswa::where('siswa_id', $user->id)->where('status', 'izin')->count();
$totalSakitKelas = AbsensiKelasSiswa::where('siswa_id', $user->id)->where('status', 'sakit')->count();
$totalAlpaKelas  = AbsensiKelasSiswa::where('siswa_id', $user->id)->where('status', 'alpa')->count();

$persentaseKehadiran = $totalAbsenKelas > 0 ? round(($totalHadirKelas / $totalAbsenKelas) * 100) : 100;

// 2.5. Kehadiran Sekolah
$absenSekolah = \App\Models\AbsensiSiswa::where('user_id', $user->id)
    ->where('tanggal', Carbon::today())
    ->first();

// 3. Statistik Literasi E-book
$totalBukuDibaca = ProgresEbook::where('user_id', $user->id)->count();
if($totalBukuDibaca == 0) $totalBukuDibaca = "Belum Ada";

// 4. Status Sholat Hari Ini
$sholatHariIni = AbsensiSholatSiswa::where('user_id', $user->id)
    ->whereDate('tanggal', Carbon::today())
    ->first();

$statusSholatStr = 'Belum Absen';
if ($sholatHariIni) {
    $status = strtolower($sholatHariIni->status);
    if ($status == 'hadir') {
        $statusSholatStr = 'Sudah Absen';
    } elseif ($status == 'udzur') {
        $statusSholatStr = 'Udzur';
    } else {
        $statusSholatStr = ucfirst($status);
    }
}

$jam = date('H');
$sapaan = 'Selamat Pagi';
if ($jam >= 11 && $jam < 15) $sapaan = 'Selamat Siang';
elseif ($jam >= 15 && $jam < 18) $sapaan = 'Selamat Sore';
elseif ($jam >= 18) $sapaan = 'Selamat Malam';

// 6. Quote of the Day (Berubah setiap hari)
$quotes = [
    ['text' => 'Pendidikan adalah senjata paling ampuh yang bisa kamu gunakan untuk mengubah dunia.', 'author' => 'Nelson Mandela'],
    ['text' => 'Hiduplah seolah engkau mati besok. Belajarlah seolah engkau hidup selamanya.', 'author' => 'Mahatma Gandhi'],
    ['text' => 'Ilmu itu seperti air. Jika ia tidak bergerak, menjadi keruh lalu busuk.', 'author' => 'Imam Syafi\'i'],
    ['text' => 'Masa depan adalah milik mereka yang menyiapkan hari ini.', 'author' => 'Malcolm X'],
    ['text' => 'Barangsiapa belum pernah merasakan pahitnya belajar walau sesaat, ia akan menelan hinanya kebodohan sepanjang hayat.', 'author' => 'Imam Syafi\'i'],
    ['text' => 'Pendidikan bukanlah persiapan untuk hidup, melainkan kehidupan itu sendiri.', 'author' => 'John Dewey'],
    ['text' => 'Tujuan pendidikan itu untuk mempertajam kecerdasan, memperkukuh kemauan serta memperhalus perasaan.', 'author' => 'Tan Malaka'],
];
// Menggunakan sisa bagi hari dalam setahun agar quote berubah tiap hari, tapi tetap sama sepanjang hari
$dailyQuote = $quotes[date('z') % count($quotes)];
@endphp

<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard Murid</span>
    </x-slot>

    <div class="space-y-6 pb-10 bg-slate-50 min-h-screen">

        {{-- 1. Header Profil Interaktif --}}
        <div class="relative overflow-hidden rounded-2xl bg-[#1e3a6e] p-6 sm:p-8 shadow-lg">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-white opacity-5 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row items-center sm:justify-between gap-6">
                <div class="flex items-center gap-5 w-full sm:w-auto">
                    <!-- Foto Profil -->
                    <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center text-[#1e3a6e] font-bold text-2xl shadow-inner border-4 border-white/30 shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    
                    <!-- Sapaan & Info -->
                    <div>
                        <p class="text-blue-100 text-sm font-medium tracking-wide">{{ $sapaan }},</p>
                        <h1 class="text-white text-2xl font-bold tracking-tight">{{ $user->name }}</h1>
                        <div class="flex flex-wrap items-center gap-3 mt-1.5 text-blue-50 text-sm">
                            <span class="bg-black/20 px-2 py-0.5 rounded backdrop-blur-sm border border-white/10 font-semibold">{{ $kelasLengkap ?: 'Kelas -' }}</span>
                            @if($user->nomor_induk)
                                <span>NISN: {{ $user->nomor_induk }}</span>
                            @endif
                            @if($profile?->nis)
                                <span>NIS: {{ $profile->nis }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Waktu & Tanggal (Realtime) -->
                <div class="flex flex-col items-start sm:items-end w-full sm:w-auto bg-black/10 sm:bg-transparent p-4 sm:p-0 rounded-xl">
                    <p class="text-white font-bold text-lg sm:text-2xl" id="realtime-clock">--:--:--</p>
                    <p class="text-blue-100 text-sm font-medium mt-0.5">
                        <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- 2. Grid Statistik Cepat (4 Kartu) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            <!-- Kartu 1: Kehadiran Sekolah -->
            <a href="{{ route('absensi') }}" class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:shadow-lg hover:border-blue-200 transition-all border border-slate-100 overflow-hidden group block">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-[#1e3a6e] group-hover:text-white transition-colors text-[#1e3a6e]">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-[11px] sm:text-sm text-slate-500 font-medium truncate flex items-center justify-between">
                        <span>Absen Sekolah</span>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-600 transition-colors hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </p>
                    <h3 class="text-sm sm:text-lg font-bold truncate {{ ($absenSekolah && $absenSekolah->waktu_datang) ? 'text-emerald-600' : 'text-slate-800' }}">
                        {{ ($absenSekolah && $absenSekolah->waktu_datang) ? 'Sudah Hadir' : 'Belum Hadir' }}
                    </h3>
                </div>
            </a>

            <!-- Kartu 2: Kehadiran Kelas -->
            <a href="{{ route('murid.monitoring-kelas') }}" 
               title="Rincian: Hadir {{ $totalHadirKelas }}, Sakit {{ $totalSakitKelas }}, Izin {{ $totalIzinKelas }}, Alpa {{ $totalAlpaKelas }} (Total {{ $totalAbsenKelas }} sesi)"
               class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:shadow-lg hover:border-blue-200 transition-all border border-slate-100 overflow-hidden group block">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-[#1e3a6e] group-hover:text-white transition-colors text-[#1e3a6e]">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-[11px] sm:text-sm text-slate-500 font-medium truncate flex items-center justify-between">
                        <span>Kehadiran Kelas</span>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-600 transition-colors hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </p>
                    <div class="flex items-baseline gap-1.5">
                        <h3 class="text-sm sm:text-xl font-bold truncate {{ $totalAbsenKelas == 0 ? 'text-[#1e3a6e]' : ($persentaseKehadiran >= 85 ? 'text-emerald-600' : ($persentaseKehadiran >= 75 ? 'text-amber-600' : 'text-rose-600')) }}">
                            {{ $persentaseKehadiran }}%
                        </h3>
                    </div>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">
                        @if($totalAbsenKelas > 0)
                            {{ $totalHadirKelas }}/{{ $totalAbsenKelas }} Sesi Hadir
                        @else
                            Belum ada sesi
                        @endif
                    </p>
                </div>
            </a>

            <!-- Kartu 3: Literasi -->
            <a href="{{ route('ebook.index') }}" class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:shadow-lg hover:border-blue-200 transition-all border border-slate-100 overflow-hidden group block">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-[#1e3a6e] group-hover:text-white transition-colors text-[#1e3a6e]">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 -960 960 960" xmlns="http://www.w3.org/2000/svg">
                        <path d="M270-80q-45 0-77.5-30.5T160-186v-558q0-38 23.5-68t61.5-38l395-78v640l-379 76q-9 2-15 9.5t-6 16.5q0 11 9 18.5t21 7.5h450v-640h80v720H270Zm90-233 200-39v-478l-200 39v478Zm-80 16v-478l-15 3q-11 2-18 9.5t-7 18.5v457q5-2 10.5-3.5T261-293l19-4Zm-40-472v482-482Z"/>
                    </svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-[11px] sm:text-sm text-slate-500 font-medium truncate flex items-center justify-between">
                        <span>e-Book Dibaca</span>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-600 transition-colors hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </p>
                    <h3 class="text-sm sm:text-xl font-bold text-slate-800 truncate">{{ $totalBukuDibaca }}</h3>
                </div>
            </a>

            @if(strtolower($user->agama) == 'islam')
            <!-- Kartu 4: Status Sholat -->
            <a href="{{ route('murid.sholat') }}" class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:shadow-lg hover:border-blue-200 transition-all border border-slate-100 overflow-hidden group block">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-[#1e3a6e] group-hover:text-white transition-colors text-[#1e3a6e]">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 -960 960 960" xmlns="http://www.w3.org/2000/svg">
                        <path d="M40-120v-491q-18-11-29-28.5T0-680q0-23 24-56t56-64q32 31 56 64t24 56q0 23-11 40.5T120-611v171h80v-80q0-25 16-48t46-30q-11-17-16.5-37t-5.5-41q0-40 19-74t51-56l170-114 170 114q32 22 51 56t19 74q0 21-5.5 41T698-598q30 7 46 30t16 48v80h80v-171q-18-11-29-28.5T800-680q0-23 24-56t56-64q32 31 56 64t24 56q0 23-11 40.5T920-611v491H520v-160q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280v160H40Zm356-480h168q32 0 54-22t22-54q0-20-9-36.5T606-740l-126-84-126 84q-16 11-25 27.5t-9 36.5q0 32 22 54t54 22ZM120-200h240v-80q0-50 35-85t85-35q50 0 85 35t35 85v80h240v-160H680v-160H280v160H120v160Zm360-320Zm0-80Zm0 2Z"/>
                    </svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-[11px] sm:text-sm text-slate-500 font-medium truncate flex items-center justify-between">
                        <span>Absen Sholat Hari Ini</span>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-600 transition-colors hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </p>
                    @php
                        $colorClass = 'text-slate-800';
                        if ($statusSholatStr == 'Sudah Absen') $colorClass = 'text-green-600';
                        elseif ($statusSholatStr == 'Udzur') $colorClass = 'text-blue-500';
                        elseif ($statusSholatStr != 'Belum Absen') $colorClass = 'text-orange-500';
                    @endphp
                    <h3 class="text-sm sm:text-lg font-bold truncate {{ $colorClass }}">{{ $statusSholatStr }}</h3>
                </div>
            </a>
            @endif

            <!-- Kartu Info (Hanya jika non-muslim agar genap di mobile & desktop) -->
            <div class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:shadow-lg transition-shadow border border-slate-100 overflow-hidden {{ strtolower($user->agama) == 'islam' ? 'hidden' : 'block' }}">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-[11px] sm:text-sm text-slate-500 font-medium truncate">Informasi</p>
                    <h3 class="text-[10px] sm:text-xs font-bold text-slate-800 line-clamp-2">Pastikan presensi sebelum jam masuk berakhir.</h3>
                </div>
            </div>
        </div>

        {{-- Grid Utama Konten (2/3 & 1/3) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Bagian 3: Konten Utama (Kiri 2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Aksi Cepat (Quick Actions) -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                    <h2 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Aksi Cepat
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <a href="{{ route('absensi') }}" class="flex flex-col items-center justify-center gap-2 p-3 bg-blue-50 text-[#1e3a6e] rounded-xl hover:bg-[#1e3a6e] hover:text-white hover:shadow-lg transition-all duration-300 group">
                            <span class="text-xs font-semibold text-center">Presensi Sekarang</span>
                        </a>
                        <a href="{{ route('ebook.index') }}" class="flex flex-col items-center justify-center gap-2 p-3 bg-blue-50 text-[#1e3a6e] rounded-xl hover:bg-[#1e3a6e] hover:text-white hover:shadow-lg transition-all duration-300 group">
                            <span class="text-xs font-semibold text-center">Baca E-Book</span>
                        </a>
                        <a href="{{ route('murid.quran') }}" class="flex flex-col items-center justify-center gap-2 p-3 bg-blue-50 text-[#1e3a6e] rounded-xl hover:bg-[#1e3a6e] hover:text-white hover:shadow-lg transition-all duration-300 group">
                            <span class="text-xs font-semibold text-center">Catat Al-Quran</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center gap-2 p-3 bg-blue-50 text-[#1e3a6e] rounded-xl hover:bg-[#1e3a6e] hover:text-white hover:shadow-lg transition-all duration-300 group">
                            <span class="text-xs font-semibold text-center">Profil Saya</span>
                        </a>
                    </div>
                </div>

                <!-- Jadwal Pelajaran Hari Ini -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                    <h2 class="text-base font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Jadwal Kelas Hari Ini
                    </h2>

                    @if($jadwalHariIni->isEmpty())
                        <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-slate-500 font-medium">Tidak ada jadwal pelajaran untuk kelas Anda hari ini.</p>
                        </div>
                    @else
                        <div class="relative border-l-2 border-blue-100 ml-3 space-y-6">
                            @foreach($jadwalHariIni as $jadwal)
                                @php
                                    $waktuMulai = Carbon::parse($jadwal->jam_mulai)->format('H:i');
                                    $waktuSelesai = Carbon::parse($jadwal->jam_selesai)->format('H:i');
                                    
                                    // Hitung status: Belum Mulai, Berlangsung, Selesai
                                    $statusBadge = '<span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs font-semibold">Belum Mulai</span>';
                                    $dotColor = 'bg-slate-300';
                                    
                                    if ($currentTime >= $jadwal->jam_mulai && $currentTime <= $jadwal->jam_selesai) {
                                        $statusBadge = '<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold flex items-center gap-1"><span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></span> Berlangsung</span>';
                                        $dotColor = 'bg-blue-500 ring-4 ring-blue-50';
                                    } elseif ($currentTime > $jadwal->jam_selesai) {
                                        $statusBadge = '<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-semibold">Selesai</span>';
                                        $dotColor = 'bg-green-500';
                                    }
                                @endphp
                                
                                <div class="relative pl-6">
                                    <div class="absolute -left-[5px] top-1.5 w-2 h-2 {{ $dotColor }} rounded-full"></div>
                                    <div class="bg-white border border-slate-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-bold text-blue-600">{{ $waktuMulai }} - {{ $waktuSelesai }}</span>
                                            </div>
                                            {!! $statusBadge !!}
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800">{{ $jadwal->mata_pelajaran ?? 'Mata Pelajaran' }}</h3>
                                        <p class="text-sm text-slate-500 flex items-center gap-1.5 mt-1">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ $jadwal->user->name ?? 'Guru Belum Diatur' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bagian 4: Samping (Kanan 1/3) --}}
            <div class="lg:col-span-1 space-y-6">
                <!-- Quote of the Day -->
                <div class="bg-blue-50 rounded-2xl shadow-sm p-6 border border-blue-100 relative overflow-hidden">
                    <svg class="absolute -bottom-4 -right-4 w-24 h-24 text-blue-100 opacity-50 transform rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                    <div class="relative z-10">
                        <h3 class="text-sm font-bold text-blue-800 mb-2">Quote of the Day</h3>
                        <p class="text-blue-900 font-medium italic text-sm leading-relaxed mb-4">
                            "{{ $dailyQuote['text'] }}"
                        </p>
                        <p class="text-xs font-bold text-blue-600">- {{ $dailyQuote['author'] }}</p>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100 text-center hidden lg:block">
                    <div class="w-16 h-16 bg-blue-50 text-[#1e3a6e] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Informasi</h3>
                    <p class="text-xs text-slate-500 mb-3">Pastikan Anda selalu melakukan presensi sebelum jam masuk berakhir.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Waktu Real-Time -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('realtime-clock').textContent = hours + ':' + minutes + ':' + seconds;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

</x-app-layout>
