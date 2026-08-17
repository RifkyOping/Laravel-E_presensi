<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Monitoring Sekolah</span>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6" 
         x-data="{ tab: new URLSearchParams(location.search).get('tab') || localStorage.getItem('monitoring_tab') || '{{ $tab }}' }"
         x-init="$watch('tab', val => localStorage.setItem('monitoring_tab', val))">
        
        {{-- Header Section --}}
        <div class="bg-gradient-to-r from-[#1e3a6e] to-[#2d4d8c] rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-white">Dashboard Monitoring</h1>
                <p class="text-blue-100 text-sm mt-1">Pantau kehadiran guru dan murid secara mendetail.</p>
            </div>
            <div class="bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/20">
                <p class="text-white text-sm font-medium">Hari ini: <span class="font-bold">{{ $today->translatedFormat('l, d F Y') }}</span></p>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="flex space-x-2 border-b border-slate-200 overflow-x-auto hide-scrollbar">
            <button @click="tab = 'ringkasan'" 
                    :class="{'border-b-2 border-[#1e3a6e] text-[#1e3a6e] font-bold': tab === 'ringkasan', 'text-slate-500 hover:text-slate-700': tab !== 'ringkasan'}" 
                    class="pb-3 px-4 text-sm whitespace-nowrap transition-colors">
                Ringkasan<span class="hidden sm:inline"> & Grafik</span>
            </button>
            <button @click="tab = 'detail_guru'" 
                    :class="{'border-b-2 border-blue-500 text-blue-600 font-bold': tab === 'detail_guru', 'text-slate-500 hover:text-slate-700': tab !== 'detail_guru'}" 
                    class="pb-3 px-4 text-sm whitespace-nowrap transition-colors">
                Detail<span class="hidden sm:inline"> Kehadiran</span> Guru
            </button>
            <button @click="tab = 'detail_siswa'" 
                    :class="{'border-b-2 border-emerald-500 text-emerald-600 font-bold': tab === 'detail_siswa', 'text-slate-500 hover:text-slate-700': tab !== 'detail_siswa'}" 
                    class="pb-3 px-4 text-sm whitespace-nowrap transition-colors">
                Detail<span class="hidden sm:inline"> Kehadiran</span> Murid
            </button>
        </div>

        {{-- TAB: RINGKASAN --}}
        <div x-show="tab === 'ringkasan'" class="space-y-6">
            {{-- Top Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Guru Stats --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">Absen Sekolah Guru</h3>
                                <p class="text-xs text-slate-400">Kehadiran Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-3xl font-black text-slate-800">{{ $stats['guru']['hadir'] }}</span>
                            <span class="text-sm font-bold text-slate-400">/ {{ $stats['guru']['total'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-lg border border-green-100">{{ $stats['guru']['persen_hadir'] }}% Hadir</span>
                        </div>
                    </div>
                    {{-- Progress Bar --}}
                    <div class="w-full bg-slate-100 h-2 rounded-full mt-4 overflow-hidden">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stats['guru']['persen_hadir'] }}%"></div>
                    </div>
                </div>

                {{-- Siswa Stats --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">Absen Sekolah Murid</h3>
                                <p class="text-xs text-slate-400">Kehadiran Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-3xl font-black text-slate-800">{{ $stats['siswa']['hadir'] }}</span>
                            <span class="text-sm font-bold text-slate-400">/ {{ $stats['siswa']['total'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-lg border border-green-100">{{ $stats['siswa']['persen_hadir'] }}% Hadir</span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full mt-4 overflow-hidden">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $stats['siswa']['persen_hadir'] }}%"></div>
                    </div>
                </div>

                {{-- Sholat Stats --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">Sholat (Dzuhur/Ashar)</h3>
                                <p class="text-xs text-slate-400">Murid Muslim Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-3xl font-black text-slate-800">{{ $stats['sholat']['hadir'] }}</span>
                            <span class="text-sm font-bold text-slate-400">/ {{ $stats['sholat']['total'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-lg border border-green-100">{{ $stats['sholat']['persen_hadir'] }}% Sholat</span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full mt-4 overflow-hidden">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $stats['sholat']['persen_hadir'] }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Secondary Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-700">Aktivitas Mengajar</h4>
                        <p class="text-xs text-slate-400">Guru yang sudah absen masuk kelas hari ini</p>
                    </div>
                    <div class="text-2xl font-black text-[#1e3a6e]">{{ $stats['guru']['mengajar'] }} <span class="text-sm font-bold text-slate-400">Guru</span></div>
                </div>
                
                <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-700">Kehadiran Kelas</h4>
                        <p class="text-xs text-slate-400">Murid yang hadir di kelas hari ini</p>
                    </div>
                    <div class="text-2xl font-black text-[#1e3a6e]">{{ $stats['siswa']['hadir_kelas'] }} <span class="text-sm font-bold text-slate-400">Murid</span></div>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="font-bold text-slate-800">Tren Kehadiran</h3>
                    <form method="GET" action="{{ route('monitoring-sekolah.dashboard') }}" class="flex items-center gap-2">
                        <select name="filter" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-lg focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                            <option value="harian" {{ $filter === 'harian' ? 'selected' : '' }}>Harian</option>
                            <option value="mingguan" {{ $filter === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan" {{ $filter === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                        <select name="bulan" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-lg focus:ring-[#1e3a6e] focus:border-[#1e3a6e]" {{ $filter === 'bulanan' ? 'disabled' : '' }}>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="tahun" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-lg focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                            @for ($y = 2026; $y <= date('Y'); $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
                <div class="p-6">
                    <div class="w-full h-80">
                        <canvas id="kehadiranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB: DETAIL GURU --}}
        <div x-show="tab === 'detail_guru'" class="bg-white rounded-2xl border border-slate-200 overflow-hidden" style="display: none;" x-data="{ searchGuru: '', quickFilterGuru: 'semua' }">
            <div class="px-6 py-4 border-b border-slate-100 bg-blue-50/50 flex flex-col gap-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="font-bold text-blue-900">
                        Kehadiran Guru 
                        <span class="text-sm font-normal text-slate-500 ml-1">({{ $detailDate->translatedFormat('d F Y') }})</span>
                    </h3>
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <form method="GET" action="{{ route('monitoring-sekolah.dashboard') }}" class="flex items-center gap-2">
                            <input type="hidden" name="tab" value="detail_guru">
                            <input type="date" name="tanggal" value="{{ $tanggal_detail }}" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" title="Pilih Tanggal">
                        </form>
                        <div class="relative w-full md:w-64">
                            <input type="text" x-model="searchGuru" placeholder="Cari nama..." class="w-full text-sm border-slate-300 rounded-lg pl-9 focus:ring-blue-500 focus:border-blue-500">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- Quick Filters --}}
                <div class="mb-2 md:mb-0">
                    {{-- Mobile Dropdown --}}
                    <select x-model="quickFilterGuru" class="md:hidden w-full text-sm font-bold text-slate-700 border-slate-300 rounded-lg focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                        <option value="semua">Semua Guru</option>
                        <option value="hadir_tanpa_kelas">🔴 Hadir Tapi Belum Ada Kelas</option>
                        <option value="terlambat">🟡 Terlambat Datang</option>
                        <option value="tidak_hadir">⚪ Tidak Hadir (Izin/Sakit/Alpa)</option>
                    </select>

                    {{-- Desktop Pills --}}
                    <div class="hidden md:flex flex-wrap gap-2">
                        <button @click="quickFilterGuru = 'semua'" :class="quickFilterGuru === 'semua' ? 'bg-[#1e3a6e] text-white border-[#1e3a6e]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="px-4 py-1.5 text-xs font-bold rounded-full border transition-colors">
                            Semua Guru
                        </button>
                        <button @click="quickFilterGuru = 'hadir_tanpa_kelas'" :class="quickFilterGuru === 'hadir_tanpa_kelas' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-red-50 hover:text-red-600'" class="px-4 py-1.5 text-xs font-bold rounded-full border transition-colors">
                            <span class="mr-1">🔴</span> Hadir Tapi Belum Ada Kelas
                        </button>
                        <button @click="quickFilterGuru = 'terlambat'" :class="quickFilterGuru === 'terlambat' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-slate-600 border-slate-200 hover:bg-amber-50 hover:text-amber-600'" class="px-4 py-1.5 text-xs font-bold rounded-full border transition-colors">
                            <span class="mr-1">🟡</span> Terlambat Datang
                        </button>
                        <button @click="quickFilterGuru = 'tidak_hadir'" :class="quickFilterGuru === 'tidak_hadir' ? 'bg-slate-600 text-white border-slate-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100'" class="px-4 py-1.5 text-xs font-bold rounded-full border transition-colors">
                            <span class="mr-1">⚪</span> Tidak Hadir (Izin/Sakit/Alpa)
                        </button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full md:table-fixed text-left text-sm text-slate-600">
                    <thead class="hidden md:table-header-group bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                        <tr>
                            <th class="px-6 py-4 w-[25%]">Nama Guru</th>
                            <th class="px-6 py-4 w-[12%]">Status</th>
                            <th class="px-6 py-4 w-[13%]">Kategori</th>
                            <th class="px-6 py-4 w-[10%]">Datang</th>
                            <th class="px-6 py-4 w-[10%]">Pulang</th>
                            <th class="px-6 py-4 w-[30%]">Aktivitas Mengajar</th>
                        </tr>
                    </thead>
                    <tbody class="block md:table-row-group divide-y-0 md:divide-y divide-slate-100 space-y-4 md:space-y-0 p-4 md:p-0 bg-slate-50/50 md:bg-transparent">
                        @foreach ($listGuru as $guru)
                            @php
                                $absenSekolah = $guru->absensiGuru->first();
                                $statusHadir = $absenSekolah ? $absenSekolah->status : 'belum absen';
                                $kategoriAbsen = $absenSekolah ? $absenSekolah->kategori : '';
                                $jumlahKelas = $guru->aktivitasMengajar->count();

                                $isHadirTanpaKelas = ($statusHadir === 'hadir' && $jumlahKelas === 0) ? 'true' : 'false';
                                $isTerlambat = ($kategoriAbsen === 'Terlambat') ? 'true' : 'false';
                                $isTidakHadir = (in_array($statusHadir, ['izin', 'sakit', 'alpa', 'cuti', 'tugas', 'belum absen'])) ? 'true' : 'false';
                            @endphp
                            <tr class="block md:table-row bg-white md:bg-transparent border border-slate-200 md:border-0 rounded-xl md:rounded-none overflow-hidden hover:bg-blue-50/30 transition-colors" :class="{'!hidden': !(
                                (searchGuru === '' || '{!! addslashes(strtolower($guru->name)) !!}'.includes(searchGuru.toLowerCase()) || '{!! addslashes(strtolower($statusHadir)) !!}'.includes(searchGuru.toLowerCase())) &&
                                (quickFilterGuru === 'semua' ||
                                (quickFilterGuru === 'hadir_tanpa_kelas' && {{ $isHadirTanpaKelas }}) ||
                                (quickFilterGuru === 'terlambat' && {{ $isTerlambat }}) ||
                                (quickFilterGuru === 'tidak_hadir' && {{ $isTidakHadir }}))
                            )}">
                                <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 font-semibold text-slate-800 border-b border-slate-100 md:border-0 bg-slate-50 md:bg-transparent">
                                    <span class="md:hidden text-xs font-normal text-slate-500">Nama Guru</span>
                                    <span>{{ $guru->name }}</span>
                                </td>
                                <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-dashed border-slate-100 md:border-0">
                                    <span class="md:hidden text-xs font-normal text-slate-500">Status</span>
                                    <div>
                                    @if ($statusHadir === 'hadir')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-700">Hadir</span>
                                    @elseif (in_array($statusHadir, ['izin', 'sakit', 'dinas', 'cuti', 'tugas']))
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700">{{ $statusHadir }}</span>
                                    @elseif ($statusHadir === 'alpa')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700">Alpa</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500">Belum</span>
                                    @endif
                                    </div>
                                </td>
                                <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-dashed border-slate-100 md:border-0">
                                    <span class="md:hidden text-xs font-normal text-slate-500">Kategori</span>
                                    <div>
                                    @if($kategoriAbsen === 'Tepat Waktu')
                                        <span class="text-xs font-bold text-emerald-600">Tepat Waktu</span>
                                    @elseif($kategoriAbsen === 'Terlambat')
                                        <span class="text-xs font-bold text-red-500">Terlambat</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                    </div>
                                </td>
                                <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-dashed border-slate-100 md:border-0 font-mono text-xs">
                                    <span class="md:hidden text-xs font-sans font-normal text-slate-500">Datang</span>
                                    <span>{{ $absenSekolah->waktu_datang ?? '-' }}</span>
                                </td>
                                <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-dashed border-slate-100 md:border-0 font-mono text-xs">
                                    <span class="md:hidden text-xs font-sans font-normal text-slate-500">Pulang</span>
                                    <span>{{ $absenSekolah->waktu_pulang ?? '-' }}</span>
                                </td>
                                <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4">
                                    <div class="md:hidden text-xs font-normal text-slate-500 mb-2">Aktivitas Mengajar</div>
                                    @if($guru->aktivitasMengajar->isEmpty())
                                        <span class="text-xs text-slate-400 italic">Tidak ada jadwal</span>
                                    @else
                                        <div class="flex flex-wrap gap-2">
                                        @foreach($guru->aktivitasMengajar as $mengajar)
                                            <div class="px-3 py-1.5 rounded-lg border text-[11px] {{ $mengajar->waktu_absen_keluar ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-blue-50 border-blue-200 text-blue-800' }}" title="{{ $mengajar->mata_pelajaran }}">
                                                <span class="font-bold">{{ $mengajar->kelas }}</span> 
                                                (Jam {{ $mengajar->jam_ke }})<br>
                                                Masuk: {{ $mengajar->waktu_absen_masuk ?? '-' }} | Keluar: {{ $mengajar->waktu_absen_keluar ?? '-' }}
                                            </div>
                                        @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB: DETAIL SISWA --}}
        <div x-show="tab === 'detail_siswa'" class="bg-white rounded-2xl border border-slate-200 overflow-hidden" style="display: none;" x-data="{ searchSiswa: '', quickFilterSiswa: 'semua' }">
            <div class="px-6 py-4 border-b border-slate-100 bg-emerald-50/50 flex flex-col gap-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="font-bold text-emerald-900">
                        Kehadiran Murid 
                        <span class="text-sm font-normal text-slate-500 ml-1">({{ $detailDate->translatedFormat('d F Y') }})</span>
                    </h3>
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <form method="GET" action="{{ route('monitoring-sekolah.dashboard') }}" class="flex items-center gap-2">
                            <input type="hidden" name="tab" value="detail_siswa">
                            <input type="date" name="tanggal" value="{{ $tanggal_detail }}" onchange="this.form.submit()" class="text-sm border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" title="Pilih Tanggal">
                        </form>
                        <div class="relative w-full md:w-64">
                            <input type="text" x-model="searchSiswa" :placeholder="window.innerWidth < 768 ? 'Cari nama/status...' : 'Cari nama, kelas, atau status...'" class="w-full text-sm border-slate-300 rounded-lg pl-9 focus:ring-emerald-500 focus:border-emerald-500">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- Quick Filters --}}
                <div class="mb-2 md:mb-0">
                    {{-- Mobile Dropdown --}}
                    <select x-model="quickFilterSiswa" class="md:hidden w-full text-sm font-bold text-slate-700 border-slate-300 rounded-lg focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                        <option value="semua">Semua Murid</option>
                        <option value="indikasi_cabut">🔴 Indikasi Bolos Kelas</option>
                        <option value="bolos_sholat">🟡 Hadir Tapi Bolos Sholat</option>
                        <option value="tidak_hadir">⚪ Tidak Hadir Sekolah</option>
                    </select>

                    {{-- Desktop Pills --}}
                    <div class="hidden md:flex flex-wrap gap-2">
                        <button @click="quickFilterSiswa = 'semua'" :class="quickFilterSiswa === 'semua' ? 'bg-[#1e3a6e] text-white border-[#1e3a6e]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="px-4 py-1.5 text-xs font-bold rounded-full border transition-colors">
                            Semua Murid
                        </button>
                        <button @click="quickFilterSiswa = 'indikasi_cabut'" :class="quickFilterSiswa === 'indikasi_cabut' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-red-50 hover:text-red-600'" class="px-4 py-1.5 text-xs font-bold rounded-full border transition-colors">
                            <span class="mr-1">🔴</span> Indikasi Bolos Kelas
                        </button>
                        <button @click="quickFilterSiswa = 'bolos_sholat'" :class="quickFilterSiswa === 'bolos_sholat' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-slate-600 border-slate-200 hover:bg-amber-50 hover:text-amber-600'" class="px-4 py-1.5 text-xs font-bold rounded-full border transition-colors">
                            <span class="mr-1">🟡</span> Hadir Tapi Bolos Sholat
                        </button>
                        <button @click="quickFilterSiswa = 'tidak_hadir'" :class="quickFilterSiswa === 'tidak_hadir' ? 'bg-slate-600 text-white border-slate-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100'" class="px-4 py-1.5 text-xs font-bold rounded-full border transition-colors">
                            <span class="mr-1">⚪</span> Tidak Hadir Sekolah
                        </button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="hidden md:table-header-group bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Nama Murid</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4">Absen Sekolah</th>
                            <th class="px-6 py-4">Kehadiran Kelas</th>
                            <th class="px-6 py-4">Sholat</th>
                        </tr>
                    </thead>
                    <tbody class="block md:table-row-group divide-y-0 md:divide-y divide-slate-100 space-y-4 md:space-y-0 p-4 md:p-0 bg-emerald-50/50 md:bg-transparent">
                        @foreach ($listSiswa as $siswa)
                            @php
                                $absenSekolah = $siswa->absensiSiswa->first();
                                $statusSekolah = $absenSekolah ? $absenSekolah->status : 'belum absen';
                                $jmlKelasHadir = $siswa->absensiKelasSiswa->where('status', 'hadir')->count();
                                $absenSholat = $siswa->absensiSholat->first();
                                $agama = $siswa->siswaProfile->agama ?? '-';
                                
                                $className = trim(($siswa->siswaProfile->kelas ?? '') . ' ' . ($siswa->siswaProfile->jurusan ?? '') . ' ' . ($siswa->siswaProfile->rombel ?? ''));
                                
                                $isIndikasiCabut = ($statusSekolah === 'hadir' && $jmlKelasHadir === 0) ? 'true' : 'false';
                                
                                $isBolosSholat = 'false';
                                if ($statusSekolah === 'hadir' && strtolower(trim($agama)) === 'islam') {
                                    if (!$absenSholat || !in_array($absenSholat->status, ['hadir', 'berjamaah', 'haid'])) {
                                        $isBolosSholat = 'true';
                                    }
                                }
                                
                                $isTidakHadir = (in_array($statusSekolah, ['izin', 'sakit', 'alpa', 'belum absen'])) ? 'true' : 'false';
                            @endphp
                            <tr class="block md:table-row bg-white md:bg-transparent border border-slate-200 md:border-0 rounded-xl md:rounded-none overflow-hidden hover:bg-emerald-50/30 transition-colors" :class="{'!hidden': !(
                                (searchSiswa === '' || '{!! addslashes(strtolower($siswa->name)) !!}'.includes(searchSiswa.toLowerCase()) || '{!! addslashes(strtolower($className)) !!}'.includes(searchSiswa.toLowerCase()) || '{!! addslashes(strtolower($statusSekolah)) !!}'.includes(searchSiswa.toLowerCase())) &&
                                (quickFilterSiswa === 'semua' ||
                                (quickFilterSiswa === 'indikasi_cabut' && {{ $isIndikasiCabut }}) ||
                                (quickFilterSiswa === 'bolos_sholat' && {{ $isBolosSholat }}) ||
                                (quickFilterSiswa === 'tidak_hadir' && {{ $isTidakHadir }}))
                            )}">
                                <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 font-semibold text-slate-800 border-b border-slate-100 md:border-0 bg-emerald-50 md:bg-transparent">
                                    <span class="md:hidden text-xs font-normal text-slate-500">Nama Murid</span>
                                    <span>{{ $siswa->name }}</span>
                                </td>
                                <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-dashed border-slate-100 md:border-0 text-xs font-bold text-slate-500">
                                    <span class="md:hidden text-xs font-normal text-slate-500">Kelas</span>
                                    <span>{{ $siswa->siswaProfile->kelas ?? '-' }} {{ $siswa->siswaProfile->jurusan ?? '' }} {{ $siswa->siswaProfile->rombel ?? '' }}</span>
                                </td>
                                <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-dashed border-slate-100 md:border-0">
                                    <span class="md:hidden text-xs font-normal text-slate-500">Absen Sekolah</span>
                                    <div class="text-right md:text-left">
                                    @if ($statusSekolah === 'hadir')
                                        <span class="text-emerald-600 font-bold">Hadir</span><br>
                                        <span class="text-[10px] text-slate-400">M: {{ $absenSekolah->waktu_datang ?? '-' }} | K: {{ $absenSekolah->waktu_pulang ?? '-' }}</span>
                                    @elseif (in_array($statusSekolah, ['izin', 'sakit']))
                                        <span class="text-amber-600 font-bold">{{ ucfirst($statusSekolah) }}</span>
                                    @else
                                        <span class="text-slate-400 font-medium">Alpa</span>
                                    @endif
                                    </div>
                                </td>
                                <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-dashed border-slate-100 md:border-0">
                                    <span class="md:hidden text-xs font-normal text-slate-500">Kehadiran Kelas</span>
                                    <div>
                                    @if($jmlKelasHadir > 0)
                                        <span class="text-xs font-bold text-[#1e3a6e]">{{ $jmlKelasHadir }} Jam Pelajaran</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                    </div>
                                </td>
                                <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-dashed border-slate-100 md:border-0">
                                    <span class="md:hidden text-xs font-normal text-slate-500">Sholat</span>
                                    <div>
                                    @if($siswa->siswaProfile && strtolower(trim($siswa->siswaProfile->agama)) === 'islam')
                                        @if($absenSholat && in_array($absenSholat->status, ['hadir', 'berjamaah']))
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-lg uppercase">Sholat</span>
                                        @elseif($absenSholat && $absenSholat->status === 'haid')
                                            <span class="px-2 py-1 bg-pink-100 text-pink-700 text-[10px] font-bold rounded-lg uppercase">Haid</span>
                                        @else
                                            <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-lg uppercase">Tidak</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400 italic">Non-Muslim</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Chart.js integration --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('kehadiranChart').getContext('2d');
            const chartData = @json($chartData);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Murid Hadir',
                            data: chartData.siswa,
                            borderColor: '#10b981', // emerald-500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#10b981'
                        },
                        {
                            label: 'Guru Hadir',
                            data: chartData.guru,
                            borderColor: '#3b82f6', // blue-500
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#3b82f6'
                        },
                        {
                            label: 'Murid Sholat',
                            data: chartData.sholat,
                            borderColor: '#a855f7', // purple-500
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#a855f7'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: "'Inter', sans-serif",
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: {
                                family: "'Inter', sans-serif",
                                size: 13
                            },
                            bodyFont: {
                                family: "'Inter', sans-serif",
                                size: 13
                            },
                            padding: 12,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif"
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        });
    </script>
</x-app-layout>
