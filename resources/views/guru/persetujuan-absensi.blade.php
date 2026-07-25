<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Persetujuan Izin & Sakit Murid</span>
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
        <div class="alert-success">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 font-semibold px-5 py-3.5 rounded-xl text-sm mb-6">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Pengajuan Menunggu Persetujuan --}}
        <div class="app-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Menunggu Persetujuan
                </h3>
            </div>
            
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full app-tbl">
                    <thead>
                        <tr>
                            <th class="text-left">Tanggal</th>
                            <th class="text-left">Nama Siswa</th>
                            <th class="text-center">Judul Pengajuan</th>
                            <th class="text-left">Keterangan</th>
                            <th class="text-center">Surat</th>
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
                                        Lihat Surat
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-xs">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('guru.persetujuan-absensi.approve', ['id' => $p->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white p-2 rounded-xl shadow-sm transition duration-200 border border-emerald-200 hover:border-emerald-600" title="Setujui Pengajuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('guru.persetujuan-absensi.reject', ['id' => $p->id]) }}" method="POST">
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
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada pengajuan siswa yang menunggu persetujuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div class="md:hidden flex flex-col divide-y divide-slate-100">
                @forelse($pengajuanSiswa as $p)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                                {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 text-sm block">{{ $p->user->name ?? 'User Dihapus' }}</span>
                                <span class="text-xs font-medium text-slate-500">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                    @if($p->tanggal_selesai && \Carbon\Carbon::parse($p->tanggal_selesai)->format('Y-m-d') !== \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d'))
                                        - {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M y') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <span class="app-badge {{ $p->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize text-[10px] px-2 py-0.5">
                            {{ $p->status }}
                        </span>
                    </div>
                    
                    @if($p->keterangan)
                    <div class="bg-slate-50 rounded-xl p-3 text-xs text-slate-600 border border-slate-100 leading-relaxed">
                        {{ $p->keterangan }}
                    </div>
                    @endif
                    
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            @if($p->file_bukti)
                                <a href="{{ asset('storage/' . $p->file_bukti) }}" target="_blank" class="inline-flex items-center gap-1.5 text-[#1e3a6e] bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-bold text-xs transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Surat
                                </a>
                            @else
                                <span class="text-slate-400 italic text-[11px]">Tak ada surat</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('guru.persetujuan-absensi.reject', ['id' => $p->id]) }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmReject(this)" class="bg-red-50 text-red-600 px-4 py-1.5 rounded-lg text-xs font-bold border border-red-200">Tolak</button>
                            </form>
                            <form action="{{ route('guru.persetujuan-absensi.approve', ['id' => $p->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-emerald-50 text-emerald-600 px-4 py-1.5 rounded-lg text-xs font-bold border border-emerald-200">Setujui</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400 text-sm">Tidak ada pengajuan.</div>
                @endforelse
            </div>
        </div>

        {{-- Riwayat Keputusan --}}
        <div class="mt-8">
            <h2 class="text-base font-black text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat Keputusan (30 Terakhir)
            </h2>
            
            <div class="app-card overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full app-tbl">
                        <thead>
                            <tr>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Tanggal</th>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Nama Siswa</th>
                                <th class="text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Judul Pengajuan</th>
                                <th class="text-left px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Keterangan</th>
                                <th class="text-center px-4 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-600">Surat</th>
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
                                <td class="px-4 py-3 font-semibold text-slate-800 whitespace-nowrap">{{ $r->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="app-badge {{ $r->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize">{{ $r->status }}</span>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $r->keterangan }}">{{ $r->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($r->file_bukti)
                                        <a href="{{ asset('storage/' . $r->file_bukti) }}" target="_blank" class="inline-flex items-center gap-1 text-[#1e3a6e] hover:text-[#2d5099] font-semibold text-xs transition whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat Surat
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
                            <tr><td colspan="6" class="text-center py-6 text-slate-400">Belum ada riwayat keputusan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards View for Riwayat --}}
                <div class="md:hidden flex flex-col divide-y divide-slate-100">
                    @forelse($riwayatSiswa as $r)
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm border border-slate-200">
                                    {{ strtoupper(substr($r->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 text-sm block">{{ $r->user->name ?? 'User Dihapus' }}</span>
                                    <span class="text-xs font-medium text-slate-500">
                                        {{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}
                                        @if($r->tanggal_selesai && \Carbon\Carbon::parse($r->tanggal_selesai)->format('Y-m-d') !== \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d'))
                                            - {{ \Carbon\Carbon::parse($r->tanggal_selesai)->translatedFormat('d M y') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1.5">
                                <span class="app-badge {{ $r->status === 'izin' ? 'b-amber' : 'b-slate' }} capitalize text-[9px] px-2 py-0.5">
                                    {{ $r->status }}
                                </span>
                                @if($r->status_pengajuan === 'approved')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Disetujui</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-600 border border-red-100">Ditolak</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($r->keterangan)
                        <div class="bg-slate-50 rounded-xl p-3 text-xs text-slate-600 border border-slate-100 leading-relaxed">
                            {{ $r->keterangan }}
                        </div>
                        @endif
                        
                        @if($r->file_bukti)
                        <div class="pt-1">
                            <a href="{{ asset('storage/' . $r->file_bukti) }}" target="_blank" class="inline-flex items-center gap-1.5 text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg font-bold text-xs transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Lihat Surat
                            </a>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada riwayat keputusan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
