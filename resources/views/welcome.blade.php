<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Presensi | SMKN 1 Majene</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }
    </style>
</head>
<body class="bg-white text-[#24417c] font-sans antialiased flex flex-col min-h-screen">

    <!-- Navbar -->
    <header class="w-full py-6 px-4 sm:px-6 lg:px-8 border-b-2 border-[#24417c]/10">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="font-black text-2xl tracking-tighter">
                <img src="https://i0.wp.com/smkn1majene.sch.id/wp-content/uploads/2019/01/HEADER-PANJANG-SMK-BARU.png?w=2452" alt="Logo SMKN 1 Majene" class="h-10 w-auto">
            </div>
            
            @if (Route::has('login'))
                <nav class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-bold border-2 border-[#24417c] px-6 py-2 rounded-full hover:bg-[#24417c] hover:text-white transition duration-300">
                            Ke Dashboard
                        </a>
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Dekorasi Latar Belakang Abstrak -->
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50/30 to-white z-0"></div>
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-[#24417c] rounded-full opacity-[0.03] blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-[#24417c] rounded-full opacity-[0.03] blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

        <div class="max-w-5xl mx-auto text-center relative z-10 py-12">
            
            <h2 class="animate-fade-in-up text-sm sm:text-lg font-bold tracking-[0.3em] uppercase mb-4 text-[#24417c]/70">
                Sistem Informasi Terpadu
            </h2>
            
            <h1 class="animate-fade-in-up delay-100 text-5xl sm:text-7xl lg:text-8xl font-black tracking-tighter mb-6 drop-shadow-sm">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#24417c] to-blue-500">E-PRESENSI</span> <br> 
                <span class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-[#24417c]/80">SMKN 1 MAJENE</span>
            </h1>
            
            <p class="animate-fade-in-up delay-200 mt-6 text-lg sm:text-2xl max-w-3xl mx-auto leading-relaxed font-medium mb-10">
                Melakukan absensi kehadiran dan mengakses buku digital kini lebih mudah, cepat, dan terpadu dalam satu sistem.
            </p>
            
            <div class="animate-fade-in-up delay-300 flex flex-col sm:flex-row justify-center items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto bg-gradient-to-r from-[#24417c] to-blue-600 text-white font-bold text-lg px-10 py-4 rounded-full border border-blue-400 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 hover:-translate-y-1">
                        Buka Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto bg-gradient-to-r from-[#24417c] to-blue-600 text-white font-bold text-lg px-12 py-4 rounded-full border border-blue-400 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 hover:-translate-y-1">
                        Masuk Sekarang
                    </a>
                @endauth
            </div>
            
        </div>
    </main>
    <!-- Footer -->
    <footer class="bg-[#24417c] text-white pt-16 pb-8 px-4 sm:px-6 lg:px-8 border-t border-white/10 relative shadow-inner">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
            <!-- Kolom 1: Tentang -->
            <div class="animate-fade-in-up delay-100">
                <div class="flex items-center gap-3 mb-6">
                    <img src="https://i0.wp.com/smkn1majene.sch.id/wp-content/uploads/2019/01/HEADER-PANJANG-SMK-BARU.png?w=2452" alt="Logo SMKN 1 Majene" class="h-12 bg-white p-2 rounded-lg shadow-md">
                </div>
                <p class="text-white/80 leading-relaxed font-medium">
                    Kreatif, Inovatif, & Profesional.<br>
                    Mewujudkan generasi tangguh dan kompeten.<br>
                    Karena SMK BISA! SMK HEBAT! SMK SIAP KERJA!
                </p>
            </div>

            <!-- Kolom 2: Kontak -->
            <div class="animate-fade-in-up delay-200">
                <h3 class="text-xl font-bold mb-6 text-blue-200">Hubungi Kami</h3>
                <ul class="space-y-4 text-white/80 font-medium">
                    <li class="flex items-start gap-3 hover:text-white transition">
                        <span class="mt-0.5">📍</span>
                        <span>Jl. Pangeran Diponegoro No. 89, Kel. Baurung, Kec. Banggae Timur, Kab. Majene, Sulawesi Barat</span>
                    </li>
                    <li class="flex items-center gap-3 hover:text-white transition">
                        <span>📞</span>
                        <span>(0422) 21132</span>
                    </li>
                    <li class="flex items-center gap-3 hover:text-white transition">
                        <span>📧</span>
                        <span>smkn1majene@yahoo.co.id</span>
                    </li>
                </ul>
            </div>

            <!-- Kolom 3: Tautan -->
            <div class="animate-fade-in-up delay-300">
                <h3 class="text-xl font-bold mb-6 text-blue-200">Tautan Cepat</h3>
                <ul class="space-y-3 font-medium text-white/80">
                    <li><a href="https://smkn1majene.sch.id/" target="_blank" class="hover:text-blue-200 hover:translate-x-2 transition inline-flex items-center gap-2"><span>🌐</span> Website Utama</a></li>
                    <li><a href="https://web.facebook.com/smkn1majene" target="_blank" class="hover:text-blue-200 hover:translate-x-2 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6c1.05 0 2.05.2 2.05.2v2.25h-1.16c-1.14 0-1.39.71-1.39 1.35V12h2.5l-.4 3h-2.1v6.8C18.56 20.87 22 16.84 22 12z"/></svg> Facebook
                    </a></li>
                    <li><a href="https://www.instagram.com/smkn1majene" target="_blank" class="hover:text-blue-200 hover:translate-x-2 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg> Instagram
                    </a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto border-t border-white/10 pt-8 text-center text-sm font-medium text-white/50 animate-fade-in-up delay-400">
            &copy; {{ date('Y') }} UPTD SMK Negeri 1 Majene. Hak Cipta Dilindungi.<br>
            Sistem Informasi Terpadu E-Presensi
        </div>
    </footer>

</body>
</html>