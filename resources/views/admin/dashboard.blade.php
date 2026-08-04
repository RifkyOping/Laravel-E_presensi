<x-app-layout>
<div class="min-h-screen bg-slate-50 p-6 font-sans text-slate-800">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- 1. Header Profil Interaktif (Admin) -->
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
                    <div class="flex items-center bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20 w-full sm:w-auto justify-center sm:justify-start">
                        @if($systemStatus['failed_jobs'] > 0)
                            <span class="relative flex h-3 w-3 mr-3" title="Ada Job Gagal">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            <span class="text-sm font-medium text-red-100">Error ({{ $systemStatus['failed_jobs'] }} Job Gagal)</span>
                        @elseif($systemStatus['pending_jobs'] > 5)
                            <span class="relative flex h-3 w-3 mr-3" title="Antrean Padat">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                            </span>
                            <span class="text-sm font-medium text-amber-100">Sibuk ({{ $systemStatus['pending_jobs'] }} Antrean)</span>
                        @else
                            <span class="relative flex h-3 w-3 mr-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <span class="text-sm font-medium text-blue-50">Sistem Normal @if($systemStatus['pending_jobs'] > 0)({{ $systemStatus['pending_jobs'] }} Antrean)@endif</span>
                        @endif
                    </div>
                    <div class="flex flex-col items-start sm:items-end w-full sm:w-auto">
                        <p class="text-white font-bold text-2xl" id="realtime-clock">--:--:--</p>
                        <p class="text-blue-100 text-sm font-medium mt-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Top Metrics (Grid 4 Kartu) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8">
            <!-- Kartu 1: Total Murid Aktif -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 sm:p-6 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md group">
                <div class="flex justify-between items-start gap-2">
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mb-1 truncate">Total Murid Aktif</p>
                        <h3 class="text-xl sm:text-3xl font-bold text-slate-800 truncate">{{ number_format($stats['total_siswa'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 sm:p-3 bg-[#24417c]/10 rounded-lg group-hover:bg-[#24417c]/20 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-4 flex items-center text-[10px] sm:text-sm">
                    <span class="text-slate-400 truncate">Total terdaftar aktif</span>
                </div>
            </div>

            <!-- Kartu 2: Total Guru  -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 sm:p-6 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md group">
                <div class="flex justify-between items-start gap-2">
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mb-1 truncate">Total Guru</p>
                        <h3 class="text-xl sm:text-3xl font-bold text-slate-800 truncate">{{ number_format($stats['total_guru'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 sm:p-3 bg-[#24417c]/10 rounded-lg group-hover:bg-[#24417c]/20 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-4 flex items-center text-[10px] sm:text-sm">
                    <span class="text-slate-400 truncate">Total terdaftar aktif</span>
                </div>
            </div>

            <!-- Kartu 3: Murid Hadir Hari Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 sm:p-6 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md group">
                <div class="flex justify-between items-start gap-2">
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mb-1 truncate">Murid Hadir Hari Ini</p>
                        <h3 class="text-xl sm:text-3xl font-bold text-slate-800 truncate">{{ number_format($stats['siswa_hadir'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 sm:p-3 bg-[#24417c]/10 rounded-lg group-hover:bg-[#24417c]/20 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-4 flex items-center text-[10px] sm:text-sm flex-wrap">
                     <span class="text-[#24417c] font-medium flex items-center">
                         @if(($stats['total_siswa'] ?? 0) > 0)
                             {{ number_format(($stats['siswa_hadir'] / $stats['total_siswa']) * 100, 1) }}%
                         @else
                             0%
                         @endif
                     </span>
                     <span class="text-slate-400 ml-1 sm:ml-2 truncate">dari total murid</span>
                </div>
            </div>

            <!-- Kartu 4: Guru Hadir Hari Ini -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 sm:p-6 transition-transform duration-300 hover:-translate-y-1 hover:shadow-md group">
                <div class="flex justify-between items-start gap-2">
                    <div class="overflow-hidden">
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mb-1 truncate">Guru Hadir Hari Ini</p>
                        <h3 class="text-xl sm:text-3xl font-bold text-slate-800 truncate">{{ number_format($stats['guru_hadir'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 sm:p-3 bg-[#24417c]/10 rounded-lg group-hover:bg-[#24417c]/20 transition-colors shrink-0">
                        <svg class="w-6 h-6 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-4 flex items-center text-[10px] sm:text-sm flex-wrap">
                    <span class="text-[#24417c] font-medium flex items-center">
                        @if(($stats['total_guru'] ?? 0) > 0)
                             {{ number_format(($stats['guru_hadir'] / $stats['total_guru']) * 100, 1) }}%
                         @else
                             0%
                         @endif
                    </span>
                    <span class="text-slate-400 ml-1 sm:ml-2 truncate">dari total guru</span>
                </div>
            </div>
        </div>

        <!-- 3. Area Tabel & Manajemen (Bagian Bawah) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Tabel Aktivitas Terkini (Lebar 2/3) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 lg:col-span-2 overflow-hidden flex flex-col order-2 lg:order-1">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0">
                    <h2 class="text-lg font-bold text-[#24417c]">Aktivitas Terkini</h2>
                </div>
                <div class="overflow-hidden md:overflow-x-auto flex-grow">
                    <table class="w-full text-left border-collapse block md:table">
                        <thead class="hidden md:table-header-group">
                            <tr class="bg-slate-50/80 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="px-6 py-3.5 font-semibold">Pengguna</th>
                                <th class="px-6 py-3.5 font-semibold">Peran</th>
                                <th class="px-6 py-3.5 font-semibold">Aktivitas</th>
                                <th class="px-6 py-3.5 font-semibold">Waktu Terakhir Aktif</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-700 divide-y-2 md:divide-y divide-slate-200 md:divide-slate-100 block md:table-row-group">
                            @forelse($aktivitasHariIni as $aktivitas)
                            <tr class="hover:bg-blue-50/40 transition-colors group block md:table-row p-4 md:p-0">
                                <td class="px-0 md:px-6 py-1 md:py-4 font-medium text-slate-800 group-hover:text-blue-700 transition-colors flex md:table-cell items-center justify-between">
                                    <span>{{ $aktivitas->name ?? 'Tidak diketahui' }}</span>
                                    <span class="md:hidden inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#24417c]/10 text-[#24417c] border border-[#24417c]/20 capitalize">
                                        {{ $aktivitas->role }}
                                    </span>
                                </td>
                                <td class="px-0 md:px-6 py-1 md:py-4 hidden md:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-[#24417c]/10 text-[#24417c] border border-[#24417c]/20 capitalize">
                                        {{ $aktivitas->role }}
                                    </span>
                                </td>
                                <td class="px-0 md:px-6 py-1 md:py-4 text-slate-600 block md:table-cell">
                                    {{ $aktivitas->description }}
                                </td>
                                <td class="px-0 md:px-6 py-1 md:py-4 block md:table-cell">
                                    <div class="flex md:flex-col justify-between items-center md:items-start mt-1 md:mt-0">
                                        <span class="text-xs md:text-sm text-slate-800 font-medium">
                                            {{ \Carbon\Carbon::createFromTimestamp($aktivitas->last_activity)->diffForHumans() }}
                                        </span>
                                        <span class="text-[10px] md:text-xs text-slate-500 mt-0 md:mt-0.5">{{ \Carbon\Carbon::createFromTimestamp($aktivitas->last_activity)->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="block md:table-row">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 block md:table-cell">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p>Belum ada aktivitas pengguna hari ini.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Menu Manajemen Cepat (Lebar 1/3) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex flex-col order-1 lg:order-2">
                <div class="px-6 py-5 border-b border-slate-100 bg-white rounded-t-xl">
                    <h2 class="text-lg font-bold text-[#24417c]">Akses Cepat</h2>
                </div>
                <div class="p-4 grid grid-cols-2 gap-4 flex-grow content-start">
                    
                    <a href="{{ route('admin.users') }}" class="group flex flex-col justify-center items-center p-5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-[#24417c] hover:border-[#24417c] hover:shadow-md hover:-translate-y-1 transition-all text-center">
                        <div class="w-12 h-12 rounded-full bg-[#24417c]/10 text-[#24417c] flex items-center justify-center mb-3 group-hover:bg-[#24417c] group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <span class="text-sm font-bold text-slate-700 group-hover:text-white transition-colors">Kelola<br>Pengguna</span>
                    </a>

                    <a href="{{ route('admin.geofence') }}" class="group flex flex-col justify-center items-center p-5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-[#24417c] hover:border-[#24417c] hover:shadow-md hover:-translate-y-1 transition-all text-center">
                        <div class="w-12 h-12 rounded-full bg-[#24417c]/10 text-[#24417c] flex items-center justify-center mb-3 group-hover:bg-[#24417c] group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="text-sm font-bold text-slate-700 group-hover:text-white transition-colors">Pengaturan<br>Sistem</span>
                    </a>

                    <a href="{{ route('admin.absensi-guru') }}" class="group flex flex-col justify-center items-center p-5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-[#24417c] hover:border-[#24417c] hover:shadow-md hover:-translate-y-1 transition-all text-center">
                        <div class="w-12 h-12 rounded-full bg-[#24417c]/10 text-[#24417c] flex items-center justify-center mb-3 group-hover:bg-[#24417c] group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <span class="text-sm font-bold text-slate-700 group-hover:text-white transition-colors">Absensi<br>Guru</span>
                    </a>

                    <a href="{{ route('admin.absensi-siswa') }}" class="group flex flex-col justify-center items-center p-5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-[#24417c] hover:border-[#24417c] hover:shadow-md hover:-translate-y-1 transition-all text-center">
                        <div class="w-12 h-12 rounded-full bg-[#24417c]/10 text-[#24417c] flex items-center justify-center mb-3 group-hover:bg-[#24417c] group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <span class="text-sm font-bold text-slate-700 group-hover:text-white transition-colors">Absensi<br>Murid</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Waktu Real-Time -->
    <script>
        function updateClock() {
            const clockEl = document.getElementById('realtime-clock');
            if(clockEl) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockEl.textContent = hours + ':' + minutes + ':' + seconds;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</div>
</x-app-layout>
