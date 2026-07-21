<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Monitoring Kelas</span>
    </x-slot>

    <div class="space-y-7">
        {{-- ── JADWAL & AKTIVITAS HARI INI ── --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm w-full mx-auto mb-7">
            <h2 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Jadwal & Aktivitas Mengajar Hari Ini
            </h2>

            <form method="GET" action="{{ route('guru.buku-kemajuan') }}" class="flex flex-col sm:flex-row gap-4 mb-6">
                <div class="flex-1">
                    <select name="filter_kelas" class="app-input w-full" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            @php $namaKelas = $k->tingkat . ' ' . $k->jurusan . ' ' . $k->rombel; @endphp
                            <option value="{{ $namaKelas }}" {{ (request('filter_kelas') == $namaKelas) ? 'selected' : '' }}>
                                {{ $namaKelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-full bg-[#1e3a6e] hover:bg-[#15294d] text-white font-bold text-sm transition shadow-lg shadow-[#1e3a6e]/30 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari
                </button>
            </form>

            @if(request()->has('filter_kelas'))
                @if(isset($jadwalHariIni) && $jadwalHariIni->isNotEmpty())
                    <div class="overflow-x-auto border border-slate-200 rounded-xl">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-bold border-b border-slate-200">Waktu</th>
                                    <th class="p-4 font-bold border-b border-slate-200">Mata Pelajaran</th>
                                    <th class="p-4 font-bold border-b border-slate-200">Nama</th>
                                    <th class="p-4 font-bold border-b border-slate-200 text-center">Masuk</th>
                                    <th class="p-4 font-bold border-b border-slate-200 text-center">Keluar</th>
                                    <th class="p-4 font-bold border-b border-slate-200 text-center">Absen Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($jadwalHariIni as $jadwal)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="p-4 whitespace-nowrap">
                                            <span class="font-semibold text-slate-800">Jam ke-{{ $jadwal->jam_ke }}</span>
                                            <div class="text-xs text-slate-500 mt-1">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</div>
                                        </td>
                                        <td class="p-4 font-medium text-slate-800">{{ $jadwal->mata_pelajaran }}</td>
                                        <td class="p-4">{{ $jadwal->user->name ?? '-' }}</td>
                                        <td class="p-4 text-center">
                                            @if($jadwal->waktu_datang)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                    {{ Carbon\Carbon::parse($jadwal->waktu_datang)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-rose-50 text-rose-600 text-xs font-bold border border-rose-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($jadwal->waktu_pulang)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                    {{ Carbon\Carbon::parse($jadwal->waktu_pulang)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-rose-50 text-rose-600 text-xs font-bold border border-rose-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($jadwal->sudah_absen_kelas)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                    Sudah
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-rose-50 text-rose-600 text-xs font-bold border border-rose-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    Belum
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-slate-800 font-bold mb-1">Tidak ada jadwal hari ini</h3>
                        <p class="text-slate-500 text-sm">Tidak ditemukan jadwal mengajar untuk kelas ini pada hari {{ $hariIni }} ({{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }}).</p>
                    </div>
                @endif
            @endif
        </div>

        {{-- ── FORM CETAK ── --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm w-full mx-auto">
            <h2 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Form Cetak
            </h2>

            <form method="GET" action="{{ route('guru.buku-kemajuan.cetak') }}" target="_blank" class="space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Pilih
                            Kelas</label>
                        <select name="kelas" class="app-input w-full" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->tingkat }} {{ $k->jurusan }} {{ $k->rombel }}">
                                    {{ $k->tingkat }} {{ $k->jurusan }} {{ $k->rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Semester</label>
                        <select name="semester" class="app-input w-full" required>
                            <option value="GANJIL">Ganjil</option>
                            <option value="GENAP">Genap</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal
                            Mulai (Awal Semester)</label>
                        <input type="date" name="tanggal_mulai" class="app-input w-full" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal
                            Akhir (Akhir Semester)</label>
                        <input type="date" name="tanggal_akhir" class="app-input w-full" required>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#1e3a6e] hover:bg-[#15294d] text-white font-bold text-sm transition shadow-lg shadow-[#1e3a6e]/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>