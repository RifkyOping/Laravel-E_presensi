<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('ebook.index') }}" class="text-slate-400 hover:text-emerald-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('ebook.index') }}" class="text-slate-400 hover:text-emerald-600 text-sm transition-colors">Literasi</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Literasi Keagamaan Saya</span>
        </div>
    </x-slot>

<div class="space-y-6">

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-2xl px-5 py-5 sm:px-8 sm:py-7 shadow-xl"
         style="background: linear-gradient(135deg, #1e3a6e 0%, #2d5299 60%, #162d57 100%);">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-block text-[.65rem] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-2"
                      style="background:rgba(255,255,255,.15);color:#bfdbfe;">Literasi Keagamaan Saya</span>
                <h1 class="text-white text-2xl font-black leading-tight">{{ $siswa->name }}</h1>
                <div class="flex flex-wrap gap-2 mt-2">
                    @if($siswa->kelas)
                    <span class="text-xs font-bold bg-white/20 text-white px-3 py-1 rounded-full">Kelas {{ $siswa->kelas }}</span>
                    @endif
                    @if($siswa->jurusan)
                    <span class="text-xs font-bold bg-white/20 text-white px-3 py-1 rounded-full">{{ $siswa->jurusan }}</span>
                    @endif
                </div>
            </div>
            <div class="bg-white/15 rounded-2xl px-6 py-4 text-center flex-shrink-0">
                <p class="text-blue-200 text-xs font-black uppercase tracking-widest">Total Catatan</p>
                <p class="text-white text-4xl font-black mt-1">{{ $totalCatatan }}</p>
                <p class="text-blue-300 text-xs mt-0.5">dari guru</p>
            </div>
        </div>
        <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-20 -bottom-10 w-36 h-36 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute bottom-3 left-8 text-white/10 select-none">
            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </div>
    </div>

    @if($catatan->isEmpty())
    {{-- Kosong --}}
    <div class="bg-white rounded-2xl border border-slate-200 py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="font-bold text-slate-500 text-lg">Belum ada catatan dari guru</p>
        <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">
            Catatan literasi keagamaan akan muncul di sini setelah guru mencatatnya untuk Anda.
        </p>
    </div>
    @else

    {{-- List Catatan --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-[#1e3a6e] flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-black text-slate-800">Riwayat Catatan</h3>
                    <p class="text-xs text-slate-500 font-medium">Menampilkan catatan terbaru</p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($catatan as $item)
            <div class="px-6 py-5 flex items-start gap-4 hover:bg-slate-50/50 transition duration-200">
                <div class="w-2 h-2 rounded-full bg-[#1e3a6e] mt-2 flex-shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $item->catatan }}</p>
                    <div class="flex items-center gap-3 mt-3">
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full bg-[#1e3a6e] flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="text-white text-[8px] font-black">
                                    {{ strtoupper(substr($item->guru->name ?? 'G', 0, 1)) }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-600 font-semibold">{{ $item->guru->name ?? 'Guru' }}</span>
                        </div>
                        <span class="text-slate-300">·</span>
                        <span class="text-xs text-slate-400 font-medium">
                            {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                        @if($item->updated_at->gt($item->created_at->addMinute()))
                        <span class="text-slate-300">·</span>
                        <span class="text-[.65rem] text-slate-400 italic">diperbarui</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Info --}}
    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
        <svg class="w-4 h-4 text-[#1e3a6e] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-xs text-[#1e3a6e]/80 font-medium leading-relaxed">
            Catatan di halaman ini dibuat oleh guru pembimbing literasi keagamaan Anda. Untuk pertanyaan mengenai catatan, silakan konsultasikan langsung dengan guru yang bersangkutan.
        </p>
    </div>

</div>
</x-app-layout>
