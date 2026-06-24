<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('ebook.index') }}" class="text-slate-400 hover:text-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800">Literasi Al-Qur'an</span>
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
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-700 to-teal-800 rounded-2xl px-8 py-7 shadow-xl">
        <div class="relative z-10">
            <p class="text-emerald-300 text-sm font-semibold mb-1">Program Literasi</p>
            <h1 class="text-white text-2xl font-black leading-tight">Literasi Al-Qur'an</h1>
            <p class="text-emerald-200/70 text-sm mt-1">Pantau dan catat perkembangan literasi Al-Qur'an setiap siswa.</p>
        </div>
        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-white/10 text-8xl font-black select-none">﷽</div>
    </div>

    {{-- Filter Kelas & Jurusan --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="text-sm font-black text-slate-700 mb-4 flex items-center gap-2">
            <div class="w-5 h-5 rounded bg-emerald-100 flex items-center justify-center">
                <svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </div>
            Filter Kelas & Jurusan
        </h2>
        <form method="GET" action="{{ route('literasi.quran') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Kelas</label>
                <select name="kelas"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700
                               focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k }}" {{ $selectedKelas == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">Jurusan</label>
                <select name="jurusan"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700
                               focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j }}" {{ $selectedJurusan == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-emerald-700 hover:bg-emerald-800
                               text-white font-bold py-2.5 rounded-xl text-sm transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Tampilkan Siswa
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar Siswa --}}
    @if($selectedKelas && $selectedJurusan)
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-slate-800">
                    Daftar Siswa — Kelas {{ $selectedKelas }} · {{ $selectedJurusan }}
                </h2>
                <p class="text-sm text-slate-400 mt-0.5">{{ $siswaList->count() }} siswa ditemukan</p>
            </div>
        </div>

        @if($siswaList->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 py-16 text-center">
            <svg class="w-14 h-14 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="font-bold text-slate-400">Tidak ada siswa di kelas {{ $selectedKelas }} {{ $selectedJurusan }}</p>
            <p class="text-sm text-slate-400 mt-1">Pastikan data kelas dan jurusan sudah diisi pada profil siswa.</p>
        </div>
        @else

        <div class="space-y-3">
            @foreach($siswaList as $idx => $siswa)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">

                {{-- Row Siswa (Klik untuk buka/tutup) --}}
                <div class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-slate-50/70 transition-colors select-none"
                     onclick="toggleSiswa({{ $siswa->id }})">

                    {{-- No urut --}}
                    <span class="text-xs font-black text-slate-300 w-6 text-center flex-shrink-0">{{ $idx + 1 }}</span>

                    {{-- Avatar --}}
                    <div class="w-11 h-11 rounded-xl
                                {{ $siswa->jenis_kelamin === 'P'
                                    ? 'bg-gradient-to-br from-pink-400 to-rose-500'
                                    : 'bg-gradient-to-br from-emerald-400 to-teal-600' }}
                                flex items-center justify-center flex-shrink-0 shadow-sm">
                        <span class="text-white font-black text-sm">
                            {{ strtoupper(substr($siswa->name, 0, 2)) }}
                        </span>
                    </div>

                    {{-- Info utama --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 text-sm truncate">{{ $siswa->name }}</p>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5">
                            @if($siswa->nis)
                            <span class="text-[.65rem] text-slate-400 font-medium">NIS: {{ $siswa->nis }}</span>
                            @endif
                            @if($siswa->nisn)
                            <span class="text-[.65rem] text-slate-400 font-medium">NISN: {{ $siswa->nisn }}</span>
                            @endif
                            @if($siswa->jenis_kelamin)
                            <span class="text-[.65rem] text-slate-400 font-medium">{{ $siswa->jenis_kelamin_lengkap }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Badge catatan + chevron --}}
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="hidden sm:inline text-xs font-bold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full">
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

                        {{-- Kartu Profil Siswa --}}
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-emerald-100 p-5">
                            <h4 class="text-xs font-black text-emerald-800 uppercase tracking-wide mb-4 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profil Siswa
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                @php
                                    $profilItems = [
                                        ['label' => 'Nama Lengkap',        'value' => $siswa->name],
                                        ['label' => 'NIS',                  'value' => $siswa->nis ?? '-'],
                                        ['label' => 'NISN',                 'value' => $siswa->nisn ?? '-'],
                                        ['label' => 'Kelas',                'value' => ($siswa->kelas && $siswa->jurusan) ? $siswa->kelas . ' ' . $siswa->jurusan : ($siswa->kelas ?? '-')],
                                        ['label' => 'Jenis Kelamin',        'value' => $siswa->jenis_kelamin_lengkap],
                                        ['label' => 'Tempat, Tgl Lahir',    'value' => $siswa->tempat_tanggal_lahir],
                                        ['label' => 'Agama',                'value' => $siswa->agama ?? '-'],
                                        ['label' => 'Email',                'value' => $siswa->email],
                                    ];
                                @endphp
                                @foreach($profilItems as $item)
                                <div>
                                    <p class="text-[.6rem] font-black text-emerald-600/70 uppercase tracking-widest mb-0.5">
                                        {{ $item['label'] }}
                                    </p>
                                    <p class="text-xs font-semibold text-slate-700 leading-snug">
                                        {{ $item['value'] }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Form Tambah Catatan (guru & admin) --}}
                        @if(in_array(Auth::user()->role, ['guru', 'admin']))
                        <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                            <h4 class="text-xs font-black text-slate-600 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Catatan
                            </h4>
                            <form method="POST" action="{{ route('literasi.quran.store') }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div class="sm:col-span-2">
                                        <textarea name="catatan" rows="2" required
                                                  placeholder="Catatan perkembangan literasi Al-Qur'an..."
                                                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700
                                                         focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 resize-none transition"></textarea>
                                    </div>
                                    <div>
                                        <select name="jenis"
                                                class="w-full h-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700
                                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                                            <option value="hafalan">📿 Hafalan</option>
                                            <option value="tilawah">📖 Tilawah</option>
                                            <option value="tajwid">✨ Tajwid</option>
                                            <option value="umum">📝 Umum</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit"
                                                class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 rounded-xl text-sm transition duration-200">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

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
                                <p class="text-sm text-slate-400 font-medium">Belum ada catatan untuk siswa ini.</p>
                            </div>
                            @else
                            <div class="space-y-2.5">
                                @foreach($siswa->catatanQuran as $catatan)
                                @php
                                    $jenisStyles = [
                                        'hafalan' => ['bg' => 'bg-purple-50 border-purple-100', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => 'text-purple-500'],
                                        'tilawah' => ['bg' => 'bg-blue-50 border-blue-100',     'badge' => 'bg-blue-100 text-blue-700',     'icon' => 'text-blue-500'],
                                        'tajwid'  => ['bg' => 'bg-amber-50 border-amber-100',   'badge' => 'bg-amber-100 text-amber-700',   'icon' => 'text-amber-500'],
                                        'umum'    => ['bg' => 'bg-slate-50 border-slate-200',   'badge' => 'bg-slate-100 text-slate-600',   'icon' => 'text-slate-400'],
                                    ][$catatan->jenis] ?? ['bg' => 'bg-slate-50 border-slate-200', 'badge' => 'bg-slate-100 text-slate-600', 'icon' => 'text-slate-400'];
                                @endphp
                                <div class="rounded-xl border {{ $jenisStyles['bg'] }} p-4" id="catatan-{{ $catatan->id }}">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1">
                                            {{-- Meta --}}
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <span class="text-[.65rem] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wide {{ $jenisStyles['badge'] }}">
                                                    {{ ucfirst($catatan->jenis) }}
                                                </span>
                                                <span class="text-[.65rem] text-slate-400">
                                                    {{ $catatan->created_at->translatedFormat('d F Y, H:i') }}
                                                </span>
                                                <span class="text-[.65rem] text-slate-400">
                                                    · oleh <strong class="text-slate-600">{{ $catatan->guru->name ?? '-' }}</strong>
                                                </span>
                                            </div>
                                            {{-- Teks catatan --}}
                                            <p class="text-sm text-slate-700 leading-relaxed" id="text-catatan-{{ $catatan->id }}">
                                                {{ $catatan->catatan }}
                                            </p>
                                        </div>

                                        {{-- Aksi (hanya guru pembuat) --}}
                                        @if(Auth::id() === $catatan->guru_id)
                                        <div class="flex gap-1.5 flex-shrink-0">
                                            <button onclick="toggleEdit({{ $catatan->id }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </button>
                                            <form method="POST"
                                                  action="{{ route('literasi.quran.destroy', $catatan->id) }}"
                                                  onsubmit="return confirm('Hapus catatan ini?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition duration-200">
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
                                        <form method="POST" action="{{ route('literasi.quran.update', $catatan->id) }}"
                                              class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                                            @csrf @method('PUT')
                                            <div class="sm:col-span-2">
                                                <textarea name="catatan" rows="2" required
                                                          class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700
                                                                 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 resize-none transition">{{ $catatan->catatan }}</textarea>
                                            </div>
                                            <div>
                                                <select name="jenis"
                                                        class="w-full h-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700
                                                               focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition">
                                                    @foreach(['hafalan','tilawah','tajwid','umum'] as $j)
                                                    <option value="{{ $j }}" {{ $catatan->jenis === $j ? 'selected' : '' }}>{{ ucfirst($j) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 items-end">
                                                <button type="submit"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                                                    Simpan
                                                </button>
                                                <button type="button" onclick="toggleEdit({{ $catatan->id }})"
                                                        class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2.5 rounded-xl text-xs transition">
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

        @endif {{-- end $siswaList->isEmpty() --}}
    </div>
    @else
    {{-- Empty State --}}
    <div class="bg-white rounded-2xl border border-dashed border-slate-200 py-16 text-center">
        <svg class="w-14 h-14 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="font-bold text-slate-400 text-lg">Pilih Kelas & Jurusan</p>
        <p class="text-sm text-slate-400 mt-1">Gunakan dropdown di atas untuk menampilkan daftar siswa.</p>
    </div>
    @endif

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
</script>
</x-app-layout>
