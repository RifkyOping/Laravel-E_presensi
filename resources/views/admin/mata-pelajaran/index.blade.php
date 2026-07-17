<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Mata Pelajaran</span>
    </x-slot>

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-800">Manajemen Mata Pelajaran</h2>
            <p class="text-sm text-slate-400 mt-0.5">Kelola daftar mata pelajaran dan status keaktifannya</p>
        </div>
        <a href="{{ route('admin.mata-pelajaran.create') }}"
           class="inline-flex items-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Mata Pelajaran
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-3 gap-4">
        @php $statmp = [
            ['label'=>'Total Mapel',   'value'=>$stats['total'],    'accent'=>'#1e3a6e', 'sub'=>'tersedia'],
            ['label'=>'Mapel Aktif',   'value'=>$stats['aktif'],    'accent'=>'#166534', 'sub'=>'aktif'],
            ['label'=>'Mapel Nonaktif','value'=>$stats['nonaktif'], 'accent'=>'#64748b', 'sub'=>'dinonaktifkan'],
        ]; @endphp
        @foreach($statmp as $s)
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3 hover:-translate-y-1 hover:shadow-md transition-all duration-200">
            <div class="w-8 h-1 rounded-full" style="background:{{ $s['accent'] }}"></div>
            <p class="text-3xl font-black" style="color:{{ $s['accent'] }}">{{ $s['value'] }}</p>
            <div>
                <p class="text-[.8rem] font-semibold text-slate-700 leading-tight">{{ $s['label'] }}</p>
                <p class="text-[.68rem] text-slate-400 uppercase tracking-wide font-semibold">{{ $s['sub'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div x-data="{ showFilter: {{ request()->hasAny(['search','status']) ? 'true' : 'false' }} }" class="bg-white rounded-xl border border-slate-200 p-6">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter & Pencarian Mata Pelajaran</h2>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk mencari berdasarkan nama atau status</p>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100" style="display: none;">
            <form method="GET" action="{{ route('admin.mata-pelajaran.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama mata pelajaran..."
                       class="flex-1 border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">

                <select name="status"
                        class="border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                    <option value="">Status</option>
                    <option value="aktif"    {{ request('status')==='aktif'    ?'selected':'' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status')==='nonaktif' ?'selected':'' }}>Nonaktif</option>
                </select>
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition flex items-center gap-2 shadow-md shadow-[#1e3a6e]/20 w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </button>
                    @if(request()->hasAny(['search','status']))
                    <a href="{{ route('admin.mata-pelajaran.index') }}"
                       class="px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm rounded-xl transition text-center w-full sm:w-auto flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <p class="text-sm text-slate-500">
                Menampilkan <span class="font-bold text-slate-800">{{ $mapel->total() }}</span> mata pelajaran
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">No</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Nama Mata Pelajaran</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mapel as $i => $mp)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="py-3.5 px-5 text-sm text-slate-400 font-semibold">{{ $mapel->firstItem() + $i }}</td>
                        <td class="py-3.5 px-5">
                            <p class="font-semibold text-slate-800 text-sm">{{ $mp->nama }}</p>
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            <form method="POST" action="{{ route('admin.mata-pelajaran.toggle', $mp->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="inline-block px-3 py-1.5 rounded-lg text-[.7rem] font-bold border transition duration-200
                                               {{ $mp->aktif
                                                  ? 'bg-green-50 text-green-700 border-green-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200'
                                                  : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200' }}">
                                    {{ $mp->aktif ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="py-3.5 px-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.mata-pelajaran.edit', $mp->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('admin.mata-pelajaran.destroy', $mp->id) }}"
                                      onsubmit="return confirm('Hapus mata pelajaran {{ $mp->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-400 text-sm">
                            Belum ada mata pelajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mapel->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $mapel->links() }}</div>
        @endif
    </div>

</div>
</x-app-layout>
