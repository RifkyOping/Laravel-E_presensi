<section>
    <header>
        <h2 class="text-2xl font-black text-[#24417c]">
            {{ __('Ubah Kata Sandi') }}
        </h2>
        <p class="mt-1 text-sm font-medium text-[#24417c]/70">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        {{-- Kata Sandi Saat Ini --}}
        <div x-data="{ show: false }">
            <label for="update_password_current_password" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Kata Sandi Saat Ini') }}
            </label>
            <div class="relative">
                <input id="update_password_current_password" name="current_password"
                    :type="show ? 'text' : 'password'"
                    class="block w-full rounded-xl border-2 {{ $errors->updatePassword->has('current_password') ? 'border-red-400 bg-red-50' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] shadow-sm transition duration-300 bg-white pr-10"
                    autocomplete="current-password" />
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#24417c] focus:outline-none transition-colors">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" style="display:none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            @if ($errors->updatePassword->has('current_password'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->updatePassword->first('current_password') }}</span>
                </div>
            @endif
        </div>

        {{-- Kata Sandi Baru --}}
        <div x-data="{ show: false }">
            <label for="update_password_password" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Kata Sandi Baru') }}
            </label>
            <div class="relative">
                <input id="update_password_password" name="password"
                    :type="show ? 'text' : 'password'"
                    class="block w-full rounded-xl border-2 {{ $errors->updatePassword->has('password') ? 'border-red-400 bg-red-50' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] shadow-sm transition duration-300 bg-white pr-10"
                    autocomplete="new-password" />
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#24417c] focus:outline-none transition-colors">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" style="display:none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>

            {{-- Indikator Kekuatan Password --}}
            <div x-data="{
                    strength: 0,
                    strengthColors: ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'],
                    strengthLabels: ['', 'Sangat Lemah', 'Lemah', 'Cukup Kuat', 'Kuat'],
                    update(val) {
                        let s = 0;
                        if (val.length >= 8) s++;
                        if (/[A-Z]/.test(val)) s++;
                        if (/[0-9]/.test(val)) s++;
                        if (/[^A-Za-z0-9]/.test(val)) s++;
                        this.strength = s;
                    }
                }"
                x-init="$watch('$el.closest(\'div\').querySelector(\'#update_password_password\')', v => {})"
                class="mt-2">
                {{-- Listener pada input --}}
                <div x-init="
                    document.getElementById('update_password_password').addEventListener('input', e => update(e.target.value))
                "></div>
                <div class="flex gap-1 mb-1">
                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="strength >= 1 ? strengthColors[strength - 1] : 'bg-gray-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="strength >= 2 ? strengthColors[strength - 1] : 'bg-gray-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="strength >= 3 ? strengthColors[strength - 1] : 'bg-gray-200'"></div>
                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300" :class="strength >= 4 ? strengthColors[strength - 1] : 'bg-gray-200'"></div>
                </div>
                <p class="text-xs font-semibold"
                    :class="strength === 0 ? 'text-gray-400' : (strength <= 2 ? 'text-red-500' : (strength === 3 ? 'text-yellow-500' : 'text-green-600'))"
                    x-text="strengthLabels[strength]"></p>
            </div>

            @if ($errors->updatePassword->has('password'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->updatePassword->first('password') }}</span>
                </div>
            @endif
        </div>

        {{-- Konfirmasi Kata Sandi --}}
        <div x-data="{ show: false }">
            <label for="update_password_password_confirmation" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Konfirmasi Kata Sandi') }}
            </label>
            <div class="relative">
                <input id="update_password_password_confirmation" name="password_confirmation"
                    :type="show ? 'text' : 'password'"
                    class="block w-full rounded-xl border-2 {{ $errors->updatePassword->has('password_confirmation') ? 'border-red-400 bg-red-50' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] shadow-sm transition duration-300 bg-white pr-10"
                    autocomplete="new-password" />
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#24417c] focus:outline-none transition-colors">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" style="display:none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->updatePassword->first('password_confirmation') }}</span>
                </div>
            @endif
        </div>

        {{-- Tombol & Status --}}
        <div class="flex items-center gap-4 pt-1">
            <button type="submit"
                class="bg-gradient-to-r from-[#24417c] to-blue-600 text-white font-bold px-6 py-2.5 rounded-xl border border-blue-400 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 hover:-translate-y-0.5">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center gap-2 text-sm font-bold text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('Kata sandi berhasil diperbarui!') }}
                </div>
            @endif
        </div>
    </form>
</section>