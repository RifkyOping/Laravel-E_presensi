@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard</span>
    </x-slot>

<div class="space-y-6">

    {{-- Header Banner --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-5 py-5 sm:px-8 sm:py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-blue-300 text-sm font-semibold mb-1">Selamat datang,</p>
                <h1 class="text-white text-2xl font-black leading-tight">{{ Auth::user()->name }}</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    {{ Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex flex-col items-start sm:items-end gap-1 shrink-0">
                <span class="text-white/40 text-xs uppercase tracking-widest font-bold">Kurikulum</span>
                <span class="text-white text-sm font-semibold">E-Presensi UPTD SMKN 1 Majene</span>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
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

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
        $cards = [
            ['label'=>'Total Guru',      'value'=>$stats['total_guru'],    'sub'=>'terdaftar', 'color'=>'text-[#1e3a6e]', 'bg'=>'bg-blue-50'],
            ['label'=>'Guru Hadir',      'value'=>$stats['guru_hadir'],    'sub'=>'hari ini',  'color'=>'text-green-600', 'bg'=>'bg-green-50'],
            ['label'=>'Sesi Mengajar',   'value'=>$stats['sesi_mengajar'], 'sub'=>'hari ini',  'color'=>'text-amber-600', 'bg'=>'bg-amber-50'],
            ['label'=>'Sudah Diverifikasi',   'value'=>$stats['sudah_diverif'], 'sub'=>'terverif',  'color'=>'text-teal-600',  'bg'=>'bg-teal-50'],
            ['label'=>'Belum Diverifikasi',   'value'=>$stats['belum_diverif'], 'sub'=>'menunggu',  'color'=>'text-red-600',   'bg'=>'bg-red-50'],
        ];
        @endphp
        @foreach($cards as $i => $c)
        <div class="stat-card animate-up delay-{{ $i+1 }}">
            <div class="flex items-start justify-end mb-3">
                <span class="text-[.68rem] font-semibold text-slate-400 uppercase tracking-wide">{{ $c['sub'] }}</span>
            </div>
            <p class="text-3xl font-black {{ $c['color'] }}">{{ $c['value'] }}</p>
            <p class="text-sm text-slate-500 font-medium mt-0.5">{{ $c['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Guru Hadir Hari Ini --}}
    <div class="app-card overflow-hidden animate-up delay-2">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Guru yang Sudah Absen Sekolah Hari Ini</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ Carbon::today()->translatedFormat('d F Y') }} — guru-guru ini dapat diverifikasi mengajar</p>
            </div>
            <span class="app-badge b-green">{{ $guruHadirHariIni->count() }} guru</span>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($guruHadirHariIni as $a)
            <div class="px-5 py-3.5 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($a->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 text-sm truncate">{{ $a->user->name }}</p>
                    <p class="text-xs text-slate-400">
                        Datang: {{ Carbon::parse($a->waktu_datang)->format('H:i') }} WITA
                        @if($a->waktu_pulang) · Pulang: {{ Carbon::parse($a->waktu_pulang)->format('H:i') }} WITA @endif
                    </p>
                </div>
                <span class="app-badge b-blue">Hadir</span>
            </div>
            @empty
            <p class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada guru yang absen hari ini.</p>
            @endforelse
        </div>
    </div>

    {{-- 2 Kolom: Menunggu & Sudah Diverif --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Menunggu Verifikasi --}}
        <div class="app-card overflow-hidden animate-up delay-3">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Menunggu Verifikasi</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Aktivitas mengajar hari ini — belum difoto</p>
                </div>
                <span class="app-badge b-red">{{ $menungguVerifikasi->count() }} sesi</span>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($menungguVerifikasi as $m)
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ $m->jam_ke }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $m->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $m->mata_pelajaran }} · Kelas {{ $m->kelas }}</p>
                    </div>
                    <a href="{{ route('kurikulum.verifikasi', $m->id) }}" class="btn-primary text-xs py-1.5 px-3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Verifikasi
                    </a>
                </div>
                @empty
                <p class="px-5 py-8 text-center text-[#1e3a6e] font-semibold text-sm">Semua sudah diverifikasi hari ini!</p>
                @endforelse
            </div>
        </div>

        {{-- Sudah Diverifikasi --}}
        <div class="app-card overflow-hidden animate-up delay-4">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Sudah Diverifikasi</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Aktivitas mengajar hari ini — sudah ada foto & catatan</p>
                </div>
                <span class="app-badge b-teal">{{ $sudahVerifikasi->count() }} sesi</span>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($sudahVerifikasi as $v)
                <div class="px-5 py-3.5 flex items-start gap-3">
                    {{-- Thumbnail foto --}}
                    @if($v->foto_verifikasi)
                    <img src="{{ Storage::url($v->foto_verifikasi) }}"
                         alt="Foto verifikasi {{ $v->user->name }}"
                         class="w-12 h-12 rounded-xl object-cover border border-slate-200 flex-shrink-0 cursor-pointer"
                         onclick="showPhotoModal('{{ Storage::url($v->foto_verifikasi) }}', '{{ addslashes($v->user->name) }}')">
                    @else
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $v->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $v->mata_pelajaran }} · Kelas {{ $v->kelas }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">💬 {{ $v->catatan_kurikulum }}</p>
                    </div>
                    <a href="{{ route('kurikulum.verifikasi', $v->id) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200 flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                </div>
                @empty
                <p class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada verifikasi hari ini.</p>
                @endforelse
            </div>
            @if($sudahVerifikasi->count() > 0)
            <div class="px-5 py-3 border-t border-slate-100">
                <a href="{{ route('kurikulum.monitoring-mengajar') }}" class="text-xs font-semibold text-[#1e3a6e] hover:underline">
                    Lihat Semua Riwayat →
                </a>
            </div>
            @endif
        </div>
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
</script>

</x-app-layout>
