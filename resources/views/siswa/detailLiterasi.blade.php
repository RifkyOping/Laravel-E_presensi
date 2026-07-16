<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-[#24417c] leading-tight">
                {{ __('Membaca Buku') }}
            </h2>
            
            <!-- Tombol Kembali -->
            <a href="{{ route('murid.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#24417c] bg-white border-2 border-[#24417c] px-4 py-2 rounded-full hover:bg-[#24417c] hover:text-white transition duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Koleksi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Informasi Buku -->
            <div class="bg-white overflow-hidden shadow-xl shadow-[#24417c]/10 sm:rounded-3xl border-2 border-[#24417c]">
                <div class="p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex-1">
                        <span class="inline-block bg-[#24417c] text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest mb-3">
                            {{-- <!-- Nanti bisa diganti dinamis: {{ $buku->kategori }} --> --}}
                            Pemrograman
                        </span>
                        {{-- <!-- Nanti bisa diganti dinamis: {{ $buku->judul }} --> --}}
                        <h1 class="text-3xl sm:text-4xl font-black text-[#24417c] tracking-tight leading-none mb-2">
                            Dasar-Dasar Laravel & Tailwind CSS
                        </h1>
                        {{-- <!-- Nanti bisa diganti dinamis: {{ $buku->penulis }} --> --}}
                        <p class="text-lg font-medium text-[#24417c]/70">
                            Oleh: Tim IT SMKN 1 Majene
                        </p>
                    </div>
                    
                    <!-- Tombol Aksi Tambahan (Misal: Download) -->
                    <div class="flex-shrink-0 w-full md:w-auto">
                        <a href="#" download class="w-full md:w-auto flex items-center justify-center gap-2 bg-[#24417c] text-white font-bold px-8 py-4 rounded-xl border-2 border-[#24417c] hover:bg-white hover:text-[#24417c] transition duration-300 shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh File PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- PDF Viewer (Area Membaca) -->
            <div class="bg-white p-4 sm:p-6 rounded-3xl shadow-xl shadow-[#24417c]/5 border-2 border-[#24417c]/20">
                
                <!-- Kotak iframe untuk memuat PDF -->
                <!-- Ubah height (h-) jika ingin area bacanya lebih tinggi atau lebih pendek -->
                <div class="w-full h-[70vh] sm:h-[800px] border-2 border-dashed border-[#24417c]/30 rounded-2xl overflow-hidden bg-[#24417c]/5 relative">
                    
                    <!-- 
                        Ganti src="" di bawah ini dengan URL file PDF asli Anda dari database.
                        {{-- Contoh jika pakai storage Laravel: src="{{ asset('storage/' . $buku->file_path) }}"  --}}
                        Tambahan #toolbar=0 di belakang URL berguna untuk menyembunyikan menu bawaan browser agar terlihat lebih rapi.
                    -->
                    <iframe 
                        src="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf#toolbar=0" 
                        class="w-full h-full"
                        title="PDF Viewer"
                    ></iframe>
                    
                    <!-- Fallback Teks jika browser siswa tidak mendukung iFrame PDF -->
                    <div class="absolute inset-0 flex items-center justify-center -z-10 text-center p-6">
                        <div>
                            <svg class="w-16 h-16 text-[#24417c]/40 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="font-bold text-[#24417c]/50 text-lg">Memuat Dokumen Buku...</p>
                            <p class="font-medium text-[#24417c]/40 text-sm mt-1">Jika tidak muncul, silakan gunakan tombol Unduh di atas.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>