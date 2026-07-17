<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Jawaban Indikator E-Book') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('guru.literasi.jawaban-indikator') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tingkat</label>
                        <select name="tingkat" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-sm">
                            <option value="">Semua Tingkat</option>
                            @foreach($tingkats as $t)
                                <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jurusan</label>
                        <select name="jurusan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-sm">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Rombel</label>
                        <select name="rombel" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-sm">
                            <option value="">Semua Rombel</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r }}" {{ request('rombel') == $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-[#1e3a6e] hover:bg-[#162d57] text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            @forelse($groupedJawabans as $key => $items)
                @php
                    $first = $items->first();
                    $siswa = $first->user;
                    // We don't have Buku model directly from JawabanIndikator since it uses morph-like (jenis_buku, buku_id).
                    // In a real scenario we'd fetch the book name here.
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">{{ $siswa->name }}</h3>
                            <p class="text-sm text-gray-500">
                                Kelas: {{ $siswa->siswaProfile?->kelas }} {{ $siswa->siswaProfile?->jurusan }} {{ $siswa->siswaProfile?->rombel }}
                                &bull; NIS: {{ $siswa->siswaProfile?->nis ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('guru.literasi.jawaban-indikator.nilai') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $siswa->id }}">
                            <input type="hidden" name="buku_id" value="{{ $first->buku_id }}">
                            <input type="hidden" name="jenis_buku" value="{{ $first->jenis_buku }}">
                            
                            <div class="space-y-6">
                                @foreach($items as $jawaban)
                                    <div class="bg-blue-50/30 p-4 rounded-xl border border-blue-100">
                                        <p class="font-semibold text-gray-800 mb-2">Q: {{ $jawaban->indikator?->pertanyaan }}</p>
                                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-700 text-sm mb-4">
                                            {{ $jawaban->jawaban }}
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Penilaian</label>
                                                <select name="nilai_guru[{{ $jawaban->indikator_id }}]" required
                                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-sm">
                                                    <option value="">Pilih Nilai</option>
                                                    <option value="4" {{ $jawaban->nilai_guru == 4 ? 'selected' : '' }}>Sangat Baik (4)</option>
                                                    <option value="3" {{ $jawaban->nilai_guru == 3 ? 'selected' : '' }}>Baik (3)</option>
                                                    <option value="2" {{ $jawaban->nilai_guru == 2 ? 'selected' : '' }}>Cukup (2)</option>
                                                    <option value="1" {{ $jawaban->nilai_guru == 1 ? 'selected' : '' }}>Kurang (1)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Catatan / Alasan</label>
                                                <input type="text" name="catatan_guru[{{ $jawaban->indikator_id }}]" value="{{ $jawaban->catatan_guru }}"
                                                       placeholder="Beri catatan untuk jawaban ini..."
                                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#1e3a6e] focus:ring focus:ring-[#1e3a6e]/20 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-sm transition">
                                    Simpan Penilaian
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 text-center rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-500 font-medium">Belum ada jawaban indikator dari murid.</p>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $jawabans->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
