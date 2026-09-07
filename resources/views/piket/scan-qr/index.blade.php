<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Scan Absensi offline</span>
    </x-slot>
    <div class="space-y-6">
        <div class="flex flex-col justify-between items-start gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Scan Absen QR Code</h2>
                <p class="text-slate-500 text-sm mt-1">Scan QR Code milik murid untuk melakukan absensi (offline murid).</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Bagian Scanner -->
            <div class="md:col-span-7 lg:col-span-8 space-y-6">
                <div class="app-card p-6">
                    <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-100 flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-sm text-blue-800">
                            <strong>Jadwal Absensi Hari Ini:</strong> 
                            @if($jadwalAbsensi && !$jadwalAbsensi->is_libur)
                                Absen Datang ({{ \Carbon\Carbon::parse($jadwalAbsensi->absen_datang_buka)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalAbsensi->absen_datang_tutup)->format('H:i') }} WITA) dan Absen Pulang ({{ \Carbon\Carbon::parse($jadwalAbsensi->absen_pulang_buka)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalAbsensi->absen_pulang_tutup)->format('H:i') }} WITA).
                            @else
                                Hari ini libur, tidak ada absensi.
                            @endif
                        </div>
                    </div>

                    <div class="mb-4 flex justify-center" id="start-btn-container">
                        <button id="btn-start-scan" class="bg-[#1e3a6e] hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-colors flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Mulai Scan
                        </button>
                    </div>

                    <div id="reader-container" class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 relative min-h-[300px] flex items-center justify-center hidden">
                        <div id="reader" style="width: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Bagian Log Hasil Scan -->
            <div class="md:col-span-5 lg:col-span-4">
                <div class="app-card p-6 h-full">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Riwayat Scan Terbaru
                    </h3>
                    
                    <div id="scan-logs" class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                        <!-- Logs akan muncul di sini -->
                        <div class="text-center text-slate-400 text-sm py-4 italic" id="empty-log">
                            Belum ada data hasil scan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gunakan html5-qrcode & SweetAlert2 -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const html5QrCode = new Html5Qrcode("reader");
            let isScanning = false;
            let isProcessing = false;
            
            // Set untuk menyimpan data QR yang sudah diproses agar tidak request berulang
            const processedQRs = new Set();

            const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };
            // Suara sintesis Web Audio API (Tidak butuh URL eksternal)
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            const audioCtx = new AudioContext();

            function playTone(freq, type, duration, startTime) {
                if (audioCtx.state === 'suspended') audioCtx.resume();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(freq, startTime);
                
                // Volume diperbesar ke 1.0
                gain.gain.setValueAtTime(1.0, startTime);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(startTime);
                osc.stop(startTime + duration);
            }

            const beepAudio = {
                play: () => {
                    playTone(880, 'square', 0.15, audioCtx.currentTime); // Lebih keras dan jelas
                    return Promise.resolve();
                },
                pause: () => {},
                currentTime: 0
            };

            const successAudio = {
                play: () => {
                    const t = audioCtx.currentTime;
                    playTone(523.25, 'triangle', 0.15, t);      // Nada 1
                    playTone(659.25, 'triangle', 0.3, t + 0.15); // Nada 2
                    return Promise.resolve();
                }
            };

            const errorAudio = {
                play: () => {
                    playTone(150, 'sawtooth', 0.3, audioCtx.currentTime);
                    playTone(155, 'sawtooth', 0.3, audioCtx.currentTime);
                    return Promise.resolve();
                }
            };

            function onScanSuccess(decodedText, decodedResult) {
                if (isProcessing) return;
                
                const cacheKey = decodedText;

                // Jika QR ini sudah pernah diproses dengan sukses (atau mendapat error logika), abaikan diam-diam
                if (processedQRs.has(cacheKey)) {
                    return;
                }

                isProcessing = true;
                beepAudio.play().catch(e => console.log('Audio play failed:', e));
                
                // Pause scanner sebentar agar tidak terscan berulang saat sedang request
                html5QrCode.pause();

                fetch("{{ route('guru.scan-qr.process') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        qr_data: decodedText
                    })
                })
                .then(async response => {
                    const status = response.status;
                    const data = await response.json();
                    return { status, data };
                })
                .then(({ status, data }) => {
                    addLog(data.success, data.message, decodedText);
                    
                    if (data.show_popup) {
                        Swal.fire({
                            title: 'Perhatian!',
                            text: data.message,
                            icon: 'warning',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#1e3a6e',
                            background: '#ffffff',
                            customClass: {
                                title: 'text-xl font-bold text-slate-800',
                                popup: 'rounded-2xl border border-slate-200 shadow-xl'
                            }
                        });
                    }
                    
                    if (data.success) {
                        successAudio.play().catch(e => console.log('Success audio play failed:', e));
                    } else {
                        errorAudio.play().catch(e => console.log('Error audio play failed:', e));
                    }
                    
                    // Jika sukses, ATAU jika mendapat response error logika dari server (seperti hari libur, sudah absen, dll)
                    if (data.success || status === 422 || status === 404 || status === 403) {
                        processedQRs.add(cacheKey);
                    }
                    
                    // Resume setelah 1.5 detik
                    setTimeout(() => {
                        isProcessing = false;
                        html5QrCode.resume();
                    }, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    addLog(false, 'Terjadi kesalahan sistem saat memproses scan.', decodedText);
                    errorAudio.play().catch(e => console.log('Error audio play failed:', e));
                    
                    setTimeout(() => {
                        isProcessing = false;
                        html5QrCode.resume();
                    }, 1500);
                });
            }

            function onScanFailure(error) {
                // Diabaikan saja karena failure ini terus berjalan selama mencari QR
            }

            // Meminta izin kamera dan memulai scanning ketika tombol ditekan
            document.getElementById('btn-start-scan').addEventListener('click', function() {
                // Mainkan suara dummy sekilas untuk membuka "Audio Context" browser
                beepAudio.play().then(() => { beepAudio.pause(); beepAudio.currentTime = 0; }).catch(e => console.log(e));
                
                document.getElementById('start-btn-container').style.display = 'none';
                document.getElementById('reader-container').classList.remove('hidden');

                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length) {
                        let cameraId = devices[0].id;
                        // Pilih kamera belakang jika memungkinkan
                        for(let i=0; i<devices.length; i++) {
                            if (devices[i].label.toLowerCase().includes('back') || devices[i].label.toLowerCase().includes('environment')) {
                                cameraId = devices[i].id;
                                break;
                            }
                        }

                        html5QrCode.start(
                            cameraId, 
                            config, 
                            onScanSuccess, 
                            onScanFailure
                        )
                        .then(() => {
                            isScanning = true;
                        })
                        .catch(err => {
                            console.error('Gagal memulai scanner:', err);
                            alert("Gagal mengakses kamera. Pastikan Anda memberikan izin kamera.");
                        });
                    } else {
                        alert("Tidak ada kamera yang terdeteksi di perangkat ini.");
                    }
                }).catch(err => {
                    console.error(err);
                    alert("Kesalahan saat mengecek ketersediaan kamera: " + err);
                });
            });

            // Helper untuk menambahkan log ke UI
            function addLog(isSuccess, message, qrText) {
                const logsContainer = document.getElementById('scan-logs');
                const emptyLog = document.getElementById('empty-log');
                if (emptyLog) emptyLog.remove();

                const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const bgColor = isSuccess ? 'bg-green-50' : 'bg-red-50';
                const borderColor = isSuccess ? 'border-green-200' : 'border-red-200';
                const textColor = isSuccess ? 'text-green-800' : 'text-red-800';
                const icon = isSuccess 
                    ? '<svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                    : '<svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

                const logEl = document.createElement('div');
                logEl.className = `p-3 rounded-xl border ${borderColor} ${bgColor} flex gap-3 anim-up`;
                logEl.innerHTML = `
                    ${icon}
                    <div class="min-w-0">
                        <p class="text-sm font-semibold ${textColor}">${message}</p>
                        <p class="text-xs text-slate-500 mt-1">NISN: ${qrText} &bull; ${time}</p>
                    </div>
                `;

                logsContainer.prepend(logEl);
            }
        });
    </script>
</x-app-layout>

