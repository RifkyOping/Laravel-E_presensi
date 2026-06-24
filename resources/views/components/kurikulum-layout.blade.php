@props(['pageTitle' => 'Panel Kurikulum', 'pageSubtitle' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} — E-Presensi SMKN 1 Majene</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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

        /* ── Sidebar ── */
        #kur-sidebar {
            width: 256px; min-width: 256px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
        }

        /* ── Nav items ── */
        .kur-nav {
            display: flex; align-items: center; gap: 10px;
            padding: .58rem .85rem; border-radius: 10px;
            font-size: .845rem; font-weight: 600; color: #64748b;
            text-decoration: none; transition: all .18s ease; margin-bottom: 2px;
        }
        .kur-nav:hover { background: #f0f4ff; color: #1e3a6e; }
        .kur-nav.active {
            background: linear-gradient(135deg, #1e3a6e, #2d5099);
            color: #fff; box-shadow: 0 4px 14px rgba(30,58,110,.28);
        }
        .kur-nav svg { flex-shrink: 0; opacity: .65; }
        .kur-nav.active svg, .kur-nav:hover svg { opacity: 1; }
        .kur-section {
            font-size: .63rem; font-weight: 800; letter-spacing: .1em;
            text-transform: uppercase; color: #94a3b8;
            padding: .9rem .85rem .35rem; display: block;
        }

        /* ── Cards & Tables ── */
        .kur-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; }
        .kur-stat {
            background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
            padding: 1.25rem 1.4rem; transition: transform .2s, box-shadow .2s;
        }
        .kur-stat:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,.08); }
        .kur-tbl thead th {
            background: #f8fafc; color: #64748b; font-size: .71rem; font-weight: 700;
            letter-spacing: .07em; text-transform: uppercase;
            padding: .75rem 1rem; border-bottom: 2px solid #e2e8f0;
        }
        .kur-tbl tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
        .kur-tbl tbody tr:hover { background: #f8fafc; }
        .kur-tbl tbody td { padding: .78rem 1rem; font-size: .84rem; color: #334155; }

        /* ── Badges ── */
        .kur-badge { padding: .22rem .65rem; border-radius: 999px; font-size: .71rem; font-weight: 700; display: inline-block; }
        .b-blue   { background: #dbeafe; color: #1d4ed8; }
        .b-green  { background: #dcfce7; color: #15803d; }
        .b-red    { background: #fee2e2; color: #dc2626; }
        .b-amber  { background: #fef3c7; color: #b45309; }
        .b-slate  { background: #f1f5f9; color: #64748b; }
        .b-purple { background: #f3e8ff; color: #7c3aed; }
        .b-navy   { background: #1e3a6e; color: #fff; }
        .b-teal   { background: #ccfbf1; color: #0f766e; }

        /* ── Inputs ── */
        .kur-input {
            width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
            padding: .58rem .9rem; font-size: .84rem; font-weight: 500;
            color: #1e293b; outline: none;
            transition: border .18s, box-shadow .18s; background: #fff;
        }
        .kur-input:focus { border-color: #1e3a6e; box-shadow: 0 0 0 3px rgba(30,58,110,.1); }
        .kur-label {
            font-size: .72rem; font-weight: 700; color: #64748b;
            text-transform: uppercase; letter-spacing: .07em;
            display: block; margin-bottom: .3rem;
        }

        /* ── Buttons ── */
        .btn-primary {
            background: #1e3a6e; color: #fff; border: none;
            padding: .55rem 1.1rem; border-radius: 10px; font-weight: 700;
            font-size: .83rem; cursor: pointer; transition: background .18s, transform .15s;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-primary:hover { background: #162d57; transform: translateY(-1px); }
        .btn-outline {
            background: transparent; color: #1e3a6e; border: 1.5px solid #cbd5e1;
            padding: .55rem 1rem; border-radius: 10px; font-weight: 600;
            font-size: .83rem; cursor: pointer; transition: all .18s;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-outline:hover { border-color: #1e3a6e; background: #f0f4ff; }
        .btn-success {
            background: #16a34a; color: #fff; border: none;
            padding: .55rem 1.1rem; border-radius: 10px; font-weight: 700;
            font-size: .83rem; cursor: pointer; transition: background .18s, transform .15s;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-success:hover { background: #15803d; transform: translateY(-1px); }
        .btn-danger {
            background: #fef2f2; color: #dc2626; border: 1.5px solid #fca5a5;
            padding: .55rem 1rem; border-radius: 10px; font-weight: 600;
            font-size: .83rem; cursor: pointer; transition: all .18s;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-danger:hover { background: #dc2626; color: #fff; border-color: #dc2626; }

        /* ── Alerts ── */
        .alert-success {
            background: #f0fdf4; border: 1.5px solid #86efac; color: #166534;
            border-radius: 12px; padding: .75rem 1rem; font-weight: 600; font-size: .85rem;
            display: flex; align-items: center; gap: .6rem;
        }
        .alert-info {
            background: #eff6ff; border: 1.5px solid #93c5fd; color: #1e40af;
            border-radius: 12px; padding: .75rem 1rem; font-weight: 600; font-size: .85rem;
            display: flex; align-items: center; gap: .6rem;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Mobile overlay ── */
        #kur-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 40; }
        #kur-overlay.active { display: block; }

        /* ── Animations ── */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .animate-up { animation: fadeUp .35s ease forwards; }
        .delay-1 { animation-delay: .05s; opacity: 0; } .delay-2 { animation-delay: .1s; opacity: 0; }
        .delay-3 { animation-delay: .15s; opacity: 0; } .delay-4 { animation-delay: .2s; opacity: 0; }

        @media (max-width: 1023px) {
            #kur-sidebar {
                position: fixed; top: 0; left: 0; bottom: 0; z-index: 50;
                transform: translateX(-100%);
                transition: transform .28s cubic-bezier(.4,0,.2,1);
            }
            #kur-sidebar.open { transform: translateX(0); }
        }

        /* ── Foto preview upload ── */
        .foto-drop-zone {
            border: 2px dashed #cbd5e1; border-radius: 14px;
            padding: 2rem; text-align: center; transition: border-color .2s, background .2s;
            cursor: pointer;
        }
        .foto-drop-zone:hover, .foto-drop-zone.drag-over {
            border-color: #1e3a6e; background: #f0f4ff;
        }
        .foto-drop-zone input[type=file] { display: none; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 flex overflow-hidden">

    <div id="kur-overlay" onclick="closeMobile()"></div>

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside id="kur-sidebar" class="flex flex-col overflow-y-auto overflow-x-hidden h-full shadow-sm flex-shrink-0">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-slate-100">
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Majene" class="w-full h-full object-contain" onerror="this.src='https://smkn1majene.sch.id/wp-content/uploads/2019/01/cropped-logo-smk-baru-e1554162985390.png'">
            </div>
            <div class="min-w-0">
                <p class="font-black text-slate-800 text-sm leading-tight">E-Presensi</p>
                <p class="text-slate-400 text-xs font-medium">SMKN 1 Majene</p>
            </div>
        </div>

        {{-- User badge --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-slate-100">
            <div class="w-9 h-9 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-sm flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-slate-800 text-sm truncate">{{ Auth::user()->name }}</p>
                <span class="inline-block text-[10px] font-bold bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full uppercase tracking-wide">Kurikulum</span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4">
            <span class="kur-section">Menu Utama</span>
            <a href="{{ route('kurikulum.dashboard') }}" class="kur-nav {{ request()->routeIs('kurikulum.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <span class="kur-section">Verifikasi Mengajar</span>
            <a href="{{ route('kurikulum.monitoring-mengajar') }}" class="kur-nav {{ request()->routeIs('kurikulum.monitoring-mengajar') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Monitoring Mengajar
            </a>
        </nav>

        {{-- Footer --}}
        <div class="px-3 py-4 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                @csrf
                <button type="submit" class="kur-nav w-full text-left" style="color:#ef4444">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
                <button class="lg:hidden p-1.5 rounded-lg hover:bg-slate-100 transition" onclick="openMobile()">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-[.9rem] font-bold text-slate-800 leading-tight">{{ $pageTitle }}</h1>
                    <p class="text-[.68rem] text-slate-400 hidden sm:block">
                        {{ $pageSubtitle ?? \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[.77rem] font-semibold text-slate-700"><span class="realtime-clock"></span> WITA</span>
                    <span class="text-[.67rem] text-slate-400">{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
                </div>
                <div class="w-8 h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-sm shadow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-5 sm:p-8">{{ $slot }}</main>

        <footer class="flex-shrink-0 py-3 px-8 text-center text-[.7rem] text-slate-400 border-t border-slate-200 bg-white">
            © {{ date('Y') }} E-Presensi SMKN 1 Majene — Panel Kurikulum
        </footer>
    </div>

    <script>
        function openMobile()  { document.getElementById('kur-sidebar').classList.add('open'); document.getElementById('kur-overlay').classList.add('active'); }
        function closeMobile() { document.getElementById('kur-sidebar').classList.remove('open'); document.getElementById('kur-overlay').classList.remove('active'); }

        /* ── Realtime Clock ── */
        (function() {
            function pad(n) { return n < 10 ? '0' + n : n; }
            function tick() {
                const now = new Date();
                const hm  = pad(now.getHours()) + ':' + pad(now.getMinutes());
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
</body>
</html>

