<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('ebook.manual.show', $buku->id) }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Edit Buku Manual Level {{ $buku->level }}</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
                <div class="p-6 text-slate-800">
                    <h3 class="text-xl font-black mb-4">Edit Form Buku Manual</h3>
                    <p class="text-sm text-slate-500 mb-6">Ubah data buku fisik yang telah Anda baca jika ada kesalahan penulisan.</p>

                    <form action="{{ route('ebook.manual.update', $buku->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Judul -->
                            <div>
                                <label for="judul" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Judul Buku <span class="text-red-500">*</span></label>
                                <input type="text" name="judul" id="judul" value="{{ old('judul', $buku->judul) }}" required
                                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                                @error('judul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Penulis -->
                            <div>
                                <label for="penulis" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Penulis/Pengarang <span class="text-red-500">*</span></label>
                                <input type="text" name="penulis" id="penulis" value="{{ old('penulis', $buku->penulis) }}" required
                                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                                @error('penulis') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Penerbit -->
                            <div>
                                <label for="penerbit" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Penerbit <span class="text-red-500">*</span></label>
                                <input type="text" name="penerbit" id="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" required
                                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                                @error('penerbit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Kota Terbit -->
                            <div>
                                <label for="kota_terbit" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Kota Terbit <span class="text-red-500">*</span></label>
                                <input type="text" name="kota_terbit" id="kota_terbit" value="{{ old('kota_terbit', $buku->kota_terbit) }}" required
                                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                                @error('kota_terbit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tahun Terbit -->
                            <div>
                                <label for="tahun_terbit" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tahun Terbit <span class="text-red-500">*</span></label>
                                <input type="number" name="tahun_terbit" id="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" min="1900" max="{{ date('Y') }}" required
                                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                                @error('tahun_terbit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Jumlah Halaman -->
                            <div>
                                <label for="jumlah_halaman" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Jumlah Halaman <span class="text-red-500">*</span></label>
                                <input type="number" name="jumlah_halaman" id="jumlah_halaman" value="{{ old('jumlah_halaman', $buku->jumlah_halaman) }}" min="1" required
                                       class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                                @error('jumlah_halaman') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Foto Sampul -->
                        <div class="mt-6 flex flex-col sm:flex-row gap-6">
                            <div class="w-full sm:w-1/4">
                                <p class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Sampul Saat Ini</p>
                                <div class="bg-slate-50 rounded-xl border border-slate-200 p-2 overflow-hidden shadow-sm">
                                    <img src="{{ asset('storage/' . $buku->foto_sampul) }}" alt="Sampul {{ $buku->judul }}" class="w-full h-auto object-cover rounded shadow-sm">
                                </div>
                            </div>
                            <div class="w-full sm:w-3/4">
                                <label for="foto_sampul" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Ganti Foto Sampul Buku (Opsional, Max 2MB)</label>
                                <input type="file" name="foto_sampul" id="foto_sampul" accept="image/*"
                                       class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#1e3a6e] hover:file:bg-blue-100 transition-colors">
                                <p class="text-xs text-slate-400 mt-2">Biarkan kosong jika tidak ingin mengubah sampul buku.</p>
                                @error('foto_sampul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                            <a href="{{ route('ebook.manual.show', $buku->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-[#1e3a6e] hover:bg-[#162d57] transition-colors shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
