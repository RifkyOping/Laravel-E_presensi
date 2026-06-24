<x-guest-layout>
    <!-- Header / Penjelasan Singkat -->
    <div class="text-center mb-6">
        <h2 class="text-3xl font-black text-[#24417c] tracking-tight mb-2">Lupa Kata Sandi?</h2>
        <p class="text-[#24417c]/80 font-medium text-sm leading-relaxed">
            {{ __('Tidak masalah. Cukup masukkan alamat email Anda yang terdaftar, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.') }}
        </p>
    </div>

    <!-- Session Status (Pesan sukses jika email berhasil dikirim) -->
    <x-auth-session-status class="mb-4 font-bold text-[#24417c]" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Alamat Email') }}
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="block w-full rounded-xl border-2 border-[#24417c]/20 focus:border-[#24417c] focus:ring-0 text-[#24417c] shadow-sm transition duration-300 bg-white">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 font-medium text-sm" />
        </div>

        <!-- Tombol Kirim & Link Kembali -->
        <div class="pt-4 space-y-4">
            <!-- Tombol yang lebih ringkas dan proporsional -->
            <button type="submit"
                class="w-full flex justify-center items-center bg-[#24417c] text-white font-bold text-base px-5 py-2 rounded-lg border-2 border-[#24417c] hover:bg-white hover:text-[#24417c] transition duration-300 shadow-md shadow-[#24417c]/10">
                {{ __('Kirim Tautan Reset') }}
            </button>

            <!-- Tautan Kembali ke Login (Tambahan UX yang baik) -->
            <div class="text-center">
                <a href="{{ route('login') }}"
                    class="text-sm font-bold text-[#24417c]/70 hover:text-[#24417c] underline underline-offset-4 transition duration-300">
                    {{ __('Kembali ke Halaman Login') }}
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
