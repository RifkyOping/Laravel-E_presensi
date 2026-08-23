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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel / CSV
                </button>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Akun
                </a>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('warning'))
            <div
                class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 font-semibold px-5 py-3.5 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @if (session('error'))
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
        @if (session('import_errors') && count(session('import_errors')) > 0)
            <div class="bg-rose-50 border-2 border-rose-200 rounded-2xl p-5 text-rose-900 shadow-sm animate-up">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-xl bg-rose-100 border border-rose-200 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-black text-rose-800 text-sm">Daftar Baris yang Gagal Diimport
                                ({{ count(session('import_errors')) }} Baris)</h4>
                            <p class="text-xs text-rose-600 mt-0.5">Periksa kembali data pada file Excel Anda atau
                                pastikan data belum pernah terdaftar sebelumnya.</p>
                        </div>
                    </div>
                </div>

                <div
                    class="max-h-64 overflow-y-auto rounded-xl border border-rose-200 bg-white divide-y divide-rose-100 text-xs shadow-inner">
                    @foreach (session('import_errors') as $err)
                        <div
                            class="p-3 hover:bg-rose-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-start sm:items-center gap-2 min-w-0">
                                <span
                                    class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-bold text-[11px] whitespace-nowrap shrink-0">
                                    Baris {{ $err['baris'] }}
                                </span>
                                <div class="truncate">
                                    <span class="font-bold text-slate-800">{{ $err['nama'] }}</span>
                                    <span class="text-slate-500 font-normal ml-1">({{ $err['detail'] }})</span>
                                </div>
                            </div>
                            <span
                                class="text-rose-600 text-[11px] font-semibold bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200 shrink-0 self-start sm:self-auto">
                                {{ $err['alasan'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Filter + Tabs --}}
        <div x-data="{
            showFilter: localStorage.getItem('filter_admin_users') === 'true' || {{ request('search') || (request('tab') && request('tab') !== 'semua') ? 'true' : 'false' }}
        }" x-init="$watch('showFilter', val => localStorage.setItem('filter_admin_users', val))" class="bg-white rounded-xl border border-slate-200 p-6">
            <button type="button" @click="showFilter = !showFilter"
                class="w-full text-left flex items-center justify-between group focus:outline-none">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors shadow-sm border border-blue-100">
                        <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-700">Filter & Pencarian Pengguna</h2>
                        <p class="text-[0.65rem] text-slate-400 font-medium">Klik untuk menyesuaikan pencarian dan role
                        </p>
                    </div>
                </div>
                <div
                    class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-50 group-hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4 text-slate-500 transition-transform duration-300"
                        :class="{ 'rotate-180': showFilter }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>

            <div x-show="showFilter" x-transition class="mt-5 pt-5 border-t border-slate-100 space-y-4"
                style="display: none;">
                {{-- Tabs --}}
                <div class="flex flex-wrap gap-2">
                    @foreach (['semua' => 'Semua', 'murid' => 'Murid', 'guru' => 'Guru', 'pengawas' => 'Pengawas', 'admin' => 'Admin'] as $key => $label)
                        <a href="{{ route('admin.users', ['tab' => $key, 'search' => request('search')]) }}"
                            data-tab="{{ $key }}"
                            class="role-tab px-4 py-1.5 rounded-lg font-semibold text-sm border transition duration-200
                          {{ $tab === $key ? 'bg-[#1e3a6e] text-white border-[#1e3a6e]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-[#1e3a6e] hover:text-[#1e3a6e]' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                {{-- Search --}}
                <form method="GET" action="{{ route('admin.users') }}" class="flex gap-3" id="searchForm">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                        placeholder="Cari nama, NIS, NISN, NIP atau ID..." oninput="handleSearchInput(this)"
                        class="flex-1 border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm">
                    @if (request('search'))
                        <a href="{{ route('admin.users', ['tab' => $tab]) }}"
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
        </div>

        {{-- Tabel --}}
        <div id="table-container" class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    Total <span class="font-bold text-slate-800">{{ $users->total() }}</span> pengguna
                </p>
                <div id="bulkActionContainer" class="flex items-center gap-2">
                    <button type="button" id="btnEditBanyak" onclick="enableBulkEditMode()"
                        class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Banyak
                    </button>
                    <button type="button" id="btnPilihBanyak" onclick="enableBulkMode()"
                        class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Banyak
                    </button>
                    <div id="bulkDeleteActions" class="hidden items-center gap-2">
                        <button type="button" onclick="disableBulkMode()"
                            class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition duration-200 shadow-sm">
                            Batal
                        </button>
                        <button type="button" id="btnBulkDelete" onclick="submitBulkDelete()" disabled
                            class="inline-flex items-center gap-2 bg-red-100 hover:bg-red-200 text-red-600 font-bold px-4 py-2 rounded-xl text-xs transition duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Terpilih
                        </button>
                    </div>
                    <div id="bulkEditActions" class="hidden items-center gap-2">
                        <button type="button" onclick="disableBulkEditMode()"
                            class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition duration-200 shadow-sm">
                            Batal
                        </button>
                        <button type="button" onclick="submitBulkEdit()"
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-xs transition duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
            <form id="bulkUpdateForm" action="{{ route('admin.users.bulk-update') }}" method="POST"
                class="hidden">
                @csrf
                @method('PUT')
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70">
                            <th class="bulk-mode-col hidden py-3 px-5 w-12 text-center">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"
                                    class="w-4 h-4 rounded border-slate-300 text-[#1e3a6e] focus:ring-[#1e3a6e]">
                            </th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">No
                            </th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Nama
                            </th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Email
                            </th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">Role
                            </th>
                            <th class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider">
                                Terdaftar</th>
                            <th
                                class="py-3 px-5 text-[.7rem] font-black text-slate-400 uppercase tracking-wider text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($users as $i => $user)
                            <tr class="hover:bg-slate-50/60 transition duration-150">
                                <td class="bulk-mode-col hidden py-3.5 px-5 text-center">
                                    @if ($user->id !== auth()->id())
                                        <input type="checkbox" value="{{ $user->id }}"
                                            onchange="updateBulkDeleteButton()"
                                            class="user-checkbox w-4 h-4 rounded border-slate-300 text-[#1e3a6e] focus:ring-[#1e3a6e]">
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-sm text-slate-400 font-semibold">
                                    {{ $users->firstItem() + $i }}</td>
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-[#1e3a6e] text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="w-full min-w-[150px]">
                                            <span
                                                class="view-mode font-semibold text-slate-800 text-sm block">{{ $user->name }}</span>
                                            <input form="bulkUpdateForm" type="text"
                                                name="users[{{ $user->id }}][name]" value="{{ $user->name }}"
                                                class="edit-mode hidden w-full text-sm border-slate-200 rounded-lg px-2 py-1 mb-1 focus:ring-[#1e3a6e] focus:border-[#1e3a6e]"
                                                required>

                                            <div class="view-mode">
                                                @if ($user->role === 'murid')
                                                    <span class="text-xs text-slate-400 font-medium block">NISN:
                                                        {{ $user->nomor_induk ?? '-' }}</span>
                                                    <span class="text-xs text-slate-400 font-medium block">NIS:
                                                        {{ $user->siswaProfile?->nis ?? '-' }}</span>
                                                @elseif($user->role === 'guru')
                                                    <span class="text-xs text-slate-400 font-medium block">NIP:
                                                        {{ $user->nomor_induk ?? '-' }}</span>
                                                @else
                                                    <span class="text-xs text-slate-400 font-medium block">ID:
                                                        {{ $user->nomor_induk ?? '-' }}</span>
                                                @endif
                                            </div>
                                            <div class="edit-mode hidden flex flex-col gap-1 mt-1">
                                                <input form="bulkUpdateForm" type="text"
                                                    name="users[{{ $user->id }}][nomor_induk]"
                                                    value="{{ $user->nomor_induk }}"
                                                    class="w-full text-xs border-slate-200 rounded-lg px-2 py-1 focus:ring-[#1e3a6e] focus:border-[#1e3a6e]"
                                                    {{ $user->role !== 'murid' ? 'required' : '' }}
                                                    placeholder="{{ $user->role === 'murid' ? 'NISN (opsional)' : 'NISN/NIP/ID' }}">
                                                @if ($user->role === 'murid')
                                                    <input form="bulkUpdateForm" type="text"
                                                        name="users[{{ $user->id }}][nis]"
                                                        value="{{ $user->siswaProfile?->nis }}"
                                                        class="w-full text-xs border-slate-200 rounded-lg px-2 py-1 focus:ring-[#1e3a6e] focus:border-[#1e3a6e]"
                                                        placeholder="NIS (opsional)">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-5 text-sm text-slate-500">
                                    <span class="view-mode">{{ $user->email }}</span>
                                    <input form="bulkUpdateForm" type="email"
                                        name="users[{{ $user->id }}][email]" value="{{ $user->email }}"
                                        class="edit-mode hidden w-full text-sm border-slate-200 rounded-lg px-2 py-1 focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="view-mode">
                                        @php
                                            $rc = match ($user->role) {
                                                'admin' => 'bg-red-50 text-red-700 border-red-200',
                                                'guru' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'pengawas' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                                            };
                                        @endphp
                                        <span
                                            class="inline-block px-2.5 py-1 rounded-lg text-[.7rem] font-bold border capitalize {{ $rc }}">
                                            {{ $user->role }}
                                        </span>
                                    </div>
                                    <div class="edit-mode hidden">
                                        <select form="bulkUpdateForm" name="users[{{ $user->id }}][role]"
                                            class="text-sm border-slate-200 rounded-lg px-2 py-1 focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                                            <option value="murid" {{ $user->role == 'murid' ? 'selected' : '' }}>
                                                Murid</option>
                                            <option value="guru" {{ $user->role == 'guru' ? 'selected' : '' }}>Guru
                                            </option>
                                            <option value="pengawas"
                                                {{ $user->role == 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                                                Admin</option>
                                        </select>
                                    </div>
                                </td>
                                <td class="py-3.5 px-5 text-sm text-slate-400">
                                    {{ $user->created_at->translatedFormat('d M Y') }}</td>
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center justify-center gap-2">
                                        <form method="POST"
                                            action="{{ route('admin.users.reset-device', $user->id) }}"
                                            onsubmit="confirmResetDevice(event, this, '{{ addslashes($user->name) }}')">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-orange-200 text-orange-500 hover:bg-orange-500 hover:text-white font-semibold text-xs transition duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Reset
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#1e3a6e]/30 text-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white font-semibold text-xs transition duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form method="POST"
                                                action="{{ route('admin.users.destroy', $user->id) }}"
                                                onsubmit="confirmDeleteUser(event, this, '{{ addslashes($user->name) }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition duration-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">Tidak ada pengguna
                                    ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">{{ $users->links() }}</div>
            @endif
        </div>

    </div>

    @push('modals')
        {{-- Import Modal --}}
        <div id="importModal"
            class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-md flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-fade-in-up">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-lg">Import Pengguna (Excel / CSV)</h3>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">1. Download Template Import</label>
                    <div class="flex gap-2">
                        <select id="templateDelimiterUser"
                            class="flex-1 text-sm border-slate-200 rounded-xl focus:ring-[#1e3a6e] focus:border-[#1e3a6e]">
                            <option value=",">Format Excel EN (,)</option>
                            <option value=";">Format Excel ID (;)</option>
                        </select>
                        <button type="button"
                            onclick="window.location.href='{{ route('admin.users.template-import') }}?delimiter=' + document.getElementById('templateDelimiterUser').value"
                            class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-[#1e3a6e] font-bold rounded-xl text-sm transition flex items-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </button>
                    </div>
                </div>
                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data"
                    class="p-6">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">2. Upload File Import</label>
                        <input type="file" name="file_csv" accept=".csv,.xlsx" required
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#1e3a6e] hover:file:bg-blue-100">
                        <p class="text-xs text-slate-400 mt-2">Maksimal ukuran file 2MB.</p>
                    </div>
                    <div class="flex items-center justify-end">
                        <div class="flex gap-2">
                            <button type="button"
                                onclick="document.getElementById('importModal').classList.add('hidden')"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-sm transition">Batal</button>
                            <button type="submit"
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition">Import</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <form id="bulkDeleteForm" method="POST" action="{{ route('admin.users.bulk-delete') }}" class="hidden">
            @csrf
            @method('DELETE')
            <div id="bulkDeleteInputs"></div>
        </form>

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
                // Efek loading transparan
                tableContainer.style.opacity = '0.5';
                tableContainer.style.pointerEvents = 'none';

                try {
                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const html = await response.text();

                    // Parse HTML respons untuk mengambil tabel yang baru
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.getElementById('table-container');

                    if (newTable) {
                        // Update isi tabel
                        tableContainer.innerHTML = newTable.innerHTML;
                    }

                    // Update URL di browser agar user bisa refresh/copy-paste URL tanpa kehilangan filter
                    window.history.pushState({}, '', url.toString());
                } catch (error) {
                    console.error('Error saat live search:', error);
                } finally {
                    // Kembalikan visibilitas tabel
                    tableContainer.style.opacity = '1';
                    tableContainer.style.pointerEvents = 'auto';
                    // Panggil ulang event listener untuk bulk delete jika diperlukan
                    if (typeof disableBulkMode === 'function') disableBulkMode();
                }
            }

            // Menangani klik pada link pagination dan tab filter agar menggunakan AJAX juga
            document.addEventListener('click', function(e) {
                const form = document.getElementById('searchForm');

                // 1. Handle klik tab filter role
                const tabLink = e.target.closest('.role-tab');
                if (tabLink) {
                    e.preventDefault();
                    const selectedTab = tabLink.getAttribute('data-tab');

                    // Update input hidden tab di form
                    if (form && form.querySelector('input[name="tab"]')) {
                        form.querySelector('input[name="tab"]').value = selectedTab;
                    }

                    // Update UI class untuk tab aktif
                    document.querySelectorAll('.role-tab').forEach(tab => {
                        if (tab.getAttribute('data-tab') === selectedTab) {
                            tab.className =
                                "role-tab px-4 py-1.5 rounded-lg font-semibold text-sm border transition duration-200 bg-[#1e3a6e] text-white border-[#1e3a6e]";
                        } else {
                            tab.className =
                                "role-tab px-4 py-1.5 rounded-lg font-semibold text-sm border transition duration-200 bg-slate-50 text-slate-600 border-slate-200 hover:border-[#1e3a6e] hover:text-[#1e3a6e]";
                        }
                    });

                    // Jalankan pencarian live
                    if (form) performLiveSearch(form);
                    return;
                }

                // 2. Handle klik link pagination
                const paginationLink = e.target.closest('#table-container nav a');
                if (paginationLink) {
                    e.preventDefault();
                    const url = new URL(paginationLink.href);

                    // Pindahkan nilai search dan tab dari form ke URL pagination (agar filter tidak hilang saat pindah halaman)
                    if (form) {
                        const formData = new FormData(form);
                        formData.forEach((value, key) => {
                            if (value) url.searchParams.set(key, value);
                        });
                    }

                    // Panggil ulang ajax
                    performLiveSearch(url.toString());
                }
            });

            // Menangani submit form (misalnya saat menekan tombol Enter)
            document.addEventListener("DOMContentLoaded", function() {
                const searchForm = document.getElementById('searchForm');
                if (searchForm) {
                    searchForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        performLiveSearch(this);
                    });
                }
            });

            function enableBulkEditMode() {
                document.getElementById('btnEditBanyak').classList.add('hidden');
                document.getElementById('btnPilihBanyak').classList.add('hidden');
                document.getElementById('bulkEditActions').classList.remove('hidden');
                document.getElementById('bulkEditActions').classList.add('flex');

                document.querySelectorAll('.view-mode').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
            }

            function disableBulkEditMode() {
                document.getElementById('btnEditBanyak').classList.remove('hidden');
                document.getElementById('btnPilihBanyak').classList.remove('hidden');
                document.getElementById('bulkEditActions').classList.add('hidden');
                document.getElementById('bulkEditActions').classList.remove('flex');

                document.querySelectorAll('.view-mode').forEach(el => el.classList.remove('hidden'));
                document.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
            }

            function submitBulkEdit() {
                const form = document.getElementById('bulkUpdateForm');
                if (form.reportValidity()) {
                    form.submit();
                }
            }

            function enableBulkMode() {
                document.getElementById('btnEditBanyak').classList.add('hidden');
                document.getElementById('btnPilihBanyak').classList.add('hidden');
                document.getElementById('bulkDeleteActions').classList.remove('hidden');
                document.getElementById('bulkDeleteActions').classList.add('flex');

                const cols = document.querySelectorAll('.bulk-mode-col');
                cols.forEach(col => col.classList.remove('hidden'));
            }

            function disableBulkMode() {
                document.getElementById('btnEditBanyak').classList.remove('hidden');
                document.getElementById('btnPilihBanyak').classList.remove('hidden');
                document.getElementById('bulkDeleteActions').classList.add('hidden');
                document.getElementById('bulkDeleteActions').classList.remove('flex');

                const cols = document.querySelectorAll('.bulk-mode-col');
                cols.forEach(col => col.classList.add('hidden'));

                // Reset checkboxes
                document.getElementById('selectAll').checked = false;
                const checkboxes = document.querySelectorAll('.user-checkbox');
                checkboxes.forEach(cb => cb.checked = false);
                updateBulkDeleteButton();
            }

            function toggleSelectAll(source) {
                const checkboxes = document.querySelectorAll('.user-checkbox');
                for (let i = 0, n = checkboxes.length; i < n; i++) {
                    checkboxes[i].checked = source.checked;
                }
                updateBulkDeleteButton();
            }

            function updateBulkDeleteButton() {
                const checkboxes = document.querySelectorAll('.user-checkbox:checked');
                const btn = document.getElementById('btnBulkDelete');
                if (btn) {
                    btn.disabled = checkboxes.length === 0;
                    if (checkboxes.length > 0) {
                        btn.innerHTML =
                            `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus Terpilih (${checkboxes.length})`;
                    } else {
                        btn.innerHTML =
                            `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus Terpilih`;
                    }
                }
            }

            function submitBulkDelete() {
                const checkboxes = document.querySelectorAll('.user-checkbox:checked');
                if (checkboxes.length === 0) return;

                Swal.fire({
                    title: 'Konfirmasi Hapus Massal',
                    text: `Apakah Anda yakin ingin menghapus ${checkboxes.length} pengguna terpilih secara permanen?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-100',
                        title: 'text-xl font-black text-slate-800',
                        confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm text-white',
                        cancelButton: 'font-bold rounded-xl px-6 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border-none shadow-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('bulkDeleteForm');
                        const inputsContainer = document.getElementById('bulkDeleteInputs');
                        inputsContainer.innerHTML = '';

                        checkboxes.forEach(cb => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'user_ids[]';
                            input.value = cb.value;
                            inputsContainer.appendChild(input);
                        });

                        form.submit();
                    }
                });
            }

            function confirmResetDevice(event, form, userName) {
                event.preventDefault();
                Swal.fire({
                    title: 'Reset Perangkat',
                    text: `Apakah Anda yakin ingin mereset perangkat untuk akun ${userName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f97316', // orange-500
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-100',
                        title: 'text-xl font-black text-slate-800',
                        confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm text-white',
                        cancelButton: 'font-bold rounded-xl px-6 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border-none shadow-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            function confirmDeleteUser(event, form, userName) {
                event.preventDefault();
                Swal.fire({
                    title: 'Hapus Akun',
                    text: `Apakah Anda yakin ingin menghapus akun ${userName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', // red-500
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-100',
                        title: 'text-xl font-black text-slate-800',
                        confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm text-white',
                        cancelButton: 'font-bold rounded-xl px-6 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border-none shadow-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        </script>
    @endpush

</x-app-layout>
