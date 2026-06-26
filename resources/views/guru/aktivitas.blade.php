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
                                    </div>
                                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-[#24417c]/60 font-bold">
                                        <span>{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                                            @if($item->jam_selesai) – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }} WITA @endif
                                        </span>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Form Tambah Sesi Mengajar --}}
            <div class="bg-white rounded-3xl shadow-lg border-2 border-[#24417c]/20 overflow-hidden">
                <div class="p-6 sm:p-8 border-b-2 border-[#24417c]/10">
                    <h3 class="text-xl font-black text-[#24417c]">Tambah Sesi Tambahan / Pengganti</h3>
                    <p class="text-[#24417c]/60 font-medium text-sm mt-1">Isi form di bawah hanya jika Anda mengajar kelas di luar jadwal tetap Anda hari ini.</p>
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
                                @foreach ($mapels as $mapel)
                                    <option value="{{ $mapel }}" {{ old('mata_pelajaran') === $mapel ? 'selected' : '' }}>
                                        {{ $mapel }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- <p class="text-xs text-[#24417c]/50 mt-1 font-medium">Daftar dapat diubah di <code class="bg-[#24417c]/10 px-1 rounded">config/sekolah.php</code></p> --}}
                        </div>

                        {{-- Kelas (Tingkat, Jurusan, Rombel) --}}
                        <div>
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <select name="tingkat" required class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-2 py-3 text-[#24417c] font-medium text-sm focus:outline-none transition bg-white">
                                    <option value="">Tingkat</option>
                                    @foreach ($tingkats as $t)
                                        <option value="{{ $t }}" {{ old('tingkat') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                                <select name="jurusan" required class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-2 py-3 text-[#24417c] font-medium text-sm focus:outline-none transition bg-white">
                                    <option value="">Jurusan</option>
                                    @foreach ($jurusans as $j)
                                        <option value="{{ $j }}" {{ old('jurusan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                                <select name="rombel" required class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-2 py-3 text-[#24417c] font-medium text-sm focus:outline-none transition bg-white">
                                    <option value="">Rombel</option>
                                    @foreach ($rombels as $r)
                                        <option value="{{ $r }}" {{ old('rombel') === $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Jam Ke- --}}
                        <div>
                            <label class="block text-sm font-black text-[#24417c] mb-2 uppercase tracking-wider">
                                Jam Ke- <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="jam_ke" value="{{ old('jam_ke') }}" min="1" required
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-4 py-3 text-[#24417c] font-medium focus:outline-none transition duration-200">
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
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-[#24417c] text-white">
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider text-center">Tanggal</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider text-center">Mata Pelajaran</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider text-center">Kelas</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider text-center">Jam ke-</th>
                                <th class="py-4 px-5 font-bold text-sm uppercase tracking-wider text-center">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-[#24417c]/10">
                            @forelse ($riwayat as $item)
                                <tr class="hover:bg-[#24417c]/5 transition duration-200">
                                    <td class="py-4 px-5 font-bold text-[#24417c] whitespace-nowrap text-center">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-5 font-bold text-[#24417c] text-center">{{ $item->mata_pelajaran }}</td>
                                    <td class="py-4 px-5 font-medium text-[#24417c] text-center">{{ $item->kelas }}</td>
                                    <td class="py-4 px-5 text-center font-bold text-[#24417c]">
                                            {{ $item->jam_ke }}
                                    </td>
                                    <td class="py-4 px-5 font-medium text-[#24417c] whitespace-nowrap text-center">
                                        {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                                        @if ($item->jam_selesai)
                                            – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 px-6 text-center font-medium text-[#24417c]/60">
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
