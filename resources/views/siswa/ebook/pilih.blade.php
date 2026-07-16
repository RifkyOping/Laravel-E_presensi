<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('murid.dashboard') }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Pilih Jenis Buku Literasi</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6 text-center">
                <h3 class="text-2xl font-black text-slate-800 mb-8">Pilih Kategori Buku Literasi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <!-- Buku Digital -->
                    <a href="{{ route('ebook.index') }}" class="block group">
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-8 hover:shadow-xl transition duration-300 transform group-hover:-translate-y-2">
                            <div class="w-20 h-20 mx-auto bg-blue-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition duration-300 shadow-md shadow-blue-500/30">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-blue-800 mb-2">Buku Digital</h4>
                            <p class="text-slate-600 font-medium text-sm">Baca e-Book digital yang telah disediakan oleh sekolah dan lengkapi kuisnya.</p>
                        </div>
                    </a>

                    <!-- Buku Manual -->
                    <a href="{{ route('ebook.manual.index') }}" class="block group">
                        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-8 hover:shadow-xl transition duration-300 transform group-hover:-translate-y-2">
                            <div class="w-20 h-20 mx-auto bg-green-500 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition duration-300 shadow-md shadow-green-500/30">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-green-800 mb-2">Buku Manual</h4>
                            <p class="text-slate-600 font-medium text-sm">Upload data buku fisik yang telah Anda baca, isi indikatornya, dan kumpulkan poin membaca.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
