<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-[#24417c] leading-tight">
            {{ __('Absensi Aktivitas Mengajar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="bg-green-50 border-2 border-green-400 text-green-800 font-bold px-6 py-4 rounded-2xl shadow flex items-center gap-3">
                    <span class="text-2xl">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border-2 border-red-400 text-red-800 font-bold px-6 py-4 rounded-2xl shadow flex items-center gap-3">
                    <span class="text-2xl">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border-2 border-red-400 text-red-800 font-bold px-6 py-4 rounded-2xl shadow">
                    <p class="flex items-center gap-2 mb-2"><span class="text-2xl">⚠️</span> Periksa kembali isian form:</p>
                    <ul class="list-disc list-inside text-sm font-medium space-y-1 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Banner --}}
            <div class="bg-[#24417c] overflow-hidden shadow-xl sm:rounded-3xl relative">
                <div class="p-8 sm:p-10 text-white relative z-10">
                    <h1 class="text-3xl font-black mb-1">Catat Aktivitas Mengajar</h1>
                    <p class="text-white/70 font-medium text-lg">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                    <p class="mt-3 text-sm text-white/50">Dokumentasikan setiap sesi mengajar Anda hari ini.</p>
                </div>
                <div class="absolute -right-10 -top-10 w-64 h-64 border-[30px] border-white/10 rounded-full"></div>
                <div class="absolute -right-4 -bottom-8 w-40 h-40 border-[20px] border-white/5 rounded-full"></div>
            </div>

            {{-- Aktivitas Hari Ini --}}
            @if ($hariIni->count() > 0)
                <div class="bg-white rounded-3xl shadow-lg border-2 border-[#24417c]/20 overflow-hidden">
                    <div class="p-6 sm:p-8 border-b-2 border-[#24417c]/10 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-black text-[#24417c]">Sesi Mengajar Hari Ini</h3>
                            <p class="text-[#24417c]/60 font-medium text-sm mt-1">{{ $hariIni->count() }} sesi tercatat</p>
                        </div>
                        <span class="bg-[#24417c] text-white text-xs font-bold px-4 py-2 rounded-full">Hari Ini</span>
                    </div>
                    <div class="divide-y-2 divide-[#24417c]/10">
                        @foreach ($hariIni as $item)
                            <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-4 hover:bg-[#24417c]/5 transition">
                                {{-- Badge Jam --}}
                                <div class="shrink-0 w-16 h-16 rounded-2xl bg-[#24417c] text-white flex flex-col items-center justify-center shadow-md shadow-[#24417c]/30">
                                    <span class="text-xs font-bold uppercase tracking-wider text-white/70">Jam</span>
                                    <span class="text-2xl font-black leading-none">{{ $item->jam_ke }}</span>
                                </div>
                                {{-- Detail --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <h4 class="text-lg font-black text-[#24417c]">{{ $item->mata_pelajaran }}</h4>
                                        <span class="bg-[#24417c]/10 text-[#24417c] text-xs font-bold px-3 py-1 rounded-full">{{ $item->kelas }}</span>
                                        <span class="bg-white border border-[#24417c]/30 text-[#24417c] text-xs font-bold px-3 py-1 rounded-full capitalize">{{ $item->metode }}</span>
                                    </div>
                                    <p class="text-[#24417c]/70 font-medium text-sm line-clamp-1">{{ $item->materi }}</p>
                                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-[#24417c]/60 font-bold">
                                        <span>{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                                            @if($item->jam_selesai) – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }} WITA @endif
                                        </span>
                                        <span>👥 {{ $item->jumlah_siswa_hadir }} siswa hadir</span>
                                    </div>
                                </div>
                                {{-- Tombol Hapus --}}
                                <form method="POST" action="{{ route('guru.aktivitas.destroy', $item->id) }}"
                                    onsubmit="return confirm('Hapus data sesi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Form Tambah Sesi Mengajar --}}
            <div class="bg-white rounded-3xl shadow-lg border-2 border-[#24417c]/20 overflow-hidden">
                <div class="p-6 sm:p-8 border-b-2 border-[#24417c]/10">
                    <h3 class="text-xl font-black text-[#24417c]">Tambah Sesi Mengajar</h3>
                    <p class="text-[#24417c]/60 font-medium text-sm mt-1">Isi form di bawah untuk mencatat sesi mengajar baru hari ini.</p>
                </div>

                <form method="POST" action="{{ route('guru.aktivitas.store') }}" class="p-6 sm:p-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Mata Pelajaran --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Mata Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <select name="mata_pelajaran"
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200 bg-white">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach (config('sekolah.mata_pelajaran') as $mapel)
                                    <option value="{{ $mapel }}" {{ old('mata_pelajaran') === $mapel ? 'selected' : '' }}>
                                        {{ $mapel }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- <p class="text-xs text-[#24417c]/50 mt-1 font-medium">Daftar dapat diubah di <code class="bg-[#24417c]/10 px-1 rounded">config/sekolah.php</code></p> --}}
                        </div>

                        {{-- Kelas --}}
                        <div>
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            <select name="kelas"
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200 bg-white">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach (config('sekolah.kelas') as $kls)
                                    <option value="{{ $kls }}" {{ old('kelas') === $kls ? 'selected' : '' }}>
                                        {{ $kls }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- <p class="text-xs text-[#24417c]/50 mt-1 font-medium">Daftar dapat diubah di <code class="bg-[#24417c]/10 px-1 rounded">config/sekolah.php</code></p> --}}
                        </div>

                        {{-- Jam Ke- --}}
                        <div>
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Jam Ke- <span class="text-red-500">*</span>
                            </label>
                            <select name="jam_ke"
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200 bg-white">
                                <option value="">-- Pilih --</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('jam_ke') == $i ? 'selected' : '' }}>
                                        Jam ke-{{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Jam Mulai --}}
                        <div>
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200">
                        </div>

                        {{-- Jam Selesai --}}
                        <div>
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Jam Selesai
                            </label>
                            <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200">
                        </div>

                        {{-- Metode Mengajar --}}
                        <div>
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Metode Mengajar <span class="text-red-500">*</span>
                            </label>
                            <select name="metode"
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200 bg-white">
                                <option value="">-- Pilih Metode --</option>
                                @foreach (['daring', 'luring'] as $m)
                                    <option value="{{ $m }}" {{ old('metode') === $m ? 'selected' : '' }}>
                                        {{ ucfirst($m) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jumlah Siswa Hadir --}}
                        <div>
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Jumlah Siswa Hadir <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="jumlah_siswa_hadir" value="{{ old('jumlah_siswa_hadir', 0) }}"
                                min="0" placeholder="0"
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200">
                        </div>

                        {{-- Materi --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Materi / Topik Pembelajaran <span class="text-red-500">*</span>
                            </label>
                            <textarea name="materi" rows="3" placeholder="Tulis materi atau topik yang diajarkan..."
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200 resize-none">{{ old('materi') }}</textarea>
                        </div>

                        {{-- Keterangan --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Keterangan Tambahan
                            </label>
                            <textarea name="keterangan" rows="2" placeholder="Opsional — catatan tambahan..."
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200 resize-none">{{ old('keterangan') }}</textarea>
                        </div>

                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                            class="bg-[#24417c] text-white border-2 border-[#24417c] font-bold text-sm px-6 py-2.5 rounded-xl hover:bg-white hover:text-[#24417c] transition duration-300 shadow-md shadow-[#24417c]/20">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabel Riwayat --}}
            <div class="bg-white overflow-hidden shadow-xl shadow-[#24417c]/10 sm:rounded-3xl border-2 border-[#24417c]/20">
                <div class="p-6 sm:p-8 border-b-2 border-[#24417c]/10">
                    <h3 class="text-2xl font-black text-[#24417c]">Riwayat Aktivitas Mengajar</h3>
                    <p class="text-[#24417c]/70 font-medium mt-1">Semua sesi mengajar yang telah Anda catat.</p>
                    
                    <div class="mt-5">
                        <form method="GET" action="{{ route('guru.aktivitas') }}" class="flex flex-wrap items-center gap-3">
                            <input type="date" name="tanggal_riwayat" value="{{ $tanggalRiwayat }}" class="border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-2 text-[#24417c] font-medium focus:outline-none transition duration-200 bg-white">
                            <button type="submit" class="bg-[#24417c] hover:bg-[#1a2f5c] text-white px-5 py-2.5 rounded-xl font-bold transition shadow-md shadow-[#24417c]/20">Terapkan</button>
                            
                            @if(request()->has('tanggal_riwayat'))
                                <a href="{{ route('guru.aktivitas') }}" class="px-4 py-2.5 rounded-xl border-2 border-slate-200 hover:border-slate-300 text-slate-500 hover:text-slate-700 font-bold text-sm transition bg-slate-50">Reset ke Hari Ini</a>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#24417c] text-white">
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider">Tanggal</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider">Mata Pelajaran</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider">Kelas</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider">Jam ke-</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider">Waktu</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider">Materi</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider">Metode</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider">Siswa Hadir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-[#24417c]/10">
                            @forelse ($riwayat as $item)
                                <tr class="hover:bg-[#24417c]/5 transition duration-200">
                                    <td class="py-4 px-5 font-bold text-[#24417c] whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-5 font-bold text-[#24417c]">{{ $item->mata_pelajaran }}</td>
                                    <td class="py-4 px-5 font-medium text-[#24417c]">{{ $item->kelas }}</td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-block bg-[#24417c] text-white font-black text-sm w-9 h-9 rounded-full flex items-center justify-center">
                                            {{ $item->jam_ke }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 font-medium text-[#24417c] whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                                        @if ($item->jam_selesai)
                                            – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-[#24417c]/80 font-medium max-w-xs">
                                        <span class="line-clamp-2 block">{{ $item->materi }}</span>
                                    </td>
                                    <td class="py-4 px-5">
                                        @php
                                            $metodeColor = match($item->metode) {
                                                'daring'  => 'bg-purple-100 text-purple-800 border-purple-300',
                                                'luring'  => 'bg-[#24417c] text-white border-[#24417c]',
                                                'diskusi' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                'praktik' => 'bg-green-100 text-green-800 border-green-300',
                                                default   => 'bg-gray-100 text-gray-700 border-gray-300',
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $metodeColor }} capitalize">
                                            {{ $item->metode }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-center font-black text-[#24417c] text-lg">
                                        {{ $item->jumlah_siswa_hadir }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-10 px-6 text-center font-medium text-[#24417c]/60">
                                        Belum ada riwayat aktivitas mengajar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($riwayat->hasPages())
                    <div class="px-6 py-4 border-t-2 border-[#24417c]/10">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
