<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', \App\Models\SchoolSetting::get()->nama_sekolah) }}</title>
        
        <!-- PWA -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4338ca">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
        
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-white">
        <div class="min-h-screen flex w-full">
            
            <!-- Left Side - Branding/Image (Hidden on Mobile) -->
            <div class="hidden lg:flex w-1/2 bg-[#24417c] relative overflow-hidden flex-col justify-center p-12 lg:p-16">
                <!-- Background Image & Overlay -->
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80')] bg-cover bg-center"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-[#24417c]/95 via-[#24417c]/80 to-blue-900/90 mix-blend-multiply"></div>
                
                <!-- Content Top -->
                <div class="relative z-10 flex items-center gap-3 mb-10">
                    <a href="/" class="transition-transform hover:scale-105 duration-300">
                        <img src="https://i0.wp.com/smkn1majene.sch.id/wp-content/uploads/2019/01/HEADER-PANJANG-SMK-BARU.png?w=2452" alt="Logo {{ \App\Models\SchoolSetting::get()->nama_sekolah }}" class="h-12 bg-white p-2.5 rounded-lg shadow-xl">
                    </a>
                </div>

                <!-- Content Bottom -->
                <div class="relative z-10 text-white">
                    <h1 class="text-5xl xl:text-6xl font-black mb-6 leading-tight tracking-tight drop-shadow-md">
                        Sistem Presensi <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-blue-400">Terpadu & Modern</span>
                    </h1>
                    <p class="text-lg xl:text-xl text-blue-100/90 max-w-md font-medium leading-relaxed mb-10">
                        Platform digital resmi {{ \App\Models\SchoolSetting::get()->nama_sekolah }} untuk pencatatan kehadiran presisi dan akses literasi e-Book interaktif.
                    </p>
                    
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10 w-fit">
                        <div class="flex -space-x-3">
                            <div class="w-10 h-10 rounded-full border-2 border-[#24417c] bg-blue-500 flex items-center justify-center text-xs font-bold shadow-md">SMK</div>
                            <div class="w-10 h-10 rounded-full border-2 border-[#24417c] bg-green-500 flex items-center justify-center text-xs font-bold text-white shadow-md">BISA</div>
                            <div class="w-10 h-10 rounded-full border-2 border-[#24417c] bg-amber-500 flex items-center justify-center text-xs font-bold text-white shadow-md">Hebat</div>
                        </div>
                        <div class="text-sm font-medium text-white/90">
                            <span class="font-bold text-white">SMK BISA!</span><br>SMK HEBAT!
                        </div>
                    </div>
                </div>

                <!-- Decorative blur -->
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-400 rounded-full mix-blend-screen filter blur-[120px] opacity-40 animate-pulse"></div>
                <div class="absolute top-1/2 right-0 w-72 h-72 bg-indigo-500 rounded-full mix-blend-screen filter blur-[120px] opacity-30 animate-pulse" style="animation-delay: 2s;"></div>
            </div>

            <!-- Right Side - Form Container -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 relative bg-slate-50 lg:bg-white">
                
                <!-- Decorative element for mobile -->
                <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-blue-50 to-transparent lg:hidden"></div>
                
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-10 relative z-10 w-full max-w-md flex justify-center">
                    <a href="/" class="transition-transform hover:scale-105 duration-300">
                        <img src="https://i0.wp.com/smkn1majene.sch.id/wp-content/uploads/2019/01/HEADER-PANJANG-SMK-BARU.png?w=2452" alt="Logo {{ \App\Models\SchoolSetting::get()->nama_sekolah }}" class="h-12 shadow-sm bg-white p-2 rounded-lg">
                    </a>
                </div>

                <div class="w-full max-w-md relative z-10 bg-white lg:bg-transparent p-8 lg:p-0 rounded-3xl lg:rounded-none shadow-xl lg:shadow-none border border-slate-100 lg:border-none">
                    {{ $slot }}
                </div>
                
                <!-- Copyright Footer -->
                <div class="absolute bottom-6 w-full text-center text-xs font-semibold text-slate-400">
                    &copy; {{ date('Y') }} UPTD SMK Negeri 1 Majene
                </div>
            </div>

        </div>
        
        <!-- PWA Service Worker Registration -->
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
        </script>
    </body>
</html>
