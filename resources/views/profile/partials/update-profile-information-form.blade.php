<section>
    <header>
        <h2 class="text-2xl font-black text-[#24417c]">
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-sm font-medium text-[#24417c]/70">
            {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block font-bold text-sm text-[#24417c] mb-1">{{ __('Nama') }}</label>
            <input id="name" name="name" type="text" class="block w-full rounded-xl border-2 border-[#24417c]/20 focus:border-[#24417c] focus:ring-0 text-[#24417c] shadow-sm transition duration-300 bg-white" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-red-600 font-medium text-sm" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block font-bold text-sm text-[#24417c] mb-1">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="block w-full rounded-xl border-2 border-[#24417c]/20 focus:border-[#24417c] focus:ring-0 text-[#24417c] shadow-sm transition duration-300 bg-white" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2 text-red-600 font-medium text-sm" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 font-medium text-[#24417c]">
                        {{ __('Alamat email Anda belum diverifikasi.') }}
                        <button form="send-verification" class="underline text-sm font-bold text-[#24417c]/80 hover:text-[#24417c] rounded-md focus:outline-none transition duration-300">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-sm text-[#24417c]">
                            {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-[#24417c] text-white font-bold px-6 py-2.5 rounded-xl border-2 border-[#24417c] hover:bg-white hover:text-[#24417c] transition duration-300 shadow-md">
                {{ __('Simpan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm font-bold text-[#24417c]/80">
                    {{ __('Tersimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>