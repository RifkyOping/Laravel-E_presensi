@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Verifikasi Aktivitas Mengajar</span>
    </x-slot>

<div class="-mt-2 sm:mt-0 space-y-3 md:space-y-6">

    {{-- Info Banner --}}
    <div class="alert-info animate-up flex items-start gap-2 mt-2 sm:mt-0">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>
            Klik tombol <strong>Verifikasi</strong> untuk mengambil/upload foto guru yang sedang mengajar dan memberikan catatan.
        </span>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
    <div class="alert-success animate-up">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter --}}
    @php
        $hasFilter = request()->hasAny(['tanggal','guru_id','status_verif']);
    @endphp
    <div x-data="{ 
        showFilter: window.innerWidth >= 768 || localStorage.getItem('filter_piket_mengajar') === 'true' || {{ $hasFilter ? 'true' : 'false' }} 
    }" 
    x-init="$watch('showFilter', val => localStorage.setItem('filter_piket_mengajar', val))"
    @resize.window="if (window.innerWidth >= 768) showFilter = true"
    class="app-card p-1.5 sm:p-6 animate-up">
        {{-- Desktop Header (Hidden on Mobile) --}}
        <div class="hidden md:block">
            <h2 class="text-sm font-black text-slate-700 flex items-center gap-2 mb-2">
                <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                    <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                Filter Jurnal Mengajar
            </h2>
            <p class="text-sm text-slate-500 mb-5 ml-7">Klik untuk menyesuaikan pencarian jurnal mengajar.</p>
        </div>

        {{-- Mobile Button (Visible only on Mobile) --}}
        <button @click="showFilter = !showFilter" type="button" 
                class="md:hidden -mt-2 w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-[#1e3a6e] text-white rounded-xl font-bold shadow-[0_4px_12px_rgba(30,58,110,0.2)] hover:bg-[#162d57] active:scale-[0.98] transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <span x-text="showFilter ? 'Tutup Filter' : 'Tampilkan Filter'" class="text-sm"></span>
            <svg class="w-4 h-4 transition-transform duration-300" :class="showFilter ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="showFilter" x-transition class="mt-4 pt-4 sm:mt-5 sm:pt-5 border-t border-slate-100" style="display: none;">
            <form id="filter-form" onsubmit="event.preventDefault(); fetchFilteredData();" method="GET" action="{{ route('piket.mengajar.index') }}" class="flex flex-row flex-wrap items-end gap-2 sm:gap-4 w-full">
                <div class="w-[calc(50%-0.25rem)] sm:flex-1 sm:w-auto min-w-0">
                    <label class="block text-[10px] sm:text-xs font-black text-slate-500 uppercase tracking-wider mb-1 sm:mb-2 truncate">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="fetchFilteredData()" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-2 sm:px-4 py-2 sm:py-2.5 text-slate-800 font-medium focus:outline-none transition text-xs sm:text-sm bg-white">
                </div>
                <div class="w-[calc(50%-0.25rem)] sm:flex-1 sm:w-auto min-w-0">
                    <label class="block text-[10px] sm:text-xs font-black text-slate-500 uppercase tracking-wider mb-1 sm:mb-2 truncate">Guru</label>
                    <select name="guru_id" onchange="fetchFilteredData()" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-2 sm:px-4 py-2 sm:py-2.5 text-slate-800 font-medium focus:outline-none transition text-xs sm:text-sm bg-white">
                        <option value="">Semua Guru</option>
                        @foreach($semuaGuru as $g)
                        <option value="{{ $g->id }}" {{ request('guru_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 sm:flex-1 min-w-0">
                    <label class="block text-[10px] sm:text-xs font-black text-slate-500 uppercase tracking-wider mb-1 sm:mb-2 truncate">Status</label>
                    <select name="status_verif" onchange="fetchFilteredData()" class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-2 sm:px-4 py-2 sm:py-2.5 text-slate-800 font-medium focus:outline-none transition text-xs sm:text-sm bg-white">
                        <option value="">Semua</option>
                        <option value="belum" {{ request('status_verif')==='belum'?'selected':'' }}>Belum Diverifikasi</option>
                        <option value="mengajar" {{ request('status_verif')==='mengajar'?'selected':'' }}>Terv. Mengajar</option>
                        <option value="tidak_mengajar" {{ request('status_verif')==='tidak_mengajar'?'selected':'' }}>Terv. Tidak</option>
                    </select>
                </div>
                <div class="flex items-end flex-shrink-0" id="reset-btn-container" style="display: {{ $hasFilter ? 'block' : 'none' }}">
                    <button type="button" onclick="resetFilter()" class="px-3 sm:px-4 py-2 sm:py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs sm:text-sm rounded-xl transition shadow-sm border border-slate-200 h-[34px] sm:h-[42px] flex items-center justify-center">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div id="data-container" class="app-card overflow-hidden animate-up delay-2 transition-opacity duration-300">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Aktivitas Mengajar</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $aktivitas->total() }} sesi ditemukan</p>
            </div>
            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-1.5 sm:gap-2">
                <span class="app-badge b-teal text-[10px] sm:text-xs">
                    {{ $aktivitas->filter(fn($a) => $a->verified_at)->count() }} diverifikasi
                </span>
                <span class="app-badge b-red text-[10px] sm:text-xs">
                    {{ $aktivitas->filter(fn($a) => !$a->verified_at)->count() }} belum
                </span>
            </div>
        </div>
        {{-- Mobile: Table View --}}
        <div class="block sm:hidden bg-white">
            <div class="flex flex-row items-center gap-2 px-4 py-3 bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-wider text-center">
                <div class="flex-1 text-left min-w-0">Guru & Kls</div>
                <div class="w-[60px] flex-shrink-0 leading-tight">Status</div>
                <div class="w-20 flex-shrink-0 leading-tight">Aksi</div>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($aktivitas as $item)
                <div class="flex flex-row items-center gap-2 px-4 py-3 hover:bg-slate-50/50 transition">
                    <div class="flex-1 min-w-0 pr-1">
                        <div class="font-bold text-slate-800 text-xs sm:text-sm leading-tight truncate flex items-center gap-1">
                            {{ $item->user->name }}
                            @if($item->foto_verifikasi)
                            <svg class="w-3.5 h-3.5 text-blue-500 cursor-pointer flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" onclick="showPhotoModal('{{ Storage::url($item->foto_verifikasi) }}', '{{ addslashes($item->user->name) }}')"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @endif
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5 truncate">{{ $item->kelas }} · Jam {{ $item->jam_ke }}</div>
                    </div>
                    <div class="w-[60px] flex-shrink-0 flex justify-center">
                        @if($item->verified_at)
                            @if($item->status_verifikasi === 'mengajar')
                                <span class="app-badge b-teal text-[9px] px-1.5 py-0.5 leading-none">Ada</span>
                            @elseif($item->status_verifikasi === 'tidak_mengajar')
                                <span class="app-badge b-red text-[9px] px-1.5 py-0.5 leading-none">Tidak</span>
                            @else
                                <span class="app-badge b-teal text-[9px] px-1.5 py-0.5 leading-none">Verif</span>
                            @endif
                        @else
                            <span class="app-badge b-amber text-[9px] px-1.5 py-0.5 leading-none">Belum</span>
                        @endif
                    </div>
                    <div class="w-20 flex-shrink-0 flex justify-center">
                        <a href="{{ route('piket.mengajar.verifikasi', $item->id) }}" class="inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-[10px] transition w-full">
                            @if($item->verified_at)
                                Edit
                            @else
                                Verifikasi
                            @endif
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-slate-400 text-sm">Tidak ada data.</div>
                @endforelse
            </div>
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full app-tbl">
                <thead><tr>
                    <th>Tanggal</th>
                    <th>Guru</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th class="text-center">Mapel ke-</th>
                    <th>Waktu</th>
                    <th class="text-center">Foto</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($aktivitas as $item)
                    <tr>
                        <td class="font-semibold whitespace-nowrap">{{ Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800 whitespace-nowrap">{{ $item->user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $item->mata_pelajaran }}</td>
                        <td><span class="app-badge b-blue">{{ $item->kelas }}</span></td>
                        <td class="text-center">{{ $item->jam_ke }}</td>
                        <td class="whitespace-nowrap">{{ Carbon::parse($item->jam_mulai)->format('H:i') }} @if($item->jam_selesai) – {{ Carbon::parse($item->jam_selesai)->format('H:i') }} @endif</td>
                        <td class="text-center">
                            @if($item->foto_verifikasi)
                            <img src="{{ Storage::url($item->foto_verifikasi) }}" alt="Foto verif"
                                 class="w-10 h-10 rounded-lg object-cover mx-auto cursor-pointer border border-slate-200 hover:scale-110 transition-transform"
                                 onclick="showPhotoModal('{{ Storage::url($item->foto_verifikasi) }}', '{{ addslashes($item->user->name) }}')">  
                            @else
                            <span class="text-slate-300 text-lg">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->verified_at)
                            <div>
                                @if($item->status_verifikasi === 'mengajar')
                                    <span class="app-badge b-teal block w-fit mx-auto">Terverifikasi Mengajar</span>
                                @elseif($item->status_verifikasi === 'tidak_mengajar')
                                    <span class="app-badge b-red block w-fit mx-auto">Terverifikasi Tidak Mengajar</span>
                                @else
                                    <span class="app-badge b-teal block w-fit mx-auto">Terverifikasi</span>
                                @endif
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ Carbon::parse($item->verified_at)->format('H:i') }}</span>
                            </div>
                            @else
                            <span class="app-badge b-amber block w-fit mx-auto">Belum</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('piket.mengajar.verifikasi', $item->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                @if($item->verified_at)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                                @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Verifikasi
                                @endif
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-10 text-slate-400">Tidak ada data aktivitas mengajar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($aktivitas->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $aktivitas->links() }}</div>
        @endif
    </div>

</div>

{{-- Modal Foto --}}
<div id="photo-modal" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4" onclick="closePhotoModal()">
    <div class="relative max-w-2xl w-full" onclick="event.stopPropagation()">
        <button onclick="closePhotoModal()"
                class="absolute -top-10 right-0 text-white font-bold text-sm hover:text-slate-300">
            ✕ Tutup
        </button>
        <img id="modal-photo-img" src="" alt="" class="w-full rounded-2xl shadow-2xl object-contain max-h-[80vh]">
        <p id="modal-photo-name" class="text-white text-center text-sm font-semibold mt-3"></p>
    </div>
</div>

<script>
function showPhotoModal(src, name) {
    document.getElementById('modal-photo-img').src = src;
    document.getElementById('modal-photo-name').textContent = name;
    document.getElementById('photo-modal').classList.remove('hidden');
}
function closePhotoModal() {
    document.getElementById('photo-modal').classList.add('hidden');
}

let debounceTimer;

function fetchFilteredData() {
    const container = document.getElementById('data-container');
    if (container) {
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';
    }

    const form = document.getElementById('filter-form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    const resetBtnContainer = document.getElementById('reset-btn-container');
    if (resetBtnContainer) {
        const hasFilter = Array.from(formData.values()).some(val => val.trim() !== '');
        resetBtnContainer.style.display = hasFilter ? 'block' : 'none';
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
            const newContainer = doc.getElementById('data-container');
            
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
    fetchFilteredData();
}

// Intercept pagination clicks for AJAX
document.addEventListener('click', function(e) {
    const link = e.target.closest('#data-container .pagination a, #data-container nav[role="navigation"] a');
    if (link) {
        e.preventDefault();
        const url = new URL(link.href);
        
        const container = document.getElementById('data-container');
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
                const newContainer = doc.getElementById('data-container');
                
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
