@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard Siswa</span>
    </x-slot>

<div class="space-y-6">

    {{-- Welcome Strip --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-5 py-5 sm:px-8 sm:py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-blue-300 text-sm font-semibold mb-1">Selamat datang,</p>
                <h1 class="text-white text-2xl font-black leading-tight">{{ Auth::user()->name }}</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    {{ Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- Quick Access --}}
    <div>
        <p class="text-[.7rem] font-black uppercase tracking-widest text-slate-400 mb-3">Akses Cepat</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Kehadiran --}}
            <a href="{{ route('absensi') }}"
               class="group app-card p-6 flex flex-col gap-3
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
                    Isi daftar hadir untuk sesi pembelajaran hari ini.
                </p>
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[.7rem] font-bold text-[#1e3a6e]/70 uppercase tracking-wide">
                        Hari ini · {{ Carbon::today()->translatedFormat('d F Y') }}
                    </span>
                </div>
            </a>

            {{-- Literasi E-Book --}}
            <a href="{{ route('ebook.index') }}"
               class="group app-card p-6 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40">
                <div class="flex items-start justify-between">
                    <h3 class="font-bold text-slate-800 group-hover:text-[#1e3a6e] transition-colors">
                        Literasi Digital
                    </h3>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-[#1e3a6e] transition-colors flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed flex-1">
                    Akses koleksi e-book bertingkat dengan verifikasi suara untuk setiap levelnya.
                </p>
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[.7rem] font-bold text-[#1e3a6e]/70 uppercase tracking-wide">
                        E-Book & Modul
                    </span>
                </div>
            </a>

            {{-- Profil Saya --}}
            <a href="{{ route('profile.edit') }}"
               class="group app-card p-6 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40">
                <div class="flex items-start justify-between">
                    <h3 class="font-bold text-slate-800 group-hover:text-[#1e3a6e] transition-colors">
                        Profil Saya
                    </h3>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-[#1e3a6e] transition-colors flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed flex-1">
                    Lihat data identitas diri: NIS, NISN, kelas, jurusan, dan informasi pribadi lainnya.
                </p>
                <div class="pt-3 border-t border-slate-100">
                    @php $user = Auth::user(); @endphp
                    <span class="text-[.7rem] font-bold text-[#1e3a6e]/70 uppercase tracking-wide">
                        {{ $user->kelas ? 'Kelas ' . $user->kelas . ($user->jurusan ? ' ' . $user->jurusan : '') . ($user->rombel ? ' ' . $user->rombel : '') : 'Lengkapi profil' }}
                    </span>
                </div>
            </a>

        </div>
    </div>

</div>
</x-app-layout>
