<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-black text-[#24417c] tracking-tight mb-2">Verifikasi Perangkat</h2>
        <p class="text-gray-500 font-medium text-sm">Akun ini terikat pada perangkat Anda. Silakan verifikasi untuk melanjutkan.</p>
    </div>

    <div id="error-message" class="hidden mb-4 p-3 bg-red-50 text-red-600 text-sm font-semibold rounded-lg border border-red-200">
    </div>

    <div class="space-y-4">
        <button id="btn-auth" type="button" class="w-full flex justify-center items-center bg-[#24417c] text-white font-bold text-lg px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
            <span id="btn-text">Verifikasi Sekarang</span>
            <span id="btn-loading" class="hidden items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            </span>
        </button>

        <form method="POST" action="{{ route('logout') }}" class="text-center pt-4">
            @csrf
            <button type="submit" class="text-sm font-medium text-gray-500 hover:text-red-600">
                Gunakan Akun Lain
            </button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAuth = document.getElementById('btn-auth');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');
        const errorDiv = document.getElementById('error-message');

        function showError(msg) {
            errorDiv.textContent = msg;
            errorDiv.classList.remove('hidden');
        }

        function hideError() {
            errorDiv.classList.add('hidden');
        }

        async function authenticateDevice() {
            hideError();
            
            if (!window.PublicKeyCredential) {
                showError("Browser atau perangkat Anda tidak mendukung verifikasi perangkat. Coba gunakan Chrome/Edge/Safari versi terbaru.");
                return;
            }

            btnAuth.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            btnLoading.classList.add('flex');

            try {
                // 1. Ambil options dari server
                const optResp = await fetch('{{ route("webauthn.authenticate.options", [], false) }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!optResp.ok) throw new Error("Gagal mengambil data dari server. Pastikan Anda sudah login.");
                const optData = await optResp.json();
                
                // Decode challenge
                optData.challenge = Uint8Array.from(atob(optData.challenge), c => c.charCodeAt(0));
                
                // Decode allowCredentials id
                if (optData.allowCredentials) {
                    optData.allowCredentials.forEach(cred => {
                        cred.id = Uint8Array.from(atob(cred.id), c => c.charCodeAt(0));
                    });
                }
                
                // 2. Minta verifikasi ke browser
                let assertion;
                try {
                    assertion = await navigator.credentials.get({ publicKey: optData });
                } catch (e) {
                    throw new Error("Verifikasi dibatalkan atau gagal. Pastikan Anda menggunakan perangkat yang sama saat mendaftar.");
                }

                if (!assertion) {
                    throw new Error("Gagal mendapatkan assertion dari perangkat.");
                }

                // 3. Siapkan data untuk dikirim
                const clientDataJSON = btoa(String.fromCharCode(...new Uint8Array(assertion.response.clientDataJSON)));
                const authenticatorData = btoa(String.fromCharCode(...new Uint8Array(assertion.response.authenticatorData)));
                const signature = btoa(String.fromCharCode(...new Uint8Array(assertion.response.signature)));
                
                // 4. Kirim hasil verifikasi ke server
                const verifyResp = await fetch('{{ route("webauthn.authenticate.verify", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: assertion.id,
                        clientDataJSON: clientDataJSON,
                        authenticatorData: authenticatorData,
                        signature: signature
                    })
                });

                const result = await verifyResp.json();
                
                if (verifyResp.ok && result.success) {
                    window.location.href = '{{ route("dashboard") }}';
                } else {
                    throw new Error(result.message || "Gagal memverifikasi di server.");
                }

            } catch (err) {
                console.error(err);
                showError(err.message);
                btnAuth.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
                btnLoading.classList.remove('flex');
            }
        }

        btnAuth.addEventListener('click', authenticateDevice);
        
        // Auto trigger jika memungkinkan
        setTimeout(() => {
            if (window.PublicKeyCredential) {
                authenticateDevice().catch(e => {
                    btnAuth.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                    btnLoading.classList.remove('flex');
                });
            }
        }, 500);
    });
    </script>
</x-guest-layout>
