<x-app-layout pageTitle="Jadwal Mengajar" pageSubtitle="Jadwal mengajar Anda yang ditetapkan oleh Admin">
    <x-slot name="header">
        <h2 class="text-sm font-bold text-slate-700">Jadwal Mengajar</h2>
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="w-full">
                    <p class="text-sm text-amber-600 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 flex items-start sm:items-center gap-3 font-medium">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Jadwal ini ditetapkan oleh Admin. Hubungi Admin jika ada perubahan jadwal.
                    </p>
                </div>
            </div>

            {{-- Tabs --}}
            @php
                $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                $hariMap = [
                    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                ];
                $hariIni = $hariMap[now()->format('l')] ?? 'Senin';
                if (!in_array($hariIni, $hariList)) {
                    $hariIni = 'Senin';
                }
            @endphp

            <div x-data="{ activeHari: '{{ $hariIni }}' }">
                <div class="-mx-1 overflow-x-auto pb-1 mb-6 border-b border-slate-200">
                    <div class="flex gap-2 px-1 min-w-max">
                        @foreach($hariList as $hari)
                            <button type="button" @click="activeHari = '{{ $hari }}'"
                               class="px-4 py-2 rounded-xl font-semibold text-sm transition-colors duration-200 whitespace-nowrap flex-shrink-0"
                               :class="activeHari === '{{ $hari }}' ? 'bg-[#1e3a6e] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                {{ $hari }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Content per Hari --}}
                @foreach($hariList as $hari)
                    @php $jadwalHari = $jadwal[$hari] ?? []; @endphp
                    
                    <div x-show="activeHari === '{{ $hari }}'" {!! $hariIni === $hari ? '' : 'style="display: none;"' !!}>
                        @if(count($jadwalHari) === 0)
                            <div class="text-center py-16 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                                <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-slate-500 font-semibold">Tidak ada jadwal di hari <strong>{{ $hari }}</strong></p>
                                <p class="text-slate-400 text-sm mt-1">Hubungi Admin jika jadwal belum diatur.</p>
                            </div>
                        @else
                            {{-- Mobile: Card --}}
                            <div class="block sm:hidden space-y-3">
                                @foreach($jadwalHari as $j)
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mapel ke-{{ $j->jam_ke }}</span>
                                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                                {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                                                @if($j->jam_selesai) – {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} @endif
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <p class="font-bold text-slate-800">{{ $j->mata_pelajaran }}</p>
                                            @if($j->tipe_blok === 'A')
                                                <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-700">Blok A</span>
                                            @elseif($j->tipe_blok === 'B')
                                                <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-md bg-purple-100 text-purple-700">Blok B</span>
                                            @else
                                                <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">Semua Blok</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-slate-500">{{ $j->kelas }}</p>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desktop: Table --}}
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="w-full text-left text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 text-slate-600">
                                            <th class="p-3 font-semibold rounded-tl-lg w-16 text-center">Mapel Ke-</th>
                                            <th class="p-3 font-semibold">Mata Pelajaran</th>
                                            <th class="p-3 font-semibold">Kelas</th>
                                            <th class="p-3 font-semibold rounded-tr-lg w-40 text-center">Jam</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($jadwalHari as $j)
                                            <tr class="hover:bg-slate-50 transition duration-150">
                                                <td class="p-3 text-center">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#1e3a6e] text-white font-bold text-sm">
                                                        {{ $j->jam_ke }}
                                                    </span>
                                                </td>
                                                <td class="p-3">
                                                    <div class="font-semibold text-slate-800">{{ $j->mata_pelajaran }}</div>
                                                    @if($j->tipe_blok === 'A')
                                                        <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 mt-1 inline-block">Blok A</span>
                                                    @elseif($j->tipe_blok === 'B')
                                                        <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 mt-1 inline-block">Blok B</span>
                                                    @else
                                                        <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 mt-1 inline-block">Semua Blok</span>
                                                    @endif
                                                </td>
                                                <td class="p-3 text-slate-600">{{ $j->kelas }}</td>
                                                <td class="p-3 text-center text-slate-600 font-medium">
                                                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                                                    @if($j->jam_selesai) – {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} @endif
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

        </div>
    </div>

</x-app-layout>