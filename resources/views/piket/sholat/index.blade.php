<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Piket Absen Sholat Murid</span>
    </x-slot>

    <div class="max-w-7xl space-y-6">
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl text-sm font-medium flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Pencatatan Absensi Sholat</h3>
                    <p class="text-xs text-slate-500 mt-1">Pilih kelas dan tanggal untuk mulai mencatat kehadiran sholat.</p>
                </div>
            </div>
            
            <div class="p-6 bg-slate-50 border-b border-slate-100">
                <form method="GET" action="{{ route('piket.sholat.index') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}" 
                               class="border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white min-w-[150px]">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Kelas</label>
                        <select name="kelas_id" class="border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white min-w-[200px]">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    {{ $k->tingkat }} {{ $k->jurusan }} {{ $k->rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-sm h-[42px] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari Murid
                    </button>
                </form>
            </div>

            @if($selectedKelas)
                @if($siswas->isEmpty())
                    <div class="p-10 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h4 class="text-slate-800 font-bold mb-1">Tidak ada murid</h4>
                        <p class="text-sm text-slate-500">Belum ada data murid untuk kelas {{ $selectedKelas ? $selectedKelas->tingkat.' '.$selectedKelas->jurusan.' '.$selectedKelas->rombel : '' }}.</p>
                    </div>
                @else
                    <div class="p-0 overflow-x-auto">
                        <form method="POST" action="{{ route('piket.sholat.store') }}">
                            @csrf
                            <input type="hidden" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}">
                            
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100">
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider w-16">No</th>
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Murid</th>
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Status Absensi Sholat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($siswas as $idx => $s)
                                        @php 
                                            $currentStatus = isset($absensi[$s->id]) ? $absensi[$s->id]->status : 'tidak_sholat';
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-semibold text-slate-600">{{ $idx + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-800">{{ $s->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $s->nomor_induk }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-4">
                                                    <label class="flex items-center gap-2 cursor-pointer group">
                                                        <input type="radio" name="status[{{ $s->id }}]" value="sholat" {{ $currentStatus === 'sholat' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                                        <span class="text-sm font-semibold text-slate-600 group-hover:text-emerald-700 transition">Sholat</span>
                                                    </label>
                                                    <label class="flex items-center gap-2 cursor-pointer group">
                                                        <input type="radio" name="status[{{ $s->id }}]" value="udzur" {{ $currentStatus === 'udzur' ? 'checked' : '' }} class="w-4 h-4 text-amber-500 focus:ring-amber-500 border-slate-300">
                                                        <span class="text-sm font-semibold text-slate-600 group-hover:text-amber-600 transition">Udzur (Haid/Sakit)</span>
                                                    </label>
                                                    <label class="flex items-center gap-2 cursor-pointer group">
                                                        <input type="radio" name="status[{{ $s->id }}]" value="tidak_sholat" {{ $currentStatus === 'tidak_sholat' ? 'checked' : '' }} class="w-4 h-4 text-red-500 focus:ring-red-500 border-slate-300">
                                                        <span class="text-sm font-semibold text-slate-600 group-hover:text-red-600 transition">Tidak Sholat</span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-8 py-3 rounded-xl text-sm transition shadow-sm">
                                    Simpan Absensi Sholat
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                <div class="p-12 text-center flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"/></svg>
                    <h4 class="text-slate-800 font-bold text-lg mb-1">Silakan Pilih Kelas</h4>
                    <p class="text-slate-500 text-sm max-w-sm">Pilih kelas pada form filter di atas untuk mulai mendata absensi sholat siswa.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
