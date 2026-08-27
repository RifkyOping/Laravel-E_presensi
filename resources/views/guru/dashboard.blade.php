@php 
    use Carbon\Carbon;
    
    $user = Auth::user();
    $userId = $user->id;
    $today = Carbon::today()->toDateString();
    $currentTime = Carbon::now()->format('H:i:s');
    
    $hariMap = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];
    $hariIni = $hariMap[Carbon::now()->format('l')];

    // Sapaan Waktu
    $jam = date('H');
    $sapaan = 'Selamat Pagi';
    if ($jam >= 11 && $jam < 15) $sapaan = 'Selamat Siang';
    elseif ($jam >= 15 && $jam < 18) $sapaan = 'Selamat Sore';
    elseif ($jam >= 18) $sapaan = 'Selamat Malam';

    $setting = \App\Models\SchoolSetting::get();
    $blokAktif = $setting->blok_jadwal_aktif ?? 'A';

    // Membungkus query dalam cache (10 Menit)
    $cacheKeyGuru = 'guru_dashboard_' . $userId . '_' . $today;
    $dashboardData = \Illuminate\Support\Facades\Cache::remember($cacheKeyGuru, 600, function() use ($userId, $today, $blokAktif, $hariIni) {
        $absenPribadi = \App\Models\AbsensiGuru::where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        if ($blokAktif === 'TEFA') {
            $jadwalHariIni = collect();
        } else {
            $jadwalHariIni = \App\Models\JadwalMengajar::where('user_id', $userId)
                ->where('hari', $hariIni)
                ->whereIn('tipe_blok', ['Semua', $blokAktif])
                ->orderBy('jam_ke')
                ->get();
        }
        
        foreach ($jadwalHariIni as $jadwal) {
            $jadwal->sudah_absen_kelas = \App\Models\AbsensiKelasSiswa::where('jadwal_mengajar_id', $jadwal->id)
                ->where('tanggal', $today)
                ->exists();
        }
        
        $totalKelas = $jadwalHariIni->count();
        $kelasSelesai = $jadwalHariIni->where('sudah_absen_kelas', true)->count();

        $pendingCount = \App\Models\AbsensiSiswa::where('guru_id', $userId)
            ->where('status_pengajuan', 'pending')
            ->count();

        return compact('absenPribadi', 'jadwalHariIni', 'totalKelas', 'kelasSelesai', 'pendingCount');
    });

    extract($dashboardData);

    // Penentuan NIP/Nomor Induk
    $nip = $user->nomor_induk ?? '-';
@endphp

<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard Guru</span>
    </x-slot>

    <div class="space-y-6 pb-10 bg-slate-50 min-h-screen">
        
        {{-- 1. Header Profil & Sapaan Profesional --}}
        <div class="relative overflow-hidden rounded-2xl bg-[#1e3a6e] p-6 sm:p-8 shadow-md">
            <!-- Elemen Dekoratif -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-white opacity-10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-6 relative z-10 w-full">
                
                <div class="flex flex-col w-full sm:w-auto gap-2 sm:gap-0">
                    <!-- Foto Profil & Nama (Selalu sejajar) -->
                    <div class="flex flex-row items-center gap-4 sm:gap-5 w-full">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white flex items-center justify-center text-[#1e3a6e] font-bold text-2xl sm:text-3xl shadow-inner border-4 border-white/20 shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <p class="text-blue-100 text-xs sm:text-sm font-medium tracking-wide truncate">{{ $sapaan }}, Bapak/Ibu</p>
                            <h1 class="text-white text-lg sm:text-2xl font-bold tracking-tight leading-tight mt-0.5 truncate">{{ $user->name }}</h1>
                            <!-- NIP Desktop -->
                            <div class="hidden sm:flex flex-wrap items-center gap-3 mt-2 text-blue-50 text-sm">
                                <span class="bg-black/20 px-2.5 py-1 rounded backdrop-blur-sm border border-white/10 font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                    NIP: {{ $nip }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- NIP Mobile (Lebar Penuh) -->
                    <div class="sm:hidden w-full mt-2">
                        <div class="bg-black/10 p-3.5 rounded-xl border border-white/10 text-blue-50 text-sm font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            <span class="truncate">NIP: {{ $nip }}</span>
                        </div>
                    </div>
                </div>

                <!-- Waktu & Tanggal -->
                <div class="flex flex-col items-start sm:items-end w-full sm:w-auto bg-black/10 sm:bg-transparent p-4 sm:p-0 rounded-xl border border-white/10 sm:border-none shrink-0">
                    <p class="text-white font-bold text-2xl" id="realtime-clock">--:--:--</p>
                    <p class="text-blue-100 text-sm font-medium mt-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- 2. Grid Statistik & Tugas Cepat (4 Kartu) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            <!-- Kartu 1: Absensi Pribadi Guru -->
            <div class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 border border-slate-100">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Absen Sekolah</p>
                    <h3 class="text-sm sm:text-lg font-bold truncate {{ ($absenPribadi && $absenPribadi->waktu_datang) ? 'text-[#1e3a6e]' : 'text-slate-800' }}">
                        {{ ($absenPribadi && $absenPribadi->waktu_datang) ? 'Sudah Hadir' : 'Belum Hadir' }}
                    </h3>
                </div>
            </div>

            <!-- Kartu 2: Jadwal Hari Ini -->
            <div class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 border border-slate-100">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Jadwal Mengajar</p>
                    <h3 class="text-sm sm:text-xl font-bold text-slate-800 truncate">{{ $totalKelas }} <span class="text-xs sm:text-sm font-normal text-slate-400">Kelas</span></h3>
                </div>
            </div>

            <!-- Kartu 3: Kelas Selesai -->
            <div class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 border border-slate-100">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Selesai Diabsen</p>
                    <h3 class="text-sm sm:text-xl font-bold text-slate-800 truncate">{{ $kelasSelesai }} <span class="text-xs sm:text-sm font-normal text-slate-400">/ {{ $totalKelas }}</span></h3>
                </div>
            </div>

            <!-- Kartu 4: Persetujuan Tertunda -->
            <div class="bg-white rounded-xl shadow-md p-3 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 border border-slate-100">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div class="w-full overflow-hidden">
                    <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Validasi Tertunda</p>
                    <h3 class="text-sm sm:text-xl font-bold text-slate-800 truncate">{{ $pendingCount }} <span class="text-xs sm:text-sm font-normal text-slate-400">Pengajuan</span></h3>
                </div>
            </div>
        </div>

        {{-- Grid Konten Utama & Samping --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- 3. Bagian Konten Utama (2/3 -> col-span-8) --}}
            <div class="lg:col-span-8 order-2 lg:order-1 space-y-6">
                
                <!-- Aksi Cepat (Quick Actions) -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                    <h2 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Aksi Cepat
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <a href="{{ route('guru.absen-kelas.index') }}" class="flex flex-col items-center justify-center gap-2 p-3 bg-blue-50 text-[#1e3a6e] rounded-xl hover:bg-[#1e3a6e] hover:text-white hover:shadow-lg transition-all duration-300 group">
                            <span class="text-xs font-semibold text-center">Input Absen Kelas</span>
                        </a>
                        <a href="{{ route('guru.literasi.catatan') }}" class="flex flex-col items-center justify-center gap-2 p-3 bg-blue-50 text-[#1e3a6e] rounded-xl hover:bg-[#1e3a6e] hover:text-white hover:shadow-lg transition-all duration-300 group">
                            <span class="text-xs font-semibold text-center">Literasi E-Book</span>
                        </a>
                        <a href="{{ route('guru.literasi.quran') }}" class="flex flex-col items-center justify-center gap-2 p-3 bg-blue-50 text-[#1e3a6e] rounded-xl hover:bg-[#1e3a6e] hover:text-white hover:shadow-lg transition-all duration-300 group">
                            <span class="text-xs font-semibold text-center">Literasi Keagamaan</span>
                        </a>
                        <a href="{{ route('guru.buku-kemajuan') }}" class="flex flex-col items-center justify-center gap-2 p-3 bg-blue-50 text-[#1e3a6e] rounded-xl hover:bg-[#1e3a6e] hover:text-white hover:shadow-lg transition-all duration-300 group">
                            <span class="text-xs font-semibold text-center">Monitoring Kelas</span>
                        </a>
                    </div>
                </div>

                <!-- Jadwal Mengajar Hari Ini -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-slate-100">
                    <h2 class="text-base font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Jadwal Mengajar Hari Ini
                    </h2>

                    @if($jadwalHariIni->isEmpty())
                        <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <p class="text-slate-500 font-medium">Tidak ada jadwal mengajar hari ini.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($jadwalHariIni as $jadwal)
                                @php
                                    $waktuMulai = Carbon::parse($jadwal->jam_mulai)->format('H:i');
                                    $waktuSelesai = Carbon::parse($jadwal->jam_selesai)->format('H:i');
                                    $isBerlangsung = ($currentTime >= $jadwal->jam_mulai && $currentTime <= $jadwal->jam_selesai);
                                @endphp
                                
                                <div class="bg-white border {{ $isBerlangsung ? 'border-blue-300 shadow-md ring-1 ring-blue-100' : 'border-slate-100' }} rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-blue-50 border border-blue-100 rounded-lg px-3 py-2 text-center shrink-0 min-w-[80px]">
                                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Mapel Ke-{{ $jadwal->jam_ke }}</p>
                                            <p class="text-sm font-bold text-[#1e3a6e]">{{ $waktuMulai }}</p>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-slate-800">{{ $jadwal->kelas }} <span class="text-slate-400 font-normal mx-1">•</span> {{ $jadwal->mata_pelajaran }}</h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($jadwal->sudah_absen_kelas)
                                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Selesai Absen
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Belum Absen
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="shrink-0">
                                        <a href="{{ route('guru.absen-kelas.show', $jadwal->id) }}" class="block w-full text-center px-4 py-2 bg-[#1e3a6e] hover:bg-[#152a51] text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                                            Isi Presensi Kelas
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- 4. Bagian Samping/Manajemen (1/3 -> col-span-4) --}}
            <div class="lg:col-span-4 order-1 lg:order-2 flex flex-col gap-4 sm:gap-6">
                
                <!-- Daftar Tugas Prioritas -->
                <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6 border border-slate-100 h-full flex flex-col">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Tugas Prioritas
                    </h3>
                    
                    <ul class="space-y-3">
                        @if(!$absenPribadi || !$absenPribadi->waktu_datang)
                            <li class="flex gap-3 bg-red-50 text-red-800 p-3 rounded-xl border border-red-100">
                                <div class="shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path></svg>
                                </div>
                                <div class="text-sm font-medium">Anda belum melakukan Absen Sekolah hari ini.</div>
                            </li>
                        @endif

                        @if($pendingCount > 0)
                            <li class="flex gap-3 bg-amber-50 text-amber-800 p-3 rounded-xl border border-amber-100">
                                <div class="shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="text-sm font-medium">Terdapat <strong>{{ $pendingCount }}</strong> pengajuan presensi siswa yang menunggu persetujuan Anda.</div>
                            </li>
                        @endif
                        
                        @if($totalKelas > 0 && $kelasSelesai < $totalKelas)
                            <li class="flex gap-3 bg-blue-50 text-blue-800 p-3 rounded-xl border border-blue-100">
                                <div class="shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="text-sm font-medium">Anda memiliki jadwal kelas yang belum diisi presensinya hari ini.</div>
                            </li>
                        @endif

                        @if(($absenPribadi && $absenPribadi->waktu_datang) && $pendingCount == 0 && ($totalKelas == 0 || $kelasSelesai == $totalKelas))
                            <li class="text-sm text-slate-500 font-medium text-center py-4 bg-slate-50 rounded-xl border border-slate-100">
                                Semua tugas Anda sudah terselesaikan! 🎉
                            </li>
                        @endif
                    </ul>
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
