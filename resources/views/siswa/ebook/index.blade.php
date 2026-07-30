<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-800">Buku Digital</span>
        </div>
    </x-slot>

<div class="space-y-6">

    {{-- Alert --}}
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-3 text-sm text-red-700 font-medium flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-800">Buku Digital</h2>
            <p class="text-sm text-slate-400 mt-0.5">Baca secara bertahap. Selesaikan setiap level untuk membuka buku berikutnya.</p>
        </div>
    </div>

    {{-- Info Level --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 sm:p-6 flex items-start gap-3 sm:gap-4 shadow-sm">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#1e3a6e] flex items-center justify-center flex-shrink-0 shadow-md mt-0.5 sm:mt-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="text-sm sm:text-lg font-black text-[#1e3a6e] mb-2 sm:mb-3">Cara Membuka Level Berikutnya</h3>
            <ul class="text-xs sm:text-sm text-blue-800/80 space-y-1.5 sm:space-y-2 list-outside ml-4 sm:ml-5 marker:text-blue-500 font-medium leading-relaxed">
                <li>Baca e-Book yang berstatus <span class="font-bold text-blue-600">terbuka</span>.</li>
                <li>Lakukan <span class="font-bold text-blue-600">Verifikasi Suara</span> dengan skor minimal <span class="font-bold text-green-600">60%</span>.</li>
                <li>Kerjakan <span class="font-bold text-blue-600">Kuis</span> (jika tersedia) dengan nilai minimal <span class="font-bold text-green-600">60</span>.</li>
                <li>Isi form <span class="font-bold text-blue-600">Indikator Pemahaman</span> yang telah disediakan.</li>
                <li>Level selanjutnya otomatis <strong class="text-blue-700">terbuka</strong> setelah syarat terpenuhi!</li>
            </ul>
        </div>
    </div>

    {{-- Grid e-Book --}}
    @if($ebooks->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 py-16 text-center">
        <svg class="w-14 h-14 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="font-bold text-slate-400">Belum ada e-Book tersedia.</p>
    </div>
    @else
    <div class="flex flex-col gap-4">
        @foreach($ebooks as $buku)
        <div class="bg-white rounded-2xl border {{ $buku->terbuka ? 'border-slate-200' : 'border-slate-100' }} p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5 transition-all duration-300 {{ $buku->terbuka ? 'hover:shadow-lg hover:-translate-y-1 hover:border-blue-300' : 'opacity-75' }}">
            
            <div class="flex items-start sm:items-center gap-3 sm:gap-4 w-full sm:w-auto flex-1">
                {{-- Level Badge --}}
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex-shrink-0 flex items-center justify-center font-black shadow-inner border-2
                            {{ $buku->sudah_selesai ? 'bg-green-50 border-green-200 text-green-600' : ($buku->terbuka ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                    @if($buku->sudah_selesai)
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <span class="text-base sm:text-lg">{{ $buku->level }}</span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        @if(!$buku->terbuka)
                            <span class="flex items-center gap-1 text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded-md">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Terkunci
                            </span>
                        @endif
                        @if($buku->sudah_selesai)
                            <span class="flex items-center gap-1 text-[0.65rem] font-bold text-green-600 uppercase tracking-wider bg-green-100 px-2 py-0.5 rounded-md">
                                Selesai Dibaca
                            </span>
                        @endif
                    </div>
                    <h3 class="font-black text-slate-800 text-base sm:text-lg leading-tight">{{ $buku->judul }}</h3>
                    <p class="text-xs sm:text-sm text-slate-500 line-clamp-2 mt-1 leading-relaxed">{{ $buku->deskripsi }}</p>
                </div>
            </div>

            {{-- Action --}}
            <div class="w-full sm:w-auto">
                @if($buku->terbuka)
                    <a href="{{ route('ebook.read', $buku->id) }}"
                       class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 rounded-xl text-sm font-bold text-white transition-all duration-300 shadow-md
                              {{ $buku->sudah_selesai ? 'bg-green-600 hover:bg-green-700 hover:shadow-green-600/30' : 'bg-[#1e3a6e] hover:bg-[#162d57] hover:shadow-[#1e3a6e]/30' }}">
                        {{ $buku->sudah_selesai ? 'Baca Ulang' : 'Mulai Membaca' }}
                    </a>
                @else
                    <button disabled class="w-full sm:w-auto px-6 py-3 rounded-xl text-sm font-bold bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200">
                        Level Belum Terbuka
                    </button>
                @endif
            </div>

        </div>
        @endforeach
    </div>
    @endif

</div>
</x-app-layout>
