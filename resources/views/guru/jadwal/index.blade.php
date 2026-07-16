<x-app-layout pageTitle="Jadwal Mengajar" pageSubtitle="Jadwal mengajar mingguan Anda yang ditetapkan oleh Admin">

    <div class="max-w-5xl mx-auto space-y-6">

        @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Jadwal Mengajar Mingguan</h2>
                    <p class="text-sm text-slate-500 mt-1">Jadwal ini ditetapkan oleh Admin. Hubungi Admin jika ada perubahan jadwal.</p>
                </div>
            </div>

            {{-- Tabs --}}
            @php
                $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $activeHari = request('hari', 'Senin');
            @endphp

            <div class="-mx-1 overflow-x-auto pb-1 mb-6 border-b border-slate-200">
                <div class="flex gap-2 px-1 min-w-max">
                    @foreach($hariList as $hari)
                        <a href="{{ route('guru.jadwal.index', ['hari' => $hari]) }}"
                           class="px-4 py-2 rounded-xl font-semibold text-sm transition-colors duration-200 whitespace-nowrap flex-shrink-0
                               {{ $activeHari === $hari ? 'bg-[#1e3a6e] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $hari }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Content per Hari --}}
            @php $jadwalHariIni = $jadwal[$activeHari] ?? []; @endphp

            @if(count($jadwalHariIni) === 0)
                <div class="text-center py-16 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-slate-500 font-semibold">Tidak ada jadwal di hari <strong>{{ $activeHari }}</strong></p>
                    <p class="text-slate-400 text-sm mt-1">Hubungi Admin jika jadwal belum diatur.</p>
                </div>
            @else
                {{-- Mobile: Card --}}
                <div class="block sm:hidden space-y-3">
                    @foreach($jadwalHariIni as $j)
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jam ke-{{ $j->jam_ke }}</span>
                                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}
                                    @if($j->jam_selesai) – {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} @endif
                                </span>
                            </div>
                            <p class="font-bold text-slate-800">{{ $j->mata_pelajaran }}</p>
                            <p class="text-sm text-slate-500">{{ $j->kelas }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Desktop: Table --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600">
                                <th class="p-3 font-semibold rounded-tl-lg w-16 text-center">Jam Ke-</th>
                                <th class="p-3 font-semibold">Mata Pelajaran</th>
                                <th class="p-3 font-semibold">Kelas</th>
                                <th class="p-3 font-semibold rounded-tr-lg w-40 text-center">Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($jadwalHariIni as $j)
                                <tr class="hover:bg-slate-50 transition duration-150">
                                    <td class="p-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#1e3a6e] text-white font-bold text-sm">
                                            {{ $j->jam_ke }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-semibold text-slate-800">{{ $j->mata_pelajaran }}</td>
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

            <div class="mt-6 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Jika ada perubahan jadwal, hubungi Admin untuk pembaruan.
                </p>
            </div>
        </div>
    </div>

</x-app-layout>