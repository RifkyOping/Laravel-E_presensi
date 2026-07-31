<x-app-layout pageTitle="Rekap Keseluruhan Jadwal">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.jadwal-mengajar.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <span class="text-sm font-bold text-slate-800">Rekap Keseluruhan Jadwal</span>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ activeTab: '{{ $activeTab }}' }">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-6">
                <div>
                    <h2 class="text-xl font-black text-slate-800">Jadwal Lengkap Sekolah</h2>
                    <p class="text-sm text-slate-500 mt-1">Lihat keseluruhan jadwal berdasarkan hari dan tipe blok.</p>
                </div>
                
                <div class="w-full lg:w-auto flex justify-start lg:justify-end">
                    <select x-model="filterBlok" class="border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-sm focus:border-[#1e3a6e] shrink-0 font-semibold text-slate-700 bg-white w-full sm:w-auto">
                        <option value="">Semua Tipe Blok</option>
                        <option value="A">Blok A</option>
                        <option value="B">Blok B</option>
                        <option value="Semua">Semua</option>
                    </select>
                </div>
            </div>

            {{-- Tabs Hari --}}
            <div class="-mx-1 overflow-x-auto pb-1 mb-6 border-b border-slate-200 custom-scrollbar">
                <div class="flex gap-2 px-1 min-w-max pb-2">
                    @foreach($hariList as $hari)
                        <button type="button" @click="activeTab = '{{ $hari }}'"
                           class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 whitespace-nowrap flex-shrink-0"
                           :class="activeTab === '{{ $hari }}' ? 'bg-[#1e3a6e] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                            {{ $hari }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Table Matrix per Hari --}}
            @foreach($hariList as $hari)
            <div x-show="activeTab === '{{ $hari }}'" style="display: none;" x-init="if(activeTab === '{{ $hari }}') $el.style.display = 'block'">
                <div class="overflow-x-auto custom-scrollbar border border-slate-200 rounded-xl">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="bg-slate-50 text-slate-700">
                                <th class="p-4 font-black border-b border-r border-slate-200 text-center sticky left-0 bg-slate-50 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] w-40">Kelas</th>
                                @for($i = 1; $i <= $maxJam; $i++)
                                    <th class="p-3 font-bold border-b border-r border-slate-200 text-center min-w-[200px] w-[200px]">Jam Ke-{{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($kelasList as $kelas)
                                @php
                                    $namaKelas = trim("{$kelas->tingkat} {$kelas->jurusan} {$kelas->rombel}");
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 font-black text-slate-800 border-r border-slate-200 text-center sticky left-0 bg-white z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] whitespace-nowrap">
                                        {{ $namaKelas }}
                                    </td>
                                    @for($i = 1; $i <= $maxJam; $i++)
                                        @php
                                            $jadwals = $matrix[$hari][$namaKelas][$i] ?? [];
                                        @endphp
                                        <td class="p-2 border-r border-slate-200 align-top">
                                            @if(count($jadwals) > 0)
                                                <div class="space-y-2">
                                                    @foreach($jadwals as $j)
                                                        <div x-show="filterBlok === '' || filterBlok === '{{ $j->tipe_blok }}'" class="bg-slate-50 border border-slate-200 rounded-lg p-2.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 relative group cursor-default">
                                                            <div class="flex items-start justify-between gap-1 mb-1.5">
                                                                <p class="text-xs font-black text-slate-800 leading-tight line-clamp-2" title="{{ $j->mata_pelajaran }}">{{ $j->mata_pelajaran }}</p>
                                                                @if($j->tipe_blok === 'A')
                                                                    <span class="inline-flex flex-shrink-0 text-[9px] font-black px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">A</span>
                                                                @elseif($j->tipe_blok === 'B')
                                                                    <span class="inline-flex flex-shrink-0 text-[9px] font-black px-1.5 py-0.5 rounded bg-purple-100 text-purple-700">B</span>
                                                                @else
                                                                    <span class="inline-flex flex-shrink-0 text-[9px] font-black px-1.5 py-0.5 rounded bg-slate-200 text-slate-600">S</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-[10px] font-semibold text-slate-500 flex items-center gap-1 leading-tight line-clamp-1" title="{{ $j->user->name ?? '-' }}">
                                                                <svg class="w-3 h-3 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                                {{ $j->user->name ?? '-' }}
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="h-full min-h-[4rem] flex items-center justify-center">
                                                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">-</span>
                                                </div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $maxJam + 1 }}" class="p-8 text-center text-slate-500 font-semibold">Tidak ada data kelas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

            <style>
                .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 8px; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 8px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
            </style>
        </div>
    </div>
</x-app-layout>
