@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Rekap RPP</span>
    </x-slot>

    @php
        $pageTitle    = 'Rekap RPP';
        $pageSubtitle = 'Lihat semua RPP yang diunggah guru';
    @endphp

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h2 class="text-2xl font-black text-slate-800">Rekap RPP Guru</h2>
            <p class="text-slate-500 text-sm mt-1 font-medium">Semua RPP yang pernah diunggah oleh guru</p>
        </div>

        {{-- Filter --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <form method="GET" action="{{ route('admin.rekap-rpp') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Nama Guru</label>
                    <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..."
                           class="w-full rounded-xl border border-slate-200 text-sm focus:ring-[#1e3a6e] focus:border-[#1e3a6e] h-[42px] px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Periode</label>
                    <select name="periode" class="w-full rounded-xl border border-slate-200 text-sm focus:ring-[#1e3a6e] focus:border-[#1e3a6e] h-[42px] px-4 py-2.5">
                        <option value="">Semua</option>
                        @foreach($periodeList as $p)
                            <option value="{{ $p }}" {{ request('periode') === $p ? 'selected' : '' }}>
                                {{ Carbon::createFromFormat('Y-m', $p)->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Tingkat</label>
                    <select name="tingkat" class="w-full rounded-xl border border-slate-200 text-sm focus:ring-[#1e3a6e] focus:border-[#1e3a6e] h-[42px] px-4 py-2.5">
                        <option value="">Semua</option>
                        @foreach($tingkatList as $t)
                            <option value="{{ $t }}" {{ request('tingkat') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 text-sm focus:ring-[#1e3a6e] focus:border-[#1e3a6e] h-[42px] px-4 py-2.5">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition">Filter</button>
                    @if(request()->hasAny(['nama', 'periode', 'tingkat', 'jurusan', 'status']))
                        <a href="{{ route('admin.rekap-rpp') }}" class="flex items-center justify-center px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 transition">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            
            {{-- Mobile View --}}
            <div class="block sm:hidden divide-y divide-slate-100">
                @forelse($rppList as $rpp)
                    @php
                        $badge = match($rpp->rpp_status) {
                            'pending'   => 'bg-amber-100 text-amber-700 border-amber-200',
                            'disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'ditolak'   => 'bg-red-100 text-red-700 border-red-200',
                            default     => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                        $label = match($rpp->rpp_status) {
                            'pending'   => 'Pending',
                            'disetujui' => 'Disetujui',
                            'ditolak'   => 'Ditolak',
                            default     => ucfirst($rpp->rpp_status),
                        };
                    @endphp
                    <div class="px-5 py-4">
                        <div class="flex items-center justify-between gap-3 mb-1.5">
                            <span class="font-bold text-slate-800 text-sm truncate">{{ $rpp->user->name ?? '-' }}</span>
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $badge }}">{{ $label }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span>{{ $rpp->tingkat }} {{ $rpp->jurusan }}</span>
                            <span>·</span>
                            <span>{{ Carbon::createFromFormat('Y-m', $rpp->rpp_periode)->translatedFormat('F Y') }}</span>
                            <span>·</span>
                            <a href="{{ Storage::url($rpp->rpp_file) }}" target="_blank" class="text-[#1e3a6e] font-semibold hover:underline">Lihat</a>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-slate-400 text-sm">Tidak ada data RPP.</div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-4">Nama Guru</th>
                            <th class="px-6 py-4 text-center">Tingkat</th>
                            <th class="px-6 py-4 text-center">Jurusan</th>
                            <th class="px-6 py-4 text-center">Periode</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4">Tanggal Upload</th>
                            <th class="px-6 py-4 text-center">File</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rppList as $rpp)
                            @php
                                $badge = match($rpp->rpp_status) {
                                    'pending'   => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'ditolak'   => 'bg-red-100 text-red-700 border-red-200',
                                    default     => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                $label = match($rpp->rpp_status) {
                                    'pending'   => 'Pending',
                                    'disetujui' => 'Disetujui',
                                    'ditolak'   => 'Ditolak',
                                    default     => ucfirst($rpp->rpp_status),
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $rpp->user->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">NIP: {{ $rpp->user->nomor_induk ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-slate-700">{{ $rpp->tingkat }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-slate-700">{{ $rpp->jurusan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-semibold text-slate-700">
                                    {{ Carbon::createFromFormat('Y-m', $rpp->rpp_periode)->translatedFormat('F Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full border {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                    {{ $rpp->created_at->translatedFormat('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ Storage::url($rpp->rpp_file) }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#1e3a6e] hover:underline bg-[#1e3a6e]/5 px-3 py-1.5 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="font-bold text-slate-700">Tidak ada data RPP</p>
                                        <p class="text-xs text-slate-400 mt-1">Belum ada RPP yang diunggah oleh guru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rppList->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $rppList->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
