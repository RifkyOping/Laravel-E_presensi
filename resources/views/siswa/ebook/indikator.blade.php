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
            </div>
        </div>
    </div>
</x-app-layout>
