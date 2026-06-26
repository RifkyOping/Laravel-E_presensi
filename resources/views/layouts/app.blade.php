<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'E-Presensi') }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUpDashboard {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Automatically animate all direct children of dashboard containers */
        .space-y-6>div,
        .space-y-7>div,
        .space-y-6>form,
        .space-y-7>form {
            animation: fadeInUpDashboard 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .space-y-6>*:nth-child(1),
        .space-y-7>*:nth-child(1) {
            animation-delay: 0.05s;
        }

        .space-y-6>*:nth-child(2),
        .space-y-7>*:nth-child(2) {
            animation-delay: 0.15s;
        }

        .space-y-6>*:nth-child(3),
        .space-y-7>*:nth-child(3) {
            animation-delay: 0.25s;
        }

        .space-y-6>*:nth-child(4),
        .space-y-7>*:nth-child(4) {
            animation-delay: 0.35s;
        }

        .space-y-6>*:nth-child(n+5),
        .space-y-7>*:nth-child(n+5) {
            animation-delay: 0.45s;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        /* ── Sidebar ── */
        #app-sidebar {
            width: 256px;
            min-width: 256px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
        }

        /* ── Nav items ── */
        .app-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: .58rem .85rem;
            border-radius: 10px;
            font-size: .845rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all .18s ease;
            margin-bottom: 2px;
        }

        .app-nav:hover {
            background: #f0f4ff;
            color: #1e3a6e;
        }

        .app-nav.active {
            background: linear-gradient(135deg, #1e3a6e, #2d5099);
            color: #fff;
            box-shadow: 0 4px 14px rgba(30, 58, 110, .28);
        }

        .app-nav svg {
            flex-shrink: 0;
            opacity: .65;
        }

        .app-nav.active svg,
        .app-nav:hover svg {
            opacity: 1;
        }

        .app-section {
            font-size: .63rem;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #94a3b8;
            padding: .9rem .85rem .35rem;
            display: block;
        }

        /* ── Cards & Tables ── */
        .app-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
        }

        .app-tbl thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: .71rem;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            padding: .75rem 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .app-tbl tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background .15s;
        }

        .app-tbl tbody tr:hover {
            background: #f8fafc;
        }

        .app-tbl tbody td {
            padding: .78rem 1rem;
            font-size: .84rem;
            color: #334155;
        }

        /* ── Badge ── */
        .app-badge {
            padding: .22rem .65rem;
            border-radius: 999px;
            font-size: .71rem;
            font-weight: 700;
            display: inline-block;
        }

        .b-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .b-green {
            background: #dcfce7;
            color: #15803d;
        }

        .b-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .b-amber {
            background: #fef3c7;
            color: #b45309;
        }

        .b-slate {
            background: #f1f5f9;
            color: #64748b;
        }

        .b-purple {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .b-navy {
            background: #1e3a6e;
            color: #fff;
        }

        /* ── Form inputs ── */
        .app-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: .58rem .9rem;
            font-size: .84rem;
            font-weight: 500;
            color: #1e293b;
            outline: none;
            transition: border .18s, box-shadow .18s;
            background: #fff;
        }

        .app-input:focus {
            border-color: #1e3a6e;
            box-shadow: 0 0 0 3px rgba(30, 58, 110, .1);
        }

        .app-label {
            font-size: .72rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .07em;
            display: block;
            margin-bottom: .3rem;
        }

        /* ── Buttons ── */
        .btn-primary {
            background: #1e3a6e;
            color: #fff;
            border: none;
            padding: .55rem 1.1rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: .83rem;
            cursor: pointer;
            transition: background .18s, transform .15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary:hover {
            background: #162d57;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: #1e3a6e;
            border: 1.5px solid #cbd5e1;
            padding: .55rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: .83rem;
            cursor: pointer;
            transition: all .18s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-outline:hover {
            border-color: #1e3a6e;
            background: #f0f4ff;
        }

        .btn-danger {
            background: #fef2f2;
            color: #dc2626;
            border: 1.5px solid #fca5a5;
            padding: .55rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: .83rem;
            cursor: pointer;
            transition: all .18s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-danger:hover {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }

        /* ── Alert ── */
        .alert-success {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            color: #166534;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-weight: 600;
            font-size: .85rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            color: #991b1b;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-weight: 600;
            font-size: .85rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        /* ── Stat cards ── */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 1.25rem 1.4rem;
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ── Mobile overlay ── */
        #app-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 40;
        }

        #app-overlay.active {
            display: block;
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .anim-up {
            animation: fadeUp .35s ease forwards;
        }

        .d1 {
            animation-delay: .05s;
            opacity: 0;
        }

        .d2 {
            animation-delay: .1s;
            opacity: 0;
        }

        .d3 {
            animation-delay: .15s;
            opacity: 0;
        }

        .d4 {
            animation-delay: .2s;
            opacity: 0;
        }

        @media (max-width: 1023px) {
            #app-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform .28s cubic-bezier(.4, 0, .2, 1);
            }

            #app-sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="h-full bg-slate-50 text-slate-800 flex overflow-hidden">

    <div id="app-overlay" onclick="closeSidebar()"></div>

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside id="app-sidebar" class="flex flex-col overflow-y-auto overflow-x-hidden h-full shadow-sm flex-shrink-0">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-slate-100">
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Majene" class="w-full h-full object-contain"
                    onerror="this.src='https://smkn1majene.sch.id/wp-content/uploads/2019/01/cropped-logo-smk-baru-e1554162985390.png'">
            </div>
            <div class="min-w-0">
                <p class="font-black text-slate-800 text-sm leading-tight">E-Presensi</p>
                <p class="text-slate-400 text-xs font-medium">SMKN 1 Majene</p>
            </div>
        </div>

        {{-- User Badge --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-slate-100">
            <div
                class="w-9 h-9 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-sm flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-slate-800 text-sm truncate">{{ Auth::user()->name }}</p>
                @php
                    $roleLabel = match (Auth::user()->role) {
                        'admin' => ['Admin', 'bg-red-100 text-red-700'],
                        'guru' => ['Guru', 'bg-blue-100 text-blue-700'],
                        'pengawas' => ['Pengawas', 'bg-indigo-100 text-indigo-700'],
                        'kurikulum' => ['Kurikulum', 'bg-teal-100 text-teal-700'],
                        default => ['Siswa', 'bg-slate-100 text-slate-600'],
                    };
                @endphp
                <span
                    class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide {{ $roleLabel[1] }}">
                    {{ $roleLabel[0] }}
                </span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4">

            {{-- === ADMIN === --}}
            @if(Auth::user()->role === 'admin')
                <span class="app-section">Menu Utama</span>
                <a href="{{ route('admin.dashboard') }}"
                    class="app-nav {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <span class="app-section">Manajemen</span>
                <a href="{{ route('admin.users') }}"
                    class="app-nav {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    Pengguna
                </a>
                <a href="{{ route('admin.mata-pelajaran.index') }}"
                    class="app-nav {{ request()->routeIs('admin.mata-pelajaran*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Mata Pelajaran
                </a>
                <a href="{{ route('admin.kelas.index') }}"
                    class="app-nav {{ request()->routeIs('admin.kelas*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Daftar Kelas
                </a>
                <a href="{{ route('admin.ebook.index') }}"
                    class="app-nav {{ request()->routeIs('admin.ebook*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    E-Book Literasi
                </a>
                <span class="app-section">Monitoring</span>
                <a href="{{ route('admin.absensi-guru') }}"
                    class="app-nav {{ request()->routeIs('admin.absensi-guru') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Absensi Guru
                </a>
                <a href="{{ route('admin.absensi-siswa') }}"
                    class="app-nav {{ request()->routeIs('admin.absensi-siswa') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Absensi Siswa
                </a>
                <a href="{{ route('admin.aktivitas-guru') }}"
                    class="app-nav {{ request()->routeIs('admin.aktivitas-guru') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Aktivitas Mengajar
                </a>
                <a href="{{ route('admin.persetujuan-absensi') }}"
                    class="app-nav {{ request()->routeIs('admin.persetujuan-absensi') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Persetujuan Absensi
                </a>
                <span class="app-section">Pengaturan</span>
                <a href="{{ route('admin.geofence') }}"
                    class="app-nav {{ request()->routeIs('admin.geofence') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </a>

                {{-- === GURU === --}}
            @elseif(Auth::user()->role === 'guru')
                <span class="app-section">Menu Utama</span>
                <a href="{{ route('guru.dashboard') }}"
                    class="app-nav {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <span class="app-section">Presensi</span>
                <a href="{{ route('guru.absensi') }}"
                    class="app-nav {{ request()->routeIs('guru.absensi') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Absen Sekolah
                </a>
                <a href="{{ route('guru.jadwal.index') }}"
                    class="app-nav {{ request()->routeIs('guru.jadwal.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Jadwal Mengajar
                </a>
                <a href="{{ route('guru.aktivitas') }}"
                    class="app-nav {{ request()->routeIs('guru.aktivitas') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Aktivitas Mengajar
                </a>
                <a href="{{ route('guru.literasi.quran') }}"
                    class="app-nav {{ request()->routeIs('guru.literasi.quran') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Literasi Al Quran
                </a>

                {{-- === SISWA === --}}
            @elseif(Auth::user()->role === 'siswa')
                <span class="app-section">Menu Utama</span>
                <a href="{{ route('siswa.dashboard') }}"
                    class="app-nav {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <span class="app-section">Akademik</span>
                <a href="{{ route('absensi') }}" class="app-nav {{ request()->routeIs('absensi') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Absensi
                </a>
                <a href="{{ route('ebook.index') }}" class="app-nav {{ request()->routeIs('ebook.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Literasi E-Book
                </a>
                <a href="{{ route('siswa.quran') }}"
                    class="app-nav {{ request()->routeIs('siswa.quran') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Literasi Al-Qur'an
                </a>

                {{-- === PENGAWAS (fallback jika masih pakai x-app-layout) === --}}
            @elseif(Auth::user()->role === 'pengawas')
                <span class="app-section">Menu Utama</span>
                <a href="{{ route('pengawas.dashboard') }}"
                    class="app-nav {{ request()->routeIs('pengawas.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                {{-- === KURIKULUM === --}}
            @elseif(Auth::user()->role === 'kurikulum')
                <span class="app-section">Menu Utama</span>
                <a href="{{ route('kurikulum.dashboard') }}"
                    class="app-nav {{ request()->routeIs('kurikulum.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <span class="app-section">Verifikasi Mengajar</span>
                <a href="{{ route('kurikulum.monitoring-mengajar') }}"
                    class="app-nav {{ request()->routeIs('kurikulum.monitoring-mengajar') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Monitoring Mengajar
                </a>
            @endif
        </nav>

        {{-- Footer --}}
        <div class="px-3 py-4 border-t border-slate-100 space-y-1">
            <a href="{{ route('profile.edit') }}"
                class="app-nav {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil Saya
            </a>
            <form method="POST" action="{{ route('logout') }}"
                onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                @csrf
                <button type="submit" class="app-nav w-full text-left" style="color:#ef4444">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ════════════ MAIN ════════════ --}}
    <div class="flex-1 flex flex-col min-h-full overflow-hidden">

        {{-- Topbar --}}
        <header class="h-[60px] flex-shrink-0 sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm
                       flex items-center justify-between px-5 sm:px-8 gap-4">
            <div class="flex items-center gap-3">
                {{-- Mobile hamburger --}}
                <button class="lg:hidden p-1.5 rounded-lg hover:bg-slate-100 transition" onclick="openSidebar()">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Header slot (title from view) --}}
                @isset($header)
                    <div>{{ $header }}</div>
                @else
                    <p class="text-sm font-bold text-slate-700">{{ config('app.name', 'E-Presensi') }}</p>
                @endisset
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[.77rem] font-semibold text-slate-700"><span class="realtime-clock"></span>
                        WITA</span>
                </div>
                <div
                    class="w-8 h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-sm shadow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-5 sm:p-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer
            class="flex-shrink-0 py-3 px-8 text-center text-[.7rem] text-slate-400 border-t border-slate-200 bg-white">
            © {{ date('Y') }} E-Presensi SMKN 1 Majene
        </footer>
    </div>

    <script>
        function openSidebar() { document.getElementById('app-sidebar').classList.add('open'); document.getElementById('app-overlay').classList.add('active'); }
        function closeSidebar() { document.getElementById('app-sidebar').classList.remove('open'); document.getElementById('app-overlay').classList.remove('active'); }

        /* ── Realtime Clock ── */
        (function () {
            function pad(n) { return n < 10 ? '0' + n : n; }
            function tick() {
                const now = new Date();
                const hm = pad(now.getHours()) + ':' + pad(now.getMinutes());
                const hms = hm + ':' + pad(now.getSeconds());
                document.querySelectorAll('.realtime-clock').forEach(el => {
                    el.textContent = el.dataset.showSeconds === '1' ? hms : hm;
                });
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('return confirm(')) {
                    // Extract the message from confirm('Message')
                    const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    const message = match ? match[1] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

                    // Remove the onsubmit attribute to prevent default browser confirm
                    form.removeAttribute('onsubmit');

                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Lanjutkan!',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            customClass: {
                                popup: 'rounded-2xl shadow-2xl border border-slate-100',
                                title: 'text-xl font-black text-slate-800',
                                confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm',
                                cancelButton: 'font-bold rounded-xl px-6 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border-none shadow-sm'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                }
            });
        });
    </script>
    @stack('modals')
</body>

</html>