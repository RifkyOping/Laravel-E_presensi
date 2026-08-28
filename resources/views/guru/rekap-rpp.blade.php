@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Rekap RPP</span>
    </x-slot>

    @php
        $pageTitle    = 'Rekap RPP';
        $pageSubtitle = 'Histori semua RPP yang pernah Anda unggah';
    @endphp

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800">Rekap RPP Saya</h2>
                <p class="text-slate-500 text-sm mt-1 font-medium">
                    Riwayat semua RPP yang pernah diunggah
                </p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <form id="filter-form" method="GET" action="{{ route('guru.rekap-rpp') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Periode</label>
                    <select id="filter_periode" name="periode" class="w-full rounded-xl border border-slate-200 text-sm focus:ring-[#1e3a6e] focus:border-[#1e3a6e] h-[42px] px-4 py-2.5">
                        <option value="">Semua Periode</option>
                        @foreach($periodeList as $periode)
                            <option value="{{ $periode }}" {{ request('periode') === $periode ? 'selected' : '' }}>
                                {{ Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Loading indicator -->
                <div id="loading-indicator" class="flex-shrink-0 flex items-center justify-center h-[42px] px-3 hidden">
                    <svg class="animate-spin h-5 w-5 text-[#1e3a6e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div id="data-container" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            
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
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <div>
                                <span class="font-bold text-slate-800">{{ $rpp->tingkat }} {{ $rpp->jurusan }}</span>
                                <span class="inline-flex ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge }}">{{ $label }}</span>
                            </div>
                            <a href="{{ Storage::url($rpp->rpp_file) }}" target="_blank" class="text-xs font-semibold text-[#1e3a6e] hover:underline">Lihat</a>
                        </div>
                        <div class="text-xs text-slate-500">
                            Periode: {{ Carbon::createFromFormat('Y-m', $rpp->rpp_periode)->translatedFormat('F Y') }} · Upload: {{ $rpp->created_at->translatedFormat('d M Y H:i') }}
                        </div>
                        @if($rpp->rpp_status === 'ditolak' && $rpp->rpp_pesan)
                            <div class="mt-2 p-2.5 bg-red-50 border border-red-100 rounded-lg">
                                <p class="text-[10px] font-bold text-red-600">Alasan: {{ $rpp->rpp_pesan }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-10 text-center text-slate-400 text-sm">Belum ada RPP yang diunggah.</div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-4">Periode</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4">Tanggal Upload</th>
                            <th class="px-6 py-4 text-center">File</th>
                            <th class="px-6 py-4">Keterangan</th>
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
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-slate-800">
                                    {{ Carbon::createFromFormat('Y-m', $rpp->rpp_periode)->translatedFormat('F Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                    {{ $rpp->tingkat }} {{ $rpp->jurusan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full border {{ $badge }}">
                                        {{ $label }}
                                    </span>
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
                                <td class="px-6 py-4">
                                    @if($rpp->rpp_status === 'ditolak' && $rpp->rpp_pesan)
                                        <div class="p-2 bg-red-50 border border-red-100 rounded-lg max-w-xs">
                                            <p class="text-xs text-red-800">{{ $rpp->rpp_pesan }}</p>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="font-bold text-slate-700">Belum ada RPP</p>
                                        <p class="text-xs text-slate-400 mt-1">Anda belum pernah mengunggah RPP.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodeInput = document.getElementById('filter_periode');
            const form = document.getElementById('filter-form');
            const container = document.getElementById('data-container');
            const loading = document.getElementById('loading-indicator');

            function fetchFilteredData() {
                const periode = periodeInput.value;
                
                loading.classList.remove('hidden');

                const url = new URL(form.action);
                if (periode) url.searchParams.set('periode', periode);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('data-container');
                    
                    if(newContent) {
                        container.innerHTML = newContent.innerHTML;
                    }
                    
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                })
                .finally(() => {
                    loading.classList.add('hidden');
                });
            }

            if(periodeInput) periodeInput.addEventListener('change', fetchFilteredData);
        });
    </script>
</x-app-layout>
