<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Profil Saya</span>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Alerts --}}
        @if(session('success'))
            <div
                class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('status') === 'profile-updated')
            <div
                class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Profil berhasil diperbarui.
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-sm text-red-700">
                <p class="font-bold mb-1">Periksa kembali isian form:</p>
                <ul class="list-disc list-inside space-y-0.5 font-medium">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Hero --}}
        <div
            class="relative overflow-hidden bg-gradient-to-br from-[#1e3a6e] to-[#2d5299] rounded-2xl px-5 py-5 sm:px-8 sm:py-8 shadow-xl">
            <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6">

                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-2xl flex-shrink-0 shadow-lg flex items-center justify-center text-2xl font-black text-white select-none
                        {{ $siswa->siswaProfile?->jenis_kelamin === 'P'
    ? 'bg-gradient-to-br from-pink-400 to-rose-500'
    : 'bg-gradient-to-br from-sky-400 to-[#1e3a6e]' }}">
                    {{ strtoupper(substr($siswa->name, 0, 2)) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 text-center sm:text-left">
                    <p class="text-blue-300 text-[.65rem] font-bold uppercase tracking-[.15em] mb-1">Profil Saya</p>
                    <h1 class="text-white text-2xl font-black leading-tight truncate">{{ $siswa->name }}</h1>
                    <p class="text-blue-200/70 text-xs mt-0.5 mb-3">{{ $siswa->email }}</p>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        @if($siswa->siswaProfile?->kelas)
                            <span
                                class="text-[.7rem] font-bold bg-white/15 hover:bg-white/25 text-white px-3 py-1 rounded-full border border-white/20 transition">
                                Kelas {{ $siswa->siswaProfile->kelas }} {{ $siswa->siswaProfile->jurusan }}
                                {{ $siswa->siswaProfile->rombel }}
                            </span>
                        @endif
                        @if($siswa->siswaProfile?->jenis_kelamin)
                            <span
                                class="text-[.7rem] font-bold bg-white/15 hover:bg-white/25 text-white px-3 py-1 rounded-full border border-white/20 transition">
                                {{ $siswa->siswaProfile->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}
                            </span>
                        @endif
                        @if($siswa->siswaProfile?->agama)
                            <span
                                class="text-[.7rem] font-bold bg-white/15 hover:bg-white/25 text-white px-3 py-1 rounded-full border border-white/20 transition">
                                {{ $siswa->siswaProfile->agama }}
                            </span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Decorative circles --}}
            <div
                class="absolute -right-12 -top-12 w-56 h-56 rounded-full border-[40px] border-white/5 pointer-events-none">
            </div>
            <div class="absolute right-20 -bottom-10 w-36 h-36 rounded-full bg-white/5 pointer-events-none"></div>
        </div>
        {{-- Info Kelas/Jurusan --}}
        <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
            <svg class="w-4 h-4 text-[#1e3a6e] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-[0.75rem] text-[#1e3a6e]/80 font-medium leading-relaxed">
                <strong>Ada beberapa data</strong> yang hanya dapat diubah oleh Admin. Silakan hubungi pihak
                sekolah jika terdapat kesalahan data.
            </p>
        </div>
        {{-- ═══ FORM UTAMA — Gabungan semua field ═══ --}}
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf @method('PATCH')

            {{-- 1. Data Akun --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                            <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        Akun & Identitas
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Nama, email, dan data identitas sekolah.</p>
                </div>
                <div class="px-6 py-6 space-y-5">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $siswa->name) }}" required
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10
                                      rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                                Email
                            </label>
                            <input type="email" name="email" value="{{ old('email', $siswa->email) }}"
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10
                                      rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                                Nomor Induk / NISN
                                <span class="text-slate-300 font-normal normal-case ml-1">(admin)</span>
                            </label>
                            <div
                                class="w-full border border-slate-100 bg-slate-50 rounded-xl px-4 py-2.5 text-slate-400 text-sm font-medium cursor-not-allowed select-none">
                                {{ $siswa->nomor_induk ?? '—' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                                Agama
                                <span class="text-slate-300 font-normal normal-case ml-1">(admin)</span>
                            </label>
                            <div
                                class="w-full border border-slate-100 bg-slate-50 rounded-xl px-4 py-2.5 text-slate-400 text-sm font-medium cursor-not-allowed select-none">
                                {{ $siswa->siswaProfile?->agama ?? '—' }}
                            </div>
                        </div>
                    </div>

                    {{-- Kelas & Jurusan — read only --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                                Tingkat
                                <span class="text-slate-300 font-normal normal-case ml-1">(admin)</span>
                            </label>
                            <div
                                class="w-full border border-slate-100 bg-slate-50 rounded-xl px-4 py-2.5 text-slate-400 text-sm font-medium cursor-not-allowed select-none">
                                {{ $siswa->siswaProfile?->kelas ?? '—' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                                Jurusan
                                <span class="text-slate-300 font-normal normal-case ml-1">(admin)</span>
                            </label>
                            <div
                                class="w-full border border-slate-100 bg-slate-50 rounded-xl px-4 py-2.5 text-slate-400 text-sm font-medium cursor-not-allowed select-none">
                                {{ $siswa->siswaProfile?->jurusan ?? '—' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                                Rombel
                                <span class="text-slate-300 font-normal normal-case ml-1">(admin)</span>
                            </label>
                            <div
                                class="w-full border border-slate-100 bg-slate-50 rounded-xl px-4 py-2.5 text-slate-400 text-sm font-medium cursor-not-allowed select-none">
                                {{ $siswa->siswaProfile?->rombel ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Jenis
                                Kelamin</label>
                            <select name="jenis_kelamin"
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10
                                       rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ old('jenis_kelamin', $siswa->siswaProfile?->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $siswa->siswaProfile?->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tempat
                                Lahir</label>
                            <input type="text" name="tempat_lahir"
                                value="{{ old('tempat_lahir', $siswa->siswaProfile?->tempat_lahir) }}"
                                placeholder="Kota kelahiran"
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10
                                      rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal
                                Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $siswa->siswaProfile?->tanggal_lahir?->format('Y-m-d')) }}"
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10
                                      rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                        </div>
                    </div>



                </div>
            </div>

            {{-- 2. Ganti Password --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                            <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        Ubah Password
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Kosongkan jika tidak ingin mengubah password.</p>
                </div>
                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Password Baru --}}
                    <div x-data="{ show: false }">
                        <label for="siswa_password_new"
                            class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Password
                            Baru</label>
                        <div class="relative">
                            <input id="siswa_password_new" :type="show ? 'text' : 'password'" name="password"
                                placeholder="Minimal 8 karakter"
                                class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-[#1e3a6e]' }} focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm pr-10">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-[#1e3a6e] focus:outline-none transition-colors">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        {{-- Indikator kekuatan --}}
                        <div x-data="{
                        strength: 0,
                        colors: ['bg-red-400','bg-orange-400','bg-yellow-400','bg-green-500'],
                        labels: ['','Sangat Lemah','Lemah','Cukup Kuat','Kuat'],
                        update(v) {
                            let s=0;
                            if(v.length>=8) s++;
                            if(/[A-Z]/.test(v)) s++;
                            if(/[0-9]/.test(v)) s++;
                            if(/[^A-Za-z0-9]/.test(v)) s++;
                            this.strength=s;
                        }
                    }" x-init="document.getElementById('siswa_password_new').addEventListener('input', e => update(e.target.value))"
                            class="mt-2">
                            <div class="flex gap-1 mb-1">
                                <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                    :class="strength>=1 ? colors[strength-1] : 'bg-gray-200'"></div>
                                <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                    :class="strength>=2 ? colors[strength-1] : 'bg-gray-200'"></div>
                                <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                    :class="strength>=3 ? colors[strength-1] : 'bg-gray-200'"></div>
                                <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                    :class="strength>=4 ? colors[strength-1] : 'bg-gray-200'"></div>
                            </div>
                            <p class="text-xs font-semibold"
                                :class="strength===0 ? 'text-gray-400' : (strength<=2 ? 'text-red-500' : (strength===3 ? 'text-yellow-500' : 'text-green-600'))"
                                x-text="labels[strength]"></p>
                        </div>
                        @if($errors->has('password'))
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

                    {{-- Konfirmasi Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Konfirmasi
                            Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password_confirmation"
                                placeholder="Ulangi password baru"
                                class="w-full border {{ $errors->has('password_confirmation') ? 'border-red-400 bg-red-50' : 'border-slate-200 focus:border-[#1e3a6e]' }} focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm pr-10">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-[#1e3a6e] focus:outline-none transition-colors">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @if($errors->has('password_confirmation'))
                            <div
                                class="mt-2 flex items-center gap-2 text-red-600 font-semibold text-sm bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $errors->first('password_confirmation') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-8 py-3 rounded-xl text-sm transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>