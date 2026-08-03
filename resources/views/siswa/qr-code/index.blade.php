<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">QR Code Absensi</h2>
                <p class="text-slate-500 text-sm mt-1">Gunakan QR Code ini untuk melakukan absensi saat Anda tidak memiliki koneksi internet.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="alert-error anim-up">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="app-card p-6 md:p-8 flex flex-col items-center justify-center anim-up">
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6" id="qr-code-container">
                {!! $qrCode !!}
            </div>
            
            <h3 class="text-xl font-bold text-slate-800">{{ $user->name }}</h3>
            <p class="text-slate-500 font-medium mt-1">NISN: {{ $user->nomor_induk }}</p>
            
            <div class="mt-8 flex gap-3">
                <button onclick="downloadQR()" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download QR Code
                </button>
                <button onclick="printQR()" class="btn-outline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak
                </button>
            </div>
            
            <div class="mt-8 max-w-md w-full text-left bg-blue-50 text-blue-800 p-5 rounded-xl text-sm leading-relaxed border border-blue-100 shadow-sm">
                <p class="font-bold mb-2 text-base">Cara Penggunaan:</p>
                <ol class="list-decimal pl-5 space-y-1">
                    <li>Simpan gambar QR Code ini ke galeri HP Anda, atau cetak di kertas.</li>
                    <li>Saat di sekolah, tunjukkan QR Code ini ke guru yang bertugas piket.</li>
                    <li>Guru akan melakukan scan menggunakan sistem ini untuk mencatat kehadiran Anda.</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Scripts for Download and Print -->
    <script>
        function downloadQR() {
            const svg = document.querySelector('#qr-code-container svg');
            if (!svg) return alert('QR Code tidak ditemukan');
            
            // Serialize the SVG
            const serializer = new XMLSerializer();
            let source = serializer.serializeToString(svg);
            
            // Add xml namespaces
            if(!source.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)){
                source = source.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
            }
            if(!source.match(/^<svg[^>]+"http\:\/\/www\.w3\.org\/1999\/xlink"/)){
                source = source.replace(/^<svg/, '<svg xmlns:xlink="http://www.w3.org/1999/xlink"');
            }
            
            source = '<?xml version="1.0" standalone="no"?>\r\n' + source;
            const url = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(source);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = "QR_Code_Absensi_{{ $user->nomor_induk }}.svg";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        function printQR() {
            const svgContent = document.querySelector('#qr-code-container').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Cetak QR Code</title>
                        <style>
                            body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; }
                            .container { text-align: center; border: 2px dashed #ccc; padding: 40px; border-radius: 20px; }
                            h2 { margin: 10px 0 5px; color: #333; }
                            p { margin: 0; color: #666; font-size: 18px; }
                            svg { width: 300px; height: 300px; margin-bottom: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            ${svgContent}
                            <h2>{{ $user->name }}</h2>
                            <p>NISN: {{ $user->nomor_induk }}</p>
                        </div>
                        <script>
                            window.onload = function() { window.print(); window.close(); }
                        <\/script>
                    </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
</x-app-layout>
