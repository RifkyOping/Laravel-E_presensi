<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('ebook.pilih') }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Buku Manual</span>
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
    
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-800">Koleksi Buku Manual</h2>
            <p class="text-sm text-slate-400 mt-0.5">Upload data buku fisik yang telah Anda baca, isi indikatornya untuk menyelesaikan level.</p>
        </div>
    </div>

    {{-- Grid e-Book --}}
    <div class="flex flex-col gap-4">
        @foreach($levels as $item)
        <div class="bg-white rounded-2xl border {{ $item->terbuka ? 'border-slate-200' : 'border-slate-100' }} p-5 flex flex-col sm:flex-row sm:items-center gap-5 transition-all duration-300 {{ $item->terbuka ? 'hover:shadow-lg hover:-translate-y-1' : 'opacity-75' }}">
            
            {{-- Level Badge --}}
            <div class="w-14 h-14 rounded-full flex-shrink-0 flex items-center justify-center font-black shadow-inner border-2
                        {{ ($item->buku && $item->buku->status_selesai) ? 'bg-green-50 border-green-200 text-green-600' : ($item->terbuka ? 'bg-green-50 border-green-200 text-green-700' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                @if($item->buku && $item->buku->status_selesai)
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                @else
                    <span class="text-lg">{{ $item->level }}</span>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    @if(!$item->terbuka)
                        <span class="flex items-center gap-1 text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider bg-slate-100 px-2.5 py-0.5 rounded-md">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Terkunci
                        </span>
                    @endif
                    @if($item->buku && $item->buku->status_selesai)
                        <span class="flex items-center gap-1 text-[0.65rem] font-bold text-green-600 uppercase tracking-wider bg-green-100 px-2.5 py-0.5 rounded-md">
                            Selesai Dibaca
                        </span>
                    @endif
                </div>
                
                @if($item->buku)
                    <h3 class="font-black text-slate-800 text-lg leading-tight">{{ $item->buku->judul }}</h3>
                    <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                        Karya {{ $item->buku->penulis }} • {{ $item->buku->penerbit }} ({{ $item->buku->tahun_terbit }})
                    </p>
                @else
                    <h3 class="font-black text-slate-400 text-lg leading-tight">Belum Ada Buku</h3>
                    <p class="text-sm text-slate-400 mt-1 leading-relaxed">Upload informasi buku untuk level ini.</p>
                @endif
            </div>

            {{-- Action --}}
            <div class="mt-4 sm:mt-0 sm:ml-auto w-full sm:w-auto flex flex-col sm:flex-row gap-2">
                @if($item->buku)
                    <a href="{{ route('ebook.manual.show', $item->buku->id) }}"
                       class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 rounded-xl text-sm font-bold text-[#1e3a6e] bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-all duration-300">
                        Detail Buku
                    </a>
                    
                    @if($item->buku->status_selesai)
                        <!-- Selesai -->
                        <button disabled class="w-full sm:w-auto px-6 py-3 rounded-xl text-sm font-bold bg-green-100 text-green-600 cursor-not-allowed">
                            Selesai
                        </button>
                    @elseif($item->terbuka)
                        <a href="{{ route('ebook.indikator.show', ['jenis' => 'manual', 'id' => $item->buku->id]) }}"
                           class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 rounded-xl text-sm font-bold text-white transition-all duration-300 shadow-md bg-yellow-500 hover:bg-yellow-600 hover:shadow-yellow-500/30">
                            Isi Indikator
                        </a>
                    @else
                        <button disabled class="w-full sm:w-auto px-6 py-3 rounded-xl text-sm font-bold bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200">
                            Selesaikan Level Sebelumnya
                        </button>
                    @endif
                @else
                    <a href="{{ route('ebook.manual.create', $item->level) }}"
                       class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 rounded-xl text-sm font-bold text-white transition-all duration-300 shadow-md bg-[#1e3a6e] hover:bg-[#162d57] hover:shadow-[#1e3a6e]/30">
                        Upload Buku
                    </a>
                @endif
            </div>

        </div>
        @endforeach
    </div>

</div>
</x-app-layout>
