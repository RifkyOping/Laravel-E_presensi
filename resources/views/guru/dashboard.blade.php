@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard Guru</span>
    </x-slot>

<div class="space-y-6">

    {{-- Welcome Strip --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-blue-300 text-sm font-semibold mb-1">Selamat datang,</p>
                <h1 class="text-white text-2xl font-black leading-tight">{{ Auth::user()->name }}</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    {{ Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex flex-col items-start sm:items-end gap-1 shrink-0">
                <span class="text-white/40 text-xs uppercase tracking-widest font-bold">Guru</span>
                <span class="text-white text-sm font-semibold">E-Presensi SMKN 1 Majene</span>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- Quick Access --}}
    <div>
        <p class="text-[.7rem] font-black uppercase tracking-widest text-slate-400 mb-3">Akses Cepat</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Absen Sekolah --}}
            <a href="{{ route('guru.absensi') }}"
               class="group bg-white rounded-xl border border-slate-200 p-6 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40">
                <div class="flex items-start justify-between">
                    <h3 class="font-bold text-slate-800 group-hover:text-[#1e3a6e] transition-colors">
                        Absen Sekarang
                    </h3>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-[#1e3a6e] transition-colors flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed flex-1">
                    Catat kehadiran datang dan pulang sekolah untuk hari ini.
                </p>
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[.7rem] font-bold text-[#1e3a6e]/70 uppercase tracking-wide">
                        Hari ini · {{ Carbon::today()->translatedFormat('d F Y') }}
                    </span>
                </div>
            </a>

            {{-- Aktivitas Mengajar --}}
            <a href="{{ route('guru.aktivitas') }}"
               class="group bg-white rounded-xl border border-slate-200 p-6 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40">
                <div class="flex items-start justify-between">
                    <h3 class="font-bold text-slate-800 group-hover:text-[#1e3a6e] transition-colors">
                        Aktivitas Mengajar
                    </h3>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-[#1e3a6e] transition-colors flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed flex-1">
                    Rekam dan dokumentasikan aktivitas pembelajaran di kelas yang Anda ampu.
                </p>
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[.7rem] font-bold text-[#1e3a6e]/70 uppercase tracking-wide">
                        Jurnal Mengajar
                    </span>
                </div>
            </a>

            {{-- Literasi Al-Qur'an --}}
            <a href="{{ route('guru.literasi.quran') }}"
               class="group bg-white rounded-xl border border-slate-200 p-6 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-emerald-500/40">
                <div class="flex items-start justify-between">
                    <h3 class="font-bold text-slate-800 group-hover:text-emerald-700 transition-colors">
                        Literasi Al-Qur'an
                    </h3>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-emerald-600 transition-colors flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed flex-1">
                    Pantau dan catat perkembangan hafalan, tilawah, dan tajwid siswa per kelas.
                </p>
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[.7rem] font-bold text-emerald-700/70 uppercase tracking-wide">
                        Catatan Literasi Siswa
                    </span>
                </div>
            </a>

        </div>
    </div>

</div>
</x-app-layout>

{{-- Modal Wajib Isi Jadwal --}}
@if(!Auth::user()->is_jadwal_set)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl animate-up text-center border-4 border-amber-400">
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <span class="text-4xl">📅</span>
        </div>
        <h2 class="text-2xl font-black text-slate-800 mb-3">Atur Jadwal Mengajar</h2>
        <p class="text-slate-600 mb-8 leading-relaxed">
            Sistem E-Presensi sekarang dilengkapi <strong>Otomatisasi Jurnal Mengajar</strong>. Anda wajib mengatur jadwal mengajar mingguan Anda terlebih dahulu agar sistem dapat membuatkan jurnal harian secara otomatis.
        </p>
        <a href="{{ route('guru.jadwal.index') }}" class="block w-full py-3.5 px-6 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition-all text-lg">
            Atur Jadwal Sekarang
        </a>
    </div>
</div>
@endif