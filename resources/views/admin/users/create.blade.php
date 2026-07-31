<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Tambah Akun Baru</span>
    </x-slot>

<div class="max-w-3xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users') }}" class="text-slate-400 hover:text-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-black text-slate-800">Tambah Akun Baru</h2>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-sm">
        <p class="font-bold mb-2">Periksa kembali isian form:</p>
        <ul class="list-disc list-inside space-y-1 font-medium">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5" id="createForm">
        @csrf

        {{-- Data Akun --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-bold text-slate-800">Data Akun</h3></div>
            <div class="px-6 py-6 space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap pengguna"
                               class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com"
                               class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">NISN / NIP <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_induk" value="{{ old('nomor_induk') }}" placeholder="NISN atau NIP"
                               class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Role <span class="text-red-500">*</span></label>
                        <select name="role" id="roleSelect"
                                class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                            <option value="">-- Pilih Role --</option>
                            <option value="murid"    {{ old('role')==='murid'    ?'selected':'' }}>Murid</option>
                            <option value="guru"     {{ old('role')==='guru'     ?'selected':'' }}>Guru</option>
                            <option value="pengawas" {{ old('role')==='pengawas' ?'selected':'' }}>Pengawas</option>
                            <option value="kurikulum" {{ old('role')==='kurikulum' ?'selected':'' }}>Kurikulum</option>
                            <option value="admin"    {{ old('role')==='admin'    ?'selected':'' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div x-data="{ show: false }">
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" placeholder="Minimal 6 karakter"
                                   class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm pr-10">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-[#1e3a6e] focus:outline-none transition-colors">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>
                    <div x-data="{ show: false }">
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi password"
                                   class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm pr-10">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-[#1e3a6e] focus:outline-none transition-colors">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Profil Murid (hanya muncul jika role=murid) --}}
        <div id="profilSiswa" class="{{ old('role') === 'murid' ? '' : 'hidden' }}
                                      bg-white rounded-xl border border-emerald-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-emerald-100 bg-emerald-50/50">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-white text-[10px] font-black">S</span>
                    Data Akademik Murid
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Data lengkap profil murid.</p>
            </div>
            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Kelas</label>
                        <select name="kelas_id" class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                            <option value="">Pilih Kelas</option>
                            @foreach ($kelasList as $kelasOption)
                                <option value="{{ $kelasOption->id }}" {{ old('kelas_id') == $kelasOption->id ? 'selected' : '' }}>
                                    {{ $kelasOption->tingkat }} {{ $kelasOption->jurusan }} {{ $kelasOption->rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                                class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin')==='L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin')==='P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Kota kelahiran"
                               class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                               class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Agama</label>
                    <select name="agama"
                            class="w-full border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                        <option value="">-- Pilih Agama --</option>
                        @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                        <option value="{{ $ag }}" {{ old('agama')===$ag ? 'selected' : '' }}>{{ $ag }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Profil Guru (hanya muncul jika role=guru) --}}
        <div id="profilGuru" class="{{ old('role') === 'guru' ? '' : 'hidden' }}
                                      bg-white rounded-xl border border-blue-200 overflow-hidden mt-5">
            <div class="px-6 py-4 border-b border-blue-100 bg-blue-50/50">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center text-white text-[10px] font-black">G</span>
                    Pengaturan Guru
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Centang untuk mengaktifkan tugas khusus guru ini.</p>
            </div>
            <div class="px-6 py-6 space-y-4">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-200 bg-slate-50 hover:bg-blue-50/30 cursor-pointer transition group">
                    <input type="checkbox" name="is_piket_sholat" value="1"
                           {{ old('is_piket_sholat') ? 'checked' : '' }}
                           class="w-5 h-5 text-[#1e3a6e] border-slate-300 rounded focus:ring-[#1e3a6e]">
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition">Petugas Piket Sholat</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-200 bg-slate-50 hover:bg-blue-50/30 cursor-pointer transition group">
                    <input type="checkbox" name="is_piket_mengajar" value="1"
                           {{ old('is_piket_mengajar') ? 'checked' : '' }}
                           class="w-5 h-5 text-[#1e3a6e] border-slate-300 rounded focus:ring-[#1e3a6e]">
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition">Petugas Verifikasi Aktivitas Mengajar</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-200 bg-slate-50 hover:bg-blue-50/30 cursor-pointer transition group">
                    <input type="checkbox" name="is_piket_absen_qr" value="1"
                           {{ old('is_piket_absen_qr') ? 'checked' : '' }}
                           class="w-5 h-5 text-[#1e3a6e] border-slate-300 rounded focus:ring-[#1e3a6e]">
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition">Piket Scan Absen QR</span>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-200 bg-slate-50 hover:bg-blue-50/30 cursor-pointer transition group">
                    <input type="checkbox" name="is_guru_bahasa" value="1"
                           {{ old('is_guru_bahasa') ? 'checked' : '' }}
                           class="w-5 h-5 text-[#1e3a6e] border-slate-300 rounded focus:ring-[#1e3a6e]">
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-600 transition">Guru B. Indonesia (Literasi)</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.users') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm">
                Simpan Akun
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function () {
    const panelSiswa = document.getElementById('profilSiswa');
    const panelGuru = document.getElementById('profilGuru');

    if (panelSiswa) panelSiswa.classList.toggle('hidden', this.value !== 'murid');
    if (panelGuru) panelGuru.classList.toggle('hidden', this.value !== 'guru');
});
</script>
</x-app-layout>
