<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Tambah Mata Pelajaran</span>
    </x-slot>

<div class="max-w-2xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.mata-pelajaran.index') }}" class="text-slate-400 hover:text-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-black text-slate-800">Tambah Mata Pelajaran</h2>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Detail Mata Pelajaran</h3>
            <p class="text-xs text-slate-400 mt-0.5">Isi semua kolom yang bertanda bintang (*)</p>
        </div>
        <form method="POST" action="{{ route('admin.mata-pelajaran.store') }}" class="px-6 py-6 space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Matematika Wajib"
                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm @error('nama') border-red-300 @enderror">
                @error('nama')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                    Kode <span class="text-red-500">*</span>
                    <span class="font-normal normal-case text-slate-400 ml-1">(maks. 20 karakter, huruf besar)</span>
                </label>
                <input type="text" name="kode" value="{{ old('kode') }}" placeholder="Contoh: MTK, IPA, B.ING" maxlength="20"
                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm uppercase @error('kode') border-red-300 @enderror">
                @error('kode')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tingkat Kelas <span class="text-red-500">*</span></label>
                    <select name="tingkat"
                            class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white @error('tingkat') border-red-300 @enderror">
                        <option value="">-- Pilih Tingkat --</option>
                        @foreach(['X','XI','XII','Semua'] as $t)
                        <option value="{{ $t }}" {{ old('tingkat')===$t?'selected':'' }}>
                            {{ $t==='Semua' ? 'Semua Tingkat' : 'Kelas '.$t }}
                        </option>
                        @endforeach
                    </select>
                    @error('tingkat')<p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Jurusan <span class="text-slate-400 font-medium normal-case">(opsional)</span></label>
                    <input type="text" name="jurusan" value="{{ old('jurusan') }}" placeholder="Contoh: TKJ, RPL, AK"
                           class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Deskripsi <span class="text-slate-400 font-medium normal-case">(opsional)</span></label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang mata pelajaran ini..."
                          class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm resize-none">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="flex items-center gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-200">
                <input type="hidden" name="aktif" value="0">
                <input type="checkbox" id="aktif" name="aktif" value="1"
                       {{ old('aktif', '1') == '1' ? 'checked' : '' }}
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
                    Simpan Mata Pelajaran
                </button>
            </div>
        </form>
    </div>

</div>
</x-app-layout>
