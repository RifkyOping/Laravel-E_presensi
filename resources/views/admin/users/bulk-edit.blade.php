<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Edit Massal Pengguna</span>
    </x-slot>

    <div class="max-w-5xl space-y-6">

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users') }}" class="text-slate-400 hover:text-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-black text-slate-800">Edit Massal ({{ $users->count() }} Pengguna)</h2>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-sm">
            <p class="font-bold mb-2">Periksa kembali isian form:</p>
            <ul class="list-disc list-inside space-y-1 font-medium">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.bulk-update') }}" class="space-y-5">
            @csrf @method('PUT')

            @foreach($users as $index => $user)
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                {{-- Header Accordion --}}
                <button type="button" @click="open = !open"
                    class="w-full px-6 py-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <span class="font-bold text-slate-800 text-sm block">{{ $user->name }}</span>
                            <span class="text-xs text-slate-400">
                                {{ ucfirst($user->role) }} &middot;
                                {{ $user->nomor_induk ?? '-' }}
                                @if($user->role === 'murid' && $user->siswaProfile?->nis)
                                    &middot; NIS: {{ $user->siswaProfile->nis }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Body Form --}}
                <div x-show="open" x-transition class="border-t border-slate-100 px-6 py-6 space-y-5">
                    {{-- Data Akun --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="users[{{ $user->id }}][name]" value="{{ old("users.{$user->id}.name", $user->name) }}" required
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" name="users[{{ $user->id }}][email]" value="{{ old("users.{$user->id}.email", $user->email) }}"
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">NISN / NIP</label>
                            <input type="text" name="users[{{ $user->id }}][nomor_induk]" value="{{ old("users.{$user->id}.nomor_induk", $user->nomor_induk) }}"
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Role <span class="text-red-500">*</span></label>
                            <select name="users[{{ $user->id }}][role]"
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                                <option value="murid" {{ $user->role === 'murid' ? 'selected' : '' }}>Murid</option>
                                <option value="guru" {{ $user->role === 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="pengawas" {{ $user->role === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                    </div>

                    {{-- Profil Murid --}}
                    @if($user->role === 'murid')
                    <div class="border-t border-emerald-100 pt-5">
                        <h4 class="text-xs font-black text-emerald-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center text-white text-[8px] font-black">S</span>
                            Profil Murid
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">NIS</label>
                                <input type="text" name="users[{{ $user->id }}][nis]" value="{{ old("users.{$user->id}.nis", $user->siswaProfile?->nis) }}"
                                    class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Kelas</label>
                                <select name="users[{{ $user->id }}][kelas_id]"
                                    class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelasList as $kelasOption)
                                        @php
                                            $isSelected = ($user->siswaProfile?->kelas === $kelasOption->tingkat &&
                                                           $user->siswaProfile?->jurusan === $kelasOption->jurusan &&
                                                           $user->siswaProfile?->rombel === $kelasOption->rombel);
                                        @endphp
                                        <option value="{{ $kelasOption->id }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $kelasOption->tingkat }} {{ $kelasOption->jurusan }} {{ $kelasOption->rombel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                                <select name="users[{{ $user->id }}][jenis_kelamin]"
                                    class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ $user->siswaProfile?->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $user->siswaProfile?->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Agama</label>
                                <select name="users[{{ $user->id }}][agama]"
                                    class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                                    <option value="">-- Pilih Agama --</option>
                                    @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Kepercayaan kpd Tuhan YME'] as $ag)
                                    <option value="{{ $ag }}" {{ strtolower($user->siswaProfile?->agama) === strtolower($ag) ? 'selected' : '' }}>{{ $ag }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tempat Lahir</label>
                                <input type="text" name="users[{{ $user->id }}][tempat_lahir]" value="{{ old("users.{$user->id}.tempat_lahir", $user->siswaProfile?->tempat_lahir) }}"
                                    class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                                <input type="date" name="users[{{ $user->id }}][tanggal_lahir]" value="{{ old("users.{$user->id}.tanggal_lahir", $user->siswaProfile?->tanggal_lahir?->format('Y-m-d')) }}"
                                    class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Tombol Simpan --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.users') }}"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-sm transition">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
