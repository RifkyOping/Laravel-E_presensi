<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-800">Baca Al-Qur'an</span>
        </div>
    </x-slot>

<div class="space-y-6" x-data="quranApp()">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl px-8 py-7 shadow-xl"
         style="background: linear-gradient(135deg, #2a5298 0%, #1e3a6e 100%);">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-block text-[.65rem] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-2"
                      style="background:rgba(255,255,255,.15);color:#ccfbf1;">30 Juz Lengkap</span>
                <h1 class="text-white text-2xl font-black leading-tight">Membaca Al-Qur'an</h1>
                <p class="text-blue-100/80 text-sm mt-1">Lantunan ayat suci Al-Qur'an tanpa terjemahan.</p>
            </div>
            <div class="hidden sm:block text-right opacity-20 select-none">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"></path>
                </svg>
            </div>
        </div>
        <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
    </div>

    {{-- Tabs --}}
    <div class="flex p-1 bg-slate-100 rounded-2xl w-full sm:max-w-md mx-auto mb-6">
        <button @click="activeTab = 'surah'" 
                class="flex-1 py-2.5 text-sm font-semibold rounded-xl transition-all"
                :class="activeTab === 'surah' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
            Surah
        </button>
        <button @click="activeTab = 'juz'" 
                class="flex-1 py-2.5 text-sm font-semibold rounded-xl transition-all"
                :class="activeTab === 'juz' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
            Juz
        </button>
    </div>

    {{-- Search Bar (Only for Surah) --}}
    <div class="relative" x-show="activeTab === 'surah'">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" x-model="searchQuery" placeholder="Cari nama surah (contoh: Yasin, Al-Baqarah)..."
               class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-200 bg-white text-sm text-slate-700 shadow-sm
                      focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition">
    </div>

    {{-- Loading State --}}
    <div x-show="isLoading" class="py-20 text-center">
        <div class="animate-spin w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full mx-auto mb-4"></div>
        <p class="text-slate-500 font-medium animate-pulse">Memuat data...</p>
    </div>

    {{-- Error State --}}
    <div x-show="hasError" style="display: none;" class="py-12 px-4 text-center bg-red-50 rounded-2xl border border-red-200">
        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-red-600 font-bold">Gagal memuat data Al-Qur'an.</p>
        <p class="text-red-500 text-sm mt-1 mb-4">Pastikan Anda terhubung ke internet dan coba lagi.</p>
        <button @click="fetchSurahs()" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition">Coba Lagi</button>
    </div>

    {{-- Surah Grid --}}
    <div x-show="!isLoading && !hasError && activeTab === 'surah'" style="display: none;" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <template x-for="surah in filteredSurahs" :key="surah.nomor">
            <a :href="`/murid/baca-quran/surah/${surah.nomor}`"
               class="group bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 hover:shadow-lg hover:border-blue-300 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold relative overflow-hidden group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <span x-text="surah.nomor" class="relative z-10"></span>
                    <div class="absolute inset-0 bg-blue-100 opacity-0 group-hover:opacity-20 transition-opacity rotate-45 scale-150"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-700 transition-colors truncate" x-text="surah.namaLatin"></h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5"><span x-text="surah.tempatTurun"></span> · <span x-text="surah.jumlahAyat"></span> Ayat</p>
                </div>
                <div class="text-right flex-shrink-0 text-xl font-bold text-slate-800" style="font-family: 'Traditional Arabic', serif;" x-text="surah.nama"></div>
            </a>
        </template>
        
        {{-- Empty Search Result --}}
        <div x-show="filteredSurahs.length === 0" class="col-span-full py-16 text-center border-2 border-dashed border-slate-200 rounded-2xl">
            <p class="text-slate-400 font-medium">Surah tidak ditemukan.</p>
        </div>
    </div>

    {{-- Juz Grid --}}
    <div x-show="activeTab === 'juz'" style="display: none;" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <template x-for="i in 30" :key="i">
            <a :href="`/murid/baca-quran/juz/${i}`"
               class="group bg-white rounded-2xl border border-slate-200 p-5 flex items-center justify-center hover:shadow-lg hover:border-blue-300 hover:bg-blue-50 transition duration-300">
                <h3 class="font-bold text-slate-700 text-lg group-hover:text-blue-700 transition-colors">Juz <span x-text="i"></span></h3>
            </a>
        </template>
    </div>
</div>

<script>
function quranApp() {
    return {
        activeTab: 'surah',
        surahs: [],
        searchQuery: '',
        isLoading: true,
        hasError: false,

        init() {
            this.fetchSurahs();
        },

        async fetchSurahs() {
            this.isLoading = true;
            this.hasError = false;
            try {
                // Check local storage cache first to save bandwidth
                const cached = localStorage.getItem('quran_surahs');
                if (cached) {
                    this.surahs = JSON.parse(cached);
                    this.isLoading = false;
                    return;
                }

                const response = await fetch('https://equran.id/api/v2/surat');
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                
                if (data.code === 200) {
                    this.surahs = data.data;
                    localStorage.setItem('quran_surahs', JSON.stringify(data.data));
                } else {
                    throw new Error('API returned error');
                }
            } catch (error) {
                console.error('Error fetching surahs:', error);
                this.hasError = true;
            } finally {
                this.isLoading = false;
            }
        },

        get filteredSurahs() {
            if (this.searchQuery === '') return this.surahs;
            const query = this.searchQuery.toLowerCase();
            return this.surahs.filter(s => 
                s.namaLatin.toLowerCase().includes(query) || 
                s.arti.toLowerCase().includes(query)
            );
        }
    }
}
</script>
</x-app-layout>
