@php
    $isEdit = isset($ebook);
    $title  = $isEdit ? 'Edit e-Book: ' . $ebook->judul : 'Tambah e-Book Baru';
    $action = $isEdit ? route('admin.ebook.update', $ebook->id) : route('admin.ebook.store');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.ebook.index') }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">{{ $title }}</span>
        </div>
    </x-slot>

<div class="max-w-3xl space-y-6">

    <h2 class="text-xl font-black text-slate-800">{{ $title }}</h2>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-sm">
        <p class="font-bold mb-2">Periksa kembali isian form:</p>
        <ul class="list-disc list-inside space-y-1 font-medium">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Info Dasar --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                        <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    Informasi Dasar
                </h3>
            </div>
            <div class="px-6 py-6 space-y-5">

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                        Level <span class="text-red-500">*</span>
                        <span class="font-normal normal-case text-slate-400 ml-1">(urutan baca)</span>
                    </label>
                    <input type="number" name="level" min="1"
                           value="{{ old('level', $ebook->level ?? $nextLevel ?? '') }}"
                           placeholder="Contoh: 1"
                           class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                        Judul e-Book <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul"
                           value="{{ old('judul', $ebook->judul ?? '') }}"
                           placeholder="Judul e-Book..."
                           class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="2"
                              placeholder="Deskripsi singkat e-Book (max 500 karakter)..."
                              class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm resize-none">{{ old('deskripsi', $ebook->deskripsi ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">
                        Teks Referensi Verifikasi Suara
                        <span class="font-normal normal-case text-slate-400 ml-1">(teks yang harus dibacakan murid)</span>
                    </label>
                    <textarea name="konten_teks" rows="5"
                              placeholder="Masukkan paragraf atau kalimat yang akan dibacakan murid sebagai verifikasi sebelum lanjut ke level berikutnya..."
                              class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm resize-none">{{ old('konten_teks', $ebook->konten_teks ?? '') }}</textarea>
                    <p class="text-[.7rem] text-slate-400 mt-1.5">Sistem akan membandingkan suara murid dengan teks ini. Minimal kesamaan 60% untuk membuka level berikutnya.</p>
                </div>

                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="aktif" value="0">
                        <input type="checkbox" name="aktif" value="1"
                               {{ old('aktif', $ebook->aktif ?? true) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white after:content-['']
                                    after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300
                                    after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                    peer-checked:bg-[#1e3a6e]"></div>
                    </label>
                    <span class="text-sm font-semibold text-slate-700">e-Book Aktif (tampil ke murid)</span>
                </div>
            </div>
        </div>

        {{-- Upload PDF --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <div class="w-5 h-5 rounded bg-red-100 flex items-center justify-center">
                        <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    File PDF e-Book
                </h3>
            </div>
            <div class="px-6 py-6 space-y-4">

                @if($isEdit && $ebook->file_pdf)
                {{-- Preview file lama --}}
                <div class="flex items-center justify-between bg-red-50 border border-red-100 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">File PDF Saat Ini</p>
                            <a href="{{ asset('storage/' . $ebook->file_pdf) }}" target="_blank"
                               class="text-xs text-[#1e3a6e] hover:underline font-medium">
                                Lihat / Unduh PDF →
                            </a>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="hapus_pdf" value="1" class="rounded border-slate-300 text-red-500">
                        <span class="text-xs font-bold text-red-600">Hapus file</span>
                    </label>
                </div>
                @endif

                {{-- Dropzone Upload --}}
                <div class="relative border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center
                            hover:border-[#1e3a6e]/40 transition-colors group cursor-pointer"
                     id="dropzone"
                     onclick="document.getElementById('file_pdf').click()">
                    <div id="dropzoneContent">
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 group-hover:bg-[#1e3a6e]/5 flex items-center justify-center mx-auto mb-3 transition-colors">
                            <svg class="w-7 h-7 text-slate-400 group-hover:text-[#1e3a6e] transition-colors"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <p class="font-bold text-slate-600 group-hover:text-[#1e3a6e] text-sm transition-colors">
                            Klik untuk upload file PDF
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Format PDF saja · Maksimal 20MB</p>
                    </div>
                    <div id="dropzonePreview" class="hidden">
                        <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="font-bold text-red-600 text-sm" id="fileName">-</p>
                        <p class="text-xs text-slate-400 mt-1" id="fileSize">-</p>
                        <button type="button" onclick="clearFile(event)"
                                class="mt-2 text-xs text-slate-500 hover:text-red-500 font-semibold transition">
                            × Batalkan pilihan
                        </button>
                    </div>
                    <input type="file" name="file_pdf" id="file_pdf" accept=".pdf"
                           class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                           onchange="previewFile(this)" onclick="event.stopPropagation()">
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.ebook.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm transition">
                Batal
            </a>
            <button type="submit"
                    class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm">
                {{ $isEdit ? 'Perbarui e-Book' : 'Simpan e-Book' }}
            </button>
        </div>
    </form>
</div>

<script>
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('dropzoneContent').classList.add('hidden');
    document.getElementById('dropzonePreview').classList.remove('hidden');
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
}
function clearFile(e) {
    e.stopPropagation();
    document.getElementById('file_pdf').value = '';
    document.getElementById('dropzoneContent').classList.remove('hidden');
    document.getElementById('dropzonePreview').classList.add('hidden');
}
</script>
</x-app-layout>
