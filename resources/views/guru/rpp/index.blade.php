@php use Carbon\Carbon; @endphp
<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Upload RPP</span>
    </x-slot>

    @php
        $pageTitle = 'Upload RPP';
        $pageSubtitle = 'Kelola RPP untuk setiap kelas yang Anda ajar';
    @endphp

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800">Dokumen RPP</h2>
                <p class="text-slate-500 text-sm mt-1 font-medium">Unggah RPP Anda berdasarkan Tingkat dan Jurusan.</p>
            </div>
            <a href="{{ route('guru.rekap-rpp') }}"
                class="inline-flex items-center gap-1.5 text-sm font-bold text-white hover:text-white bg-[#1e3a6e] hover:bg-[#162d57] px-4 py-2.5 rounded-xl transition shadow-sm flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Rekap RPP
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl text-sm font-semibold">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Terjadi kesalahan:
                </div>
                <ul class="list-disc list-inside space-y-1 ml-8 text-xs font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Status & Upload RPP per Tingkat+Jurusan --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            @if ($rppSlots->isEmpty())
                <div class="px-5 py-10">
                    <div class="flex flex-col items-center justify-center text-center">
                        <div
                            class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-700 text-lg">Belum Ada Kelas</h3>
                        <p class="text-slate-500 text-sm mt-1 max-w-sm">Anda belum memiliki jadwal mengajar, sehingga
                            tidak ada RPP yang perlu diunggah. Hubungi admin untuk mengatur jadwal Anda.</p>
                    </div>
                </div>
            @else
                {{-- Mobile View --}}
                <form action="{{ route('guru.upload-rpp') }}" method="POST" enctype="multipart/form-data"
                    class="block sm:hidden">
                    @csrf
                    <div class="divide-y divide-slate-100">
                        @foreach ($rppSlots as $slot)
                            @php
                                $statusData = match ($slot['status']) {
                                    'kosong' => ['Belum Upload', 'bg-red-50 text-red-700 border-red-200'],
                                    'pending' => ['Menunggu', 'bg-amber-50 text-amber-700 border-amber-200'],
                                    'disetujui' => ['Disetujui', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                    'ditolak' => ['Ditolak', 'bg-red-50 text-red-700 border-red-200'],
                                    default => ['Belum Upload', 'bg-slate-50 text-slate-700 border-slate-200'],
                                };
                                $targetLabel = Carbon::createFromFormat(
                                    'Y-m',
                                    $slot['target_periode'],
                                )->translatedFormat('F Y');
                            @endphp
                            <div class="px-5 py-4">
                                <div class="flex items-center justify-between gap-3 mb-1">
                                    <div>
                                        <span class="font-bold text-slate-800">{{ $slot['tingkat'] }}
                                            {{ $slot['jurusan'] }}</span>
                                        <span
                                            class="inline-flex ml-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusData[1] }}">{{ $statusData[0] }}</span>
                                    </div>
                                    @if ($slot['file'])
                                        <a href="{{ Storage::url($slot['file']) }}" target="_blank"
                                            class="text-xs font-semibold text-[#1e3a6e] hover:underline bg-[#1e3a6e]/5 px-2.5 py-1 rounded-md">Lihat
                                            File</a>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 font-semibold mb-3">
                                    Periode Target: <span class="text-slate-700">{{ $targetLabel }}</span>
                                </div>

                                @if ($slot['status'] === 'ditolak' && $slot['pesan'])
                                    <div class="mb-3 p-3 bg-red-50/50 border border-red-100 rounded-xl">
                                        <p class="text-xs font-bold text-red-600 mb-0.5">Alasan Penolakan:</p>
                                        <p class="text-xs text-red-800">{{ $slot['pesan'] }}</p>
                                    </div>
                                @endif

                                <div x-data="{ fileName: '', fileUrl: '' }">
                                    @if (in_array($slot['status'], ['pending', 'disetujui']))
                                        <div class="flex flex-col gap-2">
                                            <div
                                                class="bg-emerald-50 text-emerald-700 text-[11px] font-bold px-4 py-3 rounded-xl border border-emerald-100 flex justify-between items-center gap-2">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    RPP telah diunggah
                                                </div>
                                                <button type="button" @click="$refs.fileInput.click()"
                                                    class="text-xs text-[#1e3a6e] hover:underline font-bold px-2 py-1 bg-[#1e3a6e]/10 rounded-md">Ganti
                                                    File</button>
                                            </div>
                                            <div x-show="fileName" style="display: none;"
                                                class="mt-1 flex flex-col gap-1.5 bg-blue-50/50 border border-blue-100 p-3 rounded-xl">
                                                <span class="text-[11px] text-slate-600 font-medium">File baru: <span
                                                        x-text="fileName"
                                                        class="text-slate-800 font-bold"></span></span>
                                                <a x-show="fileUrl" :href="fileUrl" target="_blank"
                                                    class="inline-flex items-center justify-center gap-1.5 text-[11px] font-bold text-[#1e3a6e] bg-[#1e3a6e]/10 hover:bg-[#1e3a6e]/20 px-3 py-1.5 rounded-lg transition w-max">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Lihat File
                                                </a>
                                            </div>
                                            <input type="file" x-ref="fileInput"
                                                name="rpp_files[{{ $slot['tingkat'] }}|{{ $slot['jurusan'] }}|{{ $slot['target_periode'] }}]"
                                                accept=".pdf,.doc,.docx" class="hidden"
                                                @change="if($event.target.files[0]) { fileName = $event.target.files[0].name; fileUrl = URL.createObjectURL($event.target.files[0]) }">
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-2">
                                            <div x-show="fileName" style="display: none;" class="text-[11px] text-slate-600 font-medium">
                                                File terpilih: <span x-text="fileName" class="text-slate-800 font-bold"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="$refs.newFileInput.click()" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#1e3a6e] bg-[#1e3a6e]/10 hover:bg-[#1e3a6e]/20 px-3 py-2 rounded-xl transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    Pilih File
                                                </button>
                                                <a x-show="fileUrl" style="display: none;" :href="fileUrl" target="_blank"
                                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#1e3a6e] bg-[#1e3a6e]/10 hover:bg-[#1e3a6e]/20 px-3 py-2 rounded-xl transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Lihat File
                                                </a>
                                            </div>
                                            <input type="file" x-ref="newFileInput"
                                                name="rpp_files[{{ $slot['tingkat'] }}|{{ $slot['jurusan'] }}|{{ $slot['target_periode'] }}]"
                                                accept=".pdf,.doc,.docx" class="hidden"
                                                @change="if($event.target.files[0]) { fileName = $event.target.files[0].name; fileUrl = URL.createObjectURL($event.target.files[0]) }">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end">
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-3 rounded-xl text-sm transition shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Simpan Dokumen RPP
                        </button>
                    </div>
                </form>

                {{-- Desktop Table View --}}
                <form action="{{ route('guru.upload-rpp') }}" method="POST" enctype="multipart/form-data"
                    class="hidden sm:block overflow-x-auto">
                    @csrf
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                            <tr>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4 text-center">Periode Target</th>
                                <th class="px-6 py-4 text-center">Status RPP</th>
                                <th class="px-6 py-4 text-center">File RPP</th>
                                <th class="px-6 py-4">Unggah Dokumen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($rppSlots as $slot)
                                @php
                                    $statusData = match ($slot['status']) {
                                        'kosong' => ['Belum Upload', 'bg-red-100 text-red-700 border-red-200'],
                                        'pending' => [
                                            'Menunggu Persetujuan',
                                            'bg-amber-100 text-amber-700 border-amber-200',
                                        ],
                                        'disetujui' => [
                                            'Disetujui',
                                            'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        ],
                                        'ditolak' => ['Ditolak', 'bg-red-100 text-red-700 border-red-200'],
                                        default => ['Belum Upload', 'bg-slate-100 text-slate-700 border-slate-200'],
                                    };
                                    $targetLabel = Carbon::createFromFormat(
                                        'Y-m',
                                        $slot['target_periode'],
                                    )->translatedFormat('F Y');
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="font-black text-slate-800 text-base">{{ $slot['tingkat'] }}
                                            {{ $slot['jurusan'] }}</div>
                                        @if ($slot['status'] === 'ditolak' && $slot['pesan'])
                                            <div
                                                class="mt-2 p-2.5 bg-red-50 border border-red-100 rounded-lg max-w-[200px] whitespace-normal">
                                                <p
                                                    class="text-[10px] font-bold text-red-600 uppercase tracking-wider mb-0.5">
                                                    Alasan Penolakan:</p>
                                                <p class="text-xs text-red-800">{{ $slot['pesan'] }}</p>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <span
                                            class="font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg">{{ $targetLabel }}</span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full border {{ $statusData[1] }}">
                                            {{ $statusData[0] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        @if ($slot['file'])
                                            <a href="{{ Storage::url($slot['file']) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1e3a6e] hover:underline bg-[#1e3a6e]/5 hover:bg-[#1e3a6e]/10 px-3 py-2 rounded-xl transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Lihat File
                                            </a>
                                        @else
                                            <span
                                                class="text-xs font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded-md">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5" x-data="{ fileName: '', fileUrl: '' }">
                                        @if (in_array($slot['status'], ['pending', 'disetujui']))
                                            <div class="flex flex-col gap-1.5">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="bg-emerald-50 text-emerald-700 text-xs font-bold px-4 py-3 rounded-xl border border-emerald-100 inline-flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Selesai
                                                    </div>
                                                    <button type="button" @click="$refs.fileInput.click()"
                                                        class="text-xs text-[#1e3a6e] hover:underline font-bold px-3 py-2 bg-[#1e3a6e]/10 rounded-xl transition">Ganti
                                                        File</button>
                                                </div>
                                                <div x-show="fileName" style="display: none;"
                                                    class="mt-1.5 flex flex-col gap-1.5 bg-blue-50/50 border border-blue-100 p-3 rounded-xl w-max">
                                                    <span class="text-[11px] text-slate-600 font-medium">File baru:
                                                        <span x-text="fileName"
                                                            class="text-slate-800 font-bold"></span></span>
                                                    <a x-show="fileUrl" :href="fileUrl" target="_blank"
                                                        class="inline-flex items-center justify-center gap-1.5 text-[11px] font-bold text-[#1e3a6e] bg-[#1e3a6e]/10 hover:bg-[#1e3a6e]/20 px-3 py-1.5 rounded-lg transition w-max">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Lihat File
                                                    </a>
                                                </div>
                                                <input type="file" x-ref="fileInput"
                                                    name="rpp_files[{{ $slot['tingkat'] }}|{{ $slot['jurusan'] }}|{{ $slot['target_periode'] }}]"
                                                    accept=".pdf,.doc,.docx" class="hidden"
                                                    @change="if($event.target.files[0]) { fileName = $event.target.files[0].name; fileUrl = URL.createObjectURL($event.target.files[0]) }">
                                            </div>
                                        @else
                                            <div class="flex flex-col gap-1.5 w-full">
                                                <div x-show="fileName" style="display: none;" class="text-[11px] text-slate-600 font-medium">
                                                    File terpilih: <span x-text="fileName" class="text-slate-800 font-bold"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="$refs.newFileInput.click()" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#1e3a6e] bg-[#1e3a6e]/10 hover:bg-[#1e3a6e]/20 px-3 py-2 rounded-xl transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                        Pilih File
                                                    </button>
                                                    <a x-show="fileUrl" style="display: none;" :href="fileUrl" target="_blank"
                                                        class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#1e3a6e] bg-[#1e3a6e]/10 hover:bg-[#1e3a6e]/20 px-3 py-2 rounded-xl transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Lihat File
                                                    </a>
                                                </div>
                                                <input type="file" x-ref="newFileInput"
                                                    name="rpp_files[{{ $slot['tingkat'] }}|{{ $slot['jurusan'] }}|{{ $slot['target_periode'] }}]"
                                                    accept=".pdf,.doc,.docx" class="hidden"
                                                    @change="if($event.target.files[0]) { fileName = $event.target.files[0].name; fileUrl = URL.createObjectURL($event.target.files[0]) }">
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                        <button type="submit"
                            class="flex items-center justify-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-3 rounded-xl text-sm transition shadow-sm w-full sm:w-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Simpan Dokumen RPP
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
