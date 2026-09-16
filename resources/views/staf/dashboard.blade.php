<x-app-layout>
<div class="min-h-screen bg-slate-50 p-6 font-sans text-slate-800">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- 1. Header Profil Interaktif -->
        @php
            $jam = date('H');
            $sapaan = 'Selamat Pagi';
            if ($jam >= 11 && $jam < 15) $sapaan = 'Selamat Siang';
            elseif ($jam >= 15 && $jam < 18) $sapaan = 'Selamat Sore';
            elseif ($jam >= 18) $sapaan = 'Selamat Malam';
            $user = Auth::user();
        @endphp
        <div class="relative overflow-hidden rounded-2xl bg-[#24417c] p-6 sm:p-8 shadow-md mb-8">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-32 h-32 bg-white opacity-10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row items-center sm:justify-between gap-6">
                <div class="flex items-center gap-5 w-full sm:w-auto">
                    <!-- Foto Profil -->
                    <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center text-[#24417c] font-bold text-3xl shadow-inner border-4 border-white/20 shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    
                    <!-- Sapaan & Info -->
                    <div>
                        <p class="text-blue-100 text-sm font-medium tracking-wide">{{ $sapaan }},</p>
                        <h1 class="text-white text-2xl font-bold tracking-tight">{{ $user->name }}</h1>
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-blue-50 text-sm">
                            <span class="bg-black/20 px-2.5 py-1 rounded backdrop-blur-sm border border-white/10 font-semibold capitalize flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.092 2.027-.267 3.018m-4.664 3.754a10.038 10.038 0 003.543-1.61m-3.543 1.61L12 21m0 0l-.36-.088M12 21v-4"></path></svg>
                                {{ $user->role }}
                            </span>
                            <span class="bg-black/20 px-2.5 py-1 rounded backdrop-blur-sm border border-white/10 font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                ID: {{ $user->nomor_induk ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Waktu & Status Sistem (Realtime) -->
                <div class="flex flex-col items-start sm:items-end w-full sm:w-auto bg-black/10 sm:bg-transparent p-4 sm:p-0 rounded-xl border border-white/10 sm:border-none gap-3 sm:gap-2">
                    <div class="flex flex-col items-start sm:items-end w-full sm:w-auto">
                        <p class="text-white font-bold text-2xl tabular-nums" id="realtime-clock">--:--:--</p>
                        <p class="text-blue-100 text-sm font-medium mt-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @php
            $totalAktivitas = $belumVerifikasi + $sudahVerifikasi;
            $persen = $totalAktivitas > 0 ? round(($sudahVerifikasi / $totalAktivitas) * 100) : 0;
        @endphp

        <!-- 2. Top Metrics (Grid 4 Kartu) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8">
            <!-- Kartu 1: Belum Verifikasi -->
            <a href="{{ route('staf.verifikasi.index') }}?status_verif=belum" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 sm:p-6 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md group relative overflow-hidden">
                <div class="absolute inset-0 bg-amber-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                <div class="flex justify-between items-start gap-2 relative z-10">
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mb-1 truncate">Belum Diverifikasi</p>
                        <h3 class="text-xl sm:text-3xl font-bold text-amber-600 truncate">{{ number_format($belumVerifikasi, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 sm:p-3 bg-amber-100 rounded-lg group-hover:bg-amber-200 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-4 flex items-center text-[10px] sm:text-sm relative z-10">
                    <span class="text-slate-400 truncate">Perlu ditindaklanjuti</span>
                </div>
                @if ($belumVerifikasi > 0)
                    <span class="absolute top-3 right-3 flex h-3 w-3 z-10">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                @endif
            </a>

            <!-- Kartu 2: Sudah Verifikasi  -->
            <a href="{{ route('staf.verifikasi.index') }}?status_verif=mengajar" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 sm:p-6 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md group relative overflow-hidden">
                <div class="absolute inset-0 bg-emerald-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                <div class="flex justify-between items-start gap-2 relative z-10">
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mb-1 truncate">Sudah Diverifikasi</p>
                        <h3 class="text-xl sm:text-3xl font-bold text-emerald-600 truncate">{{ number_format($sudahVerifikasi, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 sm:p-3 bg-emerald-100 rounded-lg group-hover:bg-emerald-200 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-4 flex items-center text-[10px] sm:text-sm relative z-10">
                    <span class="text-slate-400 truncate">Aktivitas mengajar selesai</span>
                </div>
            </a>

            <!-- Kartu 3: Total Aktivitas -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 sm:p-6 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md group">
                <div class="flex justify-between items-start gap-2">
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mb-1 truncate">Progress Verifikasi</p>
                        <h3 class="text-xl sm:text-3xl font-bold text-slate-800 truncate">{{ $persen }}%</h3>
                    </div>
                    <div class="p-2 sm:p-3 bg-[#24417c]/10 rounded-lg group-hover:bg-[#24417c]/20 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-4 flex items-center text-[10px] sm:text-sm flex-wrap">
                     <span class="text-[#24417c] font-medium flex items-center">
                         {{ $sudahVerifikasi }} / {{ $totalAktivitas }}
                     </span>
                     <span class="text-slate-400 ml-1 sm:ml-2 truncate">dari total aktivitas</span>
                </div>
            </div>

            <!-- Kartu 4: Guru Hadir Hari Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 sm:p-6 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md group">
                <div class="flex justify-between items-start gap-2">
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mb-1 truncate">Guru Hadir (Absen Masuk)</p>
                        <h3 class="text-xl sm:text-3xl font-bold text-blue-600 truncate">{{ number_format($guruHadir, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 sm:p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-4 flex items-center text-[10px] sm:text-sm flex-wrap">
                    <span class="text-slate-400 truncate">Guru yang hadir hari ini</span>
                </div>
            </div>
        </div>

        <!-- 3. Aktivitas Terbaru & Aksi Cepat -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Aktivitas Terbaru (Mendominasi) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                        <h2 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            Aktivitas Mengajar Terbaru
                        </h2>
                        <a href="{{ route('staf.verifikasi.index') }}"
                           class="text-xs font-semibold text-[#24417c] hover:text-blue-800 transition-colors flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
        
                    @if ($aktivitasTerbaru->isEmpty())
                        <div class="py-14 text-center">
                            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-slate-400 text-sm font-medium">Belum ada aktivitas mengajar hari ini</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50">
                            @foreach ($aktivitasTerbaru as $item)
                                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        {{-- Avatar guru --}}
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-[#24417c] font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($item->user?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-700 truncate">{{ $item->user?->name ?? '-' }}</p>
                                            <p class="text-xs text-slate-400 truncate">
                                                {{ $item->mata_pelajaran }} · {{ $item->kelas }} · Jam ke-{{ $item->jam_ke }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 ml-3">
                                        @if ($item->verified_at)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                                {{ $item->status_verifikasi === 'mengajar' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if ($item->status_verifikasi === 'mengajar')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    @endif
                                                </svg>
                                                {{ $item->status_verifikasi === 'mengajar' ? 'Mengajar' : 'Tidak Mengajar' }}
                                            </span>
                                        @else
                                            <a href="{{ route('staf.verifikasi.show', $item) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Verifikasi
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Aksi Cepat & Progress -->
            <div class="space-y-6">
                <!-- Aksi Cepat -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Aksi Cepat
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                        <a href="{{ route('staf.verifikasi.index') }}"
                           class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl p-3 hover:bg-blue-50 hover:border-blue-200 transition-all duration-150 group">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center transition-colors shrink-0">
                                <svg class="w-5 h-5 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Verifikasi Mengajar</p>
                                <p class="text-[10px] text-slate-400">Daftar aktivitas guru</p>
                            </div>
                        </a>
        
                        <a href="{{ route('staf.scan-qr') }}"
                           class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl p-3 hover:bg-violet-50 hover:border-violet-200 transition-all duration-150 group">
                            <div class="w-10 h-10 rounded-xl bg-violet-100 group-hover:bg-violet-200 flex items-center justify-center transition-colors shrink-0">
                                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Scan QR Absen</p>
                                <p class="text-[10px] text-slate-400">Scan QR Code siswa</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Jam realtime
    function updateRealtimeClock() {
        const now = new Date();
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('realtime-clock');
        if (el) el.textContent = `${hh}:${mm}:${ss}`;
    }
    updateRealtimeClock();
    setInterval(updateRealtimeClock, 1000);
</script>
</x-app-layout>
