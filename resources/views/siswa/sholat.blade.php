@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Riwayat Sholat</span>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 -960 960 960" xmlns="http://www.w3.org/2000/svg"><path d="M40-120v-491q-18-11-29-28.5T0-680q0-23 24-56t56-64q32 31 56 64t24 56q0 23-11 40.5T120-611v171h80v-80q0-25 16-48t46-30q-11-17-16.5-37t-5.5-41q0-40 19-74t51-56l170-114 170 114q32 22 51 56t19 74q0 21-5.5 41T698-598q30 7 46 30t16 48v80h80v-171q-18-11-29-28.5T800-680q0-23 24-56t56-64q32 31 56 64t24 56q0 23-11 40.5T920-611v491H520v-160q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280v160H40Zm356-480h168q32 0 54-22t22-54q0-20-9-36.5T606-740l-126-84-126 84q-16 11-25 27.5t-9 36.5q0 32 22 54t54 22ZM120-200h240v-80q0-50 35-85t85-35q50 0 85 35t35 85v80h240v-160H680v-160H280v160H120v160Zm360-320Zm0-80Zm0 2Z"/></svg>
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
