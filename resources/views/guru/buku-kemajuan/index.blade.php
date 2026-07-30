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
                Jadwal & Aktivitas Mengajar Setiap Kelas
            </h2>

            <form id="filter-form" method="GET" action="{{ route('guru.buku-kemajuan') }}" class="flex flex-row gap-3 mb-6 relative">
                <div class="flex-1 relative">
                    <select id="filter_kelas" name="filter_kelas" class="app-input w-full" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            @php $namaKelas = $k->tingkat . ' ' . $k->jurusan . ' ' . $k->rombel; @endphp
                            <option value="{{ $namaKelas }}" {{ (request('filter_kelas') == $namaKelas) ? 'selected' : '' }}>
                                {{ $namaKelas }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Loading indicator -->
                    <div id="loading-indicator" class="absolute right-3 top-1/2 -translate-y-1/2 hidden pointer-events-none">
                        <svg class="animate-spin h-5 w-5 text-[#1e3a6e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </form>

            <div id="table-container">
            @if(request()->has('filter_kelas'))
                @if(isset($jadwalHariIni) && $jadwalHariIni->isNotEmpty())
                    {{-- Mobile View: Expandable Cards --}}
                    <div class="block md:hidden space-y-4 mb-4">
                        @foreach($jadwalHariIni as $jadwal)
                            <div x-data="{ expanded: false }" class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 shadow-sm">
                                <div @click="expanded = !expanded" class="p-4 cursor-pointer hover:bg-slate-100 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                <span class="bg-[#1e3a6e]/10 text-[#1e3a6e] text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">Jam ke-{{ $jadwal->jam_ke }}</span>
                                                <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 shadow-sm px-2 py-0.5 rounded-md">
                                                    {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                                                </span>
                                            </div>
                                            <h3 class="font-bold text-slate-800 text-base leading-tight mb-1">{{ $jadwal->mata_pelajaran }}</h3>
                                            <p class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                {{ $jadwal->user->name ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="flex flex-col items-end flex-shrink-0">
                                            <div class="w-6 h-6 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm mt-1">
                                                <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="expanded" style="display: none;" class="px-4 pb-4 pt-3 border-t border-slate-200 bg-white">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Detail Absensi Sesi Ini</p>
                                    <div class="flex items-center justify-between mt-1 px-1">
                                        <div class="flex flex-col">
                                            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Masuk</p>
                                            @if($jadwal->waktu_datang)
                                                <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ Carbon\Carbon::parse($jadwal->waktu_datang)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold text-slate-400">-</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Keluar</p>
                                            @if($jadwal->waktu_pulang)
                                                <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                                    {{ Carbon\Carbon::parse($jadwal->waktu_pulang)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold text-slate-400">-</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Absen Kelas</p>
                                            @if($jadwal->sudah_absen_kelas)
                                                <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Sudah
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold text-slate-400">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop View --}}
                    <div class="hidden md:block border border-slate-200 rounded-xl overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="hidden md:table-header-group">
                                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-bold border-b border-slate-200">Waktu</th>
                                    <th class="p-4 font-bold border-b border-slate-200">Mata Pelajaran</th>
                                    <th class="p-4 font-bold border-b border-slate-200">Nama</th>
                                    <th class="p-4 font-bold border-b border-slate-200 text-center">Masuk</th>
                                    <th class="p-4 font-bold border-b border-slate-200 text-center">Keluar</th>
                                    <th class="p-4 font-bold border-b border-slate-200 text-center">Absen Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group divide-y divide-slate-100 text-sm">
                                @foreach($jadwalHariIni as $jadwal)
                                    <tr class="block md:table-row hover:bg-slate-50 transition border-b-4 md:border-none border-slate-100 p-4 md:p-0">
                                        <td class="block md:table-cell md:p-4 md:whitespace-nowrap mb-3 md:mb-0 align-middle">
                                            <div class="flex justify-between md:block items-center">
                                                <span class="md:hidden font-bold text-slate-500 text-xs uppercase">Waktu</span>
                                                <div class="text-right md:text-left">
                                                    <span class="font-semibold text-slate-800">Jam ke-{{ $jadwal->jam_ke }}</span>
                                                    <div class="text-xs text-slate-500 mt-0.5 md:mt-1">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="block md:table-cell md:p-4 font-medium text-slate-800 mb-2 md:mb-0 align-middle">
                                            <div class="flex justify-between md:block items-center">
                                                <span class="md:hidden font-bold text-slate-500 text-xs uppercase">Mata Pelajaran</span>
                                                <span class="text-right md:text-left">{{ $jadwal->mata_pelajaran }}</span>
                                            </div>
                                        </td>
                                        <td class="block md:table-cell md:p-4 mb-3 md:mb-0 align-middle">
                                            <div class="flex justify-between md:block items-center">
                                                <span class="md:hidden font-bold text-slate-500 text-xs uppercase">Nama</span>
                                                <span class="text-right md:text-left">{{ $jadwal->user->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="block md:table-cell md:p-4 md:text-center border-t border-slate-100 md:border-none pt-3 mt-3 md:mt-0 mb-2 md:mb-0 align-middle">
                                            <div class="flex justify-between md:block items-center md:mt-1">
                                                <span class="md:hidden font-bold text-slate-500 text-xs uppercase">Masuk</span>
                                                @if($jadwal->waktu_datang)
                                                    <span class="min-w-[80px] justify-center inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        {{ Carbon\Carbon::parse($jadwal->waktu_datang)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="min-w-[80px] justify-center inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-rose-50 text-rose-600 text-xs font-bold border border-rose-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        Belum
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="block md:table-cell md:p-4 md:text-center mb-2 md:mb-0 align-middle">
                                            <div class="flex justify-between md:block items-center md:mt-1">
                                                <span class="md:hidden font-bold text-slate-500 text-xs uppercase">Keluar</span>
                                                @if($jadwal->waktu_pulang)
                                                    <span class="min-w-[80px] justify-center inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        {{ Carbon\Carbon::parse($jadwal->waktu_pulang)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="min-w-[80px] justify-center inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-rose-50 text-rose-600 text-xs font-bold border border-rose-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        Belum
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="block md:table-cell md:p-4 md:text-center align-middle">
                                            <div class="flex justify-between md:block items-center md:mt-1">
                                                <span class="md:hidden font-bold text-slate-500 text-xs uppercase">Absen Kelas</span>
                                                @if($jadwal->sudah_absen_kelas)
                                                    <span class="min-w-[80px] justify-center inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        Sudah
                                                    </span>
                                                @else
                                                    <span class="min-w-[80px] justify-center inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-rose-50 text-rose-600 text-xs font-bold border border-rose-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        Belum
                                                    </span>
                                                @endif
                                            </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectKelas = document.getElementById('filter_kelas');
            const form = document.getElementById('filter-form');
            const container = document.getElementById('table-container');
            const loading = document.getElementById('loading-indicator');

            if(selectKelas) {
                selectKelas.addEventListener('change', function() {
                    const kelas = this.value;
                    
                    if (!kelas) {
                        container.innerHTML = '';
                        const url = new URL(form.action);
                        window.history.pushState({}, '', url);
                        return;
                    }

                    loading.classList.remove('hidden');

                    const url = new URL(form.action);
                    url.searchParams.set('filter_kelas', kelas);

                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok && response.status !== 401) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('table-container');
                        
                        if(newContent) {
                            container.innerHTML = newContent.innerHTML;
                        }
                        
                        window.history.pushState({}, '', url.toString());
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        loading.classList.add('hidden');
                    });
                });
            }
        });
    </script>
</x-app-layout>