<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('ebook.index') }}" class="text-slate-400 hover:text-[#1e3a6e] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('ebook.index') }}" class="text-slate-400 hover:text-[#1e3a6e] text-sm transition-colors">e-Book</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-bold text-slate-800 truncate max-w-xs">Kuis: {{ $ebook->judul }}</span>
        </div>
    </x-slot>

{{-- Overlay Sukses --}}
<div id="successOverlay" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-black/60 backdrop-blur-sm">
    <div id="successCard" class="bg-white rounded-3xl shadow-2xl p-10 max-w-sm w-full mx-4 text-center transform scale-75 opacity-0 transition-all duration-500">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="text-4xl mb-3">🎉</div>
        <h2 class="text-2xl font-black text-slate-800 mb-2">Selamat!</h2>
        <p class="text-slate-500 text-sm mb-1">Kamu berhasil menyelesaikan</p>
        <p class="text-[#1e3a6e] font-black text-base mb-6">{{ $ebook->judul }}</p>
        <div class="flex items-center justify-center gap-2 text-green-600 text-sm font-semibold">
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Mengalihkan ke halaman e-Book...
        </div>
    </div>
</div>

<style>
@keyframes float-up {
    0%   { transform: translateY(0) rotate(0deg); opacity: 1; }
    100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
}
.confetti-piece {
    position: fixed;
    bottom: -20px;
    width: 10px;
    height: 10px;
    border-radius: 2px;
    animation: float-up linear forwards;
    z-index: 9999;
    pointer-events: none;
}
</style>

<div class="space-y-5 max-w-3xl mx-auto">
    {{-- Header Info --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                {{-- Level Badge --}}
                <div class="w-14 h-14 rounded-2xl bg-[#1e3a6e] flex items-center justify-center flex-shrink-0 shadow-lg">
                    <span class="text-white text-lg font-black">{{ $ebook->level }}</span>
                </div>
                <div>
                    <span class="inline-block text-[.65rem] font-bold bg-[#1e3a6e]/10 text-[#1e3a6e] px-2.5 py-0.5 rounded-full uppercase tracking-wide mb-1">
                        {{ $ebook->kategori ?? 'e-Book' }} · Level {{ $ebook->level }}
                    </span>
                    <h1 class="text-xl font-black text-slate-800 leading-tight">Kuis: {{ $ebook->judul }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Jawab pertanyaan berikut untuk menuntaskan e-Book.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quiz Panel --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4" id="quizPanel">
        <h3 class="text-sm font-black text-slate-700 flex items-center gap-2">
            <div class="w-5 h-5 rounded bg-[#1e3a6e]/10 flex items-center justify-center">
                <svg class="w-3 h-3 text-[#1e3a6e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </div>
            Pertanyaan Kuis
        </h3>

        <div id="quizContainer" class="space-y-4 pt-2">
            <div class="flex justify-center py-6 text-[#1e3a6e]">
                <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
        </div>

        <button id="btnSubmitQuiz" class="hidden w-full items-center justify-center gap-2 bg-[#1e3a6e] text-white font-bold py-3 rounded-xl text-sm transition duration-200 hover:bg-[#162d57]">
            Kirim Jawaban
        </button>
        <div id="hasilKuis" class="hidden"></div>
    </div>

    <script>
        // ── Confetti ─────────────────────────────────────────────────────────
        function launchConfetti() {
            const colors = ['#1e3a6e','#3b82f6','#22c55e','#f59e0b','#ec4899','#a855f7','#f97316'];
            for (let i = 0; i < 120; i++) {
                setTimeout(() => {
                    const el = document.createElement('div');
                    el.classList.add('confetti-piece');
                    el.style.left              = Math.random() * 100 + 'vw';
                    el.style.background        = colors[Math.floor(Math.random() * colors.length)];
                    el.style.width             = (Math.random() * 10 + 6) + 'px';
                    el.style.height            = (Math.random() * 10 + 6) + 'px';
                    el.style.borderRadius      = Math.random() > 0.5 ? '50%' : '2px';
                    el.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    el.style.animationDelay    = (Math.random() * 0.5) + 's';
                    document.body.appendChild(el);
                    setTimeout(() => el.remove(), 4000);
                }, i * 20);
            }
        }

        // ── Show Success Overlay ──────────────────────────────────────────────
        function showSuccessOverlay() {
            launchConfetti();
            const overlay = document.getElementById('successOverlay');
            const card    = document.getElementById('successCard');
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            requestAnimationFrame(() => requestAnimationFrame(() => {
                card.style.transform = 'scale(1)';
                card.style.opacity   = '1';
            }));
            setTimeout(() => { window.location.href = '{{ route("e-Book.index") }}'; }, 4000);
        }

        // ── Quiz Logic ────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route("e-Book.kuis.get", $ebook->id) }}')
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('quizContainer');
                    const btnSubmit = document.getElementById('btnSubmitQuiz');

                    if (data.error) {
                        container.innerHTML = `<p class="text-sm text-red-500">${data.error}</p>`;
                        return;
                    }

                    if (data.questions && data.questions.length > 0) {
                        let html = '';
                        data.questions.forEach((q, idx) => {
                            html += `
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <p class="text-sm font-bold text-slate-800 mb-3">${idx + 1}. ${q.pertanyaan}</p>
                                <div class="space-y-2">
                            `;
                            q.opsi.forEach(opsi => {
                                html += `
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="mt-0.5">
                                        <input type="radio" name="jawaban[${q.id}]" value="${opsi}"
                                               class="w-4 h-4 text-[#1e3a6e] border-slate-300 focus:ring-[#1e3a6e]">
                                    </div>
                                    <span class="text-sm text-slate-600 group-hover:text-slate-800 transition">${opsi}</span>
                                </label>`;
                            });
                            html += `</div></div>`;
                        });
                        container.innerHTML = html;
                        btnSubmit.classList.remove('hidden');
                        btnSubmit.classList.add('flex');
                    } else {
                        container.innerHTML = `<p class="text-sm text-slate-500">Soal kuis belum tersedia.</p>`;
                    }
                });

            document.getElementById('btnSubmitQuiz').addEventListener('click', function () {
                const inputs  = document.querySelectorAll('#quizContainer input[type="radio"]:checked');
                const answers = {};
                inputs.forEach(i => {
                    answers[i.getAttribute('name').match(/\d+/)[0]] = i.value;
                });

                const numQuestions = document.querySelectorAll('#quizContainer > div').length;
                if (Object.keys(answers).length < numQuestions) {
                    alert('Silakan jawab semua pertanyaan terlebih dahulu.');
                    return;
                }

                this.disabled  = true;
                this.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg> Memproses...`;

                fetch('{{ route("e-Book.kuis.submit", $ebook->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ jawaban: answers })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.lulus) {
                        showSuccessOverlay();
                    } else {
                        const hk = document.getElementById('hasilKuis');
                        hk.classList.remove('hidden');
                        hk.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-bold text-center">${data.pesan}</div>`;
                        setTimeout(() => location.reload(), 3000);
                    }
                });
            });
        });
    </script>
</div>
</x-app-layout>
