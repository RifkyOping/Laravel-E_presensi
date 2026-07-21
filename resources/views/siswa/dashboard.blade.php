@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard</span>
    </x-slot>

    <style>
        /* Modern 3D Animated Background for Header */
        .cyber-header {
            background: linear-gradient(-45deg, #1e3a6e, #2d5099, #3b82f6, #1e3a6e);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px -15px rgba(30, 58, 110, 0.4), 
                        inset 0 1px 0 rgba(255,255,255,0.2), 
                        inset 0 -3px 0 rgba(0,0,0,0.1);
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glowing Orbs in Header */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.6;
            animation: float 10s infinite ease-in-out alternate;
            pointer-events: none;
        }
        .orb-1 { width: 150px; height: 150px; background: #2563eb; top: -50px; left: -20px; animation-delay: 0s; }
        .orb-2 { width: 200px; height: 200px; background: #60a5fa; bottom: -80px; right: 10%; animation-delay: -3s; }
        .orb-3 { width: 120px; height: 120px; background: #3b82f6; top: 20px; right: 40%; animation-delay: -7s; }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(-30px) scale(1.1); }
        }

        /* 3D Glassmorphism Cards */
        .tilt-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-bottom: 2px solid rgba(255, 255, 255, 0.8);
            border-right: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 1.25rem;
            transform-style: preserve-3d;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }

        /* 3D Elements Inside Card */
        .tilt-card-content {
            transform: translateZ(30px);
        }
        
        .tilt-card-icon {
            transform: translateZ(50px);
            background: linear-gradient(135deg, #1e3a6e, #3b82f6);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
        }

        .tilt-card:hover {
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.2);
        }

        .cyber-text-shadow {
            text-shadow: 0 2px 10px rgba(255,255,255,0.3);
        }
        
        .title-3d {
            transform: translateZ(40px);
        }
    </style>

<div class="space-y-8 pb-10">

    {{-- Welcome Strip - Animated 3D Background --}}
    <div class="cyber-header px-6 py-8 sm:px-10 sm:py-10 text-white transform-style-3d perspective-1000">
        {{-- Animated Glowing Orbs --}}
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 title-3d">
            <div class="space-y-1">
                <p class="text-blue-100 text-sm font-semibold tracking-wider uppercase drop-shadow-md">Welcome Back,</p>
                <h1 class="text-white text-3xl sm:text-4xl font-black leading-tight cyber-text-shadow tracking-tight">
                    {{ Auth::user()->name }}
                </h1>
                <p class="text-blue-50 font-medium text-sm mt-2 bg-white/10 inline-block px-3 py-1 rounded-full backdrop-blur-sm border border-white/20">
                    <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            
            <div class="flex flex-col items-start sm:items-end gap-1 shrink-0 bg-black/20 p-4 rounded-2xl backdrop-blur-md border border-white/10 shadow-inner">
                <span class="text-blue-200 text-[0.65rem] uppercase tracking-[0.2em] font-black">Murid</span>
                <span class="text-white text-sm font-bold drop-shadow">UPTD SMKN 1 Majene</span>
            </div>
        </div>
    </div>

    {{-- Quick Access Grid --}}
    <div>
        <div class="flex items-center gap-2 mb-4 pl-1">
            <div class="w-1.5 h-6 bg-gradient-to-b from-[#1e3a6e] to-blue-500 rounded-full"></div>
            <p class="text-sm font-black uppercase tracking-widest text-slate-700">Akses Utama</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Kehadiran --}}
            <a href="{{ route('absensi') }}" data-tilt data-tilt-max="8" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2"
               class="tilt-card block p-6 h-full text-slate-800 cursor-pointer relative overflow-hidden group">
                <div class="tilt-card-content flex flex-col h-full">
                    <div class="flex justify-between items-start mb-4">
                        <div class="tilt-card-icon w-12 h-12 rounded-2xl flex items-center justify-center text-white transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                    <h3 class="font-black text-xl mb-2 text-slate-800">Absen Sekarang</h3>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed flex-1">
                        Isi daftar hadir untuk sesi pembelajaran hari ini dengan menekan tombol masuk.
                    </p>
                    <div class="mt-4 pt-4 border-t border-slate-200/60">
                        <span class="inline-flex items-center gap-1.5 text-[0.75rem] font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            Hari ini · {{ Carbon::today()->translatedFormat('d M') }}
                        </span>
                    </div>
                </div>
            </a>

            {{-- Literasi e-Book --}}
            <a href="{{ route('ebook.index') }}" data-tilt data-tilt-max="8" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2"
               class="tilt-card block p-6 h-full text-slate-800 cursor-pointer relative overflow-hidden group">
                <div class="tilt-card-content flex flex-col h-full">
                    <div class="flex justify-between items-start mb-4">
                        <div class="tilt-card-icon w-12 h-12 rounded-2xl flex items-center justify-center text-white transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3" style="background: linear-gradient(135deg, #1e3a6e, #3b82f6); box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                    <h3 class="font-black text-xl mb-2 text-slate-800">Literasi Digital</h3>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed flex-1">
                        Akses koleksi e-Book bertingkat dengan verifikasi suara (Voice) untuk tiap level.
                    </p>
                    <div class="mt-4 pt-4 border-t border-slate-200/60">
                        <span class="inline-flex items-center gap-1.5 text-[0.75rem] font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wide">
                            📖 e-Book & Modul
                        </span>
                    </div>
                </div>
            </a>

            {{-- Profil Saya --}}
            <a href="{{ route('profile.edit') }}" data-tilt data-tilt-max="8" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.2"
               class="tilt-card block p-6 h-full text-slate-800 cursor-pointer relative overflow-hidden group">
                <div class="tilt-card-content flex flex-col h-full">
                    <div class="flex justify-between items-start mb-4">
                        <div class="tilt-card-icon w-12 h-12 rounded-2xl flex items-center justify-center text-white transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3" style="background: linear-gradient(135deg, #1e3a6e, #3b82f6); box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                    <h3 class="font-black text-xl mb-2 text-slate-800">Profil Saya</h3>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed flex-1">
                        Lihat data identitas diri, kelas, jurusan, serta kelola keamanan akun Anda.
                    </p>
                    <div class="mt-4 pt-4 border-t border-slate-200/60">
                        @php $user = Auth::user(); @endphp
                        <span class="inline-flex items-center gap-1.5 text-[0.75rem] font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wide">
                            🎓 {{ $user->kelas ? 'Kelas ' . $user->kelas . ($user->jurusan ? ' ' . $user->jurusan : '') : 'Lengkapi profil' }}
                        </span>
                    </div>
                </div>
            </a>

        </div>
    </div>

</div>

<!-- Vanilla Tilt JS for 3D Hover Effects -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

</x-app-layout>
