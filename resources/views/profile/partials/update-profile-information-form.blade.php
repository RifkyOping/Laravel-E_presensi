@php
    $nomorIndukLabel = match ($user->role) {
        'guru'      => 'NIP (Nomor Induk Pegawai)',
        'admin'     => 'ID Admin',
        'pengawas'  => 'ID Pengawas',
        'kurikulum' => 'ID Kurikulum',
        default     => 'ID / Nomor Induk',
    };
    $nomorIndukPlaceholder = match ($user->role) {
        'guru'      => 'Contoh: 198001012005011001',
        'admin'     => 'Masukkan ID Admin',
        'pengawas'  => 'Masukkan ID Pengawas',
        'kurikulum' => 'Masukkan ID Kurikulum',
        default     => 'Masukkan ID / Nomor Induk',
    };
    $nomorIndukHelp = match ($user->role) {
        'guru'      => 'NIP digunakan sebagai username untuk login ke sistem E-Presensi.',
        'admin'     => 'ID Admin digunakan sebagai username untuk login ke sistem E-Presensi.',
        'pengawas'  => 'ID Pengawas digunakan sebagai username untuk login ke sistem E-Presensi.',
        'kurikulum' => 'ID Kurikulum digunakan sebagai username untuk login ke sistem E-Presensi.',
        default     => 'ID / Nomor Induk digunakan sebagai username untuk login ke sistem E-Presensi.',
    };
    $roleBadge = match ($user->role) {
        'admin'     => ['Admin', 'bg-red-50 text-red-700 border-red-200'],
        'guru'      => ['Guru', 'bg-blue-50 text-blue-700 border-blue-200'],
        'pengawas'  => ['Pengawas', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
        'kurikulum' => ['Kurikulum', 'bg-teal-50 text-teal-700 border-teal-200'],
        default     => [ucfirst($user->role), 'bg-slate-50 text-slate-700 border-slate-200'],
    };
@endphp

<section>
    <header class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#24417c]">
                {{ __('Informasi Profil') }}
            </h2>
            <p class="mt-1 text-sm font-medium text-[#24417c]/70">
                {{ __("Perbarui nama, {$nomorIndukLabel}, dan alamat email akun Anda.") }}
            </p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $roleBadge[1] }}">
            {{ $roleBadge[0] }}
        </span>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        {{-- Nama Lengkap --}}
        <div>
            <label for="name" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Nama Lengkap') }} <span class="text-red-500">*</span>
            </label>
            <input id="name" name="name" type="text"
                class="block w-full rounded-xl border-2 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] font-medium shadow-sm transition duration-300 bg-white"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @if($errors->has('name'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first('name') }}</span>
                </div>
            @endif
        </div>

        {{-- NIP / ID Akun --}}
        <div>
            <label for="nomor_induk" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ $nomorIndukLabel }} <span class="text-red-500">*</span>
            </label>
            <input id="nomor_induk" name="nomor_induk" type="text"
                class="block w-full rounded-xl border-2 {{ $errors->has('nomor_induk') ? 'border-red-400 bg-red-50' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] font-medium shadow-sm transition duration-300 bg-white"
                placeholder="{{ $nomorIndukPlaceholder }}"
                value="{{ old('nomor_induk', $user->nomor_induk) }}" required />
            <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#24417c]/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $nomorIndukHelp }}</span>
            </p>
            @if($errors->has('nomor_induk'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first('nomor_induk') }}</span>
                </div>
            @endif
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block font-bold text-sm text-[#24417c] mb-1">
                {{ __('Email') }}
            </label>
            <input id="email" name="email" type="email"
                class="block w-full rounded-xl border-2 {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-[#24417c]/20 focus:border-[#24417c]' }} focus:ring-0 text-[#24417c] font-medium shadow-sm transition duration-300 bg-white"
                placeholder="nama@contoh.com"
                value="{{ old('email', $user->email) }}" autocomplete="username" />
            @if($errors->has('email'))
                <div class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

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

        {{-- Tombol Simpan & Status --}}
        <div class="flex items-center gap-4 pt-1">
            <button type="submit"
                class="bg-gradient-to-r from-[#24417c] to-blue-600 text-white font-bold px-6 py-2.5 rounded-xl border border-blue-400 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300 hover:-translate-y-0.5">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated' || session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center gap-2 text-sm font-bold text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') ?? __('Profil berhasil diperbarui!') }}
                </div>
            @endif
        </div>
    </form>
</section>