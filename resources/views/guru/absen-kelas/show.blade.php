@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="title">Absen Kelas – {{ $jadwal->mata_pelajaran }}</x-slot>

    @php
        $pageTitle    = 'Absen Kelas: ' . $jadwal->kelas;
        $pageSubtitle = $jadwal->mata_pelajaran . ' · Jam ke-' . $jadwal->jam_ke . ' · ' . Carbon::parse($jadwal->jam_mulai)->format('H:i') . '–' . Carbon::parse($jadwal->jam_selesai)->format('H:i') . ' WITA';
    @endphp

    <div class="space-y-6">

        {{-- Back Button --}}
        <div>
            <a href="{{ route('guru.absen-kelas.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#1e3a6e] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Jadwal
            </a>
        </div>

        {{-- Info Jadwal --}}
        <div class="bg-[#1e3a6e] rounded-2xl p-5 md:p-6 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-white/60 text-xs font-bold uppercase tracking-wider mb-1">Mata Pelajaran</p>
                    <h2 class="text-xl md:text-2xl font-black">{{ $jadwal->mata_pelajaran }}</h2>
                    <p class="text-white/75 text-sm mt-2 font-medium">
                        Kelas {{ $jadwal->kelas }} &mdash; Jam ke-{{ $jadwal->jam_ke }}
                        ({{ Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WITA)
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-white/60 text-xs font-bold uppercase tracking-wider mb-1">Tanggal</p>
                    <p class="text-white font-black text-lg">{{ Carbon::parse($today)->translatedFormat('d M Y') }}</p>
                    @if($sudahDiabsen)
                        <span class="inline-flex items-center gap-1.5 bg-emerald-400/20 border border-emerald-400/40 text-emerald-300 font-bold text-xs px-3 py-1 rounded-full mt-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Sudah Diabsen
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-orange-400/20 border border-orange-400/40 text-orange-300 font-bold text-xs px-3 py-1 rounded-full mt-2">
                            Belum Diabsen
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if($siswas->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center shadow-sm">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-700 text-lg">Tidak Ada Murid</h3>
                <p class="text-slate-400 text-sm mt-2">Tidak ada murid terdaftar di kelas <strong>{{ $jadwal->kelas }}</strong>.</p>
                <p class="text-slate-400 text-xs mt-1">Pastikan data rombel murid sudah diatur oleh admin.</p>
            </div>
        @else
            {{-- REKAP MODE (sudah diabsen) --}}
            @if($sudahDiabsen)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Rekap Absensi Kelas
                        </h3>
                        <p class="text-sm text-slate-500 mt-0.5">Absensi sudah disimpan dan tidak dapat diubah.</p>
                    </div>
                    {{-- Summary badges --}}
                    <div class="flex flex-wrap gap-2">
                        @php
                            $countHadir = $absensiHariIni->where('status','hadir')->count();
                            $countAlpa  = $absensiHariIni->where('status','alpa')->count();
                            $countSakit = $absensiHariIni->where('status','sakit')->count();
                            $countIzin  = $absensiHariIni->where('status','izin')->count();
                        @endphp
                        <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-600 border border-slate-200 font-semibold text-xs px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Hadir: {{ $countHadir }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-600 border border-slate-200 font-semibold text-xs px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Alpa: {{ $countAlpa }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-600 border border-slate-200 font-semibold text-xs px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Sakit: {{ $countSakit }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-600 border border-slate-200 font-semibold text-xs px-3 py-1.5 rounded-full">
                            <svg class="w-3.5 h-3.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Izin: {{ $countIzin }}
                        </span>
                    </div>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($siswas as $i => $siswa)
                    @php $absensi = $absensiHariIni->get($siswa->id); @endphp
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition">
                        <div class="w-8 h-8 rounded-full bg-[#1e3a6e]/10 text-[#1e3a6e] flex items-center justify-center font-black text-xs flex-shrink-0">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $siswa->name }}</p>
                            <p class="text-xs text-slate-400 font-medium">NIS: {{ $siswa->siswaProfile?->nis ?? '-' }}</p>
                        </div>
                        @if($absensi)
                            @php
                                $badgeData = match($absensi->status) {
                                    'hadir'  => ['text-emerald-600 bg-emerald-50 border-emerald-100', '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>', 'Hadir'],
                                    'alpa'   => ['text-red-600 bg-red-50 border-red-100', '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>', 'Alpa'],
                                    'sakit'  => ['text-blue-600 bg-blue-50 border-blue-100', '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'Sakit'],
                                    'izin'   => ['text-yellow-600 bg-yellow-50 border-yellow-100', '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>', 'Izin'],
                                    default  => ['text-slate-600 bg-slate-50 border-slate-100', '', ucfirst($absensi->status)],
                                };
                            @endphp
                            <span class="flex-shrink-0 inline-flex items-center gap-1.5 {{ $badgeData[0] }} border font-medium text-xs px-3 py-1.5 rounded-full">
                                {!! $badgeData[1] !!} {{ $badgeData[2] }}
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- FORM ABSEN MODE --}}
            @else
            <form method="POST" action="{{ route('guru.absen-kelas.store', $jadwal->id) }}" id="form-absen-kelas">
                @csrf
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Daftar Murid — {{ $siswas->count() }} Orang
                            </h3>
                            <p class="text-sm text-slate-500 mt-0.5">Tandai status kehadiran setiap siswa. Default: <strong>Hadir</strong>.</p>
                        </div>
                        {{-- Tombol Hadir Semua --}}
                        <button type="button" onclick="hadirSemua()"
                            class="flex-shrink-0 inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-sm px-4 py-2 rounded-2xl hover:bg-emerald-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Tandai Semua Hadir
                        </button>
                    </div>

                    {{-- Daftar Murid --}}
                    <div class="divide-y divide-slate-100">
                        @foreach($siswas as $i => $siswa)
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-6 py-4 hover:bg-slate-50/50 transition" id="row-siswa-{{ $siswa->id }}">
                            {{-- Nomor & Nama --}}
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-[#1e3a6e]/10 text-[#1e3a6e] flex items-center justify-center font-black text-sm flex-shrink-0">
                                    {{ $i + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 text-sm leading-tight">{{ $siswa->name }}</p>
                                    <p class="text-xs text-slate-400 font-medium">
                                        NIS: {{ $siswa->siswaProfile?->nis ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Status Buttons --}}
                            <div class="flex gap-4 flex-shrink-0 items-center">
                                @foreach(['hadir' => 'Hadir', 'alpa' => 'Alpa', 'sakit' => 'Sakit', 'izin' => 'Izin'] as $val => $label)
                                <label class="flex items-center gap-1.5 cursor-pointer text-sm text-slate-700 font-medium">
                                    <input type="radio"
                                           name="absensi[{{ $siswa->id }}][status]"
                                           value="{{ $val }}"
                                           class="w-4 h-4 text-[#1e3a6e] border-slate-300 focus:ring-[#1e3a6e] status-radio-{{ $siswa->id }}"
                                           {{ $val === 'hadir' ? 'checked' : '' }}>
                                    <span>{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>

                        </div>
                        @endforeach
                    </div>

                    {{-- Footer Submit --}}
                    <div class="px-6 py-5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-slate-500 font-medium">
                            Pastikan semua status sudah benar sebelum menyimpan. <strong>Absensi tidak dapat diubah setelah disimpan.</strong>
                        </p>
                        <button type="submit"
                            onclick="confirmSubmit(event, this.form)"
                            class="flex-shrink-0 flex items-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-3 rounded-2xl text-sm transition duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </div>
            </form>

            <style>
                .swal-custom-popup {
                    border-radius: 28px !important;
                }
                .swal-confirm-btn, .swal-cancel-btn {
                    border-radius: 9999px !important;
                }
            </style>
            <script>
                function hadirSemua() {
                    document.querySelectorAll('input[type="radio"][value="hadir"]').forEach(r => {
                        r.checked = true;
                    });
                }

                function confirmSubmit(e, form) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Simpan Absensi?',
                        text: 'Pastikan semua status sudah benar. Data tidak dapat diubah setelah disimpan.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#1e3a6e',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        customClass: {
                            popup: 'swal-custom-popup',
                            confirmButton: 'swal-confirm-btn',
                            cancelButton: 'swal-cancel-btn',
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            </script>
            @endif
        @endif

    </div>
</x-app-layout>
