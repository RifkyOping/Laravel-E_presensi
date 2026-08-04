<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-800">Catatan Membaca Murid</span>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Header & Filter --}}
        <div x-data="{ 
            showFilter: window.innerWidth >= 768 || localStorage.getItem('filter_guru_literasi_catatan') === 'true' 
        }" 
        x-init="$watch('showFilter', val => localStorage.setItem('filter_guru_literasi_catatan', val))"
        @resize.window="if (window.innerWidth >= 768) showFilter = true"
        class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            
            {{-- Desktop Header (Hidden on Mobile) --}}
            <div class="hidden md:block">
                <h2 class="text-sm font-black text-slate-700 flex items-center gap-2 mb-2">
                    <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                        <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                    </div>
                    Filter Murid
                </h2>
                <p class="text-sm text-slate-500 mb-5 ml-7">Pantau perkembangan bacaan buku digital maupun cetak murid.</p>
            </div>

            {{-- Mobile Button (Visible only on Mobile) --}}
            <button @click="showFilter = !showFilter" type="button" 
                    class="md:hidden w-full flex items-center justify-center gap-2 px-5 py-3 bg-[#1e3a6e] text-white rounded-xl font-bold shadow-[0_4px_12px_rgba(30,58,110,0.2)] hover:bg-[#162d57] active:scale-[0.98] transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                <span x-text="showFilter ? 'Tutup Filter' : 'Tampilkan Filter'"></span>
                <svg class="w-5 h-5 transition-transform duration-300" :class="showFilter ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100" style="display: none;">
                <form id="filter-form" onsubmit="event.preventDefault(); fetchCatatan();" method="GET" action="{{ route('guru.literasi.catatan') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Filter Kelas --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Kelas</label>
                        <select name="kelas_id" onchange="fetchCatatan()"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700
                                       focus:outline-none focus:ring-2 focus:ring-[#1e3a6e]/20 focus:border-[#1e3a6e] transition cursor-pointer">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->tingkat }} {{ $kelas->jurusan }} {{ $kelas->rombel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Jenis Buku --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Jenis Buku</label>
                        <select name="jenis" onchange="fetchCatatan()"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700
                                       focus:outline-none focus:ring-2 focus:ring-[#1e3a6e]/20 focus:border-[#1e3a6e] transition cursor-pointer">
                            <option value="">-- Semua Buku --</option>
                            <option value="digital" {{ request('jenis') == 'digital' ? 'selected' : '' }}>Buku Digital</option>
                            <option value="manual" {{ request('jenis') == 'manual' ? 'selected' : '' }}>Buku Cetak</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Cari Murid</label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" oninput="handleSearch()"
                                       placeholder="Ketik nama Murid..."
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-sm text-slate-700
                                              focus:outline-none focus:ring-2 focus:ring-[#1e3a6e]/20 focus:border-[#1e3a6e] transition">
                            </div>
                            <button type="button" id="reset-btn" onclick="resetFilter()"
                                    style="display: {{ request('kelas_id') || request('jenis') || request('search') ? 'block' : 'none' }}"
                                    class="flex-shrink-0 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition shadow-sm border border-slate-200">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Daftar Catatan --}}
        <div id="catatan-container" class="transition-opacity duration-300">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($catatans as $catatan)
                    <div
                        class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col h-full relative overflow-hidden group">
                        <div class="flex items-start justify-between mb-4 relative z-10">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 flex-shrink-0">
                                    {{ substr($catatan->user->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $catatan->user->name ?? 'Siswa Tidak Ditemukan' }}</p>
                                    <p class="text-[0.65rem] text-slate-400">{{ $catatan->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if($catatan->jenis_buku == 'digital')
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-blue-100 text-blue-800">
                                    Digital
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-emerald-100 text-emerald-800">
                                    Cetak
                                </span>
                            @endif
                        </div>

                        <div class="mb-4 relative z-10 flex-grow">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Judul Buku:</p>
                            <p class="text-sm font-bold text-[#1e3a6e] mb-3 line-clamp-2">{{ $catatan->judul_buku }}</p>

                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Catatan Progres:</p>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-sm text-slate-700 whitespace-pre-wrap">{{ $catatan->catatan }}</div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div
                            class="bg-white rounded-2xl border border-slate-200 p-10 flex flex-col items-center justify-center text-center shadow-sm">
                            <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Catatan</h3>
                            <p class="text-sm text-slate-500 max-w-sm">Saat ini belum ada catatan progres membaca dari siswa.
                                Coba sesuaikan filter pencarian jika Anda mencari data spesifik.</p>
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

    </div>

<script>
    let debounceTimer;
    
    function handleSearch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchCatatan();
        }, 500); // 500ms debounce
    }

    function fetchCatatan() {
        const container = document.getElementById('catatan-container');
        if (container) {
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';
        }

        const form = document.getElementById('filter-form');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        const resetBtn = document.getElementById('reset-btn');
        if (resetBtn) {
            const hasFilter = Array.from(formData.values()).some(val => val.trim() !== '');
            resetBtn.style.display = hasFilter ? 'block' : 'none';
        }
        
        const url = new URL(form.action);
        url.search = params.toString();

        // Update url without reloading
        window.history.pushState({}, '', url);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.getElementById('catatan-container');
                
                if (container && newContainer) {
                    container.innerHTML = newContainer.innerHTML;
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                }
            })
            .catch(err => {
                console.error('Gagal mengambil data', err);
                if (container) {
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                }
            });
    }

    function resetFilter() {
        const form = document.getElementById('filter-form');
        form.querySelectorAll('select, input').forEach(el => el.value = '');
        fetchCatatan();
    }

    // Intercept pagination clicks for AJAX
    document.addEventListener('click', function(e) {
        const link = e.target.closest('#catatan-container .pagination a, #catatan-container nav[role="navigation"] a');
        if (link) {
            e.preventDefault();
            const url = new URL(link.href);
            
            const container = document.getElementById('catatan-container');
            if (container) {
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';
            }

            window.history.pushState({}, '', url);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('catatan-container');
                    
                    if (container && newContainer) {
                        container.innerHTML = newContainer.innerHTML;
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                        window.scrollTo({ top: document.getElementById('filter-form').offsetTop - 20, behavior: 'smooth' });
                    }
                })
                .catch(err => {
                    console.error('Gagal mengambil data pagination', err);
                    if (container) {
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                    }
                });
        }
    });
</script>
</x-app-layout>