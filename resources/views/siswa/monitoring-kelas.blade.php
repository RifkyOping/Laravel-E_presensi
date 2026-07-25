<x-app-layout pageTitle="Monitoring Kelas" pageSubtitle="Lihat jadwal pelajaran, status kehadiran guru, dan status absensi Anda">

    <div class="space-y-6">

        @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Jadwal & Monitoring Kelas</h2>
                    <p class="text-sm text-slate-500 mt-1">Status guru dan absen Anda hanya muncul pada jadwal hari ini (<strong>{{ $hariIniStr }}</strong>).</p>
                </div>
            </div>

            {{-- Tabs --}}
            @php
                $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            @endphp

            <div class="-mx-1 overflow-x-auto pb-1 mb-6 border-b border-slate-200">
                <div class="flex gap-2 px-1 min-w-max">
                    @foreach($hariList as $hari)
                        <a href="{{ route('murid.monitoring-kelas', ['hari' => $hari]) }}"
                           class="px-4 py-2 rounded-xl font-semibold text-sm transition-colors duration-200 whitespace-nowrap flex-shrink-0
                               {{ $activeTab === $hari ? 'bg-[#1e3a6e] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $hari }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Content per Hari --}}
            @php $jadwalHariIni = $jadwalList[$activeTab] ?? []; @endphp

            @if(count($jadwalHariIni) === 0)
                <div class="text-center py-16 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-slate-500 font-semibold">Tidak ada jadwal pelajaran di hari <strong>{{ $activeTab }}</strong></p>
                </div>
            @else
                {{-- Mobile: Card --}}
                <div class="block sm:hidden space-y-4">
                    @foreach($jadwalHariIni as $j)
                        @php
                            $isToday = $activeTab === $hariIniStr;
                            $guruStatus = null;
                            $muridStatus = null;
                            
                            if ($isToday) {
                                $key = $j->user_id . '_' . $j->mata_pelajaran . '_' . $j->jam_ke;
                                $guruMasuk = isset($absensiMengajar[$key]);
                                $wMasuk = null;
                                $guruKeluar = false;
                                $wKeluar = null;
                                $kondisiLabel = 'Belum Mulai';
                                $kondisiColor = 'bg-slate-100 text-slate-500 border-slate-200';
                                
                                if ($guruMasuk) {
                                    $wMasuk = \Carbon\Carbon::parse($absensiMengajar[$key]->waktu_absen_masuk)->format('H:i');
                                    if ($absensiMengajar[$key]->waktu_absen_keluar) {
                                        $guruKeluar = true;
                                        $wKeluar = \Carbon\Carbon::parse($absensiMengajar[$key]->waktu_absen_keluar)->format('H:i');
                                        $kondisiLabel = 'Selesai';
                                        $kondisiColor = 'bg-green-50 text-green-600 border-green-200';
                                    } else {
                                        $kondisiLabel = 'Sedang Berlangsung';
                                        $kondisiColor = 'bg-blue-50 text-blue-600 border-blue-200';
                                    }
                                }

                                $absenKelas = $absensiKelas[$j->id] ?? null;
                                if ($absenKelas) {
                                    $muridStatus = $absenKelas->status;
                                }
                            }
                        @endphp
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jam ke-{{ $j->jam_ke }}</span>
                                <span class="text-xs font-semibold text-[#1e3a6e] bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full">
                                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                                    @if($j->jam_selesai) – {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} @endif
                                </span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-base mb-0.5 flex flex-wrap items-center gap-2">
                                    {{ $j->mata_pelajaran }}
                                    @if($isToday)
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wider {{ $kondisiColor }}">
                                        {{ $kondisiLabel }}
                                    </span>
                                    @endif
                                </p>
                                <p class="text-sm text-slate-500 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ $j->user->name ?? 'Guru Tidak Diketahui' }}
                                </p>
                            </div>
                            @if($isToday)
                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Masuk</p>
                                    @if($guruMasuk)
                                        <span class="text-xs font-semibold text-green-600 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $wMasuk }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">-</span>
                                    @endif
                                </div>
                                <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Keluar</p>
                                    @if($guruKeluar)
                                        <span class="text-xs font-semibold text-blue-600 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            {{ $wKeluar }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">-</span>
                                    @endif
                                </div>
                                <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Absen Anda</p>
                                    @if($muridStatus)
                                        @php
                                            $mColor = match(strtolower($muridStatus)) {
                                                'hadir' => 'text-green-600',
                                                'izin', 'sakit' => 'text-amber-600',
                                                'alpa' => 'text-red-600',
                                                default => 'text-slate-600'
                                            };
                                        @endphp
                                        <span class="text-xs font-semibold {{ $mColor }} capitalize">{{ $muridStatus }}</span>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">Belum Diabsen</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Desktop: Table --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                                <th class="p-3 font-semibold text-center w-20 whitespace-nowrap">Jam Ke-</th>
                                <th class="p-3 font-semibold text-center w-32 whitespace-nowrap">Waktu</th>
                                <th class="p-3 font-semibold min-w-[180px]">Mata Pelajaran</th>
                                <th class="p-3 font-semibold min-w-[150px]">Guru</th>
                                @if($activeTab === $hariIniStr)
                                <th class="p-3 font-semibold text-center w-24 whitespace-nowrap">Masuk</th>
                                <th class="p-3 font-semibold text-center w-24 whitespace-nowrap">Keluar</th>
                                <th class="p-3 font-semibold text-center w-28 whitespace-nowrap">Absen Anda</th>
                                <th class="p-3 font-semibold text-center w-36 whitespace-nowrap">Status</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($jadwalHariIni as $j)
                                @php
                                    $isToday = $activeTab === $hariIniStr;
                                    $guruStatus = null;
                                    $muridStatus = null;
                                    $guruMasuk = false;
                                    
                                    if ($isToday) {
                                        $key = $j->user_id . '_' . $j->mata_pelajaran . '_' . $j->jam_ke;
                                        $guruMasuk = isset($absensiMengajar[$key]);
                                        $wMasuk = null;
                                        $guruKeluar = false;
                                        $wKeluar = null;
                                        $kondisiLabel = 'Belum Mulai';
                                        $kondisiColor = 'bg-slate-100 text-slate-500 border-slate-200';
                                        
                                        if ($guruMasuk) {
                                            $wMasuk = \Carbon\Carbon::parse($absensiMengajar[$key]->waktu_absen_masuk)->format('H:i');
                                            if ($absensiMengajar[$key]->waktu_absen_keluar) {
                                                $guruKeluar = true;
                                                $wKeluar = \Carbon\Carbon::parse($absensiMengajar[$key]->waktu_absen_keluar)->format('H:i');
                                                $kondisiLabel = 'Selesai';
                                                $kondisiColor = 'bg-green-50 text-green-600 border-green-200';
                                            } else {
                                                $kondisiLabel = 'Sedang Berlangsung';
                                                $kondisiColor = 'bg-blue-50 text-blue-600 border-blue-200';
                                            }
                                        }

                                        $absenKelas = $absensiKelas[$j->id] ?? null;
                                        if ($absenKelas) {
                                            $muridStatus = $absenKelas->status;
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50 transition duration-150 border-b border-slate-100 last:border-b-0">
                                    <td class="p-3 text-center align-middle">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#1e3a6e] text-white font-bold text-sm">
                                            {{ $j->jam_ke }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center text-slate-600 font-semibold whitespace-nowrap align-middle">
                                        {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                                        @if($j->jam_selesai) – {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} @endif
                                    </td>
                                    <td class="p-3 font-bold text-slate-800 align-middle">{{ $j->mata_pelajaran }}</td>
                                    <td class="p-3 text-slate-600 font-medium align-middle">{{ $j->user->name ?? '-' }}</td>
                                    @if($isToday)
                                    <td class="p-3 text-center whitespace-nowrap align-middle">
                                        @if($guruMasuk)
                                            <span class="inline-flex px-2 py-1 bg-green-50 text-green-600 rounded-lg text-xs font-bold border border-green-200">
                                                {{ $wMasuk }}
                                            </span>
                                        @else
                                            <span class="text-xs font-medium text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap align-middle">
                                        @if($guruKeluar)
                                            <span class="inline-flex px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold border border-blue-200">
                                                {{ $wKeluar }}
                                            </span>
                                        @else
                                            <span class="text-xs font-medium text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap align-middle">
                                        @if($muridStatus)
                                            @php
                                                $bgCol = match(strtolower($muridStatus)) {
                                                    'hadir' => 'bg-green-100 text-green-700',
                                                    'izin', 'sakit' => 'bg-amber-100 text-amber-700',
                                                    'alpa' => 'bg-red-100 text-red-700',
                                                    default => 'bg-slate-100 text-slate-700'
                                                };
                                            @endphp
                                            <span class="inline-flex px-3 py-1 {{ $bgCol }} rounded-full text-xs font-bold capitalize">
                                                {{ $muridStatus }}
                                            </span>
                                        @else
                                            <span class="text-xs font-medium text-slate-400">Belum Diabsen</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap align-middle">
                                        <span class="inline-flex px-2 py-1 rounded text-[10px] font-bold border uppercase tracking-wider {{ $kondisiColor }}">
                                            {{ $kondisiLabel }}
                                        </span>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
