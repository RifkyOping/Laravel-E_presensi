@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Absen Kelas – {{ $jadwal->mata_pelajaran }}</span>
    </x-slot>

    @php
        $pageTitle    = 'Absen Kelas: ' . $jadwal->kelas;
        $pageSubtitle = $jadwal->mata_pelajaran . ' · Mapel ke-' . $jadwal->jam_ke . ' · ' . Carbon::parse($jadwal->jam_mulai)->format('H:i') . '–' . Carbon::parse($jadwal->jam_selesai)->format('H:i') . ' WITA';
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
                        <span>Kelas {{ $jadwal->kelas }}</span>
                        <span class="hidden sm:inline"> &mdash; </span>
                        <span class="block sm:inline mt-0.5 sm:mt-0">
                            Mapel ke-{{ $jadwal->jam_ke }} ({{ Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WITA)
                        </span>
                    </p>
                </div>
                <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-4 flex-shrink-0 pt-3 sm:pt-0 border-t border-white/10 sm:border-t-0">
                    <div class="text-left sm:text-right">
                        <p class="text-white/60 text-xs font-bold uppercase tracking-wider mb-1">Tanggal</p>
                        <p class="text-white font-black text-base sm:text-lg">{{ Carbon::parse($today)->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        @if($aktivitas->waktu_absen_masuk)
                            <p class="text-[11px] text-white/75 font-bold">Masuk: {{ Carbon::parse($aktivitas->waktu_absen_masuk)->format('H:i') }} WITA</p>
                        @endif
                        @if($aktivitas->waktu_absen_keluar)
                            <p class="text-[11px] text-white/75 font-bold">Keluar: {{ Carbon::parse($aktivitas->waktu_absen_keluar)->format('H:i') }} WITA</p>
                        @endif
                        @if($sudahDiabsen)
                            <span class="inline-flex items-center gap-1.5 bg-emerald-400/20 border border-emerald-400/40 text-emerald-300 font-bold text-xs px-3 py-1 rounded-full mt-1.5 sm:mt-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Sudah Diabsen
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 bg-orange-400/20 border border-orange-400/40 text-orange-300 font-bold text-xs px-3 py-1 rounded-full mt-1.5 sm:mt-2">
                                Belum Diabsen
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Verifikasi Status Card jika sudah diverifikasi --}}
        @if($aktivitas->status_verifikasi || $aktivitas->verified_at)
        <div class="rounded-2xl border {{ $aktivitas->status_verifikasi === 'mengajar' ? 'border-emerald-200 bg-emerald-50/40' : 'border-rose-200 bg-rose-50/40' }} p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b {{ $aktivitas->status_verifikasi === 'mengajar' ? 'border-emerald-200/70' : 'border-rose-200/70' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $aktivitas->status_verifikasi === 'mengajar' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }} flex items-center justify-center font-black shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-sm uppercase tracking-wider {{ $aktivitas->status_verifikasi === 'mengajar' ? 'text-emerald-950' : 'text-rose-950' }}">
                                Hasil Verifikasi Mengajar
                            </h3>
                            @if($aktivitas->status_verifikasi === 'mengajar')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    Terverifikasi Mengajar
                                </span>
                            @elseif($aktivitas->status_verifikasi === 'tidak_mengajar')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                    Terverifikasi Tidak Mengajar
                                </span>
                            @endif
                        </div>
                        <p class="text-xs {{ $aktivitas->status_verifikasi === 'mengajar' ? 'text-emerald-700' : 'text-rose-700' }} font-medium mt-0.5">
                            Diverifikasi oleh <span class="font-bold">{{ $aktivitas->verifier?->name ?? 'Petugas Verifikasi' }}</span>
                            @if($aktivitas->verified_at)
                                pada {{ Carbon::parse($aktivitas->verified_at)->translatedFormat('d F Y, H:i') }} WITA
                            @endif
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-white/80 border border-slate-200 px-3 py-1 rounded-lg self-start sm:self-auto shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Hanya Lihat (Read-only)
                </span>
            </div>

            <div class="mt-4 grid grid-cols-1 {{ $aktivitas->foto_verifikasi ? 'md:grid-cols-12' : '' }} gap-3.5 items-stretch text-left">
                {{-- Catatan --}}
                <div class="{{ $aktivitas->foto_verifikasi ? 'md:col-span-7 lg:col-span-8' : 'w-full' }} flex flex-col text-left">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5 text-left">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        <span>Catatan Petugas :</span>
                    </p>
                    <div class="flex-1 bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs text-left">
                        <p class="text-sm text-slate-800 font-medium leading-relaxed whitespace-pre-line text-left break-words">{{ trim($aktivitas->catatan_kurikulum) ?: 'Tidak ada catatan khusus.' }}</p>
                    </div>
                </div>

                {{-- Foto Verifikasi --}}
                @if($aktivitas->foto_verifikasi)
                <div class="md:col-span-5 lg:col-span-4 flex flex-col text-left" x-data="{ showImgModal: false }">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5 text-left">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Foto Bukti :</span>
                    </p>
                    <div class="flex-1 bg-white rounded-xl p-3 border border-slate-200/80 shadow-2xs flex items-center gap-3 text-left">
                        <button type="button" @click="showImgModal = true"
                            class="group relative w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border border-slate-200 shrink-0 focus:outline-none focus:ring-2 focus:ring-[#1e3a6e] bg-slate-100 cursor-pointer">
                            <img src="{{ Storage::url($aktivitas->foto_verifikasi) }}" alt="Foto Bukti Verifikasi" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </div>
                        </button>
                        <div class="text-xs text-left">
                            <p class="font-bold text-slate-800">Foto Kegiatan</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Bukti lapangan</p>
                            <button type="button" @click="showImgModal = true" class="mt-1.5 text-[#1e3a6e] font-bold hover:underline inline-flex items-center gap-1 cursor-pointer">
                                <span>Perbesar Foto</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Perbesar Foto dengan Teleport --}}
                    <template x-teleport="body">
                        <div x-show="showImgModal" 
                             x-transition:enter="transition ease-out duration-200" 
                             x-transition:enter-start="opacity-0" 
                             x-transition:enter-end="opacity-100" 
                             x-transition:leave="transition ease-in duration-150" 
                             x-transition:leave-start="opacity-100" 
                             x-transition:leave-end="opacity-0" 
                             @keydown.escape.window="showImgModal = false"
                             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/85 backdrop-blur-md" 
                             style="display: none;">
                            <div class="fixed inset-0" @click="showImgModal = false"></div>
                            <div class="relative z-10 max-w-4xl w-full max-h-[90vh] flex flex-col items-center">
                                <button type="button" @click="showImgModal = false" 
                                        class="absolute -top-12 right-0 text-white hover:text-slate-200 font-bold text-xs sm:text-sm flex items-center gap-1.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-1.5 rounded-full transition shadow-lg cursor-pointer">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Tutup</span>
                                </button>
                                <div class="bg-black/40 rounded-2xl overflow-hidden border border-white/20 shadow-2xl p-2">
                                    <img src="{{ Storage::url($aktivitas->foto_verifikasi) }}" alt="Foto Bukti Verifikasi" class="max-w-full max-h-[78vh] rounded-xl object-contain mx-auto block shadow-lg">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Alert --}}
        @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 text-blue-700 px-5 py-3.5 rounded-xl text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('info') }}
            </div>
        @endif

        @if(!$aktivitas->waktu_absen_masuk)
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center shadow-sm">
                <div class="w-16 h-16 bg-[#1e3a6e]/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-[#1e3a6e]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-700 text-lg mb-2">Absen Masuk Mengajar</h3>
                <p class="text-slate-500 text-sm mb-6 max-w-md mx-auto">Anda belum melakukan Absen Masuk untuk kelas ini. Silakan klik tombol di bawah untuk mencatat waktu kehadiran Anda sebelum mengambil absen murid.</p>
                <form method="POST" action="{{ route('guru.aktivitas.masuk', $aktivitas->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-8 py-3 rounded-xl text-sm transition duration-200 shadow-sm inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Absen Masuk Sekarang
                    </button>
                </form>
            </div>
        @elseif($siswas->isEmpty())
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
            @if($sudahDiabsen && !request('edit'))
            @if(!$aktivitas->waktu_absen_keluar)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                <div>
                    <h3 class="font-bold text-amber-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Jangan Lupa Absen Keluar!
                    </h3>
                    <p class="text-sm text-amber-700 mt-1 font-medium">Absensi murid sudah disimpan. Jika kelas sudah selesai, silakan lakukan Absen Keluar.</p>
                </div>
                <form method="POST" action="{{ route('guru.aktivitas.keluar', $aktivitas->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm flex-shrink-0 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Absen Keluar Mengajar
                    </button>
                </form>
            </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Rekap Absensi Kelas
                        </h3>
                        <p class="text-sm text-slate-500 mt-1 mb-4">Absensi sudah disimpan. Anda dapat mengunduh rekap atau mengedit kembali absensi jika diperlukan.</p>
                        <form method="GET" action="{{ route('guru.absen-kelas.export', $jadwal->id) }}" class="flex items-center gap-2 mt-2 sm:mt-0">
                            <select name="delimiter" class="border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl pl-3 pr-8 py-2 text-slate-800 font-medium text-xs bg-white">
                                <option value=";">Excel ID (;)</option>
                                <option value=",">Excel EN (,)</option>
                            </select>
                            <button type="submit" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3 sm:px-4 py-2 rounded-xl transition shadow-sm">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Download<span class="hidden sm:inline"> Rekap</span></span>
                            </button>
                            <a href="{{ route('guru.absen-kelas.show', ['jadwal' => $jadwal->id, 'edit' => 'true']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200 shadow-sm">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit<span class="hidden sm:inline"> Absensi</span></span>
                            </a>
                        </form>
                    </div>
                    {{-- Summary badges --}}
                    @php
                        $countHadir = $absensiHariIni->where('status','hadir')->count();
                        $countAlpa  = $absensiHariIni->where('status','alpa')->count();
                        $countSakit = $absensiHariIni->where('status','sakit')->count();
                        $countIzin  = $absensiHariIni->where('status','izin')->count();
                    @endphp
                    <div class="flex flex-col gap-2 w-full sm:w-auto items-center sm:items-start flex-shrink-0 pt-2 sm:pt-0">
                        <div class="flex items-center justify-center sm:justify-start gap-2">
                            <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-600 border border-slate-200 font-semibold text-xs px-3 py-1.5 rounded-full">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Hadir: {{ $countHadir }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-600 border border-slate-200 font-semibold text-xs px-3 py-1.5 rounded-full">
                                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                Alpa: {{ $countAlpa }}
                            </span>
                        </div>
                        <div class="flex items-center justify-center sm:justify-start gap-2">
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
                </div>
                @php $materiHariIni = $absensiHariIni->first()?->materi; @endphp
                @if($materiHariIni)
                <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
                    <p class="text-[0.65rem] font-bold text-slate-500 uppercase tracking-wider mb-1">Materi yang Diajarkan</p>
                    <p class="text-sm text-slate-800">{{ $materiHariIni }}</p>
                </div>
                @endif
                <div class="divide-y divide-slate-100">
                    @foreach($siswas as $i => $siswa)
                    @php $absensi = $absensiHariIni->get($siswa->id); @endphp
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition">
                        <div class="w-8 h-8 rounded-full bg-[#1e3a6e]/10 text-[#1e3a6e] flex items-center justify-center font-black text-xs flex-shrink-0">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $siswa->name }}</p>
                            <p class="text-xs text-slate-400 font-medium">No. Induk: {{ $siswa->nomor_induk ?? '-' }}</p>
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
            @php $materiHariIni = $absensiHariIni->first()?->materi; @endphp
            <form method="POST" action="{{ route('guru.absen-kelas.store', $jadwal->id) }}" id="form-absen-kelas">
                @csrf
                
                {{-- Input Materi --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-5">
                        <label class="block text-sm font-bold text-slate-800 mb-2">Materi yang Diajarkan Hari Ini <span class="text-red-500">*</span></label>
                        <textarea name="materi" rows="3" required class="w-full rounded-xl border-slate-200 focus:border-[#1e3a6e] focus:ring-[#1e3a6e]/20 text-sm p-3" placeholder="Contoh: Bab 1 Pendahuluan, Diskusi Kelompok...">{{ old('materi', $materiHariIni ?? '') }}</textarea>
                        <p class="text-xs text-slate-500 mt-2">Wajib diisi. Materi ini akan disimpan sebagai catatan jurnal kelas untuk sesi ini.</p>
                    </div>
                </div>
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
                    {{-- Hidden input for actual submission --}}
                    @foreach($siswas as $i => $siswa)
                    @php
                        $currentStatus = $absensiHariIni->get($siswa->id)?->status ?? $absensiHarianSiswa->get($siswa->id)?->status ?? 'hadir';
                        $currentKeterangan = $absensiHariIni->get($siswa->id)?->keterangan ?? '';
                    @endphp
                    <input type="hidden" name="absensi[{{ $siswa->id }}][status]" id="status-hidden-{{ $siswa->id }}" value="{{ $currentStatus }}">
                    <input type="hidden" name="absensi[{{ $siswa->id }}][keterangan]" id="keterangan-hidden-{{ $siswa->id }}" value="{{ $currentKeterangan }}">
                    @endforeach

                    {{-- Mobile View --}}
                    <div class="block sm:hidden divide-y divide-slate-100">
                        <div class="flex flex-row items-center gap-2 px-4 py-3 bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center">
                            <div class="flex-1 text-left min-w-0">Nama</div>
                            <div class="w-10 flex-shrink-0">Hadir</div>
                            <div class="w-10 flex-shrink-0">Alpa</div>
                            <div class="w-10 flex-shrink-0">Sakit</div>
                            <div class="w-10 flex-shrink-0">Izin</div>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach($siswas as $i => $siswa)
                            @php
                                $currentStatus = $absensiHariIni->get($siswa->id)?->status ?? 'hadir';
                                $currentKeterangan = $absensiHariIni->get($siswa->id)?->keterangan ?? '';
                            @endphp
                            <div class="flex flex-col border-b border-slate-100 hover:bg-slate-50/50 transition">
                                <div class="flex flex-row items-center gap-2 px-4 py-3">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <p class="font-bold text-slate-800 text-[12px] leading-tight break-words">{{ $siswa->name }}</p>
                                    </div>
                                    <div class="w-10 flex-shrink-0 flex justify-center">
                                        <input type="radio" name="absensi_mobile[{{ $siswa->id }}][status]" value="hadir" onchange="syncAbsenStatus({{ $siswa->id }}, 'hadir')" {{ 'hadir' === $currentStatus ? 'checked' : '' }} class="w-5 h-5 text-[#1e3a6e] focus:ring-[#1e3a6e] border-slate-300">
                                    </div>
                                    <div class="w-10 flex-shrink-0 flex justify-center">
                                        <input type="radio" name="absensi_mobile[{{ $siswa->id }}][status]" value="alpa" onchange="syncAbsenStatus({{ $siswa->id }}, 'alpa')" {{ 'alpa' === $currentStatus ? 'checked' : '' }} class="w-5 h-5 text-[#1e3a6e] focus:ring-[#1e3a6e] border-slate-300">
                                    </div>
                                    <div class="w-10 flex-shrink-0 flex justify-center">
                                        <input type="radio" name="absensi_mobile[{{ $siswa->id }}][status]" value="sakit" onchange="syncAbsenStatus({{ $siswa->id }}, 'sakit')" {{ 'sakit' === $currentStatus ? 'checked' : '' }} class="w-5 h-5 text-[#1e3a6e] focus:ring-[#1e3a6e] border-slate-300">
                                    </div>
                                    <div class="w-10 flex-shrink-0 flex justify-center">
                                        <input type="radio" name="absensi_mobile[{{ $siswa->id }}][status]" value="izin" onchange="syncAbsenStatus({{ $siswa->id }}, 'izin')" {{ 'izin' === $currentStatus ? 'checked' : '' }} class="w-5 h-5 text-[#1e3a6e] focus:ring-[#1e3a6e] border-slate-300">
                                    </div>
                                </div>
                                <div class="px-4 pb-3">
                                    <input type="text" name="absensi_mobile[{{ $siswa->id }}][keterangan]" placeholder="Keterangan (opsional)" class="w-full text-xs border-slate-200 rounded-md focus:border-[#1e3a6e] focus:ring-[#1e3a6e]/20" onchange="syncAbsenKeterangan({{ $siswa->id }}, this.value)" onkeyup="syncAbsenKeterangan({{ $siswa->id }}, this.value)" value="{{ $currentKeterangan }}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Desktop View --}}
                    <div class="hidden sm:block divide-y divide-slate-100">
                        @foreach($siswas as $i => $siswa)
                        <div class="flex items-center gap-3 px-6 py-4 hover:bg-slate-50/50 transition" id="row-siswa-{{ $siswa->id }}">
                            {{-- Nomor & Nama --}}
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-[#1e3a6e]/10 text-[#1e3a6e] flex items-center justify-center font-black text-sm flex-shrink-0">
                                    {{ $i + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 text-sm leading-tight">{{ $siswa->name }}</p>
                                    <p class="text-xs text-slate-400 font-medium">
                                        No. Induk: {{ $siswa->nomor_induk ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Status Buttons & Keterangan --}}
                            <div class="flex gap-4 flex-shrink-0 items-center">
                                @php
                                    $currentStatus = $absensiHariIni->get($siswa->id)?->status ?? $absensiHarianSiswa->get($siswa->id)?->status ?? 'hadir';
                                    $currentKeterangan = $absensiHariIni->get($siswa->id)?->keterangan ?? '';
                                @endphp
                                @foreach(['hadir' => 'Hadir', 'alpa' => 'Alpa', 'sakit' => 'Sakit', 'izin' => 'Izin'] as $val => $label)
                                <label class="flex items-center gap-1.5 cursor-pointer text-sm text-slate-700 font-medium group">
                                    <input type="radio"
                                           name="absensi_desktop[{{ $siswa->id }}][status]"
                                           value="{{ $val }}"
                                           onchange="syncAbsenStatus({{ $siswa->id }}, '{{ $val }}')"
                                           class="w-4 h-4 text-[#1e3a6e] border-slate-300 focus:ring-[#1e3a6e] status-radio-{{ $siswa->id }}"
                                           {{ $val === $currentStatus ? 'checked' : '' }}>
                                    <span class="group-hover:text-slate-900 transition">{{ $label }}</span>
                                </label>
                                @endforeach
                                <div class="ml-4 w-48 flex-shrink-0">
                                    <input type="text" name="absensi_desktop[{{ $siswa->id }}][keterangan]" placeholder="Keterangan (opsional)" class="w-full text-xs border-slate-200 rounded-md focus:border-[#1e3a6e] focus:ring-[#1e3a6e]/20" onchange="syncAbsenKeterangan({{ $siswa->id }}, this.value)" onkeyup="syncAbsenKeterangan({{ $siswa->id }}, this.value)" value="{{ $currentKeterangan }}">
                                </div>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    {{-- Footer Submit --}}
                    <div class="px-6 py-5 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-slate-500 font-medium">
                            Pastikan semua status sudah benar sebelum menyimpan.
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
                    document.querySelectorAll('input[id^="status-hidden-"]').forEach(hidden => {
                        let id = hidden.id.replace('status-hidden-', '');
                        syncAbsenStatus(id, 'hadir');
                    });
                }

                function syncAbsenStatus(id, value) {
                    let hidden = document.getElementById('status-hidden-' + id);
                    if(hidden) hidden.value = value;
                    
                    document.querySelectorAll(`input[name="absensi_mobile[${id}][status]"][value="${value}"]`).forEach(r => r.checked = true);
                    document.querySelectorAll(`input[name="absensi_desktop[${id}][status]"][value="${value}"]`).forEach(r => r.checked = true);
                }

                function syncAbsenKeterangan(id, value) {
                    let hidden = document.getElementById('keterangan-hidden-' + id);
                    if(hidden) hidden.value = value;
                    
                    document.querySelectorAll(`input[name="absensi_mobile[${id}][keterangan]"]`).forEach(i => {
                        if (i !== document.activeElement) i.value = value;
                    });
                    document.querySelectorAll(`input[name="absensi_desktop[${id}][keterangan]"]`).forEach(i => {
                        if (i !== document.activeElement) i.value = value;
                    });
                }

                function confirmSubmit(e, form) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Simpan Absensi?',
                        text: 'Pastikan semua status sudah benar.',
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
