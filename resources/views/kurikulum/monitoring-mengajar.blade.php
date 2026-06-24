@php use Carbon\Carbon; @endphp
<x-kurikulum-layout pageTitle="Monitoring Mengajar" pageSubtitle="Verifikasi kehadiran mengajar guru — tambah foto & catatan">

<div class="space-y-6">

    {{-- Info Banner --}}
    <div class="alert-info animate-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Klik tombol <strong>Verifikasi</strong> untuk mengambil/upload foto guru yang sedang mengajar dan memberikan catatan.
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
    <form method="GET" action="{{ route('kurikulum.monitoring-mengajar') }}"
          class="kur-card p-5 grid grid-cols-1 sm:grid-cols-4 gap-4 animate-up">
        <div>
            <label class="kur-label">Tanggal</label>
            <input type="date" name="tanggal" class="kur-input" value="{{ request('tanggal') }}">
        </div>
        <div>
            <label class="kur-label">Filter Guru</label>
            <select name="guru_id" class="kur-input">
                <option value="">— Semua Guru —</option>
                @foreach($semuaGuru as $g)
                <option value="{{ $g->id }}" {{ request('guru_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="kur-label">Status Verifikasi</label>
            <select name="status_verif" class="kur-input">
                <option value="">— Semua —</option>
                <option value="belum" {{ request('status_verif')==='belum'?'selected':'' }}>Belum Diverifikasi</option>
                <option value="mengajar" {{ request('status_verif')==='mengajar'?'selected':'' }}>Terverifikasi Mengajar</option>
                <option value="tidak_mengajar" {{ request('status_verif')==='tidak_mengajar'?'selected':'' }}>Terverifikasi Tidak Mengajar</option>
            </select>
        </div>
        <div class="flex items-end gap-3">
            <button type="submit" class="btn-primary flex-1 justify-center">
                Terapkan
            </button>
            <a href="{{ route('kurikulum.monitoring-mengajar') }}" class="btn-outline">Reset</a>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="kur-card overflow-hidden animate-up delay-2">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Aktivitas Mengajar</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $aktivitas->total() }} sesi ditemukan</p>
            </div>
            <div class="flex gap-2">
                <span class="kur-badge b-teal">
                    {{ $aktivitas->filter(fn($a) => $a->verified_at)->count() }} diverifikasi
                </span>
                <span class="kur-badge b-red">
                    {{ $aktivitas->filter(fn($a) => !$a->verified_at)->count() }} belum
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full kur-tbl">
                <thead><tr>
                    <th>Tanggal</th>
                    <th>Guru</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th class="text-center">Jam ke-</th>
                    <th>Waktu</th>
                    <th>Materi</th>
                    <th class="text-center">Foto</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($aktivitas as $item)
                    <tr>
                        <td class="font-semibold whitespace-nowrap">
                            {{ Carbon::parse($item->tanggal)->format('d M Y') }}
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800 whitespace-nowrap">{{ $item->user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $item->mata_pelajaran }}</td>
                        <td><span class="kur-badge b-blue">{{ $item->kelas }}</span></td>
                        <td class="text-center">
                            <span class="inline-flex w-7 h-7 rounded-full bg-[#1e3a6e] text-white items-center justify-center font-bold text-xs">
                                {{ $item->jam_ke }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap">
                            {{ Carbon::parse($item->jam_mulai)->format('H:i') }}
                            @if($item->jam_selesai) – {{ Carbon::parse($item->jam_selesai)->format('H:i') }} @endif
                        </td>
                        <td class="max-w-[160px]">
                            <span class="line-clamp-2 block text-slate-600 text-xs">{{ $item->materi }}</span>
                        </td>
                        <td class="text-center">
                            @if($item->foto_verifikasi)
                            <img src="{{ Storage::url($item->foto_verifikasi) }}"
                                 alt="Foto verif"
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
                                    <span class="kur-badge b-teal block w-fit mx-auto">Terverifikasi Mengajar</span>
                                @elseif($item->status_verifikasi === 'tidak_mengajar')
                                    <span class="kur-badge b-red block w-fit mx-auto">Terverifikasi Tidak Mengajar</span>
                                @else
                                    <span class="kur-badge b-teal block w-fit mx-auto">Terverifikasi</span>
                                @endif
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    {{ Carbon::parse($item->verified_at)->format('H:i') }}
                                </span>
                            </div>
                            @else
                            <span class="kur-badge b-amber block w-fit mx-auto">Belum</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center gap-1.5 justify-center">
                                <a href="{{ route('kurikulum.verifikasi', $item->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                    @if($item->verified_at)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                    @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Verifikasi
                                    @endif
                                </a>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-10 text-slate-400">Tidak ada data aktivitas mengajar.</td></tr>
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
</script>

</x-kurikulum-layout>
