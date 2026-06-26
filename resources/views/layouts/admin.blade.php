<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel — E-Presensi SMKN 1 Majene</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUpDashboard {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Automatically animate all direct children of dashboard containers */
        .space-y-6 > div, .space-y-7 > div, .space-y-6 > form, .space-y-7 > form {
            animation: fadeInUpDashboard 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .space-y-6 > *:nth-child(1), .space-y-7 > *:nth-child(1) { animation-delay: 0.05s; }
        .space-y-6 > *:nth-child(2), .space-y-7 > *:nth-child(2) { animation-delay: 0.15s; }
        .space-y-6 > *:nth-child(3), .space-y-7 > *:nth-child(3) { animation-delay: 0.25s; }
        .space-y-6 > *:nth-child(4), .space-y-7 > *:nth-child(4) { animation-delay: 0.35s; }
        .space-y-6 > *:nth-child(n+5), .space-y-7 > *:nth-child(n+5) { animation-delay: 0.45s; }
        * { font-family: 'Inter', sans-serif; }
        .sidebar-link { transition: all .2s; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,255,255,0.15);
            transform: translateX(4px);
        }
        .sidebar-link.active { background: rgba(255,255,255,0.2); border-left: 3px solid white; }
        .stat-card { transition: transform .2s, box-shadow .2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(36,65,124,.18); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #24417c44; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: #24417c99; }
    </style>
</head>
<body class="h-full bg-slate-50 text-gray-800 flex" x-data="{ sidebarOpen: true, mobileSidebar: false }">

    {{-- ═══════════════════════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════════════════════ --}}
    {{-- Mobile overlay --}}
    <div x-show="mobileSidebar" x-cloak @click="mobileSidebar = false"
        class="fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

    <aside
        :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:static inset-y-0 left-0 z-40 flex flex-col w-64 bg-[#24417c] text-white transition-transform duration-300 ease-in-out h-full overflow-y-auto shadow-2xl">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Majene" class="w-full h-full object-contain" onerror="this.src='https://smkn1majene.sch.id/wp-content/uploads/2019/01/cropped-logo-smk-baru-e1554162985390.png'">
            </div>
            <div>
                <p class="font-black text-base leading-tight">E-Presensi</p>
                <p class="text-xs text-white/60 font-medium">SMKN 1 Majene</p>
            </div>
        </div>

        {{-- Admin Badge --}}
        <div class="px-6 py-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center font-black text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-sm truncate">{{ Auth::user()->name }}</p>
                    <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full uppercase tracking-wider">Admin</span>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            <p class="text-[10px] font-black uppercase tracking-widest text-white/40 px-3 mb-2">Menu Utama</p>

            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">🏠</span> Dashboard
            </a>

            <p class="text-[10px] font-black uppercase tracking-widest text-white/40 px-3 mt-5 mb-2">Manajemen</p>

            <a href="{{ route('admin.users') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.users*') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">👥</span> Pengguna
            </a>

            <a href="{{ route('admin.mata-pelajaran.index') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.mata-pelajaran*') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">📖</span> Mata Pelajaran
            </a>

            <a href="{{ route('admin.ebook.index') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.ebook*') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">📱</span> E-Book Literasi
            </a>

            <p class="text-[10px] font-black uppercase tracking-widest text-white/40 px-3 mt-5 mb-2">Monitoring</p>

            <a href="{{ route('admin.absensi-guru') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.absensi-guru') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">📋</span> Absensi Guru
            </a>

            <a href="{{ route('admin.absensi-siswa') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.absensi-siswa') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">🧑‍🎓</span> Absensi Siswa
            </a>

            <a href="{{ route('admin.aktivitas-guru') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.aktivitas-guru') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">📚</span> Aktivitas Mengajar
            </a>

            <a href="{{ route('admin.persetujuan-absensi') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.persetujuan-absensi') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">✅</span> Persetujuan Absensi
            </a>

            <p class="text-[10px] font-black uppercase tracking-widest text-white/40 px-3 mt-5 mb-2">Pengaturan</p>

            <a href="{{ route('admin.geofence') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold
                    {{ request()->routeIs('admin.geofence') ? 'active' : 'text-white/80 hover:text-white' }}">
                <span class="text-lg">📍</span> Pengaturan Absensi
            </a>
        </nav>

        {{-- Footer --}}
        <div class="px-4 py-4 border-t border-white/10 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-white/80 hover:text-white">
                <span class="text-lg">↩️</span> Keluar Panel Admin
            </a>
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                @csrf
                <button type="submit"
                    class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-300 hover:text-white hover:bg-red-500/20 transition">
                    <span class="text-lg">🚪</span> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-h-full overflow-x-hidden">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-slate-200 px-4 sm:px-8 h-16 flex items-center justify-between gap-4 shadow-sm">
            {{-- Mobile hamburger --}}
            <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title --}}
            <div class="flex-1">
                <h1 class="text-lg font-black text-[#24417c]">{{ $pageTitle ?? 'Admin Panel' }}</h1>
                <p class="text-xs text-slate-400 font-medium hidden sm:block">{{ $pageSubtitle ?? \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                <span class="hidden sm:block text-xs font-bold text-slate-400">{{ \Carbon\Carbon::now()->format('H:i') }} WITA</span>
                <div class="w-8 h-8 rounded-full bg-[#24417c] text-white flex items-center justify-center font-black text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <main class="flex-1 p-4 sm:p-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="text-center py-4 text-xs text-slate-400 font-medium border-t border-slate-200 bg-white/50">
            E-Presensi SMKN 1 Majene &copy; {{ date('Y') }} — Admin Panel
        </footer>
    </div>

</body>
</html>

