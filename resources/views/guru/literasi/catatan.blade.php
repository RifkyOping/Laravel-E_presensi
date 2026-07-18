<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Catatan Progres Membaca Siswa</span>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Header & Filter --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
            <div>
                <h1 class="text-xl font-black text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Catatan Progres Membaca
                </h1>
                <p class="text-sm text-slate-500 mt-1">Pantau perkembangan bacaan buku digital maupun manual murid.</p>
            </div>
            
            <form method="GET" action="{{ route('guru.literasi.catatan') }}" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                {{-- Filter Kelas --}}
                <select name="kelas_id" class="border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->tingkat }} {{ $kelas->jurusan }} {{ $kelas->rombel }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter Jenis Buku --}}
                <select name="jenis" class="border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                    <option value="">Semua Jenis Buku</option>
                    <option value="digital" {{ request('jenis') == 'digital' ? 'selected' : '' }}>Buku Digital</option>
                    <option value="manual" {{ request('jenis') == 'manual' ? 'selected' : '' }}>Buku Manual</option>
                </select>
                <div class="relative flex-1 sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." 
                           class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl pl-10 pr-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-[#1e3a6e] hover:bg-[#162d57] transition flex items-center justify-center shadow-sm">
                    Cari
                </button>
            </form>
        </div>

        {{-- Daftar Catatan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($catatans as $catatan)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col h-full relative overflow-hidden group">
                <div class="flex items-start justify-between mb-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 flex-shrink-0">
                            {{ substr($catatan->user->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $catatan->user->name ?? 'Siswa Tidak Ditemukan' }}</p>
                            <p class="text-[0.65rem] text-slate-400">{{ $catatan->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if($catatan->jenis_buku == 'digital')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-blue-100 text-blue-800">
                            Digital
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-emerald-100 text-emerald-800">
                            Manual
                        </span>
                    @endif
                </div>

                <div class="mb-4 relative z-10 flex-grow">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Judul Buku:</p>
                    <p class="text-sm font-bold text-[#1e3a6e] mb-3 line-clamp-2">{{ $catatan->judul_buku }}</p>
                    
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Catatan Progres:</p>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-sm text-slate-700 whitespace-pre-wrap flex-grow">{{ $catatan->catatan }}</div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-white rounded-2xl border border-slate-200 p-10 flex flex-col items-center justify-center text-center shadow-sm">
                    <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Catatan</h3>
                    <p class="text-sm text-slate-500 max-w-sm">Saat ini belum ada catatan progres membaca dari siswa. Coba sesuaikan filter pencarian jika Anda mencari data spesifik.</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($catatans->hasPages())
        <div class="mt-6">
            {{ $catatans->links() }}
        </div>
        @endif

    </div>
</x-app-layout>
