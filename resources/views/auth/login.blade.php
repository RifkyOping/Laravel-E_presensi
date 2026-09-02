<x-guest-layout>
    <!-- Header Login / Identitas -->
    <div class="text-center mb-10">
        <h2 class="text-3xl font-black text-[#24417c] tracking-tight mb-2">Selamat Datang</h2>
        <p class="text-gray-500 font-medium text-sm">Silakan masuk ke akun E-Presensi Anda</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="form-login" method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Nomor Induk -->
        <div>
            <label for="nomor_induk" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('NIS / NISN / NIP') }}
            </label>
            <input id="nomor_induk" type="text" name="nomor_induk" value="{{ old('nomor_induk') }}" required autofocus
                autocomplete="username" list="saved_nomor_induk"
                class="block w-full rounded-xl border-2 {{ $errors->has('nomor_induk') ? 'border-red-400 bg-red-50 focus:border-red-500' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] shadow-sm transition duration-300">
            <datalist id="saved_nomor_induk"></datalist>
            @if ($errors->has('nomor_induk'))
                <div
                    class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errors->first('nomor_induk') }}</span>
                </div>
            @endif
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Kata Sandi') }}
            </label>
            <div class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                    autocomplete="current-password"
                    class="block w-full rounded-xl border-2 {{ $errors->has('password') ? 'border-red-400 bg-red-50 focus:border-red-500' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] shadow-sm transition duration-300 pr-10">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-[#24417c] focus:outline-none transition-colors">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @if ($errors->has('password'))
                <div
                    class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errors->first('password') }}</span>
                </div>
            @endif
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-2">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-2 border-[#24417c]/30 text-[#24417c] shadow-sm focus:ring-[#24417c] focus:ring-offset-0 cursor-pointer w-5 h-5">
                <span class="ms-2 text-sm font-bold text-[#24417c]">{{ __('Ingat Saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-bold text-[#24417c]/70 hover:text-[#24417c] underline underline-offset-4 transition duration-300"
                    href="{{ route('password.request') }}">
                    {{ __('Lupa Kata Sandi?') }}
                </a>
            @endif
        </div>

        <!-- Tombol Login -->
        <div class="pt-6 space-y-4">
            <button type="submit"
                class="w-full flex justify-center items-center bg-gradient-to-r from-[#24417c] to-blue-600 text-white font-bold text-lg px-6 py-3.5 rounded-xl border border-blue-400 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
                <span class="relative z-10">{{ __('Masuk Sekarang') }}</span>
                <div
                    class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out pointer-events-none">
                </div>
            </button>

            <!-- Tombol Kembali -->
            <div class="text-center pt-2">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-[#24417c] transition-colors duration-300">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Kembali ke Beranda') }}
                </a>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const datalist = document.getElementById('saved_nomor_induk');
            const form = document.getElementById('form-login');
            const inputNomor = document.getElementById('nomor_induk');

            // 1. Muat riwayat NIS/NIP/NISN yang tersimpan
            let savedLogins = [];
            try {
                savedLogins = JSON.parse(localStorage.getItem('riwayat_login_nomor') || '[]');
                
                // Tampilkan ke datalist
                savedLogins.forEach(nomor => {
                    let option = document.createElement('option');
                    option.value = nomor;
                    datalist.appendChild(option);
                });
            } catch (e) {
                console.error('Local storage error:', e);
            }

            // 2. Simpan input saat tombol Masuk (submit) ditekan
            if (form) {
                form.addEventListener('submit', function() {
                    try {
                        const currentVal = inputNomor.value.trim();
                        if (currentVal) {
                            // Hapus jika sudah ada (agar nanti bisa ditaruh di urutan pertama)
                            savedLogins = savedLogins.filter(n => n !== currentVal);
                            
                            // Tambahkan di awal
                            savedLogins.unshift(currentVal);
                            
                            // Batasi maksimal 5 nomor saja yang disimpan
                            if (savedLogins.length > 5) {
                                savedLogins = savedLogins.slice(0, 5);
                            }
                            
                            localStorage.setItem('riwayat_login_nomor', JSON.stringify(savedLogins));
                        }
                    } catch (e) {
                        console.error('Error saving login history:', e);
                    }
                });
            }
        });
    </script>
</x-guest-layout>