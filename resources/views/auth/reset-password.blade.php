<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <!-- Icon Kunci -->
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#24417c]/10 mb-4">
            <svg class="w-8 h-8 text-[#24417c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>
        <h2 class="text-3xl font-black text-[#24417c] tracking-tight mb-2">Atur Ulang Kata Sandi</h2>
        <p class="text-[#24417c]/70 font-medium text-sm leading-relaxed">
            Buat kata sandi baru yang kuat untuk akun Anda
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Token (Hidden) -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Alamat Email') }}
            </label>
            <input id="email" type="email" name="email"
                value="{{ old('email', $request->email) }}"
                required autofocus autocomplete="username"
                class="block w-full rounded-xl border-2 {{ $errors->has('email') ? 'border-red-400 bg-red-50 focus:border-red-500' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] shadow-sm transition duration-300 bg-white/80">
            @if ($errors->has('email'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif
        </div>

        <!-- Kata Sandi Baru -->
        <div x-data="{ show: false }">
            <label for="password" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Kata Sandi Baru') }}
            </label>
            <div class="relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password"
                    required autocomplete="new-password"
                    x-on:input="updateStrength($event.target.value)"
                    class="block w-full rounded-xl border-2 {{ $errors->has('password') ? 'border-red-400 bg-red-50 focus:border-red-500' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] shadow-sm transition duration-300 pr-10">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#24417c] focus:outline-none transition-colors">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>

            <!-- Indikator Kekuatan Password -->
            <div x-data="passwordStrength()" class="mt-2" x-init="init()">
                <div class="flex gap-1 mb-1">
                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                        :class="strength >= 1 ? strengthColors[strength - 1] : 'bg-gray-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                        :class="strength >= 2 ? strengthColors[strength - 1] : 'bg-gray-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                        :class="strength >= 3 ? strengthColors[strength - 1] : 'bg-gray-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                        :class="strength >= 4 ? strengthColors[strength - 1] : 'bg-gray-200'"></div>
                </div>
                <p class="text-xs font-semibold transition-all duration-300"
                    :class="strength === 0 ? 'text-gray-400' : (strength <= 2 ? 'text-red-500' : (strength === 3 ? 'text-yellow-500' : 'text-green-600'))"
                    x-text="strengthLabels[strength]"></p>
            </div>

            @if ($errors->has('password'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first('password') }}</span>
                </div>
            @endif
        </div>

        <!-- Konfirmasi Kata Sandi -->
        <div x-data="{ show: false }">
            <label for="password_confirmation" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Konfirmasi Kata Sandi') }}
            </label>
            <div class="relative">
                <input id="password_confirmation" :type="show ? 'text' : 'password'"
                    name="password_confirmation"
                    required autocomplete="new-password"
                    class="block w-full rounded-xl border-2 {{ $errors->has('password_confirmation') ? 'border-red-400 bg-red-50 focus:border-red-500' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] shadow-sm transition duration-300 pr-10">
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#24417c] focus:outline-none transition-colors">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @if ($errors->has('password_confirmation'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first('password_confirmation') }}</span>
                </div>
            @endif
        </div>

        <!-- Tombol Submit -->
        <div class="pt-4">
            <button type="submit"
                class="w-full flex justify-center items-center bg-gradient-to-r from-[#24417c] to-blue-600 text-white font-bold text-lg px-6 py-3.5 rounded-xl border border-blue-400 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
                <span class="relative z-10">{{ __('Simpan Kata Sandi Baru') }}</span>
                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
            </button>
        </div>

        <!-- Kembali ke Login -->
        <div class="text-center">
            <a href="{{ route('login') }}"
                class="text-sm font-bold text-[#24417c]/70 hover:text-[#24417c] underline underline-offset-4 transition duration-300">
                {{ __('Kembali ke Halaman Login') }}
            </a>
        </div>
    </form>

    <script>
        function passwordStrength() {
            return {
                strength: 0,
                strengthColors: ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'],
                strengthLabels: ['', 'Sangat Lemah', 'Lemah', 'Cukup Kuat', 'Kuat'],
                init() {
                    document.getElementById('password').addEventListener('input', (e) => {
                        this.updateStrength(e.target.value);
                    });
                },
                updateStrength(password) {
                    let score = 0;
                    if (password.length >= 8) score++;
                    if (/[A-Z]/.test(password)) score++;
                    if (/[0-9]/.test(password)) score++;
                    if (/[^A-Za-z0-9]/.test(password)) score++;
                    this.strength = score;
                }
            }
        }
    </script>
</x-guest-layout>
