<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Manajemen Pengguna</span>
    </x-slot>

<div class="space-y-6">

    {{-- Header row --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-800">Manajemen Pengguna</h2>
            <p class="text-sm text-slate-400 mt-0.5">Kelola seluruh akun pengguna aplikasi</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import CSV
            </button>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Akun
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Filter + Tabs --}}
    <div x-data="{ showFilter: {{ request('search') || request('tab', 'semua') !== 'semua' ? 'true' : 'false' }} }" class="bg-white rounded-xl border border-slate-200 p-6">
        <button type="button" @click="showFilter = !showFilter" class="w-full text-left flex items-center justify-between group focus:outline-none">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                    <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-700">Filter & Pencarian Pengguna</h2>
                    <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk menyesuaikan pencarian dan role</p>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4 text-slate-500 transition-transform duration-300" :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>

        <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100 space-y-4" style="display: none;">
            {{-- Tabs --}}
            <div class="flex flex-wrap gap-2">
                @foreach(['semua'=>'Semua','murid'=>'Murid','guru'=>'Guru','pengawas'=>'Pengawas','admin'=>'Admin'] as $key=>$label)
                <a href="{{ route('admin.users', ['tab'=>$key,'search'=>request('search')]) }}"
                   class="px-4 py-1.5 rounded-lg font-semibold text-sm border transition duration-200
                          {{ $tab===$key ? 'bg-[#1e3a6e] text-white border-[#1e3a6e]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-[#1e3a6e] hover:text-[#1e3a6e]' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
            {{-- Search --}}
            <form method="GET" action="{{ route('admin.users') }}" class="flex gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="flex-1 border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                <button type="submit"
                        class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition flex items-center gap-2 shadow-md shadow-[#1e3a6e]/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
                @if(request('search'))
                <a href="{{ route('admin.users', ['tab'=>$tab]) }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan <span class="font-bold text-slate-800">{{ $users->total() }}</span> pengguna
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">No</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Nama</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Email</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Role</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Terdaftar</th>
                        <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $i => $user)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="py-3.5 px-5 text-sm text-slate-400 font-semibold">{{ $users->firstItem() + $i }}</td>
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-semibold text-slate-800 text-sm block">{{ $user->name }}</span>
                                    @if($user->role === 'murid')
                                        <span class="text-xs text-slate-400 font-medium">NISN: {{ $user->nomor_induk ?? '-' }}</span>
                                    @elseif($user->role === 'guru')
                                        <span class="text-xs text-slate-400 font-medium">NIP: {{ $user->nomor_induk ?? '-' }}</span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">ID: {{ $user->nomor_induk ?? '-' }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-500">{{ $user->email }}</td>
                        <td class="py-3.5 px-5">
                            @php $rc = match($user->role) {
                                'admin'    => 'bg-red-50 text-red-700 border-red-200',
                                'guru'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                'pengawas' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                default    => 'bg-slate-100 text-slate-600 border-slate-200',
                            }; @endphp
                            <span class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border capitalize {{ $rc }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-3.5 px-5 text-sm text-slate-400">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                        <td class="py-3.5 px-5">
                            <div class="flex items-center justify-center gap-2">
                                <form method="POST" action="{{ route('admin.users.reset-device', $user->id) }}"
                                      onsubmit="return confirm('Reset perangkat untuk akun {{ $user->name }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-orange-200 text-orange-500 hover:bg-orange-500 hover:text-white font-semibold text-xs transition duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Reset
                                    </button>
                                </form>
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                      onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 text-sm">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </div>

</div>

@push('modals')
{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-lg">Import Pengguna dari CSV</h3>
            <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <label class="block text-sm font-semibold text-slate-700 mb-2">1. Download Template CSV</label>
            <div class="flex gap-2">
                <select id="templateDelimiterUser" class="flex-1 text-sm border-slate-200 rounded-xl focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                    <option value=",">Format Excel EN (,)</option>
                    <option value=";">Format Excel ID (;)</option>
                </select>
                <button type="button" onclick="window.location.href='{{ route('admin.users.template-import') }}?delimiter=' + document.getElementById('templateDelimiterUser').value"
                        class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-[#1e3a6e] font-bold rounded-xl text-sm transition flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </button>
            </div>
        </div>
        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">2. Upload File CSV</label>
                <input type="file" name="file_csv" accept=".csv" required
                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#1e3a6e] hover:file:bg-blue-100">
                <p class="text-xs text-slate-400 mt-2">Maksimal ukuran file 2MB.</p>
            </div>
            <div class="flex items-center justify-end">
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-sm transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

</x-app-layout>
