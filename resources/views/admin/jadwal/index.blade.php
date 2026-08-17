<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Manajemen Jadwal Mengajar</span>
    </x-slot>

    <div class="space-y-6">

        {{-- Header row --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-800">Jadwal Mengajar Guru</h2>
                <p class="text-sm text-slate-400 mt-0.5">Kelola jadwal mengajar untuk semua guru</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.jadwal-mengajar.rekap') }}"
                    class="inline-flex items-center gap-2 bg-[#1e3a6e] hover:bg-[#1e3a6e]/90 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Lihat Jadwal Lengkap
                </a>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel / CSV
                </button>
            </div>
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div
                class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div
                class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Detail Baris Gagal Import --}}
        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="bg-rose-50 border-2 border-rose-200 rounded-2xl p-5 text-rose-900 shadow-sm animate-up">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 border border-rose-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-black text-rose-800 text-sm">Daftar Baris yang Gagal Diimport ({{ count(session('import_errors')) }} Baris)</h4>
                            <p class="text-xs text-rose-600 mt-0.5">Periksa kembali data pada file Excel Anda atau pastikan NIP/nama guru sudah terdaftar di sistem.</p>
                        </div>
                    </div>
                </div>

                <div class="max-h-64 overflow-y-auto rounded-xl border border-rose-200 bg-white divide-y divide-rose-100 text-xs shadow-inner">
                    @foreach(session('import_errors') as $err)
                        <div class="p-3 hover:bg-rose-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-start sm:items-center gap-2 min-w-0">
                                <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-bold text-[11px] whitespace-nowrap shrink-0">
                                    Baris {{ $err['baris'] }}
                                </span>
                                <div class="truncate">
                                    <span class="font-bold text-slate-800">{{ $err['nama'] }}</span>
                                    <span class="text-slate-500 font-normal ml-1">({{ $err['detail'] }})</span>
                                </div>
                            </div>
                            <span class="text-rose-600 text-[11px] font-semibold bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200 shrink-0 self-start sm:self-auto">
                                {{ $err['alasan'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Toggle Blok Aktif --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center justify-between shadow-sm">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">
                    Jadwal Aktif: <span class="text-[#1e3a6e]">{{ $blokAktif === 'TEFA' ? 'TEFA' : "Blok {$blokAktif}" }}</span>
                </h3>
                <p class="text-sm text-slate-500 mt-1">Ubah ini untuk mengatur status jadwal (TEFA: jadwal reguler dinonaktifkan).</p>
            </div>
            <form action="{{ route('admin.jadwal-mengajar.toggle-blok') }}" method="POST" class="flex items-center gap-2">
                @csrf
                <button type="submit" name="blok" value="A" 
                    class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $blokAktif === 'A' ? 'bg-[#1e3a6e] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Blok A
                </button>
                <button type="submit" name="blok" value="B" 
                    class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $blokAktif === 'B' ? 'bg-[#1e3a6e] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Blok B
                </button>
                <button type="submit" name="blok" value="TEFA" 
                    class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $blokAktif === 'TEFA' ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    TEFA
                </button>
            </form>
        </div>

        {{-- Filter + Tabs --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.jadwal-mengajar.index') }}" class="flex gap-3" id="searchForm">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama guru atau NIP..." oninput="handleSearchInput(this)"
                    class="flex-1 border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                @if(request('search'))
                    <a href="{{ route('admin.jadwal-mengajar.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel --}}
        <div id="table-container" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    Total <span class="font-bold text-slate-800">{{ $gurus->total() }}</span> guru
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="hidden md:table-header-group">
                        <tr class="border-b border-slate-100 bg-slate-50/70">
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">No
                            </th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Nama
                            </th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">NIP
                            </th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Status
                                Jadwal</th>
                            <th
                                class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="block md:table-row-group divide-y divide-slate-50">
                        @forelse($gurus as $i => $guru)
                            <tr class="block md:table-row hover:bg-slate-50/60 transition duration-150 p-4 md:p-0">
                                <td
                                    class="hidden md:table-cell py-1 md:py-3.5 px-2 md:px-5 text-sm text-slate-400 font-semibold">
                                    <span class="inline-block md:hidden font-bold text-xs text-slate-400 w-24">No:</span>
                                    {{ $gurus->firstItem() + $i }}
                                </td>
                                <td class="block md:table-cell py-1 md:py-3.5 px-2 md:px-5">
                                    <div class="flex items-center md:gap-3">
                                        <span
                                            class="inline-block md:hidden font-bold text-xs text-slate-400 w-24">Nama:</span>
                                        <div
                                            class="hidden md:flex w-8 h-8 rounded-full bg-[#1e3a6e] text-white items-center justify-center font-black text-xs flex-shrink-0">
                                            {{ strtoupper(substr($guru->name, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-slate-800 text-sm">{{ $guru->name }}</span>
                                    </div>
                                </td>
                                <td class="block md:table-cell py-1 md:py-3.5 px-2 md:px-5 text-sm text-slate-500">
                                    <span class="inline-block md:hidden font-bold text-xs text-slate-400 w-24">NIP:</span>
                                    {{ $guru->nomor_induk ?? '-' }}
                                </td>
                                <td class="block md:table-cell py-1 md:py-3.5 px-2 md:px-5">
                                    <div class="flex items-center">
                                        <span
                                            class="inline-block md:hidden font-bold text-xs text-slate-400 w-24">Status:</span>
                                        @if($guru->is_jadwal_set)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[.7rem] font-bold border bg-green-50 text-green-700 border-green-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Telah Diatur
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[.7rem] font-bold border bg-red-50 text-red-700 border-red-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Belum Diatur
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="block md:table-cell py-3 md:py-3.5 px-2 md:px-5">
                                    <div
                                        class="flex items-center md:justify-center gap-2 mt-2 md:mt-0 pt-2 border-t border-slate-100 md:border-0 md:pt-0">
                                        <a href="{{ route('admin.jadwal-mengajar.edit', $guru->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200 w-full md:w-auto justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Jadwal
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 text-sm">Tidak ada guru ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($gurus->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">{{ $gurus->links() }}</div>
            @endif
        </div>

    </div>

    <script>
        let searchTimeout;
        function handleSearchInput(input) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performLiveSearch(input.form);
            }, 500); // Tunggu 500ms
        }

        async function performLiveSearch(source) {
            let url;
            if (typeof source === 'string') {
                url = new URL(source, window.location.origin);
            } else if (source instanceof HTMLFormElement) {
                url = new URL(source.action || window.location.href, window.location.origin);
                const formData = new FormData(source);
                url.search = '';
                formData.forEach((value, key) => {
                    if (value) url.searchParams.set(key, value);
                });
            } else {
                url = new URL(window.location.href);
            }

            const tableContainer = document.getElementById('table-container');
            if (!tableContainer) return;
            tableContainer.style.opacity = '0.5';
            tableContainer.style.pointerEvents = 'none';

            try {
                const response = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('table-container');

                if (newTable) {
                    tableContainer.innerHTML = newTable.innerHTML;
                }

                window.history.pushState({}, '', url.toString());
            } catch (error) {
                console.error('Error saat live search:', error);
            } finally {
                tableContainer.style.opacity = '1';
                tableContainer.style.pointerEvents = 'auto';
            }
        }

        document.addEventListener('click', function (e) {
            const form = document.getElementById('searchForm');

            // Handle klik link pagination
            const paginationLink = e.target.closest('#table-container nav a');
            if (paginationLink) {
                e.preventDefault();
                const url = new URL(paginationLink.href);

                if (form) {
                    const formData = new FormData(form);
                    formData.forEach((value, key) => {
                        if (value) url.searchParams.set(key, value);
                    });
                }

                performLiveSearch(url.toString());
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            const searchForm = document.getElementById('searchForm');
            if (searchForm) {
                searchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    performLiveSearch(this);
                });
            }
        });
    </script>

    @push('modals')
        {{-- Import Modal --}}
        <div id="importModal"
            class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-fade-in-up">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-lg">Import Jadwal Mengajar (Excel / CSV)</h3>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">1. Download Template Excel</label>
                    <div class="flex gap-2">
                        <select id="templateDelimiterJadwal"
                            class="flex-1 text-sm border-slate-200 rounded-xl focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                            <option value=",">Format Excel EN (,)</option>
                            <option value=";">Format Excel ID (;)</option>
                        </select>
                        <button type="button"
                            onclick="window.location.href='{{ route('admin.jadwal-mengajar.template') }}?delimiter=' + document.getElementById('templateDelimiterJadwal').value"
                            class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-[#1e3a6e] font-bold rounded-xl text-sm transition flex items-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </button>
                    </div>
                </div>
                <form action="{{ route('admin.jadwal-mengajar.import') }}" method="POST" enctype="multipart/form-data"
                    class="p-6">
                    @csrf
                    <div class="mb-5 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">2. Upload File XLSX / CSV</label>
                            <input type="file" name="file_csv" accept=".csv,.xlsx,.txt" required
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#1e3a6e] hover:file:bg-blue-100">
                            <p class="text-xs text-slate-400 mt-2">Disarankan format XLSX. Maksimal ukuran file 2MB.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-end">
                        <div class="flex gap-2">
                            <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-sm transition">Batal</button>
                            <button type="submit"
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition">Import</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endpush

</x-app-layout>