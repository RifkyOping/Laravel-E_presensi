<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Daftar Kelas</span>
    </x-slot>

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-800">Manajemen Kelas</h2>
            <p class="text-sm text-slate-400 mt-0.5">Kelola daftar kelas dan status keaktifannya agar penulisan kelas seragam</p>
        </div>
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

    <div class="flex flex-col lg:flex-row gap-6">
        
        {{-- Form Tambah Kelas --}}
        <div class="lg:w-1/3">
            <div class="bg-white rounded-xl border border-slate-200 p-6 sticky top-24">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Kelas Baru</h3>
                <form action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tingkat</label>
                        <select name="tingkat" required class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                            <option value="">Pilih Tingkat</option>
                            <option value="X">X (10)</option>
                            <option value="XI">XI (11)</option>
                            <option value="XII">XII (12)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Jurusan</label>
                        <input type="text" name="jurusan" required placeholder="Contoh: RPL, TKJ, AKL" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-[#1e3a6e] uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Rombel / Kelas</label>
                        <input type="text" name="rombel" required placeholder="Contoh: 1, 2, A, B" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-[#1e3a6e] uppercase">
                    </div>
                    <button type="submit" class="w-full bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-4 py-3 rounded-lg text-sm transition">
                        Simpan Kelas
                    </button>
                </form>
            </div>
        </div>

        {{-- Tabel Daftar Kelas --}}
        <div class="lg:w-2/3 space-y-4">
            
            {{-- Filter --}}
            <form method="GET" action="{{ route('admin.kelas.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari jurusan atau rombel..." class="flex-1 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-[#1e3a6e]">
                <select name="tingkat" class="border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-[#1e3a6e]">
                    <option value="">Semua Tingkat</option>
                    <option value="X" {{ request('tingkat')==='X'?'selected':'' }}>X</option>
                    <option value="XI" {{ request('tingkat')==='XI'?'selected':'' }}>XI</option>
                    <option value="XII" {{ request('tingkat')==='XII'?'selected':'' }}>XII</option>
                </select>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-4 py-2 rounded-xl text-sm transition">
                    Cari
                </button>
                @if(request()->hasAny(['search','tingkat']))
                    <a href="{{ route('admin.kelas.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 font-semibold text-sm rounded-xl hover:bg-slate-50 transition text-center">Reset</a>
                @endif
            </form>

            {{-- Tabel --}}
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <p class="text-sm text-slate-500">Total: <span class="font-bold text-slate-800">{{ $kelas->total() }}</span> Kelas</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70">
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">No</th>
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Nama Lengkap</th>
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Tingkat</th>
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Jurusan</th>
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($kelas as $i => $k)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-5 text-sm font-semibold text-slate-400">{{ $kelas->firstItem() + $i }}</td>
                                <td class="py-3 px-5 font-bold text-slate-800 text-sm">{{ $k->nama_lengkap }}</td>
                                <td class="py-3 px-5 text-sm">{{ $k->tingkat }}</td>
                                <td class="py-3 px-5 text-sm font-medium">{{ $k->jurusan }}</td>
                                <td class="py-3 px-5 text-center">
                                    <form method="POST" action="{{ route('admin.kelas.toggle', $k->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="inline-block px-3 py-1 rounded-md text-xs font-bold border transition
                                                       {{ $k->status 
                                                          ? 'bg-green-50 text-green-700 border-green-200' 
                                                          : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                            {{ $k->status ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <form method="POST" action="{{ route('admin.kelas.destroy', $k->id) }}" onsubmit="return confirm('Hapus kelas {{ $k->nama_lengkap }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">Belum ada data kelas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($kelas->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">{{ $kelas->links() }}</div>
                @endif
            </div>

        </div>
    </div>

</div>
</x-app-layout>
