<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');
        
        @media (max-width: 768px) {
            .app-tbl th, .app-tbl td {
                padding: 0.4rem 0.2rem !important;
                font-size: 0.6rem !important;
                white-space: normal !important;
                word-break: break-word !important;
            }
            .app-tbl .w-8.h-8 {
                width: 1.25rem !important;
                height: 1.25rem !important;
                font-size: 0.5rem !important;
            }
            .app-tbl .app-badge {
                font-size: 0.5rem !important;
                padding: 0.1rem 0.25rem !important;
            }
            .app-tbl svg {
                width: 0.9rem !important;
                height: 0.9rem !important;
            }
            .overflow-x-auto {
                overflow-x: hidden !important;
            }
        }
    </style>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Persetujuan Izin & Sakit</span>
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
        <div class="alert-success">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- Pengajuan Murid --}}
        <div class="app-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Pengajuan Murid
                </h3>
            </div>
            
            {{-- Mobile Card View --}}
            <div class="p-6 bg-slate-50/30 block md:hidden">
                @if($pengajuanSiswa->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($pengajuanSiswa as $p)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow p-5 flex flex-col h-full relative overflow-hidden">
                        {{-- Top Section: User & Status --}}
                        <div class="flex justify-between items-start mb-4 gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-[#1e3a6e] to-[#2d5099] text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                                    {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-800 text-sm line-clamp-1" title="{{ $p->user->name ?? 'User Dihapus' }}">{{ $p->user->name ?? 'User Dihapus' }}</h4>
                                    <p class="text-[0.7rem] text-slate-500 font-semibold mt-0.5 truncate">
                                        {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                        @if($p->tanggal_selesai && \Carbon\Carbon::parse($p->tanggal_selesai)->format('Y-m-d') !== \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d'))
                                            <span class="mx-1">-</span>{{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="app-badge {{ $p->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize flex-shrink-0">
                                {{ $p->status }}
                            </span>
                        </div>

                        {{-- Middle Section: Details --}}
                        <div class="flex-1 flex flex-col gap-3">
                            @if($p->guru)
                            <div class="flex items-start gap-2 text-sm bg-indigo-50/50 p-2.5 rounded-xl border border-indigo-50">
                                <svg class="w-4 h-4 text-indigo-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <div>
                                    <span class="text-[0.65rem] font-bold text-indigo-400 uppercase tracking-wider block">Tujuan Pengajuan</span>
                                    <span class="text-indigo-900 font-semibold text-xs">{{ $p->guru->name }}</span>
                                </div>
                            </div>
                            @endif

                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex-1">
                                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Keterangan</p>
                                <p class="text-sm text-slate-700 leading-relaxed line-clamp-3" title="{{ $p->keterangan }}">{{ $p->keterangan ?? 'Tidak ada keterangan' }}</p>
                            </div>

                            @if($p->file_bukti)
                            <div>
                                <button type="button" onclick="openPreviewModal('{{ asset('storage/' . $p->file_bukti) }}', '{{ $p->user->name ?? 'User' }}')" class="inline-flex items-center gap-1.5 text-[#1e3a6e] hover:text-[#2d5099] font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-colors border border-blue-100 w-fit cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat File Bukti
                                </button>
                            </div>
                            @endif
                        </div>

                        {{-- Bottom Section: Actions --}}
                        <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-2 gap-3">
                            <form action="{{ route('admin.persetujuan-absensi.reject', ['type' => 'murid', 'id' => $p->id]) }}" method="POST" class="w-full">
                                @csrf
                                <button type="button" onclick="confirmReject(this)" class="w-full flex items-center justify-center gap-1.5 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 px-4 py-2.5 rounded-xl font-bold text-[0.8rem] transition-all duration-200 border-2 border-red-100 hover:border-red-500 shadow-sm" title="Tolak Pengajuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak
                                </button>
                            </form>
                            <form action="{{ route('admin.persetujuan-absensi.approve', ['type' => 'murid', 'id' => $p->id]) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-xl font-bold text-[0.8rem] transition-all duration-200 shadow-sm hover:shadow-md" title="Setujui Pengajuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Setujui
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center bg-white rounded-2xl border border-slate-100 border-dashed">
                    <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-slate-500 font-semibold">Tidak ada pengajuan murid yang menunggu persetujuan.</p>
                </div>
                @endif
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full app-tbl">
                    <thead>
                        <tr>
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Nama Murid</th>
                            <th class="text-center">Judul Pengajuan</th>
                            <th class="text-left">Keterangan</th>
                            <th class="text-center">Bukti</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuanSiswa as $p)
                        <tr>
                            <td class="font-semibold whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                @if($p->tanggal_selesai && \Carbon\Carbon::parse($p->tanggal_selesai)->format('Y-m-d') !== \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d'))
                                    <br><span class="text-xs text-slate-500 font-normal">s/d {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $p->user->name ?? 'User Dihapus' }}</div>
                                        @if($p->guru)
                                            <div class="text-[0.65rem] text-slate-500 mt-0.5 font-semibold">Tujuan: {{ $p->guru->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="app-badge {{ $p->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="max-w-xs truncate" title="{{ $p->keterangan }}">{{ $p->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                @if($p->file_bukti)
                                    <button type="button" onclick="openPreviewModal('{{ asset('storage/' . $p->file_bukti) }}', '{{ $p->user->name ?? 'User' }}')" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition cursor-pointer bg-transparent border-none p-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-slate-400 italic text-xs">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('admin.persetujuan-absensi.approve', ['type' => 'murid', 'id' => $p->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white p-2 rounded-xl shadow-sm transition duration-200 border border-emerald-200 hover:border-emerald-600" title="Setujui Pengajuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.persetujuan-absensi.reject', ['type' => 'murid', 'id' => $p->id]) }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="confirmReject(this)" class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white p-2 rounded-xl shadow-sm transition duration-200 border border-red-200 hover:border-red-600" title="Tolak Pengajuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada pengajuan murid yang menunggu persetujuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pengajuan Guru --}}
        <div class="app-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#2d5099] text-[1.25rem]"> account_circle</span>
                    Pengajuan Guru
                </h3>
            </div>
            
            {{-- Mobile Card View --}}
            <div class="p-6 bg-slate-50/30 block md:hidden">
                @if($pengajuanGuru->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($pengajuanGuru as $p)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow p-5 flex flex-col h-full relative overflow-hidden">
                        {{-- Top Section: User & Status --}}
                        <div class="flex justify-between items-start mb-4 gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-[#2d5099] to-[#4372d8] text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                                    {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-800 text-sm line-clamp-1" title="{{ $p->user->name ?? 'User Dihapus' }}">{{ $p->user->name ?? 'User Dihapus' }}</h4>
                                    <p class="text-[0.7rem] text-slate-500 font-semibold mt-0.5 truncate">
                                        {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                        @if($p->tanggal_selesai && \Carbon\Carbon::parse($p->tanggal_selesai)->format('Y-m-d') !== \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d'))
                                            <span class="mx-1">-</span>{{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="app-badge {{ $p->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize flex-shrink-0">
                                {{ $p->judul_pengajuan ?? $p->status }}
                            </span>
                        </div>

                        {{-- Middle Section: Details --}}
                        <div class="flex-1 flex flex-col gap-3">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex-1">
                                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Keterangan</p>
                                <p class="text-sm text-slate-700 leading-relaxed line-clamp-3" title="{{ $p->keterangan }}">{{ $p->keterangan ?? 'Tidak ada keterangan' }}</p>
                            </div>

                            @if($p->file_bukti)
                            <div>
                                <button type="button" onclick="openPreviewModal('{{ asset('storage/' . $p->file_bukti) }}', '{{ $p->user->name ?? 'User' }}')" class="inline-flex items-center gap-1.5 text-[#1e3a6e] hover:text-[#2d5099] font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-colors border border-blue-100 w-fit cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat File Bukti
                                </button>
                            </div>
                            @endif
                        </div>

                        {{-- Bottom Section: Actions --}}
                        <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-2 gap-3">
                            <form action="{{ route('admin.persetujuan-absensi.reject', ['type' => 'guru', 'id' => $p->id]) }}" method="POST" class="w-full">
                                @csrf
                                <button type="button" onclick="confirmReject(this)" class="w-full flex items-center justify-center gap-1.5 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 px-4 py-2.5 rounded-xl font-bold text-[0.8rem] transition-all duration-200 border-2 border-red-100 hover:border-red-500 shadow-sm" title="Tolak Pengajuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak
                                </button>
                            </form>
                            <form action="{{ route('admin.persetujuan-absensi.approve', ['type' => 'guru', 'id' => $p->id]) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-xl font-bold text-[0.8rem] transition-all duration-200 shadow-sm hover:shadow-md" title="Setujui Pengajuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Setujui
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 px-4 text-center bg-white rounded-2xl border border-slate-100 border-dashed">
                    <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-slate-500 font-semibold">Tidak ada pengajuan guru yang menunggu persetujuan.</p>
                </div>
                @endif
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full app-tbl">
                    <thead>
                        <tr>
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Nama Guru</th>
                            <th class="text-center">Judul Pengajuan</th>
                            <th class="text-left">Keterangan</th>
                            <th class="text-center">Bukti</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuanGuru as $p)
                        <tr>
                            <td class="font-semibold whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                @if($p->tanggal_selesai && \Carbon\Carbon::parse($p->tanggal_selesai)->format('Y-m-d') !== \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d'))
                                    <br><span class="text-xs text-slate-500 font-normal">s/d {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#2d5099] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-800">{{ $p->user->name ?? 'User Dihapus' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="app-badge {{ $p->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize">
                                    {{ $p->judul_pengajuan ?? $p->status }}
                                </span>
                            </td>
                            <td class="max-w-xs truncate" title="{{ $p->keterangan }}">{{ $p->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                @if($p->file_bukti)
                                    <button type="button" onclick="openPreviewModal('{{ asset('storage/' . $p->file_bukti) }}', '{{ $p->user->name ?? 'User' }}')" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition cursor-pointer bg-transparent border-none p-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-slate-400 italic text-xs">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('admin.persetujuan-absensi.approve', ['type' => 'guru', 'id' => $p->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white p-2 rounded-xl shadow-sm transition duration-200 border border-emerald-200 hover:border-emerald-600" title="Setujui Pengajuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.persetujuan-absensi.reject', ['type' => 'guru', 'id' => $p->id]) }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="confirmReject(this)" class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white p-2 rounded-xl shadow-sm transition duration-200 border border-red-200 hover:border-red-600" title="Tolak Pengajuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada pengajuan guru yang menunggu persetujuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Riwayat Keputusan --}}
    <div class="mt-8">
        <h2 class="text-base font-black text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat Keputusan (30 Terakhir)
        </h2>
        
        <div class="grid grid-cols-1 gap-6">
            {{-- Riwayat Murid --}}
            <div class="app-card overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-sm">Riwayat Murid</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full app-tbl table-fixed">
                        <thead>
                            <tr>
                                <th class="w-[15%] text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Tanggal</th>
                                <th class="w-[20%] text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Nama Murid</th>
                                <th class="w-[15%] text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Judul Pengajuan</th>
                                <th class="w-[25%] text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keterangan</th>
                                <th class="w-[10%] text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Bukti</th>
                                <th class="w-[15%] text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keputusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($riwayatSiswa as $r)
                            <tr class="hover:bg-slate-50/50 transition text-sm">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}
                                    @if($r->tanggal_selesai && \Carbon\Carbon::parse($r->tanggal_selesai)->format('Y-m-d') !== \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d'))
                                        <br><span class="text-xs text-slate-500 font-normal">s/d {{ \Carbon\Carbon::parse($r->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800">{{ $r->user->name ?? '-' }}</div>
                                    @if($r->guru)
                                        <div class="text-[0.65rem] text-slate-500 mt-0.5 font-semibold">Tujuan: {{ $r->guru->name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="app-badge {{ $r->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize">{{ $r->status }}</span>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $r->keterangan }}">{{ $r->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($r->file_bukti)
                                        <button type="button" onclick="openPreviewModal('{{ asset('storage/' . $r->file_bukti) }}', '{{ $r->user->name ?? 'User' }}')" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition whitespace-nowrap cursor-pointer bg-transparent border-none p-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat
                                        </button>
                                    @else
                                        <span class="text-slate-400 italic text-xs">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($r->status_pengajuan === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Disetujui</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-600 border border-red-100">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-6 text-slate-400">Belum ada riwayat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Riwayat Guru --}}
            <div class="app-card overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-sm">Riwayat Guru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full app-tbl table-fixed">
                        <thead>
                            <tr>
                                <th class="w-[15%] text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Tanggal</th>
                                <th class="w-[20%] text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Nama Guru</th>
                                <th class="w-[15%] text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Judul Pengajuan</th>
                                <th class="w-[25%] text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keterangan</th>
                                <th class="w-[10%] text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Bukti</th>
                                <th class="w-[15%] text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keputusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($riwayatGuru as $r)
                            <tr class="hover:bg-slate-50/50 transition text-sm">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}
                                    @if($r->tanggal_selesai && \Carbon\Carbon::parse($r->tanggal_selesai)->format('Y-m-d') !== \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d'))
                                        <br><span class="text-xs text-slate-500 font-normal">s/d {{ \Carbon\Carbon::parse($r->tanggal_selesai)->translatedFormat('d M Y') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800 whitespace-nowrap">{{ $r->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="app-badge {{ $r->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize">{{ $r->judul_pengajuan ?? $r->status }}</span>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $r->keterangan }}">{{ $r->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($r->file_bukti)
                                        <button type="button" onclick="openPreviewModal('{{ asset('storage/' . $r->file_bukti) }}', '{{ $r->user->name ?? 'User' }}')" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition whitespace-nowrap cursor-pointer bg-transparent border-none p-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat
                                        </button>
                                    @else
                                        <span class="text-slate-400 italic text-xs">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($r->status_pengajuan === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Disetujui</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-600 border border-red-100">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-6 text-slate-400">Belum ada riwayat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- Preview Modal --}}
    <div id="previewModal" class="fixed inset-0 z-[9999] hidden" aria-modal="true" role="dialog">
        {{-- Backdrop --}}
        <div id="previewBackdrop" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closePreviewModal()"></div>
        
        {{-- Modal Content --}}
        <div class="absolute inset-0 flex items-center justify-center p-4 sm:p-6 md:p-10 pointer-events-none">
            <div id="previewContent" class="relative bg-white rounded-2xl shadow-2xl border border-slate-200/60 w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden pointer-events-auto transform scale-95 opacity-0 transition-all duration-300">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex-shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#1e3a6e] to-[#2d5099] flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 truncate">Preview Bukti</h3>
                            <p id="previewUserName" class="text-[0.65rem] text-slate-500 font-semibold truncate"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a id="previewDownloadBtn" href="#" download class="inline-flex items-center gap-1.5 bg-[#1e3a6e] hover:bg-[#2d5099] text-white px-3.5 py-2 rounded-xl font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download
                        </a>
                        <button type="button" onclick="closePreviewModal()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200" title="Tutup">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div id="previewBody" class="flex-1 overflow-auto bg-slate-50/50 flex items-center justify-center min-h-[300px] relative">
                    {{-- Loading Spinner --}}
                    <div id="previewLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                        <div class="w-10 h-10 border-3 border-slate-200 border-t-[#1e3a6e] rounded-full animate-spin"></div>
                        <p class="text-xs text-slate-500 font-semibold">Memuat preview...</p>
                    </div>
                    {{-- Preview Container --}}
                    <div id="previewContainer" class="w-full h-full hidden"></div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-5 py-3 border-t border-slate-100 bg-white flex items-center justify-center flex-shrink-0">
                    <a id="previewOpenNew" href="#" target="_blank" class="inline-flex items-center gap-1 text-[0.65rem] text-slate-500 hover:text-[#1e3a6e] font-semibold transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Buka di tab baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        #previewModal.show #previewBackdrop {
            opacity: 1;
        }
        #previewModal.show #previewContent {
            transform: scale(1);
            opacity: 1;
        }
        #previewContainer img {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            border-radius: 0.5rem;
        }
        #previewContainer iframe {
            width: 100%;
            height: 70vh;
            border: none;
        }
        .preview-unsupported {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            text-align: center;
            gap: 1rem;
        }
        .preview-unsupported .icon-wrap {
            width: 4rem;
            height: 4rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-unsupported .icon-wrap svg {
            width: 1.75rem;
            height: 1.75rem;
            color: #94a3b8;
        }
        .preview-unsupported h4 {
            font-size: 0.875rem;
            font-weight: 700;
            color: #475569;
        }
        .preview-unsupported p {
            font-size: 0.75rem;
            color: #94a3b8;
            max-width: 280px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        .border-3 {
            border-width: 3px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function getFileExtension(url) {
            try {
                const pathname = new URL(url, window.location.origin).pathname;
                const ext = pathname.split('.').pop().toLowerCase();
                return ext;
            } catch {
                return '';
            }
        }

        function getFileName(url) {
            try {
                const pathname = new URL(url, window.location.origin).pathname;
                return pathname.split('/').pop();
            } catch {
                return 'file';
            }
        }

        function openPreviewModal(fileUrl, userName) {
            const modal = document.getElementById('previewModal');
            const loading = document.getElementById('previewLoading');
            const container = document.getElementById('previewContainer');
            const downloadBtn = document.getElementById('previewDownloadBtn');
            const openNewBtn = document.getElementById('previewOpenNew');
            const userNameEl = document.getElementById('previewUserName');

            // Reset state
            loading.classList.remove('hidden');
            container.classList.add('hidden');
            container.innerHTML = '';

            // Set metadata
            const fileName = getFileName(fileUrl);
            const ext = getFileExtension(fileUrl);
            downloadBtn.href = fileUrl;
            downloadBtn.setAttribute('download', fileName);
            openNewBtn.href = fileUrl;
            userNameEl.textContent = 'Bukti dari: ' + userName;

            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    modal.classList.add('show');
                });
            });

            // Determine preview type
            const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
            const pdfExts = ['pdf'];

            if (imageExts.includes(ext)) {
                const img = new Image();
                img.onload = function() {
                    loading.classList.add('hidden');
                    container.classList.remove('hidden');
                    container.style.padding = '1.5rem';
                    container.appendChild(img);
                };
                img.onerror = function() {
                    showUnsupported(container, loading, fileName);
                };
                img.src = fileUrl;
                img.alt = 'Bukti - ' + userName;
            } else if (pdfExts.includes(ext)) {
                const iframe = document.createElement('iframe');
                iframe.src = fileUrl;
                iframe.onload = function() {
                    loading.classList.add('hidden');
                    container.classList.remove('hidden');
                };
                container.style.padding = '0';
                container.appendChild(iframe);
                // Fallback timeout for PDFs
                setTimeout(() => {
                    loading.classList.add('hidden');
                    container.classList.remove('hidden');
                }, 2000);
            } else {
                showUnsupported(container, loading, fileName);
            }
        }

        function showUnsupported(container, loading, fileName) {
            loading.classList.add('hidden');
            container.classList.remove('hidden');
            container.style.padding = '0';
            container.innerHTML = `
                <div class="preview-unsupported">
                    <div class="icon-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h4>Preview tidak tersedia</h4>
                    <p>File <strong>${fileName}</strong> tidak dapat ditampilkan secara langsung. Silakan download untuk melihat file.</p>
                </div>
            `;
        }

        function closePreviewModal() {
            const modal = document.getElementById('previewModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('previewContainer').innerHTML = '';
            }, 300);
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('previewModal');
                if (!modal.classList.contains('hidden')) {
                    closePreviewModal();
                }
            }
        });

        function confirmReject(button) {
            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: "Berikan alasan penolakan (wajib diisi):",
                input: 'textarea',
                inputPlaceholder: 'Tulis alasan penolakan...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                preConfirm: (alasan) => {
                    if (!alasan.trim()) {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                        return false;
                    }
                    return alasan;
                },
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-slate-100',
                    title: 'text-xl font-black text-slate-800',
                    confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm',
                    cancelButton: 'font-bold rounded-xl px-6 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border-none shadow-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = button.closest('form');
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'alasan';
                    input.value = result.value;
                    form.appendChild(input);
                    form.submit();
                }
            })
        }
    </script>
</x-app-layout>
