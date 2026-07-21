{{-- ═══════════════════════════════════════════════════════
     POPUP NPSN VERIFIKASI — Muncul setelah login setiap sesi baru
═══════════════════════════════════════════════════════ --}}
@auth
@if(!session('npsn_verified'))
<div id="npsn-modal" class="fixed inset-0 z-[9999] flex items-center justify-center" style="backdrop-filter: blur(8px); background: rgba(15,23,42,0.65);">
    {{-- Pointer events blocker for anything behind --}}
    <div class="absolute inset-0" style="pointer-events: all;"></div>

    {{-- Modal Card --}}
    <div class="relative z-10 w-full max-w-sm mx-4" style="animation: npsnSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both;">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">

            {{-- Header --}}
            <div class="bg-[#1e3a6e] px-7 py-7 text-center">
                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-white font-black text-xl leading-tight">Verifikasi Akses</h2>
                <p class="text-white/70 text-sm mt-1.5 font-medium">E-Presensi {{ \App\Models\SchoolSetting::get()->nama_sekolah }}</p>
            </div>

            {{-- Body --}}
            <div class="px-7 py-7">
                <p class="text-slate-600 text-sm text-center mb-6 leading-relaxed">
                    Masukkan <span class="font-bold text-[#1e3a6e]">Nomor Pokok Sekolah Nasional (NPSN)</span> untuk mengakses sistem.
                </p>

                <div id="npsn-error" class="hidden mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="npsn-error-msg" class="text-red-700 text-sm font-semibold"></span>
                </div>

                <div class="relative">
                    <input
                        type="text"
                        id="npsn-input"
                        placeholder="Masukkan NPSN Sekolah..."
                        maxlength="20"
                        autocomplete="off"
                        class="w-full border-2 border-slate-200 rounded-xl px-4 py-3.5 text-slate-800 font-bold text-center tracking-widest text-lg focus:outline-none focus:border-[#1e3a6e] focus:ring-4 focus:ring-[#1e3a6e]/10 transition"
                        style="letter-spacing: 0.2em;"
                        onkeydown="if(event.key==='Enter') verifyNpsn()"
                    >
                </div>

                <button
                    id="npsn-btn"
                    onclick="verifyNpsn()"
                    class="mt-5 w-full bg-[#1e3a6e] hover:bg-[#162d57] text-white font-black py-3.5 rounded-xl text-sm transition duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg id="npsn-spin" class="w-4 h-4 hidden animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <svg id="npsn-lock" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                    Konfirmasi & Masuk
                </button>

                <p class="text-center text-xs text-slate-400 mt-5 leading-relaxed">
                    NPSN adalah identitas resmi sekolah yang<br>diterbitkan oleh Kemendikbudristek.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes npsnSlideUp {
        from { opacity: 0; transform: translateY(30px) scale(0.96); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    /* Prevent body scroll & interaction while modal is open */
    body.npsn-locked { overflow: hidden; }
    body.npsn-locked > *:not(#npsn-modal) { pointer-events: none; }
</style>

<script>
    // Lock the body immediately
    document.body.classList.add('npsn-locked');

    // Focus input on load
    document.addEventListener('DOMContentLoaded', function () {
        const inp = document.getElementById('npsn-input');
        if (inp) inp.focus();
    });

    function verifyNpsn() {
        const input = document.getElementById('npsn-input');
        const btn   = document.getElementById('npsn-btn');
        const spin  = document.getElementById('npsn-spin');
        const lock  = document.getElementById('npsn-lock');
        const errBox = document.getElementById('npsn-error');
        const errMsg = document.getElementById('npsn-error-msg');
        const npsn  = input.value.trim();

        if (!npsn) {
            input.focus();
            input.classList.add('border-red-400');
            setTimeout(() => input.classList.remove('border-red-400'), 1200);
            return;
        }

        // Loading state
        btn.disabled  = true;
        spin.classList.remove('hidden');
        lock.classList.add('hidden');
        errBox.classList.add('hidden');

        fetch('{{ route("npsn.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ npsn: npsn })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Unlock body & remove modal with success animation
                document.getElementById('npsn-modal').style.opacity = '0';
                document.getElementById('npsn-modal').style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    document.getElementById('npsn-modal').remove();
                    document.body.classList.remove('npsn-locked');
                }, 300);
            } else {
                // Show error
                errMsg.textContent = data.message || 'NPSN salah. Silakan coba lagi.';
                errBox.classList.remove('hidden');
                input.value = '';
                input.focus();
                input.classList.add('border-red-400', 'shake-anim');
                setTimeout(() => input.classList.remove('border-red-400', 'shake-anim'), 800);
                btn.disabled = false;
                spin.classList.add('hidden');
                lock.classList.remove('hidden');
            }
        })
        .catch(() => {
            errMsg.textContent = 'Terjadi kesalahan. Silakan refresh halaman.';
            errBox.classList.remove('hidden');
            btn.disabled = false;
            spin.classList.add('hidden');
            lock.classList.remove('hidden');
        });
    }
</script>
@endif
@endauth
