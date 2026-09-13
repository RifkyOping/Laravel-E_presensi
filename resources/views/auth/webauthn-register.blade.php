<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-black text-[#24417c] tracking-tight mb-2">Daftarkan Perangkat</h2>
        <p class="text-gray-500 font-medium text-sm">Untuk keamanan, akun ini akan diikat ke perangkat Anda saat ini menggunakan Passkey / Verifikasi Biometrik.</p>
    </div>

    <div id="error-message" class="hidden mb-4 p-3 bg-red-50 text-red-600 text-sm font-semibold rounded-lg border border-red-200">
    </div>

    <div class="space-y-4">
        <button id="btn-register" type="button" class="w-full flex justify-center items-center bg-[#24417c] text-white font-bold text-lg px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
            <span id="btn-text">Daftarkan Perangkat Sekarang</span>
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
                Batal dan Keluar
            </button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnRegister = document.getElementById('btn-register');
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

        async function registerDevice() {
            hideError();
            
            // Cek ketersediaan WebAuthn
            if (!window.PublicKeyCredential) {
                showError("Browser atau perangkat Anda tidak mendukung verifikasi perangkat. Coba gunakan Chrome/Edge/Safari versi terbaru.");
                return;
            }

            // Tampilkan loading
            btnRegister.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            btnLoading.classList.add('flex');

            try {
                // 1. Ambil options dari server
                const optResp = await fetch('{{ route("webauthn.register.options", [], false) }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!optResp.ok) throw new Error("Gagal mengambil data dari server. Pastikan Anda sudah login.");
                const optData = await optResp.json();
                
                // Decode base64 challenge dan user id
                optData.challenge = Uint8Array.from(atob(optData.challenge), c => c.charCodeAt(0));
                optData.user.id = Uint8Array.from(atob(optData.user.id), c => c.charCodeAt(0));
                
                // 2. Minta pembuatan credential ke browser
                let credential;
                try {
                    credential = await navigator.credentials.create({ publicKey: optData });
                } catch (e) {
                    throw new Error("Pendaftaran dibatalkan atau gagal di perangkat Anda.");
                }

                // 3. Ekstrak public key (SPKI) dari credential (Chrome 97+, Firefox 119+, Safari 16+)
                let pubKeyBase64 = null;
                if (typeof credential.response.getPublicKey === 'function') {
                    const pubKeyBuffer = credential.response.getPublicKey();
                    if (pubKeyBuffer) {
                        pubKeyBase64 = btoa(String.fromCharCode(...new Uint8Array(pubKeyBuffer)));
                    }
                }
                
                if (!pubKeyBase64) {
                    throw new Error("Browser Anda tidak mendukung metode ekstraksi public key (getPublicKey). Silakan gunakan Chrome/Edge versi terbaru.");
                }

                const clientDataJSON = btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON)));
                const authenticatorData = btoa(String.fromCharCode(...new Uint8Array(credential.response.getAuthenticatorData())));
                
                // 4. Kirim hasil kembali ke server
                const verifyResp = await fetch('{{ route("webauthn.register.verify", [], false) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: credential.id,
                        clientDataJSON: clientDataJSON,
                        authenticatorData: authenticatorData,
                        publicKey: pubKeyBase64
                    })
                });

                const result = await verifyResp.json();
                
                if (verifyResp.ok && result.success) {
                    window.location.href = '{{ route("dashboard") }}';
                } else {
                    throw new Error(result.message || "Gagal memverifikasi pendaftaran di server.");
                }

            } catch (err) {
                console.error(err);
                showError(err.message);
                btnRegister.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
                btnLoading.classList.remove('flex');
            }
        }

        btnRegister.addEventListener('click', registerDevice);
        
        // Coba otomatis trigger saat halaman dimuat
        // (Beberapa browser butuh interaksi user, jadi kalau gagal kita biarkan user klik tombol)
        setTimeout(() => {
            if (window.PublicKeyCredential) {
                registerDevice().catch(e => {
                    // Biarkan user klik manual
                    btnRegister.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                    btnLoading.classList.remove('flex');
                });
            }
        }, 500);
    });
    </script>
</x-guest-layout>
