<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('ebook.index') }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('ebook.index') }}"
                class="text-slate-400 hover:text-[#1e3a6e] text-sm transition-colors">e-Book</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800 truncate max-w-xs">{{ $ebook->judul }}</span>
        </div>
    </x-slot>

    <div class="space-y-5">

        {{-- Header Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    {{-- Level Badge --}}
                    <div
                        class="w-14 h-14 rounded-2xl bg-[#1e3a6e] flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="text-white text-lg font-black">{{ $ebook->level }}</span>
                    </div>
                    <div>
                        <span
                            class="inline-block text-[.65rem] font-bold bg-[#1e3a6e]/10 text-[#1e3a6e] px-2.5 py-0.5 rounded-full uppercase tracking-wide mb-1">
                            {{ $ebook->kategori ?? 'e-Book' }} · Level {{ $ebook->level }}
                        </span>
                        <h1 class="text-xl font-black text-slate-800 leading-tight">{{ $ebook->judul }}</h1>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $ebook->deskripsi }}</p>
                    </div>
                </div>

                {{-- Status --}}
                @if($progres->selesai)
                    <div
                        class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3 flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-xs font-black text-green-700">Sudah Selesai</p>
                            <p class="text-[.65rem] text-green-600">Skor suara: {{ $progres->skor_suara ?? '-' }}% | Kuis:
                                {{ $progres->skor_kuis ?? '-' }}</p>
                        </div>
                    </div>
                @elseif($progres->lulus_suara)
                    <div
                        class="flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <div>
                            <p class="text-xs font-black text-blue-700">Tahap Kuis</p>
                            <p class="text-[.65rem] text-blue-600">Selesaikan kuis untuk menuntaskan e-Book.</p>
                        </div>
                    </div>
                @else
                    <div
                        class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-xs font-black text-amber-700">Sedang Dibaca</p>
                            <p class="text-[.65rem] text-amber-600">Verifikasi suara setelah selesai</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Main Layout: PDF + Voice Panel --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- PDF Viewer --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="border-b border-slate-100 px-5 py-3 flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Area Membaca
                        </span>
                        @if($ebook->file_pdf)
                            <a href="{{ asset('storage/' . $ebook->file_pdf) }}" target="_blank"
                                class="text-xs font-bold text-[#1e3a6e] hover:underline flex items-center gap-1 bg-[#1e3a6e]/10 hover:bg-[#1e3a6e]/20 px-3 py-1.5 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Unduh / Buka PDF
                            </a>
                        @endif
                    </div>

                    @if($ebook->file_pdf)
                        @php
                            $pdfUrl = asset('storage/' . $ebook->file_pdf);
                            $pdfPath = storage_path('app/public/' . $ebook->file_pdf);
                            $pdfExists = file_exists($pdfPath);
                        @endphp

                        @if($pdfExists)
                            {{-- Universal PDF Viewer (PDF.js) --}}
                            <div class="relative w-full bg-slate-50 border-b border-slate-100 flex flex-col"
                                style="height: 75vh; min-height: 500px;">
                                <!-- Toolbar -->
                                <div
                                    class="bg-slate-800 text-white px-4 py-3 flex items-center justify-between shadow z-10 flex-wrap gap-3">
                                    <div class="flex items-center gap-2">
                                        <button id="prev_page"
                                            class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-lg text-sm transition flex items-center justify-center"
                                            title="Halaman Sebelumnya">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <span class="text-sm font-medium mx-1 whitespace-nowrap">Hal: <span
                                                id="page_num">1</span> / <span id="page_count">--</span></span>
                                        <button id="next_page"
                                            class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-lg text-sm transition flex items-center justify-center"
                                            title="Halaman Selanjutnya">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button id="zoom_out"
                                            class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-lg text-sm font-bold transition w-10 flex items-center justify-center"
                                            title="Perkecil">-</button>
                                        <span class="text-xs font-bold text-slate-300 w-12 text-center"
                                            id="zoom_val">100%</span>
                                        <button id="zoom_in"
                                            class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 active:bg-slate-500 rounded-lg text-sm font-bold transition w-10 flex items-center justify-center"
                                            title="Perbesar">+</button>
                                    </div>
                                </div>

                                <!-- Canvas Container -->
                                <div class="flex-1 overflow-auto bg-slate-300 flex justify-center p-2 sm:p-6"
                                    id="canvas_container">
                                    <canvas id="pdf_render_canvas" class="shadow-2xl rounded"></canvas>
                                </div>
                            </div>

                            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const url = '{{ $pdfUrl }}';
                                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

                                    let pdfDoc = null,
                                        pageNum = 1,
                                        pageRendering = false,
                                        pageNumPending = null,
                                        scale = 1.0,
                                        canvas = document.getElementById('pdf_render_canvas'),
                                        ctx = canvas.getContext('2d'),
                                        container = document.getElementById('canvas_container'),
                                        initialFit = true;

                                    function renderPage(num) {
                                        pageRendering = true;
                                        pdfDoc.getPage(num).then(function (page) {
                                            let unscaledViewport = page.getViewport({ scale: 1.0 });

                                            if (initialFit) {
                                                const containerWidth = container.clientWidth - 32;
                                                if (unscaledViewport.width > containerWidth) {
                                                    scale = containerWidth / unscaledViewport.width;
                                                }
                                                initialFit = false;
                                            }

                                            let viewport = page.getViewport({ scale: scale });

                                            // Support HiDPI-displays for better resolution on mobile/retina
                                            let outputScale = window.devicePixelRatio || 1;
                                            canvas.width = Math.floor(viewport.width * outputScale);
                                            canvas.height = Math.floor(viewport.height * outputScale);
                                            canvas.style.width = Math.floor(viewport.width) + "px";
                                            canvas.style.height = Math.floor(viewport.height) + "px";

                                            let transform = outputScale !== 1
                                                ? [outputScale, 0, 0, outputScale, 0, 0]
                                                : null;

                                            document.getElementById('zoom_val').textContent = Math.round(scale * 100) + '%';

                                            const renderContext = {
                                                canvasContext: ctx,
                                                transform: transform,
                                                viewport: viewport
                                            };
                                            const renderTask = page.render(renderContext);

                                            renderTask.promise.then(function () {
                                                pageRendering = false;
                                                if (pageNumPending !== null) {
                                                    renderPage(pageNumPending);
                                                    pageNumPending = null;
                                                }
                                            });
                                        });
                                        document.getElementById('page_num').textContent = num;
                                    }

                                    function queueRenderPage(num) {
                                        if (pageRendering) {
                                            pageNumPending = num;
                                        } else {
                                            renderPage(num);
                                        }
                                    }

                                    document.getElementById('prev_page').addEventListener('click', function () {
                                        if (pageNum <= 1) return;
                                        pageNum--;
                                        queueRenderPage(pageNum);
                                    });

                                    document.getElementById('next_page').addEventListener('click', function () {
                                        if (pageNum >= pdfDoc.numPages) return;
                                        pageNum++;
                                        queueRenderPage(pageNum);
                                    });

                                    document.getElementById('zoom_in').addEventListener('click', function () {
                                        scale += 0.2;
                                        queueRenderPage(pageNum);
                                    });

                                    document.getElementById('zoom_out').addEventListener('click', function () {
                                        if (scale <= 0.4) return;
                                        scale -= 0.2;
                                        queueRenderPage(pageNum);
                                    });

                                    pdfjsLib.getDocument(url).promise.then(function (pdfDoc_) {
                                        pdfDoc = pdfDoc_;
                                        document.getElementById('page_count').textContent = pdfDoc.numPages;
                                        renderPage(pageNum);
                                    }).catch(function (error) {
                                        console.error('Error loading PDF:', error);
                                        container.innerHTML = '<div class="flex flex-col items-center justify-center text-center p-8 w-full"><p class="text-red-500 font-bold mb-2">Gagal memuat PDF</p><p class="text-sm text-slate-500">' + error.message + '</p></div>';
                                    });
                                });
                            </script>
                        @else
                            {{-- File tidak ditemukan di disk --}}
                            <div class="flex flex-col items-center justify-center py-20 text-center bg-slate-50 px-8">
                                <svg class="w-16 h-16 text-amber-300 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="font-bold text-slate-500 text-lg mb-1">File PDF tidak ditemukan</p>
                                <p class="text-sm text-slate-400">Silakan hubungi admin untuk mengunggah ulang file PDF buku
                                    ini.</p>
                            </div>
                        @endif

                    @else
                        {{-- Tidak ada file PDF --}}
                        <div class="flex flex-col items-center justify-center py-20 text-center bg-slate-50 px-8">
                            <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="font-bold text-slate-400 text-lg mb-1">File PDF belum tersedia</p>
                            <p class="text-sm text-slate-400">Admin belum mengunggah file PDF untuk e-Book ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Verification Panel --}}
            <div class="space-y-4">

                @if(!$progres->lulus_suara && !$progres->selesai)
                    @if(Auth::user()->skip_voice_verification)
                        {{-- Bypass Voice Recorder --}}
                        <div class="bg-white rounded-2xl border border-amber-200 p-5 space-y-4" id="voicePanel">
                            <h3 class="text-sm font-black text-amber-700 flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-amber-100 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                Verifikasi Suara Dinonaktifkan
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Fitur verifikasi suara dimatikan untuk akun Anda. Anda dapat langsung melanjutkan ke tahap kuis
                                setelah selesai membaca materi e-Book.
                            </p>
                            <button id="btnSkipVoice"
                                class="w-full flex items-center justify-center gap-2 bg-amber-500 text-white font-bold py-3 rounded-xl text-sm transition duration-200 hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                Selesai Membaca & Lanjut
                            </button>
                            <div id="hasilVerifikasi" class="hidden"></div>
                        </div>
                    @else
                        {{-- Voice Recorder --}}
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4" id="voicePanel">
                            <h3 class="text-sm font-black text-slate-700 flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                    </svg>
                                </div>
                                Verifikasi Suara
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Bacakan isi e-Book untuk diverifikasi. Sistem akan mencocokkannya dengan buku. Minimal
                                <strong>60%</strong> kesamaan untuk lulus.
                            </p>

                            {{-- Visualizer --}}
                            <div id="visualizer"
                                class="h-16 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center gap-1 px-4 overflow-hidden">
                                <span class="text-xs text-slate-400 font-medium" id="visualizerText">Tekan tombol untuk mulai
                                    merekam</span>
                                <canvas id="waveCanvas" class="hidden w-full h-full"></canvas>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex gap-2">
                                <button id="btnRecord" class="flex-1 flex items-center justify-center gap-2 bg-[#1e3a6e] hover:bg-[#162d57]
                                           text-white font-bold py-3 rounded-xl text-sm transition duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                    </svg>
                                    <span id="btnRecordText">Mulai Rekam</span>
                                </button>
                                <button id="btnStop" disabled
                                    class="flex-1 flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600
                                           text-white font-bold py-3 rounded-xl text-sm transition duration-200 disabled:opacity-40 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                                    </svg>
                                    <span>Berhenti</span>
                                </button>
                            </div>

                            {{-- Transcript Box --}}
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5 block">Hasil
                                    Transkripsi</label>
                                <textarea id="transkrip" rows="4" readonly
                                    placeholder="Transkripsi suara Anda akan muncul di sini secara otomatis..."
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700
                                             focus:outline-none focus:ring-2 focus:ring-[#1e3a6e]/30 focus:border-[#1e3a6e] resize-none cursor-not-allowed">{{ $progres->akumulasi_teks ?? '' }}</textarea>
                                <div id="autoSaveIndicator" class="text-xs font-semibold text-slate-400 mt-1 h-4 flex items-center"></div>
                            </div>

                            <button id="btnVerify" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-[#1e3a6e] to-[#2d5299]
                                       text-white font-bold py-3 rounded-xl text-sm transition duration-200 hover:shadow-lg
                                       hover:shadow-[#1e3a6e]/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verifikasi & Kirim
                            </button>

                            {{-- Hasil --}}
                            <div id="hasilVerifikasi" class="hidden"></div>
                        </div>
                    @endif
                @endif

                {{-- Catatan Progres Membaca --}}
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100 flex-shrink-0">
                            <svg class="w-4 h-4 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-700">Catatan Progres Membaca</h3>
                            <p class="text-[0.65rem] text-slate-400 font-medium">Laporan perkembangan bacaanmu.</p>
                        </div>
                    </div>
                    <div class="p-5">
                        @if(session('success_catatan'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-xs font-medium px-3 py-2 rounded-xl flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success_catatan') }}
                        </div>
                        @endif
                        @error('catatan')
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-xs font-medium px-3 py-2 rounded-xl">{{ $message }}</div>
                        @enderror

                        <form action="{{ route('catatan.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jenis_buku" value="digital">
                            <input type="hidden" name="buku_id" value="{{ $ebook->id }}">
                            <textarea name="catatan" rows="4" maxlength="2000"
                                      class="w-full border border-slate-200 focus:border-[#1e3a6e] focus:ring-2 focus:ring-[#1e3a6e]/10 rounded-xl px-4 py-3 text-slate-800 font-medium focus:outline-none transition text-sm placeholder-slate-400"
                                      placeholder="Tuliskan catatan progres membacamu di sini...">{{ $catatan?->catatan }}</textarea>
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-3 gap-3">
                                <p class="text-xs text-slate-400">Maks. 2000 karakter.</p>
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1e3a6e] hover:bg-[#162d57] transition shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Navigasi --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-wide mb-3">Navigasi</p>
                    <div class="space-y-2">
                        <a href="{{ route('ebook.index') }}"
                            class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-[#1e3a6e]" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span class="text-sm font-medium text-slate-600 group-hover:text-[#1e3a6e]">Kembali ke
                                Koleksi</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const btnRecord = document.getElementById('btnRecord');
            const btnStop = document.getElementById('btnStop');
            const btnVerify = document.getElementById('btnVerify');
            const transkripEl = document.getElementById('transkrip');
            const hasilEl = document.getElementById('hasilVerifikasi');
            const vizText = document.getElementById('visualizerText');
            const waveCanvas = document.getElementById('waveCanvas');

            let recognition = null;
            let isRecording = false;

            // Speech Recognition setup
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!SpeechRecognition) {
                if (btnRecord) {
                    btnRecord.disabled = true;
                    btnRecord.textContent = 'Browser tidak mendukung';
                    btnRecord.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } else {
                recognition = new SpeechRecognition();
                recognition.lang = 'id-ID';
                recognition.continuous = true;
                recognition.interimResults = true;

                let autoSaveTimer = null;
                function autoSaveTranscript(text) {
                    const indicator = document.getElementById('autoSaveIndicator');
                    if (indicator) indicator.innerHTML = '<span class="text-amber-500">Menyimpan...</span>';
                    
                    fetch('{{ route("ebook.voice-save", $ebook->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ teks_suara: text }),
                    }).then(() => {
                        if (indicator) indicator.innerHTML = '<span class="text-green-500">Tersimpan otomatis</span>';
                        setTimeout(() => { if (indicator) indicator.innerHTML = ''; }, 2000);
                    }).catch(() => {
                        if (indicator) indicator.innerHTML = '<span class="text-red-500">Gagal menyimpan</span>';
                    });
                }

                recognition.onresult = function (event) {
                    if (!transkripEl) return;
                    let interimTranscript = '';
                    let finalTranscript = transkripEl.dataset.final || '';

                    for (let i = event.resultIndex; i < event.results.length; ++i) {
                        if (event.results[i].isFinal) {
                            finalTranscript += event.results[i][0].transcript + ' ';
                            // Trigger autosave when a sentence/phrase is final
                            clearTimeout(autoSaveTimer);
                            autoSaveTimer = setTimeout(() => autoSaveTranscript(transkripEl.dataset.final), 1000);
                        } else {
                            interimTranscript += event.results[i][0].transcript;
                        }
                    }
                    transkripEl.dataset.final = finalTranscript;
                    transkripEl.value = finalTranscript + interimTranscript;
                };

                recognition.onerror = function (e) {
                    console.error(e);
                    stopRecording();
                };

                recognition.onend = function () {
                    if (isRecording) recognition.start(); // terus rekam
                };
            }

            function startRecording() {
                if (!transkripEl) return;
                isRecording = true;
                // Preserve existing text instead of clearing it
                transkripEl.dataset.final = transkripEl.value.trim() ? transkripEl.value.trim() + ' ' : '';
                recognition.start();

                btnRecord.disabled = true;
                btnRecord.classList.add('opacity-50');
                btnStop.disabled = false;
                vizText.classList.add('hidden');
                waveCanvas.classList.remove('hidden');
                document.getElementById('btnRecordText').textContent = 'Merekam...';
                animateWave();
            }

            function stopRecording() {
                isRecording = false;
                if (recognition) recognition.stop();
                if (btnRecord) {
                    btnRecord.disabled = false;
                    btnRecord.classList.remove('opacity-50');
                    btnStop.disabled = true;
                    waveCanvas.classList.add('hidden');
                    vizText.classList.remove('hidden');
                    vizText.textContent = 'Rekaman dijeda. Klik Lanjutkan Rekam untuk meneruskan.';
                    document.getElementById('btnRecordText').textContent = 'Lanjutkan Rekam';
                }
                
                // Final autosave when manually stopped
                if (transkripEl && typeof autoSaveTranscript === 'function') {
                    autoSaveTranscript(transkripEl.dataset.final || transkripEl.value);
                }
            }

            if (btnRecord) btnRecord.addEventListener('click', startRecording);
            if (btnStop) btnStop.addEventListener('click', stopRecording);

            // Wave animation
            function animateWave() {
                const canvas = waveCanvas;
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                let t = 0;

                function draw() {
                    if (!isRecording) return;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.strokeStyle = '#1e3a6e';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    for (let x = 0; x < canvas.width; x++) {
                        const y = canvas.height / 2 + Math.sin((x * 0.04) + t) * (canvas.height / 4);
                        x === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
                    }
                    ctx.stroke();
                    t += 0.12;
                    requestAnimationFrame(draw);
                }
                draw();
            }

            // Skip Voice
            const btnSkipVoice = document.getElementById('btnSkipVoice');
            if (btnSkipVoice) {
                btnSkipVoice.addEventListener('click', function () {
                    btnSkipVoice.disabled = true;
                    btnSkipVoice.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg> Memproses...`;

                    fetch('{{ route("ebook.voice-skip", $ebook->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.lulus) {
                                showHasil('success', data.pesan);
                                confetti();
                                setTimeout(() => location.reload(), 2000);
                            } else {
                                showHasil('error', data.error || 'Terjadi kesalahan.');
                                btnSkipVoice.disabled = false;
                                btnSkipVoice.innerHTML = 'Selesai Membaca & Lanjut';
                            }
                        })
                        .catch(() => {
                            showHasil('error', 'Terjadi kesalahan jaringan.');
                            btnSkipVoice.disabled = false;
                            btnSkipVoice.innerHTML = 'Selesai Membaca & Lanjut';
                        });
                });
            }

            // Verify
            if (btnVerify) {
                btnVerify.addEventListener('click', function () {
                    const teks = transkripEl.value.trim();
                    if (!teks) {
                        showHasil('warning', 'Belum ada transkripsi. Rekam suara terlebih dahulu.');
                        return;
                    }

                    btnVerify.disabled = true;
                    btnVerify.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg> Memverifikasi...`;

                    fetch('{{ route("ebook.voice-check", $ebook->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ teks_suara: teks }),
                    })
                        .then(r => r.json())
                        .then(data => {
                            const type = data.lulus ? 'success' : 'warning';
                            showHasil(type, data.pesan, data.skor);

                            if (data.lulus) {
                                confetti();
                                setTimeout(() => location.reload(), 3000);
                            }
                        })
                        .catch(() => showHasil('error', 'Terjadi kesalahan. Coba lagi.'))
                        .finally(() => {
                            btnVerify.disabled = false;
                            btnVerify.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg> Verifikasi & Kirim`;
                        });
                });
            }

            function showHasil(type, pesan, skor) {
                if (!hasilEl) return;
                const colors = {
                    success: { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-800', icon: 'text-green-500' },
                    warning: { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-800', icon: 'text-amber-500' },
                    error: { bg: 'bg-red-50', border: 'border-red-200', text: 'text-red-800', icon: 'text-red-500' },
                };
                const c = colors[type] || colors.warning;
                hasilEl.className = `rounded-xl border p-4 ${c.bg} ${c.border}`;
                hasilEl.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 ${c.icon} flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="${type === 'success'
                        ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                        : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}"/>
                </svg>
                <div>
                    <p class="text-sm font-bold ${c.text}">${pesan}</p>
                    ${skor !== undefined ? `
                    <div class="mt-2">
                        <div class="flex justify-between text-xs font-bold ${c.text} mb-1">
                            <span>Skor Kesamaan</span><span>${skor}%</span>
                        </div>
                        <div class="w-full bg-white/60 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-700 ${type === 'success' ? 'bg-green-500' : 'bg-amber-400'}"
                                 style="width: ${Math.min(skor, 100)}%"></div>
                        </div>
                    </div>` : ''}
                </div>
            </div>`;
                hasilEl.classList.remove('hidden');
            }

            // Simple confetti
            function confetti() {
                const colors = ['#1e3a6e', '#2d5299', '#60a5fa', '#34d399', '#fbbf24'];
                for (let i = 0; i < 60; i++) {
                    const el = document.createElement('div');
                    el.style.cssText = `position:fixed;width:8px;height:8px;border-radius:50%;
                background:${colors[Math.floor(Math.random() * colors.length)]};
                left:${Math.random() * 100}vw;top:-10px;z-index:9999;
                animation:fall ${1 + Math.random() * 2}s linear forwards`;
                    document.body.appendChild(el);
                    setTimeout(() => el.remove(), 3000);
                }
                if (!document.getElementById('confettiStyle')) {
                    const s = document.createElement('style');
                    s.id = 'confettiStyle';
                    s.textContent = '@keyframes fall{to{transform:translateY(110vh) rotate(720deg);opacity:0}}';
                    document.head.appendChild(s);
                }
            }
        })();
    </script>
</x-app-layout>