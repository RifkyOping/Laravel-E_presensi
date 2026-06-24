<section class="space-y-6">
    <header>
        <h2 class="text-2xl font-black text-[#24417c]">
            {{ __('Hapus Akun') }}
        </h2>
        <p class="mt-1 text-sm font-medium text-[#24417c]/70">
            {{ __('Setelah akun Anda dihapus, semua data akan hilang secara permanen. Sebelum menghapus, harap unduh data atau informasi yang ingin Anda simpan.') }}
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="bg-white text-[#24417c] font-black px-6 py-2.5 rounded-xl border-2 border-[#24417c] hover:bg-[#24417c] hover:text-white transition duration-300 shadow-md">
        {{ __('Hapus Akun') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white">
            @csrf
            @method('delete')

            <h2 class="text-xl font-black text-[#24417c]">
                {{ __('Apakah Anda yakin ingin menghapus akun ini?') }}
            </h2>

            <p class="mt-2 text-sm font-medium text-[#24417c]/70">
                {{ __('Setelah dihapus, semua sumber daya dan data akan hilang permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.') }}
            </p>

            <div class="mt-6" x-data="{ show: false }">
                <label for="password" class="sr-only">{{ __('Kata Sandi') }}</label>
                <div class="relative w-3/4">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" class="block w-full rounded-xl border-2 border-[#24417c]/20 focus:border-[#24417c] focus:ring-0 text-[#24417c] shadow-sm transition duration-300 bg-white pr-10" placeholder="{{ __('Kata Sandi') }}" />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-[#24417c] focus:outline-none transition-colors">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-600 font-medium text-sm" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="bg-white text-[#24417c] font-bold px-6 py-2.5 rounded-xl border-2 border-[#24417c]/20 hover:border-[#24417c] transition duration-300">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="bg-[#24417c] text-white font-bold px-6 py-2.5 rounded-xl border-2 border-[#24417c] hover:bg-white hover:text-[#24417c] transition duration-300 shadow-md">
                    {{ __('Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>