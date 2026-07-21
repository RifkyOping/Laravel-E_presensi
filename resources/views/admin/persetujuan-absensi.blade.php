<x-app-layout>
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
            
            <div class="overflow-x-auto">
                <table class="w-full app-tbl">
                    <thead>
                        <tr>
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Nama Murid</th>
                            <th class="text-center">Jenis</th>
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
                                    <a href="{{ asset('storage/' . $p->file_bukti) }}" target="_blank" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Bukti
                                    </a>
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
                    <svg class="w-5 h-5 text-[#2d5099]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Pengajuan Guru
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full app-tbl">
                    <thead>
                        <tr>
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Nama Guru</th>
                            <th class="text-center">Jenis</th>
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
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="max-w-xs truncate" title="{{ $p->keterangan }}">{{ $p->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                @if($p->file_bukti)
                                    <a href="{{ asset('storage/' . $p->file_bukti) }}" target="_blank" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Bukti
                                    </a>
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
                    <table class="w-full app-tbl">
                        <thead>
                            <tr>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Tanggal</th>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Nama Murid</th>
                                <th class="text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Jenis</th>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keterangan</th>
                                <th class="text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Bukti</th>
                                <th class="text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keputusan</th>
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
                                        <a href="{{ asset('storage/' . $r->file_bukti) }}" target="_blank" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat
                                        </a>
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
                    <table class="w-full app-tbl">
                        <thead>
                            <tr>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Tanggal</th>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Nama Guru</th>
                                <th class="text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Jenis</th>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keterangan</th>
                                <th class="text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Bukti</th>
                                <th class="text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keputusan</th>
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
                                    <span class="app-badge {{ $r->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize">{{ $r->status }}</span>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $r->keterangan }}">{{ $r->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($r->file_bukti)
                                        <a href="{{ asset('storage/' . $r->file_bukti) }}" target="_blank" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat
                                        </a>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmReject(button) {
            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: "Jika ditolak, status kehadiran akan diubah menjadi Alpha.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-slate-100',
                    title: 'text-xl font-black text-slate-800',
                    confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm',
                    cancelButton: 'font-bold rounded-xl px-6 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border-none shadow-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }
    </script>
</x-app-layout>
