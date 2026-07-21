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
            <span class="text-sm font-bold text-slate-800" id="headerSurahName">Surah</span>
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

<div class="max-w-4xl mx-auto pb-10" x-data="quranReaderApp({{ $nomor }})">

    {{-- Loading State --}}
    <div x-show="isLoading" class="py-32 text-center">
        <div class="animate-spin w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full mx-auto mb-4"></div>
        <p class="text-slate-500 font-medium animate-pulse">Memuat Surah...</p>
    </div>

    {{-- Error State --}}
    <div x-show="hasError" style="display: none;" class="py-12 px-4 text-center bg-red-50 rounded-2xl border border-red-200 mt-6">
        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-red-600 font-bold">Gagal memuat surah.</p>
        <button @click="fetchSurah()" class="mt-4 px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition">Coba Lagi</button>
    </div>

    <div x-show="!isLoading && !hasError && surah" style="display: none;" class="space-y-6 mt-4">
        
        {{-- Bismillah Header (Tampil jika bukan surah At-Taubah (9) dan Al-Fatihah (1)) --}}
        <div x-show="surah.nomor !== 9 && surah.nomor !== 1" class="text-center py-8">
            <h2 class="text-3xl sm:text-4xl text-slate-800 arabic-text">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</h2>
        </div>

        {{-- Daftar Ayat --}}
        <div class="bg-white rounded-3xl border border-slate-100 p-8 sm:p-14 shadow-sm relative">
            <div class="text-justify text-[2rem] sm:text-[2.5rem] text-slate-900 arabic-text" dir="rtl" style="line-height: 2.5;">
                <template x-for="ayat in surah.ayat" :key="ayat.nomorAyat">
                    <span class="inline">
                        <span x-text="ayat.teksArab" class="hover:bg-blue-50 transition-colors rounded"></span>
                        <span class="inline-flex items-center justify-center w-10 h-10 mx-2 align-middle relative text-blue-700/80 select-none">
                            <svg class="w-full h-full absolute inset-0" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="16" cy="16" r="14" stroke="currentColor"/>
                                <circle cx="16" cy="16" r="11" stroke="currentColor" stroke-dasharray="2 2" stroke-width="1"/>
                            </svg>
                            <span class="text-[0.65rem] sm:text-xs font-bold pt-0.5 z-10 font-sans" x-text="ayat.nomorAyat"></span>
                        </span>
                    </span>
                </template>
            </div>
        </div>

        {{-- Navigasi Bawah --}}
        <div class="flex items-center justify-between pt-6 border-t border-slate-200 mt-10">
            <a :href="surah.nomor > 1 ? `/murid/baca-quran/${surah.nomor - 1}` : '#'" 
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition"
               :class="surah.nomor > 1 ? 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                &larr; Surah Sebelumnya
            </a>
            
            <a :href="surah.nomor < 114 ? `/murid/baca-quran/${surah.nomor + 1}` : '#'" 
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition"
               :class="surah.nomor < 114 ? 'bg-[#1e3a6e] hover:bg-[#162d57] text-white shadow-md' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                Surah Selanjutnya &rarr;
            </a>
        </div>

    </div>
</div>

<script>
function quranReaderApp(nomor) {
    return {
        nomor: nomor,
        surah: null,
        isLoading: true,
        hasError: false,

        init() {
            this.fetchSurah();
            
            // Simpan posisi scroll sebelum halaman dimuat ulang (refresh)
            window.addEventListener('beforeunload', () => {
                const mainEl = document.querySelector('main');
                if (mainEl) {
                    sessionStorage.setItem(`scroll_quran_surah_${this.nomor}`, mainEl.scrollTop);
                }
            });
        },

        async fetchSurah() {
            this.isLoading = true;
            this.hasError = false;
            try {
                const response = await fetch(`https://equran.id/api/v2/surat/${this.nomor}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                
                if (data.code === 200) {
                    this.surah = data.data;
                    document.getElementById('headerSurahName').textContent = this.surah.namaLatin;
                    document.title = `Surah ${this.surah.namaLatin} - Baca Al-Qur'an`;
                    
                    // Kembalikan posisi scroll setelah DOM selesai dirender
                    setTimeout(() => {
                        const savedScroll = sessionStorage.getItem(`scroll_quran_surah_${this.nomor}`);
                        const mainEl = document.querySelector('main');
                        if (savedScroll && mainEl) {
                            mainEl.scrollTo(0, parseInt(savedScroll, 10));
                        }
                    }, 100);
                } else {
                    throw new Error('API returned error');
                }
            } catch (error) {
                console.error('Error fetching surah:', error);
                this.hasError = true;
            } finally {
                this.isLoading = false;
            }
        }
    }
}
</script>
</x-app-layout>
