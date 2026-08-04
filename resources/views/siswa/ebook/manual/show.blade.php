<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('ebook.manual.index') }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Detail Buku Cetak</span>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if(session('success_catatan'))
        <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success_catatan') }}
        </div>
        @endif

        {{-- Detail Buku --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            <div class="p-6 text-slate-800">
                <div class="flex flex-col md:flex-row gap-8">
                    {{-- Sampul Buku --}}
                    <div class="w-full md:w-1/3 flex-shrink-0">
                        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-2 overflow-hidden shadow-sm">
                            <img src="{{ asset('storage/' . $buku->foto_sampul) }}" alt="Sampul {{ $buku->judul }}"
                                 class="w-full h-auto object-cover rounded-xl shadow">
                        </div>
                    </div>

                    {{-- Informasi Buku --}}
                    <div class="w-full md:w-2/3 flex flex-col justify-center">
                        <div class="mb-4">
                            <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-[#1e3a6e] text-xs font-black uppercase tracking-wider mb-2 border border-blue-200">
                                Level {{ $buku->level }}
                            </span>
                            @if($buku->status_selesai)
                                <span class="inline-block px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-black uppercase tracking-wider mb-2 border border-green-200 ml-2">
                                    Selesai Dibaca
                                </span>
                            @endif
                            @if(isset($rataNilai) && $rataNilai !== null)
                                <span class="inline-block px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-black uppercase tracking-wider mb-2 border border-purple-200 ml-2">
                                    Nilai Rata-rata: {{ $rataNilai }}
                                </span>
                            @endif
                            <h2 class="text-2xl font-black text-slate-800">{{ $buku->judul }}</h2>
                            <p class="text-lg font-medium text-slate-500 mt-1">oleh {{ $buku->penulis }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4 bg-slate-50 p-5 rounded-xl border border-slate-100">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Penerbit</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $buku->penerbit }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kota Terbit</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $buku->kota_terbit }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tahun Terbit</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $buku->tahun_terbit }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jumlah Halaman</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $buku->jumlah_halaman }} halaman</p>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100 flex md:flex-wrap justify-between md:justify-end gap-2 md:gap-3">
                            <a href="{{ route('ebook.manual.index') }}" class="flex-1 md:flex-none justify-center px-2 md:px-5 py-2.5 rounded-xl border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-[11px] md:text-sm transition flex items-center gap-1 md:gap-2 text-center text-balance">
                                <svg class="w-4 h-4 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Kembali</span>
                            </a>
                            <a href="{{ route('ebook.manual.edit', $buku->id) }}" class="flex-1 md:flex-none justify-center px-2 md:px-5 py-2.5 rounded-xl border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-blue-50 font-semibold text-[11px] md:text-sm transition flex items-center gap-1 md:gap-2 text-center text-balance">
                                <svg class="w-4 h-4 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit Buku</span>
                            </a>
                            @if(!$buku->status_selesai)
                                <a href="{{ route('ebook.indikator.show', ['jenis' => 'manual', 'id' => $buku->id]) }}"
                                   class="flex-1 md:flex-none flex items-center justify-center text-center px-2 md:px-6 py-2.5 rounded-xl text-[11px] md:text-sm font-bold text-white bg-yellow-500 hover:bg-yellow-600 transition-colors shadow-sm shadow-yellow-500/30 leading-tight">
                                    Isi Indikator
                                </a>
                            @elseif(isset($rataNilai) && $rataNilai !== null)
                                <a href="{{ route('ebook.indikator.show', ['jenis' => 'manual', 'id' => $buku->id]) }}"
                                   class="flex-1 md:flex-none flex items-center justify-center text-center px-2 md:px-6 py-2.5 rounded-xl text-[11px] md:text-sm font-bold text-white bg-purple-500 hover:bg-purple-600 transition-colors shadow-sm shadow-purple-500/30 leading-tight text-balance">
                                    Lihat Penilaian Guru
                                </a>
                            @else
                                <a href="{{ route('ebook.indikator.show', ['jenis' => 'manual', 'id' => $buku->id]) }}"
                                   class="flex-1 md:flex-none flex items-center justify-center text-center px-2 md:px-6 py-2.5 rounded-xl text-[11px] md:text-sm font-bold text-white bg-slate-500 hover:bg-slate-600 transition-colors shadow-sm shadow-slate-500/30 leading-tight text-balance">
                                    Lihat Jawaban Indikator
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan Progres Membaca --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-700">Catatan Progres Membaca</h3>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Catatan ini akan terlihat oleh guru sebagai laporan perkembangan bacaanmu.</p>
                </div>
            </div>
            <div class="p-6">
                @error('catatan')
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-medium px-4 py-3 rounded-xl">{{ $message }}</div>
                @enderror

                <form action="{{ route('catatan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jenis_buku" value="manual">
                    <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                    <textarea name="catatan" rows="5" maxlength="2000"
                              class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-3 text-slate-800 font-medium focus:outline-none transition text-sm placeholder-slate-400"
                              placeholder="Tuliskan catatan progres membacamu di sini... (misal: sudah sampai halaman berapa, kesan terhadap buku, dll)">{{ $catatan?->catatan }}</textarea>
                    <div class="flex justify-between items-center mt-3">
                        <p class="text-xs text-slate-400">Maksimal 2000 karakter.</p>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-[#1e3a6e] hover:bg-[#162d57] transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
