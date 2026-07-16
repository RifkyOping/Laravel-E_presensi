@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Riwayat Sholat</span>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Riwayat Absen Sholat</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Catatan sholat harian (Zuhur/Jumat) dari guru piket.</p>
                </div>
            </div>

            {{-- Mobile: Card List --}}
            <div class="block sm:hidden divide-y divide-slate-100">
                @forelse($riwayatSholat as $rs)
                <div class="px-4 py-3.5 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-700 text-sm leading-tight">
                            {{ Carbon::parse($rs->tanggal)->translatedFormat('d F Y') }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ Carbon::parse($rs->tanggal)->translatedFormat('l') }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        @php
                            $cls = match($rs->status) {
                                'sholat' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'udzur'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                default  => 'bg-red-50 text-red-600 border-red-100',
                            };
                            $statusText = match($rs->status) {
                                'sholat' => 'Sholat',
                                'udzur'  => 'Udzur',
                                default  => 'Tidak Sholat',
                            };
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border {{ $cls }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-slate-400 text-sm">Belum ada catatan sholat.</div>
                @endforelse
            </div>

            {{-- Desktop: Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70">
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Hari</th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Status Sholat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($riwayatSholat as $rs)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="py-3.5 px-5 font-semibold text-slate-700 text-sm">
                                {{ Carbon::parse($rs->tanggal)->translatedFormat('d F Y') }}
                            </td>
                            <td class="py-3.5 px-5 text-sm text-slate-500">
                                {{ Carbon::parse($rs->tanggal)->translatedFormat('l') }}
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                @php
                                    $cls = match($rs->status) {
                                        'sholat' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'udzur'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                        default  => 'bg-red-50 text-red-600 border-red-100',
                                    };
                                    $statusText = match($rs->status) {
                                        'sholat' => 'Sholat',
                                        'udzur'  => 'Udzur',
                                        default  => 'Tidak Sholat',
                                    };
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border {{ $cls }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-slate-400 text-sm">Belum ada catatan sholat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $riwayatSholat->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
