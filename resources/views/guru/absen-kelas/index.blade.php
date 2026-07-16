@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="title">Absen Kelas</x-slot>

    @php
        $pageTitle    = 'Absen Kelas';
        $pageSubtitle = 'Kelola kehadiran murid di kelas Anda hari ini';
    @endphp

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800">Absen Kelas Hari Ini</h2>
                <p class="text-slate-500 text-sm mt-1 font-medium">
                    {{ Carbon::now()->translatedFormat('l, d F Y') }} &mdash; {{ $hariIni }}
                </p>
            </div>
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Status & Upload RPP --}}
        @php
            $rppStatus = auth()->user()->rpp_status;
            $rppFile = auth()->user()->rpp_file;
            $statusData = match($rppStatus) {
                'kosong' => ['Belum Upload', 'bg-red-50 text-red-700 border-red-200', 'Mulai absen kelas dikunci. Silakan unggah RPP terlebih dahulu.'],
                'pending' => ['Menunggu Persetujuan', 'bg-amber-50 text-amber-700 border-amber-200', 'RPP Anda sedang menunggu persetujuan Kurikulum. Mulai absen kelas masih dikunci.'],
                'disetujui' => ['Disetujui', 'bg-emerald-50 text-emerald-700 border-emerald-200', 'RPP disetujui. Anda dapat mulai mengisi absen kelas.'],
                'ditolak' => ['Ditolak', 'bg-red-50 text-red-700 border-red-200', 'RPP Anda ditolak. Silakan perbaiki dan unggah ulang.'],
                default => ['Belum Upload', 'bg-slate-50 text-slate-700 border-slate-200', 'Silakan unggah RPP Anda.'],
            };
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex flex-col md:flex-row gap-6 items-start md:items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Status RPP Anda
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-lg">{{ $statusData[2] }}</p>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusData[1] }}">
                            {{ $statusData[0] }}
                        </span>
                        @if($rppFile)
                            <a href="{{ Storage::url($rppFile) }}" target="_blank" class="text-sm font-semibold text-[#1e3a6e] hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Lihat File
                            </a>
                        @endif
                    </div>
                    @if($rppStatus === 'ditolak' && auth()->user()->rpp_pesan)
                        <div class="mt-4 p-4 bg-red-50/50 border border-red-100 rounded-xl max-w-lg">
                            <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Alasan Penolakan:</p>
                            <p class="text-sm text-red-800 font-medium">{{ auth()->user()->rpp_pesan }}</p>
                        </div>
                    @endif
                </div>
                
                <form action="{{ route('guru.upload-rpp') }}" method="POST" enctype="multipart/form-data" class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div>
                        <input type="file" name="rpp_file" accept=".pdf,.doc,.docx" required
                               class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2.5 file:px-4
                                      file:rounded-xl file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-slate-100 file:text-slate-700
                                      hover:file:bg-slate-200 file:transition">
                        @error('rpp_file')
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="flex-shrink-0 flex items-center justify-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Unggah RPP
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Jadwal Hari Ini --}}
        @if($jadwals->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center shadow-sm">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-700 text-lg">Tidak Ada Jadwal Hari Ini</h3>
                <p class="text-slate-400 text-sm mt-2">Anda tidak memiliki jadwal mengajar pada hari {{ $hariIni }} ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($jadwals as $jadwal)
                <div class="bg-white rounded-2xl border {{ $jadwal->sudah_diabsen ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                    
                    {{-- Card Header --}}
                    <div class="px-5 pt-5 pb-4">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mata Pelajaran</p>
                                <h3 class="text-lg font-black text-slate-800 leading-tight truncate">{{ $jadwal->mata_pelajaran }}</h3>
                            </div>
                            @if($jadwal->sudah_diabsen)
                                <span class="flex-shrink-0 inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 font-bold text-xs px-3 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Selesai
                                </span>
                            @else
                                <span class="flex-shrink-0 inline-flex items-center gap-1.5 bg-orange-100 text-orange-700 font-bold text-xs px-3 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Belum Absen
                                </span>
                            @endif
                        </div>

                        {{-- Info Grid --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 rounded-xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas</p>
                                <p class="font-bold text-slate-700 text-sm">{{ $jadwal->kelas }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jam Ke</p>
                                <p class="font-bold text-slate-700 text-sm">{{ $jadwal->jam_ke }}</p>
                            </div>
                            <div class="bg-[#1e3a6e]/5 rounded-xl p-3 col-span-2">
                                <p class="text-[10px] font-bold text-[#1e3a6e]/60 uppercase tracking-wider mb-1">Waktu</p>
                                <p class="font-bold text-[#1e3a6e] text-sm">
                                    {{ Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                    – {{ Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WITA
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Card Action --}}
                    <div class="px-5 pb-5">
                        @if($jadwal->sudah_diabsen)
                            <a href="{{ route('guru.absen-kelas.show', $jadwal->id) }}"
                               class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-bold transition duration-200 bg-slate-100 text-slate-600 hover:bg-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Rekap
                            </a>
                        @elseif(auth()->user()->rpp_status !== 'disetujui')
                            <button type="button" disabled
                               class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-bold bg-slate-100 text-slate-400 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                RPP Belum Disetujui
                            </button>
                        @else
                            <a href="{{ route('guru.absen-kelas.show', $jadwal->id) }}"
                               class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-bold transition duration-200 bg-[#1e3a6e] text-white hover:bg-[#162d57] shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Mulai Absen
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
