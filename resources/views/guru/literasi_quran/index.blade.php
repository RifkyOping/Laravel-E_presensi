<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-800">Literasi Keagamaan</span>
        </div>
    </x-slot>

<div class="space-y-6">

    {{-- Alert --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-3 text-sm text-red-700 font-medium flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl px-8 py-7 shadow-xl"
         style="background: linear-gradient(135deg, #1e3a6e 0%, #2d5299 60%, #162d57 100%);">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-block text-[.65rem] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-2"
                      style="background:rgba(255,255,255,.15);color:#bfdbfe;">Program Literasi · Guru</span>
                <h1 class="text-white text-2xl font-black leading-tight">Literasi Keagamaan</h1>
                <p class="text-blue-200/70 text-sm mt-1">Pantau dan catat perkembangan literasi keagamaan setiap murid.</p>
            </div>
            <div class="text-right opacity-20 select-none">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
        </div>
        <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-20 -bottom-10 w-36 h-36 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    <div class="bg-white rounded-2xl border border-blue-200 p-6">
        <h2 class="text-sm font-black text-slate-700 flex items-center gap-2 mb-5">
            <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </div>
            Filter Murid
        </h2>

        <div class="border-t border-slate-100 pt-5">
            <form method="GET" action="{{ route('guru.literasi.quran') }}">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Kelas</label>
                    <select name="kelas_id" onchange="fetchDaftarMurid(this.value)"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700
                                   focus:outline-none focus:ring-2 focus:ring-[#1e3a6e]/20 focus:border-[#1e3a6e] transition cursor-pointer">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->tingkat }} {{ $k->jurusan }} {{ $k->rombel }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Daftar Murid --}}
    <div id="daftar-murid-container" class="transition-opacity duration-300">
    @if($selectedKelasId)
    <div class="space-y-4">
        <div>
            <h2 class="text-lg font-black text-slate-800">
                Daftar Murid Kelas {{ $selectedKelasModel->tingkat }} {{ $selectedKelasModel->jurusan }} {{ $selectedKelasModel->rombel }}
            </h2>
            <p class="text-sm text-slate-400 mt-0.5">{{ $siswaList->count() }} murid ditemukan</p>
        </div>

        @if($siswaList->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 py-16 text-center">
            <svg class="w-14 h-14 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="font-bold text-slate-400">Tidak ada murid di kelas {{ $selectedKelasModel->tingkat }} {{ $selectedKelasModel->jurusan }} {{ $selectedKelasModel->rombel }}</p>
            <p class="text-sm text-slate-400 mt-1">Pastikan data kelas & jurusan sudah diisi pada profil murid.</p>
        </div>
        @else

        <div class="space-y-3">
            @foreach($siswaList as $idx => $siswa)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">

                {{-- Row Murid --}}
                <div class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-slate-50/70 transition-colors select-none"
                     onclick="toggleSiswa({{ $siswa->id }})">

                    <span class="text-xs font-black text-slate-300 w-6 text-center flex-shrink-0">{{ $idx + 1 }}</span>


                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 text-sm truncate">{{ $siswa->name }}</p>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5">
                            @if($siswa->siswaProfile && $siswa->siswaProfile->nis)
                            <span class="text-[.65rem] text-slate-400 font-medium">NIS: {{ $siswa->siswaProfile->nis }}</span>
                            @endif
                            @if($siswa->jenis_kelamin)
                            <span class="text-[.65rem] text-slate-400 font-medium">{{ $siswa->jenis_kelamin_lengkap }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="hidden sm:inline text-xs font-bold bg-[#1e3a6e]/10 text-[#1e3a6e] px-2.5 py-1 rounded-full">
                            {{ $siswa->catatanQuran->count() }} Catatan
                        </span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                             id="chevron-{{ $siswa->id }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Akordeon Konten --}}
                <div id="siswa-{{ $siswa->id }}" class="hidden border-t border-slate-100">
                    <div class="p-6 space-y-5">

                        {{-- Form Tambah Catatan --}}
                        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                            <h4 class="text-xs font-black text-slate-600 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Catatan
                            </h4>
                            <form method="POST" action="{{ route('guru.literasi.quran.store') }}">
                                @csrf
                                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div class="sm:col-span-3">
                                        <textarea name="catatan" rows="2" required
                                                  placeholder="Catatan perkembangan literasi keagamaan..."
                                                  class="w-full h-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700
                                                         focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 resize-none transition"></textarea>
                                    </div>
                                    <div class="flex items-stretch">
                                        <button type="submit"
                                                class="w-full h-full bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold py-2.5 rounded-xl text-sm transition">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Daftar Catatan --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-black text-slate-600 uppercase tracking-wide flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Riwayat Catatan
                                </h4>
                                <span class="text-[.65rem] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">
                                    {{ $siswa->catatanQuran->count() }} total
                                </span>
                            </div>

                            @if($siswa->catatanQuran->isEmpty())
                            <div class="py-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                <p class="text-sm text-slate-400 font-medium">Belum ada catatan untuk murid ini.</p>
                            </div>
                            @else
                            <div class="space-y-2.5">
                                @foreach($siswa->catatanQuran as $catatan)
                                <div class="rounded-xl border bg-slate-50 border-slate-200 p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <span class="text-[.65rem] text-slate-400">
                                                    {{ $catatan->created_at->translatedFormat('d F Y, H:i') }}
                                                </span>
                                                <span class="text-[.65rem] text-slate-400">
                                                    · oleh <strong class="text-slate-600">{{ $catatan->guru->name ?? '-' }}</strong>
                                                </span>
                                            </div>
                                            <p class="text-sm text-slate-700 leading-relaxed">{{ $catatan->catatan }}</p>
                                        </div>

                                        {{-- Aksi (hanya guru pembuat) --}}
                                        @if(Auth::id() === $catatan->guru_id)
                                        <div class="flex flex-col sm:flex-row gap-1.5 flex-shrink-0">
                                            <button onclick="toggleEdit({{ $catatan->id }})"
                                                    class="inline-flex justify-center items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200 w-full sm:w-auto">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('guru.literasi.quran.destroy', $catatan->id) }}"
                                                  onsubmit="return confirm('Hapus catatan ini?')" class="w-full sm:w-auto">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex justify-center items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition duration-200 w-full sm:w-auto">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Form Edit Inline --}}
                                    @if(Auth::id() === $catatan->guru_id)
                                    <div id="edit-{{ $catatan->id }}" class="hidden mt-3 pt-3 border-t border-slate-200/60">
                                        <form method="POST" action="{{ route('guru.literasi.quran.update', $catatan->id) }}"
                                              class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                                            @csrf @method('PUT')
                                            <div class="sm:col-span-3">
                                                <textarea name="catatan" rows="2" required
                                                          class="w-full h-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700
                                                                 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 resize-none transition">{{ $catatan->catatan }}</textarea>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 items-stretch">
                                                <button type="submit"
                                                        class="h-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                                                    Simpan
                                                </button>
                                                <button type="button" onclick="toggleEdit({{ $catatan->id }})"
                                                        class="h-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @endif
    </div>
    @else
    <div class="bg-white rounded-2xl border border-dashed border-slate-200 py-16 text-center">
        <svg class="w-14 h-14 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="font-bold text-slate-400 text-lg">Pilih Kelas</p>
        <p class="text-sm text-slate-400 mt-1">Gunakan dropdown di atas untuk menampilkan daftar murid.</p>
    </div>
    @endif
    </div>

</div>

<script>
function toggleSiswa(id) {
    const panel   = document.getElementById('siswa-' + id);
    const chevron = document.getElementById('chevron-' + id);
    const hidden  = panel.classList.contains('hidden');
    panel.classList.toggle('hidden', !hidden);
    chevron.style.transform = hidden ? 'rotate(180deg)' : '';
}
function toggleEdit(id) {
    document.getElementById('edit-' + id).classList.toggle('hidden');
}

function fetchDaftarMurid(kelasId) {
    const container = document.getElementById('daftar-murid-container');
    if (container) {
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';
    }

    const url = new URL(window.location.href);
    if (kelasId) {
        url.searchParams.set('kelas_id', kelasId);
    } else {
        url.searchParams.delete('kelas_id');
    }
    
    // Perbarui URL browser secara visual tanpa mereload halaman
    window.history.pushState({}, '', url);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('daftar-murid-container');
            
            if (container && newContainer) {
                container.innerHTML = newContainer.innerHTML;
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            }
        })
        .catch(err => {
            console.error('Gagal mengambil data murid', err);
            if (container) {
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            }
        });
}
</script>
</x-app-layout>
