<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Edit Mata Pelajaran</span>
    </x-slot>

<div class="max-w-2xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.mata-pelajaran.index') }}" class="text-slate-400 hover:text-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-black text-slate-800">Edit: {{ $mataPelajaran->nama }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Kode: <strong>{{ $mataPelajaran->kode }}</strong> &middot;
                <span class="{{ $mataPelajaran->aktif ? 'text-green-600' : 'text-slate-400' }} font-semibold">
                    {{ $mataPelajaran->aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Detail Mata Pelajaran</h3>
        </div>
        <form method="POST" action="{{ route('admin.mata-pelajaran.update', $mataPelajaran->id) }}" class="px-6 py-6 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $mataPelajaran->nama) }}" placeholder="Contoh: Matematika Wajib"
                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm @error('nama') border-red-300 @enderror">
                @error('nama')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                    Kode <span class="text-red-500">*</span>
                    <span class="font-normal normal-case text-slate-400 ml-1">(maks. 20 karakter)</span>
                </label>
                <input type="text" name="kode" value="{{ old('kode', $mataPelajaran->kode) }}" placeholder="Contoh: MTK" maxlength="20"
                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm uppercase @error('kode') border-red-300 @enderror">
                @error('kode')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
            </div>



            <div class="flex items-center gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-200">
                <input type="hidden" name="aktif" value="0">
                <input type="checkbox" id="aktif" name="aktif" value="1"
                       {{ old('aktif', $mataPelajaran->aktif ? '1' : '0') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 rounded accent-[#1e3a6e] cursor-pointer">
                <label for="aktif" class="text-sm font-semibold text-slate-700 cursor-pointer">
                    Aktifkan mata pelajaran ini
                    <span class="block text-xs text-slate-400 font-normal">Mapel aktif akan tersedia untuk guru saat mencatat aktivitas mengajar.</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <a href="{{ route('admin.mata-pelajaran.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm transition">
                    Batal
                </a>
                <button type="submit"
                        class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-red-100 bg-red-50/50">
            <h4 class="font-bold text-red-700 text-sm">Hapus Mata Pelajaran</h4>
            <p class="text-xs text-red-500 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="px-6 py-5 flex items-center justify-between gap-4">
            <p class="text-sm text-slate-500">
                Data <span class="font-semibold text-slate-700">{{ $mataPelajaran->nama }}</span> akan dihapus permanen dari sistem.
            </p>
            <form method="POST" action="{{ route('admin.mata-pelajaran.destroy', $mataPelajaran->id) }}"
                  onsubmit="return confirm('Yakin ingin menghapus mata pelajaran \"{{ $mataPelajaran->nama }}\"?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-red-600 font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
