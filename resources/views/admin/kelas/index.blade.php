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

    <div class="flex flex-col gap-6">
        
        {{-- Form Tambah Kelas --}}
        <div class="w-full">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Kelas Baru</h3>
                <form action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tingkat</label>
                            <select name="tingkat" required class="w-full border border-slate-200 rounded-lg pl-4 pr-10 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                <option value="">Pilih Tingkat</option>
                                <option value="X">X (10)</option>
                                <option value="XI">XI (11)</option>
                                <option value="XII">XII (12)</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Jurusan</label>
                            <input type="text" name="jurusan" required placeholder="Contoh: RPL, TKJ, AKL" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-[#1e3a6e] uppercase">
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4 md:items-end">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Rombel / Kelas</label>
                            <input type="text" name="rombel" required placeholder="Contoh: 1, 2, A, B" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-[#1e3a6e] uppercase">
                        </div>
                        <div class="flex-1">
                            <button type="submit" class="w-full bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-4 py-2.5 rounded-lg text-sm transition h-[42px]">
                                Simpan Kelas
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Daftar Kelas --}}
        <div class="w-full space-y-4">
            
            {{-- Filter --}}
            <div x-data="{ 
                showFilter: localStorage.getItem('filter_admin_kelas') === 'true' || {{ request()->hasAny(['search','tingkat']) ? 'true' : 'false' }} 
            }" 
            x-init="$watch('showFilter', val => localStorage.setItem('filter_admin_kelas', val))"
            class="bg-white rounded-xl border border-slate-200 p-5">
                <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                            <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-700">Filter & Pencarian Kelas</h2>
                            <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk mencari jurusan, rombel, atau tingkat</p>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                <div x-show="showFilter" x-transition class="mt-4 pt-4 border-t border-slate-100" style="display: none;">
                    <form method="GET" action="{{ route('admin.kelas.index') }}" class="flex flex-col sm:flex-row gap-3" id="searchForm">
                        <div class="flex flex-row gap-2 sm:gap-3 flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari jurusan atau rombel..." oninput="handleSearchInput(this)" class="w-full min-w-0 flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#1e3a6e]">
                            <select name="tingkat" onchange="performLiveSearch(this.form)" class="border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-sm focus:border-[#1e3a6e] shrink-0">
                                <option value="">Semua Tingkat</option>
                                <option value="X" {{ request('tingkat')==='X'?'selected':'' }}>X</option>
                                <option value="XI" {{ request('tingkat')==='XI'?'selected':'' }}>XI</option>
                                <option value="XII" {{ request('tingkat')==='XII'?'selected':'' }}>XII</option>
                            </select>
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            @if(request()->hasAny(['search','tingkat']))
                                <a href="{{ route('admin.kelas.index') }}" class="w-full sm:w-auto px-5 py-2.5 border border-slate-200 text-slate-600 font-semibold text-sm rounded-xl hover:bg-slate-50 transition text-center flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            <div id="table-container" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <p class="text-sm text-slate-500">Total: <span class="font-bold text-slate-800">{{ $kelas->total() }}</span> Kelas</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70">
                                <th class="hidden md:table-cell py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">No</th>
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Lengkap</th>
                                <th class="hidden md:table-cell py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Tingkat</th>
                                <th class="hidden md:table-cell py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Jurusan</th>
                                <th class="hidden md:table-cell py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Rombel</th>
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                                <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($kelas as $i => $k)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="hidden md:table-cell py-3.5 px-5 text-sm font-semibold text-slate-400">
                                    {{ $kelas->firstItem() + $i }}
                                </td>
                                <td class="py-3.5 px-5 font-bold text-slate-800 text-sm">
                                    {{ $k->nama_lengkap }}
                                </td>
                                <td class="hidden md:table-cell py-3.5 px-5 text-sm text-center">
                                    {{ $k->tingkat }}
                                </td>
                                <td class="hidden md:table-cell py-3.5 px-5 text-sm font-medium text-center">
                                    {{ $k->jurusan }}
                                </td>
                                <td class="hidden md:table-cell py-3.5 px-5 text-sm font-bold text-slate-700 text-center">
                                    {{ $k->rombel }}
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    <form method="POST" action="{{ route('admin.kelas.toggle', $k->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="inline-block px-3 py-1.5 rounded-lg text-[.7rem] font-bold border transition
                                                       {{ $k->status 
                                                          ? 'bg-green-50 text-green-700 border-green-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200' 
                                                          : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200' }}">
                                            {{ $k->status ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-kelas-{{ $k->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span class="hidden md:inline">Edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('admin.kelas.destroy', $k->id) }}" onsubmit="return confirm('Hapus kelas {{ $k->nama_lengkap }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span class="hidden md:inline">Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">Belum ada data kelas.</td>
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

<script>
    let searchTimeout;
    function handleSearchInput(input) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performLiveSearch(input.form);
        }, 500); // Tunggu 500ms
    }

    async function performLiveSearch(source) {
        let url;
        if (typeof source === 'string') {
            url = new URL(source, window.location.origin);
        } else if (source instanceof HTMLFormElement) {
            url = new URL(source.action || window.location.href, window.location.origin);
            const formData = new FormData(source);
            url.search = '';
            formData.forEach((value, key) => {
                if(value) url.searchParams.set(key, value);
            });
        } else {
            url = new URL(window.location.href);
        }

        const tableContainer = document.getElementById('table-container');
        if (!tableContainer) return;
        tableContainer.style.opacity = '0.5';
        tableContainer.style.pointerEvents = 'none';

        try {
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('table-container');
            
            if(newTable) {
                tableContainer.innerHTML = newTable.innerHTML;
            }
            
            window.history.pushState({}, '', url.toString());
        } catch (error) {
            console.error('Error saat live search:', error);
        } finally {
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';
        }
    }

    document.addEventListener('click', function(e) {
        const form = document.getElementById('searchForm');
        
        // Handle klik link pagination
        const paginationLink = e.target.closest('#table-container nav a');
        if(paginationLink) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            
            if (form) {
                const formData = new FormData(form);
                formData.forEach((value, key) => {
                    if(value) url.searchParams.set(key, value);
                });
            }

            performLiveSearch(url.toString());
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        const searchForm = document.getElementById('searchForm');
        if(searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performLiveSearch(this);
            });
        }
    });
</script>

@foreach($kelas as $k)
<div x-data="{ show: false }"
     x-show="show"
     x-on:open-modal.window="$event.detail == 'edit-kelas-{{ $k->id }}' ? show = true : null"
     x-on:close-modal.window="show = false"
     x-on:keydown.escape.window="show = false"
     style="display: none;"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    
    <div x-show="show"
         x-on:click.outside="show = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col max-h-[90vh]">
        
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-800 text-lg">Edit Kelas {{ $k->nama_lengkap }}</h3>
            <button type="button" x-on:click="show = false" class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kelas.update', $k->id) }}" class="p-6 overflow-y-auto">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Tingkat</label>
                    <select name="tingkat" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-1 focus:ring-[#1e3a6e] text-slate-700 bg-white">
                        <option value="X" {{ $k->tingkat == 'X' ? 'selected' : '' }}>X (10)</option>
                        <option value="XI" {{ $k->tingkat == 'XI' ? 'selected' : '' }}>XI (11)</option>
                        <option value="XII" {{ $k->tingkat == 'XII' ? 'selected' : '' }}>XII (12)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Jurusan</label>
                    <input type="text" name="jurusan" value="{{ $k->jurusan }}" required placeholder="Contoh: RPL, TKJ, AKL" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-1 focus:ring-[#1e3a6e] uppercase text-slate-700 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">Rombel / Kelas</label>
                    <input type="text" name="rombel" value="{{ $k->rombel }}" required placeholder="Contoh: 1, 2, A, B" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-[#1e3a6e] focus:ring-1 focus:ring-[#1e3a6e] uppercase text-slate-700 bg-white">
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="show = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold rounded-xl text-sm transition shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

</x-app-layout>
