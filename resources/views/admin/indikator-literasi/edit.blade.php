<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Edit Pertanyaan Indikator Literasi</span>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.indikator.index') }}" class="text-slate-400 hover:text-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-black text-slate-800">Edit Pertanyaan Indikator Literasi</h2>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Detail Pertanyaan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Ubah kolom di bawah ini jika diperlukan</p>
            </div>
            <form action="{{ route('admin.indikator.update', $indikator->id) }}" method="POST" class="px-6 py-6 space-y-5">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="pertanyaan" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Pertanyaan <span class="text-red-500">*</span></label>
                    <textarea name="pertanyaan" id="pertanyaan" rows="3" required
                              class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm @error('pertanyaan') border-red-300 @enderror">{{ old('pertanyaan', $indikator->pertanyaan) }}</textarea>
                    @error('pertanyaan')
                        <p class="text-red-500 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-200">
                    <input type="hidden" name="aktif" value="0">
                    <input type="checkbox" id="aktif" name="aktif" value="1"
                           {{ old('aktif', $indikator->aktif) ? 'checked' : '' }}
                           class="w-4 h-4 rounded accent-[#1e3a6e] cursor-pointer">
                    <label for="aktif" class="text-sm font-semibold text-slate-700 cursor-pointer">
                        Aktifkan pertanyaan ini
                        <span class="block text-xs text-slate-400 font-normal">Pertanyaan yang aktif akan muncul di formulir indikator murid.</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.indikator.index') }}"
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
    </div>
</x-app-layout>
