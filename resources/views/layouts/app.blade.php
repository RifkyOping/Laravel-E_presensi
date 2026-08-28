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

        .app-tbl thead tr {
            border-bottom: 1px solid #f1f5f9;
            background: rgba(248, 250, 252, 0.7);
        }

        .app-tbl thead th {
            color: #94a3b8;
            font-size: .7rem;
            font-weight: 900;
            letter-spacing: .05em;
            text-transform: uppercase;
            padding: .875rem 1.5rem;
            border: none;
            background: transparent;
        }

        .app-tbl tbody tr {
            border-bottom: 1px solid #f8fafc;
            transition: background .15s;
        }

        .app-tbl tbody tr:hover {
            background: #f8fafc;
        }

        .app-tbl tbody td {
            padding: .875rem 1.25rem;
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
            padding: .625rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: .875rem;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
        }

        .btn-primary:hover {
            background: #162d57;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-outline {
            background: transparent;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: .625rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: .875rem;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-outline:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .btn-danger {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            padding: .625rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: .875rem;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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

    {{-- Global Page Loader --}}
    <div id="global-loader"
        class="fixed inset-0 z-[99999] bg-slate-50 flex flex-col items-center justify-center transition-opacity duration-500">
        <div class="relative flex justify-center items-center">
            <div class="absolute animate-ping inline-flex h-16 w-16 rounded-full bg-[#1e3a6e] opacity-20"></div>
            <div
                class="inline-flex rounded-full h-14 w-14 border-4 border-slate-200 border-t-[#1e3a6e] border-r-[#1e3a6e] animate-spin">
            </div>
            <div class="absolute w-8 h-8 bg-white/50 rounded-full"></div>
        </div>
        <p class="mt-4 text-xs font-black text-[#1e3a6e] tracking-widest uppercase animate-pulse">Memuat...</p>
    </div>

    <div id="app-overlay" onclick="closeSidebar()"></div>

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside id="app-sidebar" class="flex flex-col overflow-y-auto overflow-x-hidden h-full shadow-sm flex-shrink-0">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-slate-100">
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}"
                    alt="Logo {{ \App\Models\SchoolSetting::get()->nama_sekolah }}" class="w-full h-full object-contain"
                    onerror="this.src='https://smkn1majene.sch.id/wp-content/uploads/2019/01/cropped-logo-smk-baru-e1554162985390.png'">
            </div>
            <div class="min-w-0">
                <p class="font-black text-slate-800 text-sm leading-tight">E-Presensi</p>
                <p class="text-slate-400 text-xs font-medium">{{ \App\Models\SchoolSetting::get()->nama_sekolah }}</p>
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
                    $isKepsek = Auth::user()->is_kepsek;
                    $isKurikulum = Auth::user()->is_kurikulum;
                    $jabatanLabel = Auth::user()->jabatan_label;

                    if ($jabatanLabel) {
                        $roleText = $jabatanLabel;
                        $roleClass = $isKepsek ? 'bg-amber-100 text-amber-700' : 'bg-teal-100 text-teal-700';
                    } else {
                        $roleData = match (Auth::user()->role) {
                            'admin' => ['Admin', 'bg-red-100 text-red-700'],
                            'guru' => ['Guru', 'bg-blue-100 text-blue-700'],
                            'pengawas' => ['Pengawas', 'bg-indigo-100 text-indigo-700'],
                            'murid' => ['Murid', 'bg-slate-100 text-slate-600'],
                            default => [ucfirst(Auth::user()->role), 'bg-slate-100 text-slate-600'],
                        };
                        $roleText = $roleData[0];
                        $roleClass = $roleData[1];
                    }
                @endphp
                <span
                    class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide {{ $roleClass }}">
                    {{ $roleText }}
                </span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4">

            {{-- === ADMIN === --}}
            @if (Auth::user()->role === 'admin')
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
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 -960 960 960">
                        <path
                            d="M160-391h45l23-66h104l24 66h44l-97-258h-46l-97 258Zm81-103 38-107h2l38 107h-78Zm319-70v-68q33-14 67.5-21t72.5-7q26 0 51 4t49 10v64q-24-9-48.5-13.5T700-600q-38 0-73 9.5T560-564Zm0 220v-68q33-14 67.5-21t72.5-7q26 0 51 4t49 10v64q-24-9-48.5-13.5T700-380q-38 0-73 9t-67 27Zm0-110v-68q33-14 67.5-21t72.5-7q26 0 51 4t49 10v64q-24-9-48.5-13.5T700-490q-38 0-73 9.5T560-454ZM260-320q47 0 91.5 10.5T440-278v-394q-41-24-87-36t-93-12q-36 0-71.5 7T120-692v396q35-12 69.5-18t70.5-6Zm260 42q44-21 88.5-31.5T700-320q36 0 70.5 6t69.5 18v-396q-33-14-68.5-21t-71.5-7q-47 0-93 12t-87 36v394Zm-40 118q-48-38-104-59t-116-21q-42 0-82.5 11T100-198q-21 11-40.5-1T40-234v-482q0-11 5.5-21T62-752q46-24 96-36t102-12q58 0 113.5 15T480-740q51-30 106.5-45T700-800q52 0 102 12t96 36q11 5 16.5 15t5.5 21v482q0 23-19.5 35t-40.5 1q-37-20-77.5-31T700-240q-60 0-116 21t-104 59ZM280-499Z" />
                    </svg>
                    Mata Pelajaran
                </a>
                <a href="{{ route('admin.kelas.index') }}"
                    class="app-nav {{ request()->routeIs('admin.kelas*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 -960 960 960">
                        <path
                            d="M120-240v-80h520v80H120Zm664-40L584-480l200-200 56 56-144 144 144 144-56 56ZM120-440v-80h400v80H120Zm0-200v-80h520v80H120Z" />
                    </svg>
                    Daftar Kelas
                </a>
                <a href="{{ route('admin.ebook.index') }}"
                    class="app-nav {{ request()->routeIs('admin.ebook*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Literasi e-Book
                </a>
                <a href="{{ route('admin.jadwal-mengajar.index') }}"
                    class="app-nav {{ request()->routeIs('admin.jadwal-mengajar*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Jadwal Mengajar
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
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Absensi Murid
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
                <a href="{{ route('admin.rekap-rpp') }}"
                    class="app-nav {{ request()->routeIs('admin.rekap-rpp') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Rekap RPP
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

                <a href="{{ route('guru.scan-qr') }}"
                    class="app-nav {{ request()->routeIs('guru.scan-qr') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Scan Absen QR
                </a>

                <a href="{{ route('guru.rpp.index') }}"
                    class="app-nav {{ request()->routeIs('guru.rpp.index') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload RPP
                </a>
                <a href="{{ route('guru.absen-kelas.index') }}"
                    class="app-nav {{ request()->routeIs('guru.absen-kelas.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Absen Kelas
                </a>
                <a href="{{ route('guru.buku-kemajuan') }}"
                    class="app-nav {{ request()->routeIs('guru.buku-kemajuan*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Monitoring Kelas
                </a>
                <a href="{{ route('guru.jadwal.index') }}"
                    class="app-nav {{ request()->routeIs('guru.jadwal.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 -960 960 960">
                        <path
                            d="M200-640h560v-80H200v80Zm0 0v-80 80Zm0 560q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v227q-19-9-39-15t-41-9v-43H200v400h252q7 22 16.5 42T491-80H200Zm378.5-18.5Q520-157 520-240t58.5-141.5Q637-440 720-440t141.5 58.5Q920-323 920-240T861.5-98.5Q803-40 720-40T578.5-98.5ZM787-145l28-28-75-75v-112h-40v128l87 87Z" />
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
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 -960 960 960">
                        <path
                            d="M475-160q4 0 8-2t6-4l328-328q12-12 17.5-27t5.5-30q0-16-5.5-30.5T817-607L647-777q-11-12-25.5-17.5T591-800q-15 0-30 5.5T534-777l-11 11 74 75q15 14 22 32t7 38q0 42-28.5 70.5T527-522q-20 0-38.5-7T456-550l-75-74-175 175q-3 3-4.5 6.5T200-435q0 8 6 14.5t14 6.5q4 0 8-2t6-4l136-136 56 56-135 136q-3 3-4.5 6.5T285-350q0 8 6 14t14 6q4 0 8-2t6-4l136-135 56 56-135 136q-3 2-4.5 6t-1.5 8q0 8 6 14t14 6q4 0 7.5-1.5t6.5-4.5l136-135 56 56-136 136q-3 3-4.5 6.5T454-180q0 8 6.5 14t14.5 6Zm-1 80q-37 0-65.5-24.5T375-166q-34-5-57-28t-28-57q-34-5-56.5-28.5T206-336q-38-5-62-33t-24-66q0-20 7.5-38.5T149-506l232-231 131 131q2 3 6 4.5t8 1.5q9 0 15-5.5t6-14.5q0-4-1.5-8t-4.5-6L398-777q-11-12-25.5-17.5T342-800q-15 0-30 5.5T285-777L144-635q-9 9-15 21t-8 24q-2 12 0 24.5t8 23.5l-58 58q-17-23-25-50.5T40-590q2-28 14-54.5T87-692l141-141q24-23 53.5-35t60.5-12q31 0 60.5 12t52.5 35l11 11 11-11q24-23 53.5-35t60.5-12q31 0 60.5 12t52.5 35l169 169q23 23 35 53t12 61q0 31-12 60.5T873-437L545-110q-14 14-32.5 22T474-80Zm-99-560Z" />
                    </svg>
                    Literasi Keagamaan
                </a>
                <a href="{{ route('guru.literasi.catatan') }}"
                    class="app-nav {{ request()->routeIs('guru.literasi.catatan') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Literasi Buku
                </a>
                <a href="{{ route('guru.persetujuan-absensi') }}"
                    class="app-nav {{ request()->routeIs('guru.persetujuan-absensi') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Persetujuan Absensi
                </a>

                {{-- === SISWA === --}}
            @elseif(Auth::user()->role === 'murid')
                <span class="app-section">Menu Utama</span>
                <a href="{{ route('murid.dashboard') }}"
                    class="app-nav {{ request()->routeIs('murid.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                <span class="app-section">Akademik</span>
                <a href="{{ route('absensi') }}"
                    class="app-nav {{ request()->routeIs('absensi') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Absen Sekolah
                </a>

                @if (Auth::user()->siswaProfile && strtolower(Auth::user()->siswaProfile->agama) === 'islam')
                    <a href="{{ route('murid.sholat') }}"
                        class="app-nav {{ request()->routeIs('murid.sholat') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 -960 960 960"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M40-120v-491q-18-11-29-28.5T0-680q0-23 24-56t56-64q32 31 56 64t24 56q0 23-11 40.5T120-611v171h80v-80q0-25 16-48t46-30q-11-17-16.5-37t-5.5-41q0-40 19-74t51-56l170-114 170 114q32 22 51 56t19 74q0 21-5.5 41T698-598q30 7 46 30t16 48v80h80v-171q-18-11-29-28.5T800-680q0-23 24-56t56-64q32 31 56 64t24 56q0 23-11 40.5T920-611v491H520v-160q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280v160H40Zm356-480h168q32 0 54-22t22-54q0-20-9-36.5T606-740l-126-84-126 84q-16 11-25 27.5t-9 36.5q0 32 22 54t54 22ZM120-200h240v-80q0-50 35-85t85-35q50 0 85 35t35 85v80h240v-160H680v-160H280v160H120v160Zm360-320Zm0-80Zm0 2Z" />
                        </svg>
                        Absen Sholat
                    </a>
                @endif
                <a href="{{ route('murid.monitoring-kelas') }}"
                    class="app-nav {{ request()->routeIs('murid.monitoring-kelas') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Monitoring Kelas
                </a>
                {{-- Literasi e-Book (collapsible sub-menu) --}}
                @php
                    $isEbookActive = request()->routeIs('ebook.*');
                @endphp
                <div x-data="{ open: {{ $isEbookActive ? 'true' : 'false' }} }">
                    {{-- Parent toggle button --}}
                    <button @click="open = !open"
                        class="app-nav w-full text-left {{ $isEbookActive ? 'active' : '' }}"
                        style="justify-content: space-between;">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Literasi Buku
                        </span>
                        <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                            :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    {{-- Sub-menu items --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 ml-4 pl-3 border-l-2 border-slate-200 space-y-0.5" style="display: none;">
                        <a href="{{ route('ebook.index') }}"
                            class="app-nav text-sm {{ request()->routeIs('ebook.index') || (request()->routeIs('ebook.*') && !request()->routeIs('ebook.manual*')) ? 'active' : '' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 -960 960 960"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M300-80q-58 0-99-41t-41-99v-520q0-58 41-99t99-41h500v600q-25 0-42.5 17.5T740-220q0 25 17.5 42.5T800-160v80H300Zm-60-267q14-7 29-10t31-3h20v-440h-20q-25 0-42.5 17.5T240-740v393Zm160-13h320v-440H400v440Zm-160 13v-453 453Zm60 187h373q-6-14-9.5-28.5T660-220q0-16 3-31t10-29H300q-26 0-43 17.5T240-220q0 26 17 43t43 17Z" />
                            </svg>
                            Buku Digital
                        </a>
                        <a href="{{ route('ebook.manual.index') }}"
                            class="app-nav text-sm {{ request()->routeIs('ebook.manual*') ? 'active' : '' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 -960 960 960"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M270-80q-45 0-77.5-30.5T160-186v-558q0-38 23.5-68t61.5-38l395-78v640l-379 76q-9 2-15 9.5t-6 16.5q0 11 9 18.5t21 7.5h450v-640h80v720H270Zm90-233 200-39v-478l-200 39v478Zm-80 16v-478l-15 3q-11 2-18 9.5t-7 18.5v457q5-2 10.5-3.5T261-293l19-4Zm-40-472v482-482Z" />
                            </svg>
                            Buku Cetak
                        </a>
                    </div>
                </div>

                <a href="{{ route('murid.quran') }}"
                    class="app-nav {{ request()->routeIs('murid.quran') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 -960 960 960">
                        <path
                            d="M475-160q4 0 8-2t6-4l328-328q12-12 17.5-27t5.5-30q0-16-5.5-30.5T817-607L647-777q-11-12-25.5-17.5T591-800q-15 0-30 5.5T534-777l-11 11 74 75q15 14 22 32t7 38q0 42-28.5 70.5T527-522q-20 0-38.5-7T456-550l-75-74-175 175q-3 3-4.5 6.5T200-435q0 8 6 14.5t14 6.5q4 0 8-2t6-4l136-136 56 56-135 136q-3 3-4.5 6.5T285-350q0 8 6 14t14 6q4 0 8-2t6-4l136-135 56 56-135 136q-3 2-4.5 6t-1.5 8q0 8 6 14t14 6q4 0 7.5-1.5t6.5-4.5l136-135 56 56-136 136q-3 3-4.5 6.5T454-180q0 8 6.5 14t14.5 6Zm-1 80q-37 0-65.5-24.5T375-166q-34-5-57-28t-28-57q-34-5-56.5-28.5T206-336q-38-5-62-33t-24-66q0-20 7.5-38.5T149-506l232-231 131 131q2 3 6 4.5t8 1.5q9 0 15-5.5t6-14.5q0-4-1.5-8t-4.5-6L398-777q-11-12-25.5-17.5T342-800q-15 0-30 5.5T285-777L144-635q-9 9-15 21t-8 24q-2 12 0 24.5t8 23.5l-58 58q-17-23-25-50.5T40-590q2-28 14-54.5T87-692l141-141q24-23 53.5-35t60.5-12q31 0 60.5 12t52.5 35l11 11 11-11q24-23 53.5-35t60.5-12q31 0 60.5 12t52.5 35l169 169q23 23 35 53t12 61q0 31-12 60.5T873-437L545-110q-14 14-32.5 22T474-80Zm-99-560Z" />
                    </svg>
                    Literasi Keagamaan
                </a>

                @if (Auth::user()->siswaProfile && strtolower(Auth::user()->siswaProfile->agama) === 'islam')
                    <a href="{{ route('murid.baca-quran.index') }}"
                        class="app-nav {{ request()->routeIs('murid.baca-quran.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="-32 0 512 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM301.08 145.82c.6-1.21 1.76-1.82 2.92-1.82s2.32.61 2.92 1.82l11.18 22.65 25 3.63c2.67.39 3.74 3.67 1.81 5.56l-18.09 17.63 4.27 24.89c.36 2.11-1.31 3.82-3.21 3.82-.5 0-1.02-.12-1.52-.38L304 211.87l-22.36 11.75c-.5.26-1.02.38-1.52.38-1.9 0-3.57-1.71-3.21-3.82l4.27-24.89-18.09-17.63c-1.94-1.89-.87-5.17 1.81-5.56l24.99-3.63 11.19-22.65zm-57.89-69.01c13.67 0 27.26 2.49 40.38 7.41a6.775 6.775 0 1 1-2.38 13.12c-.67 0-3.09-.21-4.13-.21-52.31 0-94.86 42.55-94.86 94.86 0 52.3 42.55 94.86 94.86 94.86 1.03 0 3.48-.21 4.13-.21 3.93 0 6.8 3.14 6.8 6.78 0 2.98-1.94 5.51-4.62 6.42-13.07 4.87-26.59 7.34-40.19 7.34C179.67 307.19 128 255.51 128 192c0-63.52 51.67-115.19 115.19-115.19zM380.8 448H96c-19.2 0-32-12.8-32-32s16-32 32-32h284.8v64z" />
                        </svg>
                        Baca Al-Qur'an
                    </a>
                @endif

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

                <span class="app-section">Monitoring</span>
                <a href="{{ route('pengawas.absensi-guru') }}"
                    class="app-nav {{ request()->routeIs('pengawas.absensi-guru') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Absensi Guru
                </a>

                <a href="{{ route('pengawas.absensi-siswa') }}"
                    class="app-nav {{ request()->routeIs('pengawas.absensi-siswa') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Absensi Murid
                </a>

                <a href="{{ route('pengawas.aktivitas-guru') }}"
                    class="app-nav {{ request()->routeIs('pengawas.aktivitas-guru') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Aktivitas Mengajar
                </a>

            @endif

            {{-- === JABATAN (KEPSEK & KURIKULUM) === --}}
            @if (Auth::user()->is_kepsek || Auth::user()->is_kurikulum)
                <span class="app-section">Monitoring Sekolah</span>
                <a href="{{ route('monitoring-sekolah.dashboard') }}"
                    class="app-nav {{ request()->routeIs('monitoring-sekolah.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard Monitoring
                </a>
            @endif

            {{-- === PIKET === --}}
            @if (Auth::user()->is_piket_sholat ||
                    Auth::user()->is_piket_mengajar ||
                    Auth::user()->is_guru_bahasa ||
                    Auth::user()->is_piket_rpp)
                <span class="app-section">Piket</span>
                @if (Auth::user()->is_piket_sholat)
                    <a href="{{ route('piket.sholat.index') }}"
                        class="app-nav {{ request()->routeIs('piket.sholat*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 -960 960 960"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M40-120v-491q-18-11-29-28.5T0-680q0-23 24-56t56-64q32 31 56 64t24 56q0 23-11 40.5T120-611v171h80v-80q0-25 16-48t46-30q-11-17-16.5-37t-5.5-41q0-40 19-74t51-56l170-114 170 114q32 22 51 56t19 74q0 21-5.5 41T698-598q30 7 46 30t16 48v80h80v-171q-18-11-29-28.5T800-680q0-23 24-56t56-64q32 31 56 64t24 56q0 23-11 40.5T920-611v491H520v-160q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280v160H40Zm356-480h168q32 0 54-22t22-54q0-20-9-36.5T606-740l-126-84-126 84q-16 11-25 27.5t-9 36.5q0 32 22 54t54 22ZM120-200h240v-80q0-50 35-85t85-35q50 0 85 35t35 85v80h240v-160H680v-160H280v160H120v160Zm360-320Zm0-80Zm0 2Z" />
                        </svg>
                        Absen Sholat
                    </a>
                @endif
                @if (Auth::user()->is_piket_mengajar)
                    <a href="{{ route('piket.mengajar.index') }}"
                        class="app-nav {{ request()->routeIs('piket.mengajar*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Verifikasi Aktivitas
                    </a>
                @endif
                @if (Auth::user()->is_piket_rpp)
                    <a href="{{ route('piket.persetujuan-rpp') }}"
                        class="app-nav {{ request()->routeIs('piket.persetujuan-rpp*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Verifikasi RPP
                    </a>
                @endif
                @if (Auth::user()->is_guru_bahasa)
                    <a href="{{ route('piket.literasi.jawaban-indikator') }}"
                        class="app-nav {{ request()->routeIs('piket.literasi.jawaban-indikator') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Jawaban Indikator
                    </a>
                    <a href="{{ route('piket.indikator.index') }}"
                        class="app-nav {{ request()->routeIs('piket.indikator*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Manajemen Indikator
                    </a>
                @endif
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
        <header
            class="h-[60px] flex-shrink-0 sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm
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

        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-5 sm:p-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer
            class="flex-shrink-0 py-3 px-8 text-center text-[.7rem] text-slate-400 border-t border-slate-200 bg-white">
            &copy; {{ date('Y') }} E-Presensi {{ \App\Models\SchoolSetting::get()->nama_sekolah }}
        </footer>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('app-sidebar').classList.add('open');
            document.getElementById('app-overlay').classList.add('active');
        }

        function closeSidebar() {
            document.getElementById('app-sidebar').classList.remove('open');
            document.getElementById('app-overlay').classList.remove('active');
        }

        /* ── Realtime Clock ── */
        (function() {
            function pad(n) {
                return n < 10 ? '0' + n : n;
            }

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
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('return confirm(')) {
                    // Extract the message from confirm('Message')
                    const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    const message = match ? match[1] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

                    // Remove the onsubmit attribute to prevent default browser confirm
                    form.removeAttribute('onsubmit');

                    form.addEventListener('submit', function(e) {
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

    <!-- Web Cron: Trigger otomatis cron dari background browser (Berjalan setiap kali direfresh) -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Berjalan setiap kali halaman dimuat/direfresh sesuai permintaan
            // Delay 2 detik agar tidak memblokir render UI awal halaman
            setTimeout(() => {
                const cronToken = '{{ env('CRON_SECRET', 'rahasia123') }}';
                // Chain requests (dieksekusi satu-satu bergiliran) agar tidak boros koneksi TCP
                fetch('/cron/generate-aktivitas-mengajar?token=' + cronToken)
                    .finally(() => fetch('/cron/cek-alpha?token=' + cronToken))
                    .finally(() => fetch('/cron/cek-lupa-pulang?token=' + cronToken));
            }, 2000);
        });
    </script>

    @stack('modals')

    {{-- Script Global Loader --}}
    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('global-loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 500);
            }
        });

        window.addEventListener('beforeunload', function() {
            const loader = document.getElementById('global-loader');
            if (loader) {
                loader.style.display = 'flex';
                // Force reflow
                void loader.offsetWidth;
                loader.style.opacity = '1';
            }
        });
    </script>

    {{-- Script Pull to Refresh --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainEl = document.querySelector('main');
            if (!mainEl) return;

            let startY = 0;
            let currentY = 0;
            let isRefreshing = false;

            // Buat elemen indikator
            const ptrEl = document.createElement('div');
            ptrEl.className = 'w-full flex justify-center items-center overflow-hidden transition-all duration-200';
            ptrEl.style.height = '0px';
            ptrEl.innerHTML = `
                <div class="flex items-center justify-center text-[#1e3a6e] font-bold text-[10px] bg-white px-5 py-2.5 rounded-full shadow-md border border-slate-100 uppercase tracking-widest">
                    <span id="ptr-text">Usap ke bawah</span>
                </div>
            `;
            mainEl.prepend(ptrEl);

            const ptrText = document.getElementById('ptr-text');

            mainEl.addEventListener('touchstart', e => {
                if (mainEl.scrollTop === 0 && !isRefreshing) {
                    startY = e.touches[0].clientY;
                }
            }, {
                passive: true
            });

            mainEl.addEventListener('touchmove', e => {
                if (startY > 0 && !isRefreshing && mainEl.scrollTop === 0) {
                    currentY = e.touches[0].clientY;
                    const distance = currentY - startY;

                    if (distance > 0) {
                        const h = Math.min(distance * 0.4, 80);
                        ptrEl.style.height = h + 'px';
                        ptrEl.style.paddingTop = '1rem';
                        ptrEl.style.paddingBottom = '1rem';

                        if (h > 60) {
                            ptrText.textContent = 'Lepaskan untuk memuat ulang';
                        } else {
                            ptrText.textContent = 'Usap ke bawah';
                        }
                    }
                }
            }, {
                passive: true
            });

            mainEl.addEventListener('touchend', e => {
                if (startY > 0 && !isRefreshing) {
                    const distance = currentY - startY;
                    const h = Math.min(distance * 0.4, 80);

                    if (h > 60) {
                        isRefreshing = true;
                        ptrEl.style.height = '60px';
                        ptrText.textContent = 'Memuat ulang...';
                        window.location.reload();
                    } else {
                        ptrEl.style.height = '0px';
                        ptrEl.style.paddingTop = '0px';
                        ptrEl.style.paddingBottom = '0px';
                    }
                }
                startY = 0;
                currentY = 0;
            }, {
                passive: true
            });
        });
    </script>
    <x-npsn-modal />

    <script>
        // Fitur Restore Scroll Global untuk seluruh halaman dan sidebar
        document.addEventListener("DOMContentLoaded", function() {
            const mainEl = document.querySelector('main');
            const sidebarEl = document.getElementById('app-sidebar');

            // --- Restore & Save Sidebar Scroll ---
            if (sidebarEl) {
                const sidebarScrollKey = 'sidebar_scroll_' + window.location.pathname;

                // Restore
                const savedSidebarScroll = sessionStorage.getItem(sidebarScrollKey);
                if (savedSidebarScroll) {
                    setTimeout(() => {
                        sidebarEl.scrollTo(0, parseInt(savedSidebarScroll, 10));
                    }, 50);
                }

                // Simpan
                window.addEventListener('beforeunload', () => {
                    sessionStorage.setItem(sidebarScrollKey, sidebarEl.scrollTop);
                });
            }

            if (!mainEl) return;

            const scrollKey = 'global_scroll_' + window.location.pathname;

            // Pengecualian: jangan timpa halaman yang memiliki sistem auto-scroll bawaannya sendiri
            if (!window.location.pathname.includes('/baca-quran/surah') && !window.location.pathname.includes(
                    '/baca-quran/juz')) {
                // Restore
                const savedScroll = sessionStorage.getItem(scrollKey);
                if (savedScroll) {
                    setTimeout(() => {
                        mainEl.scrollTo(0, parseInt(savedScroll, 10));
                    }, 50); // Jeda singkat agar DOM sempat dimuat
                }

                // Simpan
                window.addEventListener('beforeunload', () => {
                    sessionStorage.setItem(scrollKey, mainEl.scrollTop);
                });
            }
        });
    </script>
</body>

</html>
