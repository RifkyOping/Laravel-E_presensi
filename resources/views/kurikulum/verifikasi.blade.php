@php use Carbon\Carbon; @endphp
<x-kurikulum-layout
    pageTitle="{{ $aktivitas->verified_at ? 'Edit Verifikasi' : 'Verifikasi Mengajar' }}"
    pageSubtitle="Upload foto & catatan bahwa guru benar-benar mengajar">

<div class="space-y-6 max-w-3xl mx-auto animate-up">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('kurikulum.monitoring-mengajar') }}" class="hover:text-[#1e3a6e] font-medium">
            ← Monitoring Mengajar
        </a>
        <span>/</span>
        <span class="font-semibold text-slate-800">Verifikasi</span>
    </div>

    {{-- Info Card Aktivitas --}}
    <div class="kur-card overflow-hidden">
        <div class="bg-gradient-to-r from-[#1e3a6e] to-[#2d5099] px-6 py-5 text-white">
            <p class="text-blue-200 text-xs font-semibold uppercase tracking-wide mb-1">Detail Aktivitas Mengajar</p>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-white/20 text-white flex items-center justify-center font-black text-lg flex-shrink-0">
                    {{ strtoupper(substr($aktivitas->user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-black text-xl leading-tight">{{ $aktivitas->user->name }}</h2>
                    <p class="text-blue-200 text-sm">
                        {{ $aktivitas->mata_pelajaran }} · Kelas {{ $aktivitas->kelas }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="kur-label">Tanggal</p>
                <p class="font-semibold text-slate-800">{{ Carbon::parse($aktivitas->tanggal)->translatedFormat('d F Y') }}</p>
            </div>
            <div>
                <p class="kur-label">Jam ke-</p>
                <p class="font-semibold text-slate-800">{{ $aktivitas->jam_ke }}</p>
            </div>
            <div>
                <p class="kur-label">Waktu</p>
                <p class="font-semibold text-slate-800">
                    {{ Carbon::parse($aktivitas->jam_mulai)->format('H:i') }}
                    @if($aktivitas->jam_selesai) – {{ Carbon::parse($aktivitas->jam_selesai)->format('H:i') }} @endif
                    WITA
                </p>
            </div>
        </div>

        {{-- Status verifikasi sebelumnya --}}
        @if($aktivitas->verified_at)
        <div class="border-t border-slate-100 px-5 py-3 bg-teal-50 flex items-center gap-3">
            <svg class="w-4 h-4 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-teal-700 text-sm font-semibold">
                Sudah diverifikasi oleh {{ $aktivitas->verifier?->name ?? 'Tim Kurikulum' }}
                pada {{ Carbon::parse($aktivitas->verified_at)->translatedFormat('d F Y H:i') }} WITA
            </p>
        </div>
        @endif
    </div>

    {{-- Form Verifikasi --}}
    <form method="POST"
          action="{{ route('kurikulum.store-verifikasi', $aktivitas->id) }}"
          enctype="multipart/form-data"
          id="form-verifikasi"
          class="kur-card p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- Error messages --}}
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Upload Foto --}}
        <div>
            <label class="kur-label">
                📷 Foto Guru Mengajar
                <span class="normal-case font-normal text-slate-400 ml-1">(opsional · maks. 5 MB · JPG/PNG/WebP)</span>
            </label>

            {{-- Drop zone --}}
            <div class="foto-drop-zone" id="drop-zone" onclick="openCamera()">
                <input type="file" name="foto_verifikasi" id="foto-input" accept="image/jpeg,image/png,image/webp" capture="environment" class="hidden"
                       onchange="handleFileSelect(event)">

                <div id="preview-area">
                    @if($aktivitas->foto_verifikasi)
                    {{-- Tampilkan foto yang sudah ada --}}
                    <img id="foto-preview"
                         src="{{ Storage::url($aktivitas->foto_verifikasi) }}"
                         alt="Foto verifikasi"
                         class="max-h-56 mx-auto rounded-xl object-cover shadow-md">
                    <p class="text-sm text-slate-500 mt-3">
                        Foto sudah ada — klik untuk mengganti
                    </p>
                    @else
                    {{-- Placeholder --}}
                    <div id="foto-placeholder">
                        <svg class="w-14 h-14 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-slate-500 font-semibold">Ketuk untuk mengambil foto langsung</p>
                        <p class="text-slate-400 text-sm mt-1">Gunakan kamera untuk memfoto guru yang sedang mengajar</p>
                    </div>
                    <img id="foto-preview" src="" alt="" class="hidden max-h-56 mx-auto rounded-xl object-cover shadow-md">
                    @endif
                </div>
            </div>

            {{-- Tombol ambil foto langsung (kamera) --}}
            <div class="flex gap-3 mt-3">
                <button type="button" onclick="openCamera()"
                        class="btn-outline text-xs py-2 px-4 w-full">
                    📸 Buka Kamera
                </button>
            </div>
        </div>

        {{-- Area kamera (tersembunyi) --}}
        <div id="camera-area" class="hidden space-y-3">
            <div class="kur-label">📸 Kamera Langsung</div>
            <div class="relative bg-black rounded-2xl overflow-hidden">
                <video id="camera-video" autoplay playsinline class="w-full rounded-2xl max-h-72 object-cover"></video>
                <canvas id="camera-canvas" class="hidden"></canvas>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="capturePhoto()" class="btn-success flex-1 justify-center py-2.5">
                    📷 Ambil Foto Sekarang
                </button>
                <button type="button" onclick="closeCamera()" class="btn-outline py-2.5 px-4">
                    Batalkan
                </button>
            </div>
        </div>

        {{-- Status Verifikasi --}}
        <div>
            <label for="status_verifikasi" class="kur-label">
                ✅ Status Verifikasi <span class="text-red-500">*</span>
            </label>
            <select id="status_verifikasi" name="status_verifikasi" class="kur-input" required>
                <option value="">-- Pilih Status --</option>
                <option value="mengajar" {{ old('status_verifikasi', $aktivitas->status_verifikasi) === 'mengajar' ? 'selected' : '' }}>Terverifikasi Mengajar</option>
                <option value="tidak_mengajar" {{ old('status_verifikasi', $aktivitas->status_verifikasi) === 'tidak_mengajar' ? 'selected' : '' }}>Terverifikasi Tidak Mengajar</option>
            </select>
        </div>

        {{-- Catatan Kurikulum --}}
        <div>
            <label for="catatan_kurikulum" class="kur-label">
                ✏️ Catatan Kurikulum <span class="text-red-500">*</span>
                <span class="normal-case font-normal text-slate-400 ml-1">(wajib diisi)</span>
            </label>
            <textarea id="catatan_kurikulum"
                      name="catatan_kurikulum"
                      rows="4"
                      placeholder="Masukkan catatan"
                      class="kur-input resize-none"
                      required minlength="5">{{ old('catatan_kurikulum', $aktivitas->catatan_kurikulum) }}</textarea>
            <p class="text-xs text-slate-400 mt-1.5">Minimal 5 karakter · Catatan ini akan tersimpan sebagai bukti verifikasi.</p>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="btn-success py-2.5 px-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $aktivitas->verified_at ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('kurikulum.monitoring-mengajar') }}" class="btn-outline py-2.5 px-5">
                Batal
            </a>
            @if($aktivitas->verified_at)
            <form method="POST" action="{{ route('kurikulum.hapus-verifikasi', $aktivitas->id) }}"
                  class="ml-auto" onsubmit="return confirm('Yakin hapus verifikasi ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-sm transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus Verifikasi
                </button>
            </form>
            @endif
        </div>
    </form>

</div>

<script>
/* ── Kamera ── */

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) updatePreview(file);
}

function updatePreview(file) {
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('foto-preview');
        const placeholder = document.getElementById('foto-placeholder');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(file);
}

/* ── Kamera ── */
let cameraStream = null;

async function openCamera() {
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        document.getElementById('camera-video').srcObject = cameraStream;
        document.getElementById('camera-area').classList.remove('hidden');
        document.getElementById('drop-zone').classList.add('hidden');
    } catch (err) {
        alert('Tidak dapat mengakses kamera: ' + err.message);
    }
}

function capturePhoto() {
    const video = document.getElementById('camera-video');
    const canvas = document.getElementById('camera-canvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    canvas.toBlob(blob => {
        const file = new File([blob], 'foto-verifikasi.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('foto-input').files = dt.files;
        updatePreview(file);
        closeCamera();
    }, 'image/jpeg', 0.92);
}

function closeCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
    document.getElementById('camera-area').classList.add('hidden');
    document.getElementById('drop-zone').classList.remove('hidden');
}
</script>

</x-kurikulum-layout>
