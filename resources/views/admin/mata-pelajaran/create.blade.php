<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Tambah Mata Pelajaran</span>
    </x-slot>

<div class="max-w-4xl space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.mata-pelajaran.index') }}" 
               onclick="clearDraft()"
               class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-black text-slate-800">Tambah Mata Pelajaran</h2>
                <p class="text-xs text-slate-400 mt-0.5">Tambah satu atau beberapa mata pelajaran sekaligus dalam satu formulir</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm space-y-1">
        <p class="font-bold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Terdapat beberapa kesalahan pengisian:
        </p>
        <ul class="list-disc list-inside text-xs space-y-0.5 pl-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Mata Pelajaran Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Isi nama mata pelajaran dan tentukan status aktifnya</p>
            </div>
            <span id="row-count-badge" class="px-3 py-1 bg-slate-200/80 text-slate-700 text-xs font-bold rounded-full">
                <span id="row-count-num">{{ !empty($oldMapels) ? count($oldMapels) : 1 }}</span><span class="hidden sm:inline">&nbsp;Baris</span>
            </span>
        </div>

        <form method="POST" action="{{ route('admin.mata-pelajaran.store') }}" id="form-mapel" class="p-6 space-y-5">
            @csrf

            {{-- Container Baris-Baris Mata Pelajaran --}}
            <div id="mapel-container" class="space-y-3">
                @php
                    $oldMapels = old('mapels');
                @endphp

                @if(!empty($oldMapels) && is_array($oldMapels))
                    @foreach($oldMapels as $index => $item)
                    <div class="mapel-row flex items-start sm:items-center gap-3 p-3.5 bg-slate-50/60 rounded-xl border border-slate-200/90 transition hover:border-slate-300">
                        <div class="row-num flex-shrink-0 w-8 h-8 rounded-lg bg-white border border-slate-200 font-black text-xs text-slate-600 flex items-center justify-center shadow-xs mt-0.5 sm:mt-0">
                            {{ $loop->iteration }}
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <input type="text" 
                                       name="mapels[{{ $index }}][nama]" 
                                       value="{{ $item['nama'] ?? '' }}" 
                                       placeholder="Nama mata pelajaran"
                                       required
                                       class="input-nama w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <label class="flex items-center gap-2.5 cursor-pointer select-none bg-white hover:bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 transition">
                                    <input type="checkbox" 
                                           name="mapels[{{ $index }}][aktif]" 
                                           value="1" 
                                           {{ !isset($item['aktif']) || $item['aktif'] == '1' ? 'checked' : '' }}
                                           class="w-4 h-4 rounded accent-[#1e3a6e] cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700">Aktif</span>
                                </label>

                                <button type="button" 
                                        onclick="removeRow(this)" 
                                        class="btn-remove p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition border border-transparent hover:border-red-100"
                                        title="Hapus baris ini">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    {{-- Default 1 Row Pertama --}}
                    <div class="mapel-row flex items-start sm:items-center gap-3 p-3.5 bg-slate-50/60 rounded-xl border border-slate-200/90 transition hover:border-slate-300">
                        <div class="row-num flex-shrink-0 w-8 h-8 rounded-lg bg-white border border-slate-200 font-black text-xs text-slate-600 flex items-center justify-center shadow-xs mt-0.5 sm:mt-0">
                            1
                        </div>

                        <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <input type="text" 
                                       name="mapels[0][nama]" 
                                       placeholder="Nama mata pelajaran"
                                       required
                                       class="input-nama w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <label class="flex items-center gap-2.5 cursor-pointer select-none bg-white hover:bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 transition">
                                    <input type="checkbox" 
                                           name="mapels[0][aktif]" 
                                           value="1" 
                                           checked
                                           class="w-4 h-4 rounded accent-[#1e3a6e] cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700">Aktif</span>
                                </label>

                                <button type="button" 
                                        onclick="removeRow(this)" 
                                        class="btn-remove p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition border border-transparent hover:border-red-100"
                                        title="Hapus baris ini">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tombol Tambah Baris Baru --}}
            <button type="button" 
                    onclick="addNewRow()" 
                    class="w-full py-3.5 border-2 border-dashed border-slate-200 hover:border-[#1e3a6e] hover:bg-blue-50/40 text-slate-600 hover:text-[#1e3a6e] font-bold rounded-xl text-sm transition flex items-center justify-center gap-2 group">
                <div class="w-6 h-6 rounded-lg bg-slate-100 group-hover:bg-[#1e3a6e] text-slate-500 group-hover:text-white flex items-center justify-center transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span>Tambah Baris Mata Pelajaran Baru</span>
            </button>

            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-400 font-medium text-center sm:text-left">
                    💡 <strong>Tips:</strong> Tekan tombol <span class="font-bold text-slate-600">Enter</span> di kolom nama untuk langsung menambah baris baru.
                </p>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('admin.mata-pelajaran.index') }}"
                       onclick="clearDraft()"
                       class="px-5 py-2.5 rounded-xl border border-slate-200 hover:border-slate-400 text-slate-600 font-semibold text-sm transition text-center flex-1 sm:flex-initial">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-sm text-center flex-1 sm:flex-initial flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
    const DRAFT_KEY = 'draft_admin_mata_pelajaran';
    const hasOldData = {{ !empty($oldMapels) ? 'true' : 'false' }};
    let rowIndex = {{ !empty($oldMapels) ? count($oldMapels) : 1 }};

    function saveDraft() {
        const rows = document.querySelectorAll('#mapel-container .mapel-row');
        const draft = [];
        rows.forEach(row => {
            const inputNama = row.querySelector('.input-nama');
            const checkbox = row.querySelector('input[type="checkbox"]');
            if (inputNama) {
                draft.push({
                    nama: inputNama.value,
                    aktif: checkbox ? checkbox.checked : true
                });
            }
        });
        localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
    }

    function loadDraft() {
        if (hasOldData) {
            saveDraft();
            return;
        }

        const saved = localStorage.getItem(DRAFT_KEY);
        if (!saved) return;

        try {
            const draft = JSON.parse(saved);
            if (Array.isArray(draft) && draft.length > 0) {
                // Hanya restore jika ada baris dengan nama terisi atau ada lebih dari 1 baris
                const hasFilledData = draft.some(item => item.nama && item.nama.trim() !== '') || draft.length > 1;
                if (!hasFilledData) return;

                const container = document.getElementById('mapel-container');
                container.innerHTML = '';
                rowIndex = 0;

                draft.forEach(item => {
                    addNewRow(item.nama || '', item.aktif !== false, false);
                });

                updateRowUI();
            }
        } catch (e) {
            console.error('Gagal memuat draft:', e);
        }
    }

    function clearDraft() {
        localStorage.removeItem(DRAFT_KEY);
    }

    function updateRowUI() {
        const rows = document.querySelectorAll('#mapel-container .mapel-row');
        const countNum = document.getElementById('row-count-num');
        if (countNum) {
            countNum.textContent = rows.length;
        }

        rows.forEach((row, index) => {
            const numEl = row.querySelector('.row-num');
            if (numEl) numEl.textContent = index + 1;

            const removeBtn = row.querySelector('.btn-remove');
            if (removeBtn) {
                if (rows.length === 1) {
                    removeBtn.classList.add('opacity-40', 'cursor-not-allowed');
                    removeBtn.setAttribute('disabled', 'disabled');
                } else {
                    removeBtn.classList.remove('opacity-40', 'cursor-not-allowed');
                    removeBtn.removeAttribute('disabled');
                }
            }
        });
    }

    function addNewRow(namaValue = '', isAktif = true, shouldFocus = true) {
        const container = document.getElementById('mapel-container');
        const currentIndex = rowIndex++;

        const rowDiv = document.createElement('div');
        rowDiv.className = 'mapel-row flex items-start sm:items-center gap-3 p-3.5 bg-slate-50/60 rounded-xl border border-slate-200/90 transition hover:border-slate-300';
        
        rowDiv.innerHTML = `
            <div class="row-num flex-shrink-0 w-8 h-8 rounded-lg bg-white border border-slate-200 font-black text-xs text-slate-600 flex items-center justify-center shadow-xs mt-0.5 sm:mt-0">
                1
            </div>

            <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1 min-w-0">
                    <input type="text" 
                           name="mapels[${currentIndex}][nama]" 
                           value="${namaValue.replace(/"/g, '&quot;')}"
                           placeholder="Nama mata pelajaran"
                           required
                           class="input-nama w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-2.5 text-slate-800 font-medium focus:outline-none transition text-sm bg-white">
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none bg-white hover:bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 transition">
                        <input type="checkbox" 
                               name="mapels[${currentIndex}][aktif]" 
                               value="1" 
                               ${isAktif ? 'checked' : ''}
                               class="w-4 h-4 rounded accent-[#1e3a6e] cursor-pointer">
                        <span class="text-xs font-bold text-slate-700">Aktif</span>
                    </label>

                    <button type="button" 
                            onclick="removeRow(this)" 
                            class="btn-remove p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition border border-transparent hover:border-red-100"
                            title="Hapus baris ini">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        container.appendChild(rowDiv);
        updateRowUI();
        saveDraft();

        if (shouldFocus) {
            const newInput = rowDiv.querySelector('.input-nama');
            if (newInput) {
                newInput.focus();
            }
        }
    }

    function removeRow(button) {
        const rows = document.querySelectorAll('#mapel-container .mapel-row');
        if (rows.length <= 1) return;

        const row = button.closest('.mapel-row');
        if (row) {
            row.remove();
            updateRowUI();
            saveDraft();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadDraft();
        updateRowUI();

        const container = document.getElementById('mapel-container');
        const form = document.getElementById('form-mapel');

        if (container) {
            // Simpan draft saat pengguna mengetik nama mapel
            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('input-nama')) {
                    saveDraft();
                }
            });

            // Simpan draft saat status checkbox diubah
            container.addEventListener('change', function(e) {
                if (e.target.type === 'checkbox') {
                    saveDraft();
                }
            });

            // Shortcut Enter untuk menambah baris
            container.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.classList.contains('input-nama')) {
                    e.preventDefault();
                    addNewRow();
                }
            });
        }

        // Hapus draft otomatis saat form berhasil di-submit
        if (form) {
            form.addEventListener('submit', function() {
                clearDraft();
            });
        }
    });
</script>

</x-app-layout>
