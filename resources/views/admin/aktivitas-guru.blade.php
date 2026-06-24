@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Aktivitas Mengajar</span>
    </x-slot>

<div class="space-y-7">

    {{-- ── WELCOME STRIP ── --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-8 py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-1">Monitoring Jurnal Mengajar</p>
                <h1 class="text-white text-2xl font-black leading-tight">Aktivitas Mengajar Guru</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    Daftar lengkap sesi & jurnal mengajar seluruh guru
                    @if(request('tanggal')) · {{ Carbon::parse(request('tanggal'))->translatedFormat('d F Y') }} @endif
                </p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <div class="bg-white/15 rounded-xl px-5 py-3 text-center min-w-[80px]">
                    <p class="text-white text-2xl font-black">{{ $aktivitas->total() }}</p>
                    <p class="text-blue-300 text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Sesi</p>
                </div>
                <div class="bg-white/10 rounded-xl px-5 py-3 text-center min-w-[80px]">
                    <p class="text-white text-2xl font-black">
                        {{ $aktivitas->unique('user_id')->count() }}
                    </p>
                    <p class="text-blue-300/70 text-[.68rem] font-semibold uppercase tracking-wider mt-0.5">Guru</p>
                </div>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- ── FILTER ── --}}
    <form method="GET" action="{{ route('admin.aktivitas-guru') }}"
          class="bg-white rounded-2xl border border-slate-200 p-5 grid grid-cols-1 sm:grid-cols-4 gap-4
                 hover:border-slate-300 transition-all duration-200 shadow-sm">
        <div>
            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal</label>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="app-input">
        </div>
        <div>
            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Filter Guru</label>
            <select name="guru_id" class="app-input">
                <option value="">— Semua Guru —</option>
                @foreach($semuaGuru as $g)
                <option value="{{ $g->id }}" {{ request('guru_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-3 sm:col-span-2">
            <button type="submit"
                    class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm
                           transition duration-200 shadow-sm flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                Terapkan
            </button>
            <a href="{{ route('admin.aktivitas-guru') }}"
               class="px-5 py-2.5 border border-slate-200 hover:border-slate-400 text-slate-600
                      font-semibold text-sm rounded-xl transition duration-200">
                Reset
            </a>
        </div>
    </form>

    {{-- ── TABEL AKTIVITAS ── --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Jurnal Aktivitas Mengajar</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $aktivitas->total() }} sesi ditemukan</p>
            </div>
            <span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600">
                Hal {{ $aktivitas->currentPage() }} / {{ $aktivitas->lastPage() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Guru</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Jam ke-</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Waktu</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Materi</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Metode</th>
                        <th class="py-3.5 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Siswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($aktivitas as $item)
                    <tr class="hover:bg-slate-50/60 transition duration-150">

                        {{-- Tanggal --}}
                        <td class="py-3.5 px-5">
                            <p class="text-sm font-semibold text-slate-700 whitespace-nowrap">
                                {{ Carbon::parse($item->tanggal)->format('d M Y') }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ Carbon::parse($item->tanggal)->translatedFormat('l') }}
                            </p>
                        </td>

                        {{-- Guru --}}
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                                    {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-semibold text-slate-800 whitespace-nowrap">
                                    {{ $item->user->name }}
                                </span>
                            </div>
                        </td>

                        {{-- Mata Pelajaran --}}
                        <td class="py-3.5 px-5 text-sm text-slate-600 whitespace-nowrap">
                            {{ $item->mata_pelajaran }}
                        </td>

                        {{-- Kelas --}}
                        <td class="py-3.5 px-5">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[.7rem] font-bold
                                         bg-blue-50 text-[#1e3a6e] border border-blue-100">
                                {{ $item->kelas }}
                            </span>
                        </td>

                        {{-- Jam ke- --}}
                        <td class="py-3.5 px-5 text-center">
                            <span class="inline-flex w-8 h-8 rounded-full bg-[#1e3a6e] text-white items-center justify-center font-black text-sm">
                                {{ $item->jam_ke }}
                            </span>
                        </td>

                        {{-- Waktu --}}
                        <td class="py-3.5 px-5 text-sm text-slate-600 whitespace-nowrap">
                            <span class="font-semibold text-slate-700">
                                {{ Carbon::parse($item->jam_mulai)->format('H:i') }}
                            </span>
                            @if($item->jam_selesai)
                            <span class="text-slate-400"> – {{ Carbon::parse($item->jam_selesai)->format('H:i') }}</span>
                            @endif
                        </td>

                        {{-- Materi --}}
                        <td class="py-3.5 px-5 max-w-[180px]">
                            <span class="line-clamp-2 block text-sm text-slate-500 leading-snug">
                                {{ $item->materi }}
                            </span>
                        </td>

                        {{-- Metode --}}
                        <td class="py-3.5 px-5">
                            @php
                                $mc = match($item->metode ?? '') {
                                    'daring'  => 'bg-purple-50 text-purple-700 border-purple-100',
                                    'diskusi' => 'bg-sky-50 text-sky-700 border-sky-100',
                                    'praktik' => 'bg-teal-50 text-teal-700 border-teal-100',
                                    default   => 'bg-slate-100 text-slate-600 border-slate-200',
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-1 rounded-full text-[.7rem] font-bold border capitalize {{ $mc }}">
                                {{ $item->metode ?? 'luring' }}
                            </span>
                        </td>

                        {{-- Jumlah Siswa --}}
                        <td class="py-3.5 px-5 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                         bg-slate-100 text-slate-700 font-black text-sm border border-slate-200">
                                {{ $item->jumlah_siswa_hadir }}
                            </span>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <p class="text-slate-400 text-sm font-medium">Belum ada data aktivitas mengajar.</p>
                                <p class="text-slate-300 text-xs">Coba ubah filter atau pilih tanggal yang berbeda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($aktivitas->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $aktivitas->links() }}</div>
        @endif
    </div>

</div>
</x-app-layout>
