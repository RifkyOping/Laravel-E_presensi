<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Panel Pengawas' }} — E-Presensi {{ \App\Models\SchoolSetting::get()->nama_sekolah }}</title>
    
    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4338ca">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

        /* Sidebar */
        #pw-sidebar {
            width: 256px; min-width: 256px;
            transition: transform 0.28s cubic-bezier(.4,0,.2,1), width 0.28s cubic-bezier(.4,0,.2,1);
        }

        /* Nav links */
        .pw-nav { display:flex; align-items:center; gap:10px; padding:.6rem .85rem;
            border-radius:10px; font-size:.845rem; font-weight:600; color:#64748b;
            text-decoration:none; transition:all .18s ease; margin-bottom:2px; }
        .pw-nav:hover { background:#f1f5f9; color:#1e3a6e; }
        .pw-nav.active { background:linear-gradient(135deg,#1e3a6e,#2d5099);
            color:#fff; box-shadow:0 4px 14px rgba(30,58,110,.3); }
        .pw-nav svg { flex-shrink:0; opacity:.7; }
        .pw-nav.active svg { opacity:1; }
        .pw-nav.active:hover { color:#fff; }

        .pw-section { font-size:.63rem; font-weight:800; letter-spacing:.1em;
            text-transform:uppercase; color:#94a3b8; padding:.9rem .85rem .35rem; display:block; }

        /* Cards */
        .pw-card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; }
        .pw-stat { background:#fff; border-radius:16px; border:1px solid #e2e8f0;
            padding:1.25rem 1.4rem; transition:transform .2s, box-shadow .2s; }
        .pw-stat:hover { transform:translateY(-3px); box-shadow:0 12px 30px rgba(0,0,0,.08); }

        /* Table */
        .pw-tbl thead th { background:#f8fafc; color:#64748b; font-size:.71rem;
            font-weight:700; letter-spacing:.07em; text-transform:uppercase;
            padding:.75rem 1rem; border-bottom:2px solid #e2e8f0; }
        .pw-tbl tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
        .pw-tbl tbody tr:hover { background:#f8fafc; }
        .pw-tbl tbody td { padding:.78rem 1rem; font-size:.84rem; color:#334155; }

        /* Badge */
        .pw-badge { padding:.22rem .65rem; border-radius:999px; font-size:.71rem; font-weight:700; display:inline-block; }
        .b-green  { background:#dcfce7; color:#15803d; }
        .b-red    { background:#fee2e2; color:#dc2626; }
        .b-amber  { background:#fef3c7; color:#b45309; }
        .b-blue   { background:#dbeafe; color:#1d4ed8; }
        .b-slate  { background:#f1f5f9; color:#64748b; }
        .b-purple { background:#f3e8ff; color:#7c3aed; }

        /* Form */
        .pw-input { width:100%; border:1.5px solid #e2e8f0; border-radius:10px;
            padding:.55rem .9rem; font-size:.84rem; font-weight:500; color:#1e293b;
            outline:none; transition:border .18s, box-shadow .18s; background:#fff; }
        .pw-input:focus { border-color:#1e3a6e; box-shadow:0 0 0 3px rgba(30,58,110,.1); }
        .pw-label { font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase;
            letter-spacing:.07em; display:block; margin-bottom:.3rem; }

        /* Alert */
        .pw-alert-ok { background:#f0fdf4; border:1.5px solid #86efac; color:#166534;
            border-radius:12px; padding:.75rem 1rem; font-weight:600; font-size:.84rem; display:flex; align-items:center; gap:.6rem; }

        /* Scrollbar */
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
        ::-webkit-scrollbar-thumb:hover { background:#94a3b8; }

        /* Mobile overlay */
        #pw-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:40; }
        #pw-overlay.active { display:block; }

        @media (max-width:1023px) {
            #pw-sidebar { position:fixed; top:0; left:0; bottom:0; z-index:50;
                transform:translateX(-100%); transition:transform .28s cubic-bezier(.4,0,.2,1); }
            #pw-sidebar.open { transform:translateX(0); }
        }

        /* Entry animation */
        @keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:none; } }
        .animate-up { animation: fadeUp .35s ease forwards; }
        .delay-1 { animation-delay:.05s; } .delay-2 { animation-delay:.1s; }
        .delay-3 { animation-delay:.15s; } .delay-4 { animation-delay:.2s; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 flex overflow-hidden">

    {{-- Mobile overlay --}}
    <div id="pw-overlay" onclick="closeMobile()"></div>

    {{-- ═══════ SIDEBAR ═══════ --}}
    <aside id="pw-sidebar" class="flex flex-col bg-white border-r border-slate-200 shadow-sm overflow-y-auto overflow-x-hidden h-full">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-slate-100">
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo {{ \App\Models\SchoolSetting::get()->nama_sekolah }}" class="w-full h-full object-contain" onerror="this.src='https://smkn1majene.sch.id/wp-content/uploads/2019/01/cropped-logo-smk-baru-e1554162985390.png'">
            </div>
            <div class="min-w-0">
                <p class="font-black text-slate-800 text-sm leading-tight">E-Presensi</p>
                <p class="text-slate-400 text-xs font-medium">{{ \App\Models\SchoolSetting::get()->nama_sekolah }}</p>
            </div>
        </div>

        {{-- User badge --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-slate-100">
            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-sm flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-slate-800 text-sm truncate">{{ Auth::user()->name }}</p>
                <span class="inline-block text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full uppercase tracking-wide">
                    Pengawas
                </span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4">
            <span class="pw-section">Menu Utama</span>

            <a href="{{ route('pengawas.dashboard') }}"
               class="pw-nav {{ request()->routeIs('pengawas.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <span class="pw-section">Monitoring Guru</span>

            <a href="{{ route('pengawas.absensi-guru') }}"
               class="pw-nav {{ request()->routeIs('pengawas.absensi-guru') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Absensi Guru
            </a>

            <a href="{{ route('pengawas.aktivitas-guru') }}"
               class="pw-nav {{ request()->routeIs('pengawas.aktivitas-guru') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Aktivitas Mengajar
            </a>

            <span class="pw-section">Monitoring Murid</span>

            <a href="{{ route('pengawas.absensi-siswa') }}"
               class="pw-nav {{ request()->routeIs('pengawas.absensi-siswa') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Absensi Murid
            </a>
        </nav>

        {{-- Footer --}}
        <div class="px-3 py-4 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                @csrf
                <button type="submit" class="pw-nav w-full text-left text-red-400"
                    style="color:#ef4444"
                    onmouseover="this.style.background='#fef2f2';this.style.color='#dc2626'"
                    onmouseout="this.style.background='';this.style.color='#ef4444'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════ MAIN ═══════ --}}
    <div class="flex-1 flex flex-col min-h-full overflow-hidden">

        {{-- Topbar --}}
        <header class="h-[60px] flex-shrink-0 sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm
                       flex items-center justify-between px-5 sm:px-8 gap-4">
            <div class="flex items-center gap-3">
                {{-- Mobile hamburger --}}
                <button class="lg:hidden p-1.5 rounded-lg hover:bg-slate-100 transition" onclick="openMobile()">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-[.9rem] font-bold text-slate-800 leading-tight">{{ $pageTitle ?? 'Panel Pengawas' }}</h1>
                    <p class="text-[.68rem] text-slate-400 hidden sm:block">
                        {{ $pageSubtitle ?? \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>

            {{-- Right --}}
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[.77rem] font-semibold text-slate-700">
                        {{ \Carbon\Carbon::now()->format('H:i') }} WITA
                    </span>
                    <span class="text-[.67rem] text-slate-400">
                        {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                    </span>
                </div>
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-sm shadow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-5 sm:p-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="flex-shrink-0 py-3 px-8 text-center text-[.7rem] text-slate-400 border-t border-slate-200 bg-white">
            &copy; {{ date('Y') }} E-Presensi {{ \App\Models\SchoolSetting::get()->nama_sekolah }} — Panel Pengawas
        </footer>
    </div>

    <script>
        function openMobile()  { document.getElementById('pw-sidebar').classList.add('open'); document.getElementById('pw-overlay').classList.add('active'); }
        function closeMobile() { document.getElementById('pw-sidebar').classList.remove('open'); document.getElementById('pw-overlay').classList.remove('active'); }
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
    <x-npsn-modal />
    <script>
        // Fitur Restore Scroll Sidebar
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarEl = document.getElementById('pw-sidebar');
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
        });
    </script>
    
    <!-- PWA Install Prompt -->
    <div id="pwa-install-prompt" class="hidden fixed bottom-4 left-4 right-4 sm:left-auto sm:right-4 sm:w-80 bg-white p-4 rounded-2xl shadow-xl border border-slate-200 z-[9999] transform transition-transform translate-y-full">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center shadow-inner overflow-hidden border border-slate-100 shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Install Aplikasi</h4>
                    <p class="text-xs text-slate-500">Akses lebih cepat & mudah</p>
                </div>
            </div>
            <button id="pwa-close-btn" class="text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full p-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <button id="pwa-install-btn" class="w-full bg-[#1e3a6e] hover:bg-[#162d57] text-white text-sm font-bold px-4 py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Install Sekarang
        </button>
    </div>

    <!-- PWA Service Worker Registration & Prompt Logic -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }).catch(err => {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }

        // PWA Install Prompt Logic
        let deferredPrompt;
        const pwaPrompt = document.getElementById('pwa-install-prompt');
        const pwaInstallBtn = document.getElementById('pwa-install-btn');
        const pwaCloseBtn = document.getElementById('pwa-close-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();

            // Tampilkan hanya 1x per sesi
            if (sessionStorage.getItem('pwaPromptShown')) {
                return;
            }

            deferredPrompt = e;
            if (pwaPrompt) {
                pwaPrompt.classList.remove('hidden');
                setTimeout(() => {
                    pwaPrompt.classList.remove('translate-y-full');
                }, 100);
                sessionStorage.setItem('pwaPromptShown', 'true');
            }
        });

        if (pwaInstallBtn) {
            pwaInstallBtn.addEventListener('click', async () => {
                if (pwaPrompt) {
                    pwaPrompt.classList.add('translate-y-full');
                    setTimeout(() => {
                        pwaPrompt.classList.add('hidden');
                    }, 300);
                }
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`User response to the install prompt: ${outcome}`);
                    deferredPrompt = null;
                }
            });
        }

        if (pwaCloseBtn) {
            pwaCloseBtn.addEventListener('click', () => {
                if (pwaPrompt) {
                    pwaPrompt.classList.add('translate-y-full');
                    setTimeout(() => {
                        pwaPrompt.classList.add('hidden');
                    }, 300);
                }
            });
        }
    </script>
</body>
</html>

