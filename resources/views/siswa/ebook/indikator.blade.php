<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ $jenis === 'digital' ? route('ebook.index') : route('ebook.manual.index') }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Pertanyaan Indikator Literasi</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-xl font-black text-[#1e3a6e]">Indikator Literasi: {{ $judulBuku }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Jawablah pertanyaan berikut berdasarkan pemahaman Anda terhadap isi buku.</p>
            </div>

            <div class="p-6">
                @if ($isSelesai)
                    <div class="bg-green-50 text-green-800 border border-green-200 p-4 rounded-xl mb-6 font-medium text-sm flex gap-3 items-center">
                        <svg class="w-5 h-5 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Anda sudah menyelesaikan indikator literasi untuk buku ini.
                    </div>
                    
                    <div class="space-y-6">
                        @foreach($indikators as $index => $indikator)
                            @php
                                $jawaban = isset($jawabanSiswa) ? $jawabanSiswa->get($indikator->id) : null;
                            @endphp
                            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                                <label class="block text-sm font-bold text-slate-800 mb-2">
                                    {{ $index + 1 }}. {{ $indikator->pertanyaan }}
                                </label>
                                <div class="bg-white p-4 rounded-lg border border-slate-200 text-slate-700 text-sm mb-4">
                                    {{ $jawaban ? $jawaban->jawaban : '-' }}
                                </div>
                                
                                @if($jawaban && $jawaban->nilai_guru)
                                    <div class="bg-blue-50/50 p-4 rounded-lg border border-blue-100 flex flex-col sm:flex-row sm:items-start gap-4">
                                        <div class="shrink-0 flex flex-col items-center justify-center p-3 rounded-lg border {{ $jawaban->nilai_guru == 4 ? 'bg-green-100 border-green-200 text-green-700' : ($jawaban->nilai_guru == 3 ? 'bg-blue-100 border-blue-200 text-blue-700' : ($jawaban->nilai_guru == 2 ? 'bg-yellow-100 border-yellow-200 text-yellow-700' : 'bg-red-100 border-red-200 text-red-700')) }}">
                                            <span class="text-2xl font-black">{{ $jawaban->nilai_guru }}</span>
                                            <span class="text-[0.6rem] font-bold uppercase tracking-wider">
                                                {{ $jawaban->nilai_guru == 4 ? 'Sangat Baik' : ($jawaban->nilai_guru == 3 ? 'Baik' : ($jawaban->nilai_guru == 2 ? 'Cukup' : 'Kurang')) }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Catatan Guru</h4>
                                            <p class="text-sm font-medium text-slate-800 italic">
                                                "{{ $jawaban->catatan_guru ?? 'Tidak ada catatan' }}"
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-200 text-amber-700 text-xs font-semibold flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Menunggu penilaian dari Guru Bahasa Indonesia.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="{{ $jenis === 'digital' ? route('ebook.index') : route('ebook.manual.index') }}" class="px-6 py-2.5 bg-[#1e3a6e] text-white rounded-xl text-sm font-bold shadow-sm hover:bg-[#162d57] transition">
                            Kembali ke Koleksi
                        </a>
                    </div>

                @else

                    @if ($errors->any())
                        <div class="bg-red-50 text-red-700 border border-red-200 p-4 rounded-xl mb-6 font-medium text-sm flex gap-3 items-start">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ebook.indikator.store', ['jenis' => $jenis, 'id' => $buku->id]) }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            @foreach($indikators as $index => $indikator)
                                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                                    <label for="indikator_{{ $indikator->id }}" class="block text-sm font-bold text-slate-800 mb-3">
                                        {{ $index + 1 }}. {{ $indikator->pertanyaan }} <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="jawaban[{{ $indikator->id }}]" id="indikator_{{ $indikator->id }}" rows="4" required
                                            class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm placeholder-slate-400"
                                            placeholder="Tuliskan jawaban Anda di sini...">{{ old('jawaban.'.$indikator->id) }}</textarea>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-slate-100">
                            <a href="{{ $jenis === 'digital' ? route('ebook.index') : route('ebook.manual.index') }}"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm transition">
                                Nanti Saja
                            </a>
                            <button type="submit"
                                    class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm flex items-center gap-2">
                                Kirim Jawaban & Selesaikan Level
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
