@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Monitoring Mengajar</span>
    </x-slot>

<div class="space-y-6">

    {{-- Info Banner --}}
    <div class="alert-info animate-up flex items-start gap-2">
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
        showFilter: localStorage.getItem('filter_kurikulum_monitoring_mengajar') === 'true' || {{ $hasFilter ? 'true' : 'false' }} 
    }" 
    x-init="$watch('showFilter', val => localStorage.setItem('filter_kurikulum_monitoring_mengajar', val))"
    class="app-card p-6 animate-up">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter Jurnal Mengajar</h2>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk menyesuaikan pencarian</p>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100" style="display: none;">
            <form id="filter-form" onsubmit="event.preventDefault(); fetchFilteredData();" method="GET" action="{{ route('kurikulum.monitoring-mengajar') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="app-label">Tanggal</label>
                    <input type="date" name="tanggal" class="app-input" value="{{ request('tanggal') }}" onchange="fetchFilteredData()">
                </div>
                <div>
                    <label class="app-label">Filter Guru</label>
                    <select name="guru_id" class="app-input" onchange="fetchFilteredData()">
                        <option value="">— Semua Guru —</option>
                        @foreach($semuaGuru as $g)
                        <option value="{{ $g->id }}" {{ request('guru_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="app-label">Status Verifikasi</label>
                    <select name="status_verif" class="app-input" onchange="fetchFilteredData()">
                        <option value="">— Semua —</option>
                        <option value="belum" {{ request('status_verif')==='belum'?'selected':'' }}>Belum Diverifikasi</option>
                        <option value="mengajar" {{ request('status_verif')==='mengajar'?'selected':'' }}>Terverifikasi Mengajar</option>
                        <option value="tidak_mengajar" {{ request('status_verif')==='tidak_mengajar'?'selected':'' }}>Terverifikasi Tidak Mengajar</option>
                    </select>
                </div>
                <div class="flex items-end pt-1" id="reset-btn-container" style="display: {{ $hasFilter ? 'block' : 'none' }}">
                    <button type="button" onclick="resetFilter()" class="px-5 py-2.5 border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm rounded-xl transition duration-200 flex items-center justify-center gap-1.5 w-full sm:w-auto h-[42px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
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
        {{-- Mobile: Card List --}}
        <div class="block sm:hidden divide-y divide-slate-100">
            @forelse($aktivitas as $item)
            <div class="p-4 space-y-2">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800 text-sm leading-tight">{{ $item->user->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $item->mata_pelajaran }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <span class="font-semibold">{{ $item->kelas }}</span>
                            &nbsp;·&nbsp; Jam {{ $item->jam_ke }}
                            &nbsp;·&nbsp; {{ Carbon::parse($item->jam_mulai)->format('H:i') }}{{ $item->jam_selesai ? ' – '.Carbon::parse($item->jam_selesai)->format('H:i') : '' }}
                        </p>
                        <p class="text-[.65rem] text-slate-400 mt-1">{{ Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</p>
                    </div>
                    <a href="{{ route('kurikulum.verifikasi', $item->id) }}"
                       class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition">
                        @if($item->verified_at)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                        @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Verif
                        @endif
                    </a>
                </div>
                <div class="flex items-center justify-between">
                    @if($item->verified_at)
                        @if($item->status_verifikasi === 'mengajar')
                            <span class="app-badge b-teal text-xs">Terverifikasi Mengajar</span>
                        @elseif($item->status_verifikasi === 'tidak_mengajar')
                            <span class="app-badge b-red text-xs">Tidak Mengajar</span>
                        @else
                            <span class="app-badge b-teal text-xs">Terverifikasi</span>
                        @endif
                    @else
                        <span class="app-badge b-amber text-xs">Belum</span>
                    @endif
                    @if($item->foto_verifikasi)
                    <img src="{{ Storage::url($item->foto_verifikasi) }}" alt="Foto"
                         class="w-9 h-9 rounded-lg object-cover border border-slate-200 cursor-pointer hover:scale-110 transition"
                         onclick="showPhotoModal('{{ Storage::url($item->foto_verifikasi) }}', '{{ addslashes($item->user->name) }}')">
                    @endif
                </div>
            </div>
            @empty
            <div class="py-10 text-center text-slate-400 text-sm">Tidak ada data aktivitas mengajar.</div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full app-tbl">
                <thead><tr>
                    <th>Tanggal</th>
                    <th>Guru</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th class="text-center">Jam ke-</th>
                    <th>Waktu</th>
                    <th class="text-center">Foto</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($aktivitas as $item)
                    <tr>
                        <td class="font-semibold whitespace-nowrap">{{ Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
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
                            <a href="{{ route('kurikulum.verifikasi', $item->id) }}"
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
