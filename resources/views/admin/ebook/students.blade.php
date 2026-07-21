<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.ebook.index') }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('admin.ebook.index') }}" class="text-slate-400 hover:text-[#1e3a6e] text-sm transition-colors">Manajemen e-Book</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Akses Suara Murid</span>
        </div>
    </x-slot>

<div class="space-y-6">

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-800">Kelola Akses Suara Murid</h2>
            <p class="text-sm text-slate-400 mt-0.5">Matikan wajib verifikasi suara bagi murid yang memiliki kendala perangkat.</p>
        </div>
    </div>

    {{-- Filter + Tabs --}}
    <div x-data="{ showFilter: {{ request('search') || request('tab', 'semua') !== 'semua' ? 'true' : 'false' }} }" class="bg-white rounded-xl border border-slate-200 p-6">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter & Pencarian Murid</h2>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk menyesuaikan pencarian dan status</p>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100 space-y-4" style="display: none;">
            {{-- Tabs --}}
            <div class="flex flex-wrap gap-2">
                @foreach(['semua'=>'Semua','wajib'=>'Wajib Verifikasi','bypass'=>'Bypass Aktif'] as $key=>$label)
                <a href="{{ route('admin.ebook.students', ['tab'=>$key,'search'=>request('search')]) }}"
                   class="px-4 py-1.5 rounded-lg font-semibold text-sm border transition duration-200
                          {{ $tab===$key ? 'bg-[#1e3a6e] text-white border-[#1e3a6e]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-[#1e3a6e] hover:text-[#1e3a6e]' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
            {{-- Search --}}
            <form method="GET" action="{{ route('admin.ebook.students') }}" class="flex gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, NIS, atau email..."
                       class="flex-1 border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                <button type="submit"
                        class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition flex items-center gap-2 shadow-md shadow-[#1e3a6e]/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
                @if(request('search'))
                <a href="{{ route('admin.ebook.students', ['tab'=>$tab]) }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">Nama Murid</th>
                        <th class="text-left px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">Nomor Induk</th>
                        <th class="text-center px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">Verifikasi Suara</th>
                        <th class="text-right px-5 py-3.5 text-xs font-black text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($students as $siswa)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-800">{{ $siswa->name }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-slate-600 font-medium">{{ $siswa->nomor_induk ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($siswa->skip_voice_verification)
                                <span class="inline-flex items-center gap-1 text-[0.65rem] font-black px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 uppercase tracking-wide">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Bypass Aktif (Tidak Wajib)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[0.65rem] font-black px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 uppercase tracking-wide">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                    Wajib Verifikasi
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <form method="POST" action="{{ route('admin.ebook.students.toggle', $siswa->id) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-semibold text-xs transition duration-200
                                               {{ $siswa->skip_voice_verification 
                                                    ? 'border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white' 
                                                    : 'border-amber-200 text-amber-600 hover:bg-amber-500 hover:text-white' }}">
                                    @if($siswa->skip_voice_verification)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                        Wajibkan Suara
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Matikan Verifikasi
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center">
                            <p class="font-bold text-slate-400">Belum ada data murid.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $students->links() }}
        </div>
        @endif
    </div>

</div>
</x-app-layout>
