<x-app-layout pageTitle="Monitoring Kelas" pageSubtitle="Lihat jadwal pelajaran, status kehadiran guru, dan status absensi Anda">
    <x-slot name="header">
        <p class="text-sm font-bold text-slate-700">Monitoring Kelas</p>
    </x-slot>

    <div class="space-y-6">

        @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100" x-data="{ activeTab: '{{ $activeTab }}', expandedCard: null }">
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="w-full">
                    <h2 class="text-lg font-bold text-slate-800">Jadwal & Monitoring Kelas</h2>
                    <p class="text-sm text-slate-500 mt-1">Status kehadiran guru dan riwayat absen Anda ditampilkan untuk minggu ini.</p>
                </div>
            </div>

            {{-- Tabs --}}
            @php
                $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            @endphp

            <div class="-mx-1 overflow-x-auto pb-1 mb-6 border-b border-slate-200 custom-scrollbar">
                <div class="flex gap-2 px-1 min-w-max pb-2">
                    @foreach($hariList as $hari)
                        <button type="button" @click="activeTab = '{{ $hari }}'"
                           :class="activeTab === '{{ $hari }}' ? 'bg-[#1e3a6e] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                           class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 whitespace-nowrap flex-shrink-0">
                            {{ $hari }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Content per Hari --}}
            <div class="min-h-[250px]" id="monitoring-content-container">
            @foreach($hariList as $hari)
                @php $jadwalHariIni = $jadwalList[$hari] ?? []; @endphp

                <div x-show="activeTab === '{{ $hari }}'" style="display: none;" x-init="if(activeTab === '{{ $hari }}') $el.style.display = 'block'">
                    @if(count($jadwalHariIni) === 0)
                        <div class="text-center py-16 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-slate-500 font-semibold">Tidak ada jadwal pelajaran di hari <strong>{{ $hari }}</strong></p>
                        </div>
                    @else
                        {{-- Mobile: Card --}}
                        <div class="block sm:hidden space-y-4">
                            @foreach($jadwalHariIni as $j)
                                @php
                                    $isToday = $hari === $hariIniStr;
                                    $guruStatus = null;
                                    $muridStatus = null;
                                    
                                    $key = $hari . '_' . $j->user_id . '_' . $j->mata_pelajaran . '_' . $j->jam_ke;
                                    $guruMasuk = isset($absensiMengajar[$key]);
                                    $wMasuk = null;
                                    $guruKeluar = false;
                                    $wKeluar = null;
                                    $kondisiLabel = 'Belum Mulai';
                                    $kondisiColor = 'bg-slate-100 text-slate-500 border-slate-200';
                                    if ($guruMasuk && $absensiMengajar[$key]->waktu_absen_masuk) {
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
                                    } else {
                                        $guruMasuk = false;
                                    }

                                    $absenKelas = $absensiKelas[$hari . '_' . $j->id] ?? null;
                                    if ($absenKelas) {
                                        $muridStatus = $absenKelas->status;
                                    }
                                @endphp
                                <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 shadow-sm">
                                    <div @click="expandedCard = expandedCard === '{{ $j->id }}' ? null : '{{ $j->id }}'" class="p-4 cursor-pointer hover:bg-slate-100 transition-colors">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                    <span class="bg-[#1e3a6e]/10 text-[#1e3a6e] text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">Jam ke-{{ $j->jam_ke }}</span>
                                                    <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 shadow-sm px-2 py-0.5 rounded-md">
                                                        {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                                                        @if($j->jam_selesai) - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} @endif
                                                    </span>
                                                </div>
                                                <h3 class="font-bold text-slate-800 text-base leading-tight mb-1">{{ $j->mata_pelajaran }}</h3>
                                                <p class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    {{ $j->user->name ?? 'Guru Tidak Diketahui' }}
                                                </p>
                                            </div>
                                            <div class="flex flex-col items-end flex-shrink-0">
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold border uppercase tracking-wider mb-2 text-center {{ $kondisiColor }}">
                                                    {{ $kondisiLabel }}
                                                </span>
                                                <div class="w-6 h-6 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm">
                                                    <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="expandedCard === '{{ $j->id }}' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="expandedCard === '{{ $j->id }}'" style="display: none;" class="px-4 pb-4 pt-3 border-t border-slate-200 bg-white">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Detail Absensi Sesi Ini</p>
                                        <div class="flex items-center justify-between mt-1 px-1">
                                            <div class="flex flex-col">
                                                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Masuk</p>
                                                @if($guruMasuk)
                                                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $wMasuk }}
                                                    </span>
                                                @else
                                                    <span class="text-xs font-semibold text-slate-400">-</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Keluar</p>
                                                @if($guruKeluar)
                                                    <span class="text-xs font-bold text-blue-600 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                                        {{ $wKeluar }}
                                                    </span>
                                                @else
                                                    <span class="text-xs font-semibold text-slate-400">-</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Absen</p>
                                                @if($muridStatus)
                                                    @php
                                                        $mColor = match(strtolower($muridStatus)) {
                                                            'hadir' => 'text-emerald-600',
                                                            'izin', 'sakit' => 'text-amber-600',
                                                            'alpa' => 'text-red-600',
                                                            default => 'text-slate-600'
                                                        };
                                                    @endphp
                                                    <span class="text-xs font-bold {{ $mColor }} capitalize">{{ $muridStatus }}</span>
                                                @else
                                                    <span class="text-xs font-semibold text-slate-400 leading-tight">Belum Diabsen</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
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
                                        <th class="p-3 font-semibold text-center w-24 whitespace-nowrap">Masuk</th>
                                        <th class="p-3 font-semibold text-center w-24 whitespace-nowrap">Keluar</th>
                                        <th class="p-3 font-semibold text-center w-28 whitespace-nowrap">Absen Anda</th>
                                        <th class="p-3 font-semibold text-center w-36 whitespace-nowrap">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($jadwalHariIni as $j)
                                        @php
                                            $isToday = $hari === $hariIniStr;
                                            $guruStatus = null;
                                            $muridStatus = null;
                                            $guruMasuk = false;
                                            
                                            $key = $hari . '_' . $j->user_id . '_' . $j->mata_pelajaran . '_' . $j->jam_ke;
                                            $guruMasuk = isset($absensiMengajar[$key]);
                                            $wMasuk = null;
                                            $guruKeluar = false;
                                            $wKeluar = null;
                                            $kondisiLabel = 'Belum Mulai';
                                            $kondisiColor = 'bg-slate-100 text-slate-500 border-slate-200';
                                            
                                            if ($guruMasuk && $absensiMengajar[$key]->waktu_absen_masuk) {
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
                                            } else {
                                                $guruMasuk = false;
                                            }

                                            $absenKelas = $absensiKelas[$hari . '_' . $j->id] ?? null;
                                            if ($absenKelas) {
                                                $muridStatus = $absenKelas->status;
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
            </div>
            
            <style>
                .custom-scrollbar::-webkit-scrollbar { height: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
            </style>
        </div>
    </div>

</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Poll every 5 seconds to get the latest monitoring data
        setInterval(() => {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('monitoring-content-container');
                const oldContent = document.getElementById('monitoring-content-container');
                
                if (newContent && oldContent) {
                    oldContent.innerHTML = newContent.innerHTML;
                }
            })
            .catch(err => console.error('Error fetching latest data:', err));
        }, 5000);
    });
</script>
