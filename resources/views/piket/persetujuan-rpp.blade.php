<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Persetujuan RPP Guru</span>
    </x-slot>

    @php
        $pageTitle    = 'Persetujuan RPP';
        $pageSubtitle = 'Tinjau dan setujui RPP yang diunggah oleh guru';
    @endphp

    {{-- ===== File Viewer Modal ===== --}}
    <div id="rpp-modal-overlay"
        onclick="if(event.target===this) closeRppViewer()"
        style="display:none; position:fixed; inset:0; z-index:50; background:rgba(15,23,42,0.7); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:16px;">
        <div id="rpp-modal-panel"
            style="position:relative; background:#fff; border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,0.25); width:100%; max-width:960px; display:flex; flex-direction:column; height:90vh; transform:scale(0.95); opacity:0; transition:transform 0.2s ease, opacity 0.2s ease;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #e2e8f0; flex-shrink:0; gap:12px;">
                <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                    <div style="width:36px; height:36px; background:#1e3a6e1a; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg style="width:18px;height:18px" fill="none" stroke="#1e3a6e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div style="min-width:0;">
                        <p style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin:0;">Preview Dokumen RPP</p>
                        <p id="rpp-modal-filename" style="font-size:13px; font-weight:800; color:#1e293b; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:500px;"></p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                    <a id="rpp-modal-download" href="#" download
                        style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:#475569; background:#f1f5f9; padding:7px 12px; border-radius:10px; text-decoration:none; transition:background 0.15s;"
                        onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh
                    </a>
                    <button onclick="closeRppViewer()"
                        style="width:36px; height:36px; border-radius:10px; background:#f1f5f9; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b; transition:background 0.15s;"
                        onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div id="rpp-modal-body" style="flex:1; overflow:hidden; border-radius:0 0 20px 20px; background:#f1f5f9; position:relative;"></div>
        </div>
    </div>
    {{-- ===== End Modal ===== --}}

    <div class="space-y-6">
        {{-- Header & Filter --}}
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="w-full md:w-auto text-left">
                <h2 class="font-bold text-slate-800 text-lg">Daftar RPP Guru</h2>
                <p class="text-slate-500 text-sm">Menampilkan RPP yang diunggah guru untuk periode {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
            <form id="filter-form" onsubmit="event.preventDefault();" class="flex flex-row items-end gap-2 sm:gap-3 w-full md:w-auto">
                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] sm:hidden font-black text-slate-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" onchange="filterData()" class="filter-select w-full rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-[#1e3a6e] focus:border-[#1e3a6e] h-[34px] sm:h-[42px] px-2 sm:px-4 py-1.5 sm:py-2.5">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                @if($tingkatList->isNotEmpty())
                <div class="flex-1 min-w-0 hidden sm:block">
                    <select name="tingkat" onchange="filterData()" class="filter-select w-full rounded-xl border border-slate-200 text-sm focus:ring-[#1e3a6e] focus:border-[#1e3a6e] h-[42px] px-4 py-2.5">
                        <option value="">Semua Tingkat</option>
                        @foreach($tingkatList as $tingkat)
                            <option value="{{ $tingkat }}" {{ request('tingkat') === $tingkat ? 'selected' : '' }}>{{ $tingkat }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if($jurusanList->isNotEmpty())
                <div class="flex-1 min-w-0 hidden sm:block">
                    <select name="jurusan" onchange="filterData()" class="filter-select w-full rounded-xl border border-slate-200 text-sm focus:ring-[#1e3a6e] focus:border-[#1e3a6e] h-[42px] px-4 py-2.5">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jurusan)
                            <option value="{{ $jurusan }}" {{ request('jurusan') === $jurusan ? 'selected' : '' }}>{{ $jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </form>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl text-sm font-semibold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div id="table-container" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            {{-- Mobile View --}}
            <div class="block sm:hidden bg-white">
                <div class="flex flex-row items-center gap-2 px-4 py-3 bg-slate-50 border-b border-slate-100 text-[11px] font-black text-slate-500 uppercase tracking-wider text-center">
                    <div class="flex-1 text-left min-w-0">Guru / Kelas</div>
                    <div class="w-[60px] flex-shrink-0">Status</div>
                    <div class="w-12 flex-shrink-0">File</div>
                    <div class="w-[72px] flex-shrink-0">Aksi</div>
                </div>
                <div class="divide-y divide-slate-100">
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
                                'disetujui' => 'Setuju',
                                'ditolak'   => 'Ditolak',
                                default     => ucfirst($rpp->rpp_status),
                            };
                        @endphp
                        <div class="flex flex-row items-center gap-2 px-4 py-3 hover:bg-slate-50/50 transition">
                            <div class="flex-1 min-w-0 pr-1">
                                <div class="font-bold text-slate-800 text-sm leading-tight truncate">{{ $rpp->user->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $rpp->tingkat }} {{ $rpp->jurusan }}</div>
                            </div>
                            <div class="w-[60px] flex-shrink-0 flex justify-center">
                                <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full border {{ $badge }} leading-none">{{ $label }}</span>
                            </div>
                            <div class="w-12 flex-shrink-0 flex justify-center">
                                <button type="button"
                                    onclick="openRppViewer('{{ Storage::url($rpp->rpp_file) }}', '{{ basename($rpp->rpp_file) }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#1e3a6e]/5 text-[#1e3a6e] hover:bg-[#1e3a6e]/10 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <div class="w-[72px] flex-shrink-0 flex justify-center">
                                @if($rpp->rpp_status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="confirmApprove('{{ $rpp->id }}', '{{ addslashes($rpp->user->name) }}', '{{ $rpp->tingkat }} {{ $rpp->jurusan }}')" class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-100 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <button type="button" onclick="confirmReject('{{ $rpp->id }}', '{{ addslashes($rpp->user->name) }}', '{{ $rpp->tingkat }} {{ $rpp->jurusan }}')" class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] font-semibold text-slate-400">-</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-slate-400 text-sm">Tidak ada data.</div>
                    @endforelse
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-4">Nama Guru</th>
                            <th class="px-6 py-4 text-center">Tingkat</th>
                            <th class="px-6 py-4 text-center">Jurusan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">File RPP</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rppList as $rpp)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-800">{{ $rpp->user->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">NIP: {{ $rpp->user->nomor_induk ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="font-bold text-slate-700">{{ $rpp->tingkat }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="font-bold text-slate-700">{{ $rpp->jurusan }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
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
                                    <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full border {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button type="button"
                                        onclick="openRppViewer('{{ Storage::url($rpp->rpp_file) }}', '{{ basename($rpp->rpp_file) }}')"
                                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#1e3a6e] hover:underline bg-[#1e3a6e]/5 hover:bg-[#1e3a6e]/10 px-3 py-1.5 rounded-full transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat File
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($rpp->rpp_status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <form id="form-approve-{{ $rpp->id }}" method="POST" action="{{ route('piket.persetujuan-rpp.approve', $rpp->id) }}" class="hidden">
                                                @csrf
                                            </form>
                                            <button type="button" onclick="confirmApprove('{{ $rpp->id }}', '{{ addslashes($rpp->user->name) }}', '{{ $rpp->tingkat }} {{ $rpp->jurusan }}')" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 transition" title="Setujui">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                            <form id="form-reject-{{ $rpp->id }}" method="POST" action="{{ route('piket.persetujuan-rpp.reject', $rpp->id) }}" class="hidden">
                                                @csrf
                                                <input type="hidden" name="pesan" id="reject-pesan-{{ $rpp->id }}">
                                            </form>
                                            <button type="button" onclick="confirmReject('{{ $rpp->id }}', '{{ addslashes($rpp->user->name) }}', '{{ $rpp->tingkat }} {{ $rpp->jurusan }}')" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition" title="Tolak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-slate-400">-</span>
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
                                        <p class="font-bold text-slate-700">Tidak ada RPP ditemukan</p>
                                        <p class="text-xs text-slate-400 mt-1">Belum ada RPP yang sesuai dengan kriteria yang dicari.</p>
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

    <style>
        .swal-custom-popup { border-radius: 28px !important; }
        .swal-confirm-btn, .swal-cancel-btn { border-radius: 9999px !important; }
    </style>

    <script>
        function openRppViewer(url, name) {
            var ext      = (name || url).split('.').pop().toLowerCase();
            var overlay  = document.getElementById('rpp-modal-overlay');
            var panel    = document.getElementById('rpp-modal-panel');
            var body     = document.getElementById('rpp-modal-body');
            var filename = document.getElementById('rpp-modal-filename');
            var download = document.getElementById('rpp-modal-download');
            
            filename.textContent = name || 'Dokumen RPP';
            download.href        = url;
            download.setAttribute('download', name || '');
            
            body.innerHTML = '';
            
            // Construct absolute URL safely
            var absoluteUrl = url;
            if (!absoluteUrl.startsWith('http') && !absoluteUrl.startsWith('blob:')) {
                absoluteUrl = window.location.origin + (absoluteUrl.startsWith('/') ? '' : '/') + absoluteUrl;
            }
            
            var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            var isLocalHost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

            if (ext === 'pdf') {
                var iframeUrl = url;
                if (isMobile && !isLocalHost) {
                    iframeUrl = 'https://docs.google.com/gview?url=' + encodeURIComponent(absoluteUrl) + '&embedded=true';
                }
                var iframe = document.createElement('iframe');
                iframe.src = iframeUrl;
                iframe.style.cssText = 'width:100%;height:100%;border:none;border-radius:0 0 20px 20px;display:block;';
                iframe.title = 'Preview PDF';
                body.appendChild(iframe);
            } else if (ext === 'doc' || ext === 'docx') {
                var viewerUrl = 'https://docs.google.com/gview?url=' + encodeURIComponent(absoluteUrl) + '&embedded=true';
                var iframe = document.createElement('iframe');
                iframe.src = viewerUrl;
                iframe.style.cssText = 'width:100%;height:100%;border:none;border-radius:0 0 20px 20px;display:block;';
                iframe.title = 'Preview Dokumen Word';
                body.appendChild(iframe);
            } else {
                body.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;text-align:center;padding:32px;"><p style="font-weight:800;color:#334155;font-size:16px;margin:0 0 8px;">Format Tidak Didukung</p><p style="color:#64748b;font-size:13px;margin:0;">Gunakan tombol <strong>Unduh</strong> untuk membuka file ini.</p></div>';
            }
            
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(function() {
                panel.style.transform = 'scale(1)';
                panel.style.opacity   = '1';
            });
        }

        function closeRppViewer() {
            var overlay = document.getElementById('rpp-modal-overlay');
            var panel   = document.getElementById('rpp-modal-panel');
            panel.style.transform = 'scale(0.95)';
            panel.style.opacity   = '0';
            setTimeout(function() {
                overlay.style.display = 'none';
                document.getElementById('rpp-modal-body').innerHTML = '';
                document.body.style.overflow = '';
            }, 180);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeRppViewer();
        });

        function confirmApprove(id, nama, kelas) {
            Swal.fire({
                title: 'Setujui RPP?',
                text: "Anda akan menyetujui RPP milik " + nama + " untuk kelas " + kelas + ".",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-confirm-btn', cancelButton: 'swal-cancel-btn' },
            }).then(function(result) {
                if (result.isConfirmed) document.getElementById('form-approve-' + id).submit();
            });
        }

        function confirmReject(id, nama, kelas) {
            Swal.fire({
                title: 'Tolak RPP',
                text: "Berikan alasan mengapa RPP milik " + nama + " untuk kelas " + kelas + " ditolak:",
                input: 'textarea',
                inputPlaceholder: 'Masukkan alasan di sini...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tolak RPP',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'swal-custom-popup', confirmButton: 'swal-confirm-btn', cancelButton: 'swal-cancel-btn' },
                preConfirm: function(pesan) {
                    if (!pesan || pesan.trim() === '') { Swal.showValidationMessage('Alasan tidak boleh kosong!'); return false; }
                    return pesan;
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    document.getElementById('reject-pesan-' + id).value = result.value;
                    document.getElementById('form-reject-' + id).submit();
                }
            });
        }

        async function filterData() {
            var url = new URL(window.location.href);
            document.querySelectorAll('.filter-select').forEach(function(select) {
                if (select.value) { url.searchParams.set(select.name, select.value); }
                else { url.searchParams.delete(select.name); }
            });
            window.history.pushState({}, '', url);
            var tableContainer = document.getElementById('table-container');
            if (tableContainer) { tableContainer.style.opacity = '0.5'; tableContainer.style.pointerEvents = 'none'; }
            try {
                var response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                var html = await response.text();
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newTableContainer = doc.getElementById('table-container');
                if (newTableContainer && tableContainer) {
                    tableContainer.innerHTML = newTableContainer.innerHTML;
                    tableContainer.style.opacity = '1';
                    tableContainer.style.pointerEvents = 'auto';
                }
            } catch (error) {
                console.error('Error:', error);
                if (tableContainer) { tableContainer.style.opacity = '1'; tableContainer.style.pointerEvents = 'auto'; }
            }
        }
    </script>

</x-app-layout>
