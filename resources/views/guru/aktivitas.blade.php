<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Aktivitas Mengajar</span>
    </x-slot>

    <div class="space-y-6">

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
    <div class="bg-[#24417c] overflow-hidden rounded-2xl shadow-lg relative">
        <div class="p-6 sm:p-8 text-white relative z-10">
            <h1 class="text-xl sm:text-2xl font-black mb-1">Catat Aktivitas Mengajar</h1>
            <p class="text-white/70 font-medium text-sm sm:text-base">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </p>
            <p class="mt-2 text-xs text-white/50">Dokumentasikan setiap sesi mengajar Anda hari ini.</p>
        </div>
        <div class="absolute -right-10 -top-10 w-48 h-48 border-[24px] border-white/10 rounded-full pointer-events-none"></div>
    </div>

    {{-- Aktivitas Hari Ini --}}
    @if ($hariIni->count() > 0)
    <div class="bg-white rounded-2xl border-2 border-[#24417c]/20 overflow-hidden">
        <div class="px-5 py-4 border-b-2 border-[#24417c]/10 flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-[#24417c]">Sesi Mengajar Hari Ini</h3>
                <p class="text-[#24417c]/60 font-medium text-xs mt-0.5">{{ $hariIni->count() }} sesi tercatat</p>
            </div>
            <span class="bg-[#24417c] text-white text-xs font-bold px-3 py-1.5 rounded-full">Hari Ini</span>
        </div>
        <div class="divide-y-2 divide-[#24417c]/10">
            @foreach ($hariIni as $item)
            <div class="p-4 sm:p-5 flex items-center gap-3 hover:bg-[#24417c]/5 transition">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-[#24417c] text-white flex flex-col items-center justify-center shadow-md shadow-[#24417c]/20">
                    <span class="text-[0.6rem] font-bold uppercase tracking-wider text-white/70 leading-none">Jam</span>
                    <span class="text-xl font-black leading-none mt-0.5">{{ $item->jam_ke }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <h4 class="text-sm font-black text-[#24417c] truncate">{{ $item->mata_pelajaran }}</h4>
                        <span class="bg-[#24417c]/10 text-[#24417c] text-[0.65rem] font-bold px-2 py-0.5 rounded-full whitespace-nowrap">{{ $item->kelas }}</span>
                    </div>
                    <p class="text-xs text-[#24417c]/60 font-bold mt-1">
                        {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                        @if($item->jam_selesai) – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }} WITA @endif
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Form Tambah Sesi Mengajar --}}
    <div class="bg-white rounded-2xl border-2 border-[#24417c]/20 overflow-hidden">
        <div class="px-5 py-4 sm:px-6 sm:py-5 border-b-2 border-[#24417c]/10">
            <h3 class="text-base font-black text-[#24417c]">Tambah Sesi Tambahan / Pengganti</h3>
            <p class="text-[#24417c]/60 font-medium text-xs mt-0.5">Isi form di bawah jika mengajar di luar jadwal tetap hari ini.</p>
        </div>

        <form method="POST" action="{{ route('guru.aktivitas.store') }}" class="p-5 sm:p-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Mata Pelajaran --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-black text-[#24417c] mb-1.5 uppercase tracking-wider">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <select name="mata_pelajaran"
                        class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-3 py-2.5 text-[#24417c] font-medium focus:outline-none transition duration-200 bg-white text-sm">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach ($mapels as $mapel)
                            <option value="{{ $mapel }}" {{ old('mata_pelajaran') === $mapel ? 'selected' : '' }}>
                                {{ $mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kelas (Tingkat, Jurusan, Rombel) --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-black text-[#24417c] mb-1.5 uppercase tracking-wider">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <select name="tingkat" required class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-2 py-2.5 text-[#24417c] font-medium text-sm focus:outline-none transition bg-white">
                            <option value="">Tingkat</option>
                            @foreach ($tingkats as $t)
                                <option value="{{ $t }}" {{ old('tingkat') === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                        <select name="jurusan" required class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-2 py-2.5 text-[#24417c] font-medium text-sm focus:outline-none transition bg-white">
                            <option value="">Jurusan</option>
                            @foreach ($jurusans as $j)
                                <option value="{{ $j }}" {{ old('jurusan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                        <select name="rombel" required class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-2 py-2.5 text-[#24417c] font-medium text-sm focus:outline-none transition bg-white">
                            <option value="">Rombel</option>
                            @foreach ($rombels as $r)
                                <option value="{{ $r }}" {{ old('rombel') === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jam Ke- --}}
                <div>
                    <label class="block text-xs font-black text-[#24417c] mb-1.5 uppercase tracking-wider">
                        Jam Ke- <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jam_ke" value="{{ old('jam_ke') }}" min="1" required
                        class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-3 py-2.5 text-[#24417c] font-medium focus:outline-none transition duration-200 text-sm">
                </div>

                {{-- Jam Mulai --}}
                <div>
                    <label class="block text-xs font-black text-[#24417c] mb-1.5 uppercase tracking-wider">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                        class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-3 py-2.5 text-[#24417c] font-medium focus:outline-none transition duration-200 text-sm">
                </div>

                {{-- Jam Selesai --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-black text-[#24417c] mb-1.5 uppercase tracking-wider">
                        Jam Selesai
                    </label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                        class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-3 py-2.5 text-[#24417c] font-medium focus:outline-none transition duration-200 text-sm">
                </div>


            </div>

            {{-- Tombol Simpan --}}
            <div class="mt-5 flex justify-end">
                <button type="submit"
                    class="bg-[#24417c] text-white font-bold text-sm px-6 py-2.5 rounded-xl hover:bg-[#162d57] transition duration-200 shadow-md shadow-[#24417c]/20 w-full sm:w-auto">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    {{-- Riwayat Aktivitas Mengajar --}}
    <div class="bg-white rounded-2xl border-2 border-[#24417c]/20 overflow-hidden">
        <div class="px-5 py-4 sm:px-6 sm:py-5 border-b-2 border-[#24417c]/10">
            <h3 class="text-base font-black text-[#24417c]">Riwayat Aktivitas Mengajar</h3>
            <p class="text-[#24417c]/70 font-medium text-xs mt-0.5">Semua sesi mengajar yang telah Anda catat.</p>
            <form method="GET" action="{{ route('guru.aktivitas') }}" class="mt-3 flex flex-wrap items-center gap-2">
                <input type="date" name="tanggal_riwayat" value="{{ $tanggalRiwayat }}"
                    class="flex-1 min-w-0 border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-3 py-2 text-[#24417c] font-medium focus:outline-none transition text-sm bg-white">
                <button type="submit" class="bg-[#24417c] hover:bg-[#1a2f5c] text-white px-4 py-2 rounded-xl font-bold transition text-sm">Terapkan</button>
                @if(request()->has('tanggal_riwayat'))
                    <a href="{{ route('guru.aktivitas') }}" class="px-3 py-2 rounded-xl border-2 border-slate-200 text-slate-500 font-bold text-sm transition bg-slate-50">Reset</a>
                @endif
            </form>
        </div>

        {{-- Mobile: Card List --}}
        <div class="block sm:hidden divide-y-2 divide-[#24417c]/10">
            @forelse ($riwayat as $item)
            <div class="p-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <p class="font-black text-[#24417c] text-sm">{{ $item->mata_pelajaran }}</p>
                        <p class="text-xs text-[#24417c]/60 font-semibold mt-0.5">{{ $item->kelas }}</p>
                    </div>
                    <span class="shrink-0 text-xs font-bold text-[#24417c]/70 bg-[#24417c]/10 px-2 py-0.5 rounded-lg">Jam ke-{{ $item->jam_ke }}</span>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-500 font-semibold">
                    <span>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</span>
                    <span>{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}{{ $item->jam_selesai ? ' – '.\Carbon\Carbon::parse($item->jam_selesai)->format('H:i') : '' }}</span>
                </div>
            </div>
            @empty
            <div class="py-10 text-center text-[#24417c]/40 text-sm font-medium">Belum ada riwayat aktivitas mengajar.</div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-[#24417c] text-white">
                        <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider">Kelas</th>
                        <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider">Jam ke-</th>
                        <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-[#24417c]/10">
                    @forelse ($riwayat as $item)
                    <tr class="hover:bg-[#24417c]/5 transition duration-200">
                        <td class="py-3.5 px-4 font-bold text-[#24417c] whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td class="py-3.5 px-4 font-bold text-[#24417c] text-sm">{{ $item->mata_pelajaran }}</td>
                        <td class="py-3.5 px-4 font-medium text-[#24417c] text-sm">{{ $item->kelas }}</td>
                        <td class="py-3.5 px-4 font-bold text-[#24417c] text-sm">{{ $item->jam_ke }}</td>
                        <td class="py-3.5 px-4 font-medium text-[#24417c] whitespace-nowrap text-sm">
                            {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                            @if ($item->jam_selesai) – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }} @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-10 text-center text-[#24417c]/40 text-sm">Belum ada riwayat aktivitas mengajar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($riwayat->hasPages())
        <div class="px-5 py-4 border-t-2 border-[#24417c]/10">
            {{ $riwayat->links() }}
        </div>
        @endif
    </div>

</div>
</x-app-layout>
