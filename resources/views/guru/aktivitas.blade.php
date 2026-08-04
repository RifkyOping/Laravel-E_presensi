<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Aktivitas Mengajar</span>
    </x-slot>

    <div x-data="{
        showModal: false,
        modalData: {
            status: '',
            statusLabel: '',
            statusClass: '',
            verifier: '',
            tanggal: '',
            waktuVerif: '',
            mapel: '',
            kelas: '',
            jamKe: '',
            waktu: '',
            catatan: '',
            foto: ''
        },
        openVerifModal(data) {
            this.modalData = data;
            this.showModal = true;
        },
        showImageModal: false,
        imageUrl: '',
        openImage(url) {
            this.imageUrl = url;
            this.showImageModal = true;
        }
    }" class="space-y-6">

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
        @if (session('info'))
            <div class="bg-blue-50 border-2 border-blue-400 text-blue-800 font-bold px-6 py-4 rounded-2xl shadow flex items-center gap-3">
                <span class="text-2xl">ℹ️</span>
                <span>{{ session('info') }}</span>
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
        <div class="bg-white rounded-2xl border-2 border-[#24417c]/20 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b-2 border-[#24417c]/10 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-base font-black text-[#24417c]">Sesi Mengajar Hari Ini</h3>
                    <p class="text-[#24417c]/60 font-medium text-xs mt-0.5">{{ $hariIni->count() }} sesi tercatat</p>
                </div>
                <span class="bg-[#24417c] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">Hari Ini</span>
            </div>
            <div class="divide-y-2 divide-[#24417c]/10">
                @foreach ($hariIni as $item)
                @php
                    $isVerified = (bool)($item->status_verifikasi || $item->verified_at);
                @endphp
                <div x-data="{ expanded: false }" class="transition">
                    {{-- Header Card (Klik untuk buka/tutup jika sudah ada verifikasi) --}}
                    <div @if($isVerified) @click="expanded = !expanded" @endif
                         class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition select-none {{ $isVerified ? 'cursor-pointer hover:bg-[#24417c]/[0.03]' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0 w-12 h-12 rounded-xl bg-[#24417c] text-white flex flex-col items-center justify-center shadow-md shadow-[#24417c]/20">
                                <span class="text-[0.6rem] font-bold uppercase tracking-wider text-white/70 leading-none">Jam</span>
                                <span class="text-xl font-black leading-none mt-0.5">{{ $item->jam_ke }}</span>
                            </div>
                            <div class="flex-1 min-w-0 text-left">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-sm sm:text-base font-black text-[#24417c] truncate">{{ $item->mata_pelajaran }}</h4>
                                    <span class="bg-[#24417c]/10 text-[#24417c] text-xs font-bold px-2.5 py-0.5 rounded-full whitespace-nowrap">{{ $item->kelas }}</span>
                                </div>
                                <p class="text-xs text-[#24417c]/60 font-bold mt-1 flex items-center gap-1.5 text-left">
                                    <svg class="w-3.5 h-3.5 text-[#24417c]/50 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}@if($item->jam_selesai) – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }} WITA @endif</span>
                                </p>
                            </div>
                        </div>

                        {{-- Status Verifikasi Badge & Toggle Indicator --}}
                        <div class="flex items-center gap-2 self-start sm:self-center">
                            @if($item->status_verifikasi === 'mengajar')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Terverifikasi Mengajar</span>
                                </span>
                            @elseif($item->status_verifikasi === 'tidak_mengajar')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Terverifikasi Tidak Mengajar</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Belum Diverifikasi</span>
                                </span>
                            @endif

                            @if($isVerified)
                                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 transition-all duration-200" :class="{ 'rotate-180 bg-[#24417c]/10 text-[#24417c]': expanded }">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Detail Verifikasi (Muncul Saat Card Diklik) --}}
                    @if($isVerified)
                    <div x-show="expanded" 
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 -translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 translate-y-0" 
                         x-transition:leave-end="opacity-0 -translate-y-2" 
                         style="display: none;" 
                         class="px-4 pb-5 sm:px-5">
                        <div class="rounded-2xl border {{ $item->status_verifikasi === 'mengajar' ? 'border-emerald-200 bg-emerald-50/40' : 'border-rose-200 bg-rose-50/40' }} p-4 sm:p-5 text-left space-y-4">
                            {{-- Header Verifikasi --}}
                            <div class="flex items-center gap-3 pb-3 border-b {{ $item->status_verifikasi === 'mengajar' ? 'border-emerald-200/70' : 'border-rose-200/70' }}">
                                <div class="w-8 h-8 rounded-xl {{ $item->status_verifikasi === 'mengajar' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }} flex items-center justify-center font-black shadow-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div class="text-left flex-1 min-w-0">
                                    <h5 class="text-xs font-bold uppercase tracking-wider {{ $item->status_verifikasi === 'mengajar' ? 'text-emerald-950' : 'text-rose-950' }}">Detail Verifikasi Sesi Mengajar</h5>
                                    <p class="text-xs {{ $item->status_verifikasi === 'mengajar' ? 'text-emerald-700' : 'text-rose-700' }} font-medium mt-0.5">
                                        Diverifikasi oleh <span class="font-bold">{{ $item->verifier?->name ?? 'Petugas Verifikasi' }}</span>
                                        @if($item->verified_at)
                                            pada {{ \Carbon\Carbon::parse($item->verified_at)->translatedFormat('d M Y H:i') }} WITA
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Content Grid: Catatan & Foto --}}
                            <div class="grid grid-cols-1 {{ $item->foto_verifikasi ? 'md:grid-cols-12' : '' }} gap-3.5 items-stretch text-left">
                                {{-- Catatan --}}
                                <div class="{{ $item->foto_verifikasi ? 'md:col-span-7 lg:col-span-8' : 'w-full' }} flex flex-col text-left">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5 text-left">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                        <span>Catatan Petugas :</span>
                                    </p>
                                    <div class="flex-1 bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs text-left">
                                        <p class="text-sm text-slate-800 font-medium leading-relaxed whitespace-pre-line text-left break-words">{{ trim($item->catatan_kurikulum) ?: 'Tidak ada catatan khusus.' }}</p>
                                    </div>
                                </div>

                                {{-- Foto Verifikasi --}}
                                @if($item->foto_verifikasi)
                                <div class="md:col-span-5 lg:col-span-4 flex flex-col text-left">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5 text-left">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Foto Verifikasi :</span>
                                    </p>
                                    <div class="flex-1 bg-white rounded-xl p-3 border border-slate-200/80 shadow-2xs flex items-center gap-3 text-left">
                                        <button type="button" @click.stop="openImage('{{ Storage::url($item->foto_verifikasi) }}')"
                                            class="group relative w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border border-slate-200 shrink-0 focus:outline-none focus:ring-2 focus:ring-[#24417c] bg-slate-100 cursor-pointer">
                                            <img src="{{ Storage::url($item->foto_verifikasi) }}" alt="Foto Bukti Verifikasi" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                            </div>
                                        </button>
                                        <div class="text-xs text-left">
                                            <button type="button" @click.stop="openImage('{{ Storage::url($item->foto_verifikasi) }}')" class="mt-1.5 text-[#24417c] font-bold hover:underline inline-flex items-center gap-1 cursor-pointer">
                                                <span>Perbesar Foto</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Form Tambah Sesi Mengajar --}}
        <div x-data="{ showForm: {{ $errors->any() ? 'true' : 'false' }} }" class="bg-white rounded-2xl border-2 border-[#24417c]/20 overflow-hidden shadow-sm">
            <button type="button" @click="showForm = !showForm" class="w-full px-5 py-4 sm:px-6 sm:py-5 border-b-2 border-[#24417c]/10 flex items-center justify-between text-left focus:outline-none group hover:bg-[#24417c]/5 transition-colors">
                <div>
                    <h3 class="text-base font-black text-[#24417c]">Tambah Sesi Tambahan / Pengganti</h3>
                    <p class="text-[#24417c]/60 font-medium text-xs mt-0.5">Isi form di bawah jika mengajar di luar jadwal tetap hari ini.</p>
                </div>
                <svg class="w-5 h-5 text-[#24417c]/60 transition-transform duration-200" :class="{ 'rotate-180': showForm }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="showForm" x-transition style="display: none;" class="bg-slate-50/50">
                <form method="POST" action="{{ route('guru.aktivitas.store') }}" class="p-5 sm:p-6">
                    @csrf

                    <div class="grid grid-cols-2 gap-3 sm:gap-4">

                        {{-- Mata Pelajaran --}}
                        <div class="col-span-2">
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

                        {{-- Kelas --}}
                        <div class="col-span-1">
                            <label class="block text-xs font-black text-[#24417c] mb-1.5 uppercase tracking-wider">
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            <select name="kelas" required
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-3 py-2.5 text-[#24417c] font-medium focus:outline-none transition duration-200 bg-white text-sm">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelasList as $kelas)
                                    @php $namaKelas = $kelas->tingkat . ' ' . $kelas->jurusan . ' ' . $kelas->rombel; @endphp
                                    <option value="{{ $namaKelas }}" {{ old('kelas') === $namaKelas ? 'selected' : '' }}>
                                        {{ $namaKelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jam Ke- --}}
                        <div class="col-span-1">
                            <label class="block text-xs font-black text-[#24417c] mb-1.5 uppercase tracking-wider">
                                Jam Ke- <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="jam_ke" value="{{ old('jam_ke') }}" min="1" required
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-3 py-2.5 text-[#24417c] font-medium focus:outline-none transition duration-200 text-sm">
                        </div>

                        {{-- Jam Mulai --}}
                        <div class="col-span-1">
                            <label class="block text-xs font-black text-[#24417c] mb-1.5 uppercase tracking-wider">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                                class="w-full border-2 border-[#24417c]/20 focus:border-[#24417c] rounded-xl px-3 py-2.5 text-[#24417c] font-medium focus:outline-none transition duration-200 text-sm">
                        </div>

                        {{-- Jam Selesai --}}
                        <div class="col-span-1">
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
        </div>

        {{-- Riwayat Aktivitas Mengajar --}}
        <div class="bg-white rounded-2xl border-2 border-[#24417c]/20 overflow-hidden shadow-sm">
            <div class="px-5 py-4 sm:px-6 sm:py-5 border-b-2 border-[#24417c]/10 bg-slate-50/50">
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
                <div class="p-4 space-y-2.5 text-left">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-black text-[#24417c] text-sm">{{ $item->mata_pelajaran }}</p>
                            <p class="text-xs text-[#24417c]/60 font-semibold mt-0.5">{{ $item->kelas }}</p>
                        </div>
                        <span class="shrink-0 text-xs font-bold text-[#24417c]/70 bg-[#24417c]/10 px-2 py-0.5 rounded-lg">Jam ke-{{ $item->jam_ke }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500 font-semibold">
                        <span>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</span>
                        <span>{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}{{ $item->jam_selesai ? ' – '.\Carbon\Carbon::parse($item->jam_selesai)->format('H:i') : '' }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <div>
                            @if($item->status_verifikasi === 'mengajar')
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Terverifikasi Mengajar
                                </span>
                            @elseif($item->status_verifikasi === 'tidak_mengajar')
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                    <svg class="w-3 h-3 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Terverifikasi Tidak Mengajar
                                </span>
                            @else
                                <span class="text-[11px] font-medium text-slate-400">Belum Diverifikasi</span>
                            @endif
                        </div>
                        @if($item->status_verifikasi || $item->verified_at)
                        <button type="button" @click="openVerifModal({
                            status: '{{ $item->status_verifikasi }}',
                            statusLabel: '{{ $item->status_verifikasi === 'mengajar' ? 'Terverifikasi Mengajar' : 'Terverifikasi Tidak Mengajar' }}',
                            statusClass: '{{ $item->status_verifikasi === 'mengajar' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}',
                            verifier: '{{ addslashes($item->verifier?->name ?? 'Petugas Verifikasi') }}',
                            tanggal: '{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}',
                            waktuVerif: '{{ $item->verified_at ? \Carbon\Carbon::parse($item->verified_at)->translatedFormat('d M Y H:i') . ' WITA' : '-' }}',
                            mapel: '{{ addslashes($item->mata_pelajaran) }}',
                            kelas: '{{ addslashes($item->kelas) }}',
                            jamKe: '{{ $item->jam_ke }}',
                            waktu: '{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}{{ $item->jam_selesai ? ' - ' . \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') : '' }} WITA',
                            catatan: '{{ addslashes(trim($item->catatan_kurikulum ?? '')) }}',
                            foto: '{{ $item->foto_verifikasi ? Storage::url($item->foto_verifikasi) : '' }}'
                        })" class="text-xs font-bold text-[#24417c] hover:underline flex items-center gap-1 cursor-pointer">
                            <span>Lihat Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @endif
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
                            <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider">Status Verifikasi</th>
                            <th class="py-3 px-4 font-bold text-xs uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-[#24417c]/10">
                        @forelse ($riwayat as $item)
                        <tr class="hover:bg-[#24417c]/5 transition duration-200">
                            <td class="py-3.5 px-4 font-bold text-[#24417c] whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                            <td class="py-3.5 px-4 font-bold text-[#24417c] text-sm">{{ $item->mata_pelajaran }}</td>
                            <td class="py-3.5 px-4 font-medium text-[#24417c] text-sm">{{ $item->kelas }}</td>
                            <td class="py-3.5 px-4 font-bold text-[#24417c] text-sm">{{ $item->jam_ke }}</td>
                            <td class="py-3.5 px-4 font-medium text-[#24417c] whitespace-nowrap text-sm">
                                {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                                @if ($item->jam_selesai) – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }} @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->status_verifikasi === 'mengajar')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                        <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Terverifikasi Mengajar
                                    </span>
                                @elseif($item->status_verifikasi === 'tidak_mengajar')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                        <svg class="w-3 h-3 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Terverifikasi Tidak Mengajar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium text-slate-400 bg-slate-100 border border-slate-200">
                                        Belum Diverifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->status_verifikasi || $item->verified_at)
                                    <button type="button" @click="openVerifModal({
                                        status: '{{ $item->status_verifikasi }}',
                                        statusLabel: '{{ $item->status_verifikasi === 'mengajar' ? 'Terverifikasi Mengajar' : 'Terverifikasi Tidak Mengajar' }}',
                                        statusClass: '{{ $item->status_verifikasi === 'mengajar' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}',
                                        verifier: '{{ addslashes($item->verifier?->name ?? 'Petugas Verifikasi') }}',
                                        tanggal: '{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}',
                                        waktuVerif: '{{ $item->verified_at ? \Carbon\Carbon::parse($item->verified_at)->translatedFormat('d M Y H:i') . ' WITA' : '-' }}',
                                        mapel: '{{ addslashes($item->mata_pelajaran) }}',
                                        kelas: '{{ addslashes($item->kelas) }}',
                                        jamKe: '{{ $item->jam_ke }}',
                                        waktu: '{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}{{ $item->jam_selesai ? ' - ' . \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') : '' }} WITA',
                                        catatan: '{{ addslashes(trim($item->catatan_kurikulum ?? '')) }}',
                                        foto: '{{ $item->foto_verifikasi ? Storage::url($item->foto_verifikasi) : '' }}'
                                    })" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#24417c]/10 text-[#24417c] hover:bg-[#24417c] hover:text-white transition font-bold text-xs shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </button>
                                @else
                                    <span class="text-xs text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="py-10 text-center text-[#24417c]/40 text-sm">Belum ada riwayat aktivitas mengajar.</td></tr>
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

        {{-- MODAL DETAIL VERIFIKASI RIWAYAT (DENGAN TELEPORT KE BODY) --}}
        <template x-teleport="body">
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 @keydown.escape.window="showModal = false"
                 class="fixed inset-0 z-[99998] flex items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs" 
                 style="display: none;">
                
                {{-- Backdrop click --}}
                <div class="fixed inset-0" @click="showModal = false"></div>

                <div class="relative z-10 bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100 text-left">
                    {{-- Modal Header --}}
                    <div class="bg-gradient-to-r from-[#24417c] to-[#1e3a6e] px-6 py-5 text-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center font-bold shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <h4 class="font-black text-base text-white">Detail Verifikasi Mengajar</h4>
                                <p class="text-xs text-white/70" x-text="modalData.mapel + ' · ' + modalData.kelas"></p>
                            </div>
                        </div>
                        <button type="button" @click="showModal = false" class="text-white/70 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto text-left">
                        {{-- Status Banner --}}
                        <div class="p-3.5 rounded-2xl border flex items-center justify-between" :class="modalData.statusClass">
                            <div class="text-left">
                                <p class="text-[10px] uppercase font-bold tracking-wider opacity-75">Status Hasil Verifikasi</p>
                                <p class="font-black text-sm mt-0.5" x-text="modalData.statusLabel"></p>
                            </div>
                        </div>

                        {{-- Grid Informasi Sesi --}}
                        <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs text-left">
                            <div class="text-left">
                                <p class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Tanggal Sesi</p>
                                <p class="font-bold text-slate-800 mt-0.5" x-text="modalData.tanggal"></p>
                            </div>
                            <div class="text-left">
                                <p class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Waktu Sesi</p>
                                <p class="font-bold text-slate-800 mt-0.5" x-text="'Jam ke-' + modalData.jamKe + ' (' + modalData.waktu + ')'"></p>
                            </div>
                            <div class="text-left">
                                <p class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Diverifikasi Oleh</p>
                                <p class="font-bold text-slate-800 mt-0.5" x-text="modalData.verifier"></p>
                            </div>
                            <div class="text-left">
                                <p class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Waktu Verifikasi</p>
                                <p class="font-bold text-slate-800 mt-0.5" x-text="modalData.waktuVerif"></p>
                            </div>
                        </div>

                        {{-- Catatan Verifikasi --}}
                        <div class="text-left">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 text-left">Catatan :</label>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm text-slate-800 font-medium whitespace-pre-line leading-relaxed text-left break-words"
                                 x-text="modalData.catatan || 'Tidak ada catatan khusus.'"></div>
                        </div>

                        {{-- Foto Verifikasi --}}
                        <template x-if="modalData.foto">
                            <div class="text-left">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 text-left">Foto Aktivitas :</label>
                                <div class="border border-slate-200 rounded-2xl p-2 bg-slate-50 overflow-hidden text-center">
                                    <img :src="modalData.foto" alt="Foto Verifikasi" class="max-h-64 mx-auto rounded-xl object-contain shadow-sm cursor-pointer hover:opacity-95 transition"
                                         @click="openImage(modalData.foto)">
                                    <p class="text-[11px] text-slate-400 font-semibold mt-2">Klik foto untuk melihat ukuran penuh</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                        <button type="button" @click="showModal = false" class="bg-[#24417c] text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-[#162d57] transition shadow-sm cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- MODAL LIGHTBOX FOTO PENUH (DENGAN TELEPORT KE BODY & FULLSCREEN BLUR) --}}
        <template x-teleport="body">
            <div x-show="showImageModal" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 @keydown.escape.window="showImageModal = false"
                 class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/85 backdrop-blur-md" 
                 style="display: none;">
                
                {{-- Backdrop click --}}
                <div class="fixed inset-0" @click="showImageModal = false"></div>

                {{-- Modal Box Content --}}
                <div class="relative z-10 max-w-4xl w-full max-h-[90vh] flex flex-col items-center">
                    {{-- Close Button on Top Right --}}
                    <button type="button" @click="showImageModal = false" 
                            class="absolute -top-12 right-0 sm:right-0 text-white hover:text-slate-200 font-bold text-xs sm:text-sm flex items-center gap-1.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-1.5 rounded-full transition shadow-lg cursor-pointer">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tutup</span>
                    </button>

                    <div class="bg-black/40 rounded-2xl overflow-hidden border border-white/20 shadow-2xl p-2">
                        <img :src="imageUrl" alt="Foto Bukti Verifikasi" class="max-w-full max-h-[78vh] rounded-xl object-contain mx-auto block shadow-lg">
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
