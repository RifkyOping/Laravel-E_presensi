<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('murid.baca-quran.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('murid.baca-quran.index') }}" class="text-slate-400 hover:text-blue-600 text-sm transition-colors">Al-Qur'an</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800" id="headerJuzName">Juz {{ $nomor }}</span>
        </div>
    </x-slot>

    {{-- Import Google Font untuk teks Arab --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
        .arabic-text {
            font-family: 'Amiri', serif;
            line-height: 2.5;
        }
    </style>

<div class="max-w-4xl mx-auto pb-10" x-data="quranJuzApp({{ $nomor }})">

    {{-- Loading State --}}
    <div x-show="isLoading" class="py-32 text-center">
        <div class="animate-spin w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full mx-auto mb-4"></div>
        <p class="text-slate-500 font-medium animate-pulse">Memuat Juz {{ $nomor }}...</p>
    </div>

    {{-- Error State --}}
    <div x-show="hasError" style="display: none;" class="py-12 px-4 text-center bg-red-50 rounded-2xl border border-red-200 mt-6">
        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-red-600 font-bold">Gagal memuat Juz.</p>
        <button @click="fetchJuz()" class="mt-4 px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition">Coba Lagi</button>
    </div>

    <div x-show="!isLoading && !hasError && ayahs.length > 0" style="display: none;" class="space-y-6 mt-4">
        
        {{-- Header --}}
        <div class="text-center py-8">
            <h2 class="text-2xl font-bold text-slate-700">Juz {{ $nomor }}</h2>
            <div class="flex flex-wrap justify-center gap-2 mt-3">
                <template x-for="surah in uniqueSurahs" :key="surah">
                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-sm font-medium border border-slate-200" x-text="surah"></span>
                </template>
            </div>
        </div>

        {{-- Daftar Ayat Bersambung --}}
        <div class="bg-white rounded-3xl border border-slate-100 p-8 sm:p-14 shadow-sm relative">
            <div class="text-justify text-[2rem] sm:text-[2.5rem] text-slate-900 arabic-text" dir="rtl" style="line-height: 2.5;">
                <template x-for="(ayat, index) in ayahs" :key="ayat.number">
                    <span class="inline">
                        {{-- Bismillah untuk awal surah --}}
                        <template x-if="ayat.numberInSurah === 1 && ayat.surah.number !== 1 && ayat.surah.number !== 9">
                            <span class="block text-center my-6 text-3xl sm:text-4xl">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</span>
                        </template>
                        
                        {{-- Nama surah penanda --}}
                        <template x-if="ayat.numberInSurah === 1">
                            <span class="block text-center my-8">
                                <span class="inline-block px-8 py-2 border-y-2 border-slate-200 text-xl font-bold text-blue-800" x-text="'سُورَةُ ' + ayat.surah.name.replace('سُورَةُ ', '')"></span>
                            </span>
                        </template>

                        <span x-text="cleanBismillah(ayat.text, ayat.numberInSurah, ayat.surah.number)" class="hover:bg-blue-50 transition-colors rounded"></span>
                        <span class="inline-flex items-center justify-center w-10 h-10 mx-2 align-middle relative text-blue-700/80 select-none">
                            <svg class="w-full h-full absolute inset-0" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="16" cy="16" r="14" stroke="currentColor"/>
                                <circle cx="16" cy="16" r="11" stroke="currentColor" stroke-dasharray="2 2" stroke-width="1"/>
                            </svg>
                            <span class="text-[0.65rem] sm:text-xs font-bold pt-0.5 z-10 font-sans" x-text="ayat.numberInSurah"></span>
                        </span>
                    </span>
                </template>
            </div>
        </div>

        {{-- Navigasi Bawah --}}
        <div class="flex items-center justify-between pt-6 border-t border-slate-200 mt-10">
            <a :href="nomor > 1 ? `/murid/baca-quran/juz/${nomor - 1}` : '#'" 
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition"
               :class="nomor > 1 ? 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                &larr; Juz Sebelumnya
            </a>
            
            <a :href="nomor < 30 ? `/murid/baca-quran/juz/${nomor + 1}` : '#'" 
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition"
               :class="nomor < 30 ? 'bg-[#1e3a6e] hover:bg-[#162d57] text-white shadow-md' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                Juz Selanjutnya &rarr;
            </a>
        </div>

    </div>
</div>

<script>
function quranJuzApp(nomor) {
    return {
        nomor: nomor,
        ayahs: [],
        uniqueSurahs: [],
        isLoading: true,
        hasError: false,

        init() {
            this.fetchJuz();
        },

        async fetchJuz() {
            this.isLoading = true;
            this.hasError = false;
            try {
                const response = await fetch(`https://api.alquran.cloud/v1/juz/${this.nomor}/quran-uthmani`);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                
                if (data.code === 200) {
                    this.ayahs = data.data.ayahs;
                    // Ambil daftar unik nama surah yang ada di juz ini
                    const surahMap = new Map();
                    this.ayahs.forEach(ayah => {
                        surahMap.set(ayah.surah.number, ayah.surah.englishName);
                    });
                    this.uniqueSurahs = Array.from(surahMap.values());
                    document.title = `Juz ${this.nomor} - Baca Al-Qur'an`;
                } else {
                    throw new Error('API returned error');
                }
            } catch (error) {
                console.error('Error fetching juz:', error);
                this.hasError = true;
            } finally {
                this.isLoading = false;
            }
        },

        // API alquran.cloud kadang menyertakan Bismillah di teks ayat 1, kita hilangkan agar tidak dobel
        cleanBismillah(text, numberInSurah, surahNumber) {
            if (numberInSurah === 1 && surahNumber !== 1 && surahNumber !== 9) {
                const bismillah = "بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ ";
                if (text.startsWith(bismillah)) {
                    return text.replace(bismillah, '');
                }
                const bismillahAlt = "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ ";
                if (text.startsWith(bismillahAlt)) {
                    return text.replace(bismillahAlt, '');
                }
            }
            return text;
        }
    }
}
</script>
</x-app-layout>
