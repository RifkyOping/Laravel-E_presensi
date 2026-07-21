<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Pengaturan Absensi & Zona Geofencing</span>
    </x-slot>

    <div class="space-y-6 max-w-4xl mx-auto">

        {{-- Flash --}}
        @if(session('success'))
            <div class="alert-success anim-up">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header Info --}}
        <div class="app-card p-6 anim-up">
            <div class="flex items-start gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 text-lg">Pengaturan Absensi & Geofencing</h2>
                    <p class="text-slate-500 text-sm mt-1">
                        Tentukan titik pusat lokasi sekolah dan radius area yang diizinkan untuk melakukan absensi.
                        Murid dan guru hanya bisa absen jika berada dalam radius yang ditentukan.
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.geofence.update') }}" id="form-geofence" class="space-y-6"
            onsubmit="return confirm('Apakah Anda yakin ingin menyimpan pengaturan ini?')">
            @csrf

            {{-- Form Jadwal Absensi --}}
            <div class="app-card p-6 anim-up">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                    <div>
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <span class="w-1.5 h-5 bg-[#1e3a6e] rounded-full inline-block"></span>
                            Jadwal Absensi Harian
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">Atur jam buka/tutup absen untuk setiap harinya. Tandai libur untuk akhir pekan.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-xl border border-slate-100">
                        <label class="app-label !mb-0 text-sm font-bold whitespace-nowrap">Sistem Absen</label>
                        <select name="status_absen" id="select-status-absen" class="app-input w-48 !py-1.5 !text-sm border-slate-300" required>
                            <option value="auto" {{ old('status_absen', $setting->status_absen) == 'auto' ? 'selected' : '' }}>Otomatis (Ikuti Jadwal)</option>
                            <option value="buka" {{ old('status_absen', $setting->status_absen) == 'buka' ? 'selected' : '' }}>Paksa Selalu Buka</option>
                            <option value="tutup" {{ old('status_absen', $setting->status_absen) == 'tutup' ? 'selected' : '' }}>Paksa Selalu Tutup</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar pb-2">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-black tracking-wider">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Hari</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-2 py-3 text-center" title="Jam Buka Absen Kedatangan">Masuk Buka</th>
                                <th class="px-2 py-3 text-center text-amber-600" title="Batas waktu agar tidak Terlambat">Batas Datang</th>
                                <th class="px-2 py-3 text-center text-red-500" title="Jam Tutup Absen Kedatangan">Masuk Tutup</th>
                                <th class="px-2 py-3 text-center" title="Jam Buka Absen Kepulangan (Batas Cepat)">Pulang Buka</th>
                                <th class="px-2 py-3 text-center text-amber-600" title="Batas waktu agar dianggap Cepat Pulang (Jika absen sebelum ini dianggap cepat)">Batas Pulang</th>
                                <th class="px-2 py-3 rounded-r-xl text-center text-red-500" title="Jam Tutup Absen Kepulangan">Pulang Tutup</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($jadwalAbsensi as $jadwal)
                            <tr class="hover:bg-slate-50/50 transition schedule-row" data-libur="{{ $jadwal->is_libur ? '1' : '0' }}">
                                <td class="px-4 py-3 font-bold text-slate-700">{{ $jadwal->hari }}</td>
                                <td class="px-4 py-3 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="jadwal[{{ $jadwal->hari }}][is_libur]" value="1" class="sr-only peer status-libur-checkbox" {{ $jadwal->is_libur ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-blue-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-500"></div>
                                        <span class="ml-2 text-xs font-bold peer-checked:text-red-500 text-blue-600 toggle-text w-10 text-left">{{ $jadwal->is_libur ? 'Libur' : 'Masuk' }}</span>
                                    </label>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="time" name="jadwal[{{ $jadwal->hari }}][absen_datang_buka]" class="app-input !py-1 !px-2 !text-xs w-[85px] mx-auto time-input" value="{{ \Carbon\Carbon::parse($jadwal->absen_datang_buka)->format('H:i') }}" required>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="time" name="jadwal[{{ $jadwal->hari }}][batas_waktu_terlambat]" class="app-input !py-1 !px-2 !text-xs w-[85px] mx-auto time-input border-amber-200 focus:border-amber-500 focus:ring-amber-200 bg-amber-50" value="{{ \Carbon\Carbon::parse($jadwal->batas_waktu_terlambat)->format('H:i') }}" required>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="time" name="jadwal[{{ $jadwal->hari }}][absen_datang_tutup]" class="app-input !py-1 !px-2 !text-xs w-[85px] mx-auto time-input border-red-200 focus:border-red-500 focus:ring-red-200 bg-red-50" value="{{ \Carbon\Carbon::parse($jadwal->absen_datang_tutup)->format('H:i') }}" required>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="time" name="jadwal[{{ $jadwal->hari }}][absen_pulang_buka]" class="app-input !py-1 !px-2 !text-xs w-[85px] mx-auto time-input" value="{{ \Carbon\Carbon::parse($jadwal->absen_pulang_buka)->format('H:i') }}" required>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="time" name="jadwal[{{ $jadwal->hari }}][batas_pulang_cepat]" class="app-input !py-1 !px-2 !text-xs w-[85px] mx-auto time-input border-amber-200 focus:border-amber-500 focus:ring-amber-200 bg-amber-50" value="{{ \Carbon\Carbon::parse($jadwal->batas_pulang_cepat)->format('H:i') }}" required>
                                </td>
                                <td class="px-2 py-3">
                                    <input type="time" name="jadwal[{{ $jadwal->hari }}][absen_pulang_tutup]" class="app-input !py-1 !px-2 !text-xs w-[85px] mx-auto time-input border-red-200 focus:border-red-500 focus:ring-red-200 bg-red-50" value="{{ \Carbon\Carbon::parse($jadwal->absen_pulang_tutup)->format('H:i') }}" required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <style>
                    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
                    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
                </style>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Form Setting --}}
                <div class="app-card p-6 anim-up d1">
                    <h3 class="font-bold text-slate-800 mb-5 flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-[#1e3a6e] rounded-full inline-block"></span>
                        Konfigurasi Koordinat
                    </h3>

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-4">
                            <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="app-label">Nama Sekolah</label>
                                <input type="text" name="nama_sekolah" class="app-input"
                                    value="{{ old('nama_sekolah', $setting->nama_sekolah) }}" required>
                            </div>
                            <div>
                                <label class="app-label">Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" class="app-input"
                                    value="{{ old('tahun_ajaran', $setting->tahun_ajaran) }}" required placeholder="Misal: 2026 / 2027">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="app-label">Latitude <span class="text-red-500">*</span></label>
                                <input type="number" name="latitude" id="input-lat" class="app-input" step="0.0000001"
                                    value="{{ old('latitude', $setting->latitude) }}" required>
                                <p class="text-xs text-slate-400 mt-1">Contoh: -3.5432</p>
                            </div>
                            <div>
                                <label class="app-label">Longitude <span class="text-red-500">*</span></label>
                                <input type="number" name="longitude" id="input-lng" class="app-input" step="0.0000001"
                                    value="{{ old('longitude', $setting->longitude) }}" required>
                                <p class="text-xs text-slate-400 mt-1">Contoh: 118.9759</p>
                            </div>
                        </div>

                        <div>
                            <label class="app-label">
                                Radius Area (meter) <span class="text-red-500">*</span>
                                <span class="font-normal normal-case text-slate-400 ml-1">min: 1m · maks: 500m</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="range" name="radius_meter" id="radius-slider" min="1" max="500" step="1"
                                    value="{{ old('radius_meter', $setting->radius_meter) }}"
                                    class="flex-1 accent-[#1e3a6e]" oninput="updateRadius(this.value)">
                                <div class="flex items-center gap-1">
                                    <input type="number" id="radius-display"
                                        value="{{ old('radius_meter', $setting->radius_meter) }}"
                                        class="app-input w-20 text-center text-sm font-bold" min="1" max="500"
                                        step="1" oninput="syncSlider(this.value)">
                                    <span class="text-slate-500 text-sm font-medium">m</span>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol ambil lokasi saat ini --}}
                        <div class="pt-1">
                            <button type="button" onclick="gunakanLokasiSaatIni()"
                                class="btn-outline w-full justify-center py-2.5 text-sm">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                Gunakan Lokasi Saya Sekarang (GPS)
                            </button>
                            <p class="text-xs text-slate-400 mt-1.5 text-center">Posisikan diri Anda di titik pusat
                                sekolah sebelum klik tombol ini.</p>
                        </div>

                        <div class="relative flex items-center py-1">
                            <div class="flex-grow border-t border-slate-100"></div>
                            <span
                                class="flex-shrink-0 mx-4 text-slate-400 text-[10px] font-bold tracking-wider uppercase">Atau</span>
                            <div class="flex-grow border-t border-slate-100"></div>
                        </div>

                        <div>
                            <label class="app-label">Cari dari Link / Koordinat Maps</label>
                            <div class="flex gap-2">
                                <input type="text" id="input-gmaps-url" class="app-input flex-1"
                                    placeholder="Paste URL maps di sini">
                                <button type="button" onclick="prosesUrlGmaps()"
                                    class="btn-outline px-4 flex-shrink-0 text-[#1e3a6e] border-[#1e3a6e] hover:bg-[#1e3a6e] hover:text-white">
                                    Cari
                                </button>
                            </div>
                            <p class="text-xs text-slate-500 mt-3">
                                * Paste koordinat langsung atau URL Web Google Maps lengkap.
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100">
                            <button type="submit" class="btn-primary w-full justify-center py-2.5">
                                Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Peta Preview --}}
                <div class="app-card overflow-hidden anim-up d2">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="font-bold text-slate-800 text-sm">Preview Peta Zona Absensi</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Klik pada peta untuk menggeser titik pusat</p>
                    </div>
                    <div id="map" style="height: 380px; width: 100%;"></div>
                    <div
                        class="px-5 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-3">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-3 h-3 rounded-full bg-[#1e3a6e] inline-block"></span> Titik Pusat
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span
                                class="w-3 h-3 rounded-full bg-blue-300 border-2 border-blue-400 inline-block opacity-60"></span>
                            Area Radius
                        </span>
                        <span id="info-radius" class="ml-auto font-semibold text-[#1e3a6e]">
                            Radius: {{ $setting->radius_meter }} m
                        </span>
                    </div>
                </div>
            </div>
        </form>

    </div>

    {{-- Leaflet CSS + JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const initLat = {{ $setting->latitude }};
        const initLng = {{ $setting->longitude }};
        let initRadius = {{ $setting->radius_meter }};

        // Inisialisasi peta
        const map = L.map('map').setView([initLat, initLng], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Marker + circle
        const marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);
        const circle = L.circle([initLat, initLng], {
            radius: initRadius,
            color: '#1e3a6e',
            fillColor: '#3b82f6',
            fillOpacity: 0.15,
            weight: 2,
        }).addTo(map);

        // Saat marker digeser
        marker.on('dragend', function (e) {
            const pos = marker.getLatLng();
            updateMapCoords(pos.lat, pos.lng);
        });

        // Klik peta → pindah marker
        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            circle.setLatLng(e.latlng);
            updateMapCoords(e.latlng.lat, e.latlng.lng);
        });

        function updateMapCoords(lat, lng) {
            document.getElementById('input-lat').value = lat.toFixed(7);
            document.getElementById('input-lng').value = lng.toFixed(7);
        }

        // Sinkron dari input ke peta
        document.getElementById('input-lat').addEventListener('input', syncMapFromInput);
        document.getElementById('input-lng').addEventListener('input', syncMapFromInput);

        function syncMapFromInput() {
            const lat = parseFloat(document.getElementById('input-lat').value);
            const lng = parseFloat(document.getElementById('input-lng').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                circle.setLatLng([lat, lng]);
                map.setView([lat, lng]);
            }
        }

        // Update radius
        function updateRadius(val) {
            document.getElementById('radius-display').value = val;
            document.querySelector('[name="radius_meter"]').value = val;
            circle.setRadius(parseInt(val));
            document.getElementById('info-radius').textContent = 'Radius: ' + val + ' m';
        }

        function syncSlider(val) {
            const v = parseInt(val);
            if (v >= 1 && v <= 500) {
                document.getElementById('radius-slider').value = v;
                circle.setRadius(v);
                document.getElementById('info-radius').textContent = 'Radius: ' + v + ' m';
            }
        }

        // Gunakan lokasi GPS saat ini
        function gunakanLokasiSaatIni() {
            if (!navigator.geolocation) {
                Swal.fire({
                    title: 'Error',
                    text: 'Browser ini tidak mendukung GPS.',
                    icon: 'error',
                    confirmButtonColor: '#1e3a6e',
                    confirmButtonText: 'Tutup'
                });
                return;
            }

            // Tampilkan loading popup
            Swal.fire({
                title: 'Mencari Lokasi...',
                text: 'Mohon tunggu, sedang mengambil koordinat GPS.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    marker.setLatLng([lat, lng]);
                    circle.setLatLng([lat, lng]);
                    map.setView([lat, lng], 17);
                    updateMapCoords(lat, lng);

                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Lokasi Anda saat ini berhasil didapatkan.',
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        confirmButtonText: 'Oke',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl border border-slate-100',
                            title: 'text-xl font-black text-slate-800',
                            confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm'
                        }
                    });
                },
                function (err) {
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Gagal mendapatkan lokasi: ' + err.message,
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Tutup',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl border border-slate-100',
                            title: 'text-xl font-black text-slate-800',
                            confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm'
                        }
                    });
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        // Proses Ekstrak Koordinat dari Link/Teks
        function prosesUrlGmaps() {
            const url = document.getElementById('input-gmaps-url').value.trim();
            if (!url) return;

            let lat = null;
            let lng = null;

            // 1. Pola @latitude,longitude (biasanya URL web Google Maps)
            const regex1 = /@(-?\d+\.\d+),(-?\d+\.\d+)/;
            // 2. Pola !3dlatitude!4dlongitude (format lama/lokasi spesifik)
            const regex2 = /!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/;
            // 3. Pola latitude,longitude (jika user hanya copy paste koordinat langsung)
            const regex3 = /^(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)$/;

            let match;
            if ((match = url.match(regex1))) {
                lat = parseFloat(match[1]);
                lng = parseFloat(match[2]);
            } else if ((match = url.match(regex2))) {
                lat = parseFloat(match[1]);
                lng = parseFloat(match[2]);
            } else if ((match = url.match(regex3))) {
                lat = parseFloat(match[1]);
                lng = parseFloat(match[2]);
            }

            if (lat !== null && lng !== null) {
                marker.setLatLng([lat, lng]);
                circle.setLatLng([lat, lng]);
                map.setView([lat, lng], 17);
                updateMapCoords(lat, lng);

                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Koordinat berhasil diekstrak dan peta telah diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'Oke',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-100',
                        title: 'text-xl font-black text-slate-800',
                        confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm'
                    }
                });
                document.getElementById('input-gmaps-url').value = '';
            } else {
                Swal.fire({
                    title: 'Gagal Mengekstrak Koordinat',
                    text: 'Pastikan Anda memasukkan URL lengkap Google Maps dari browser (yang mengandung @latitude,longitude) atau langsung koordinat (lat, lng). Link pendek (maps.app.goo.gl) tidak didukung karena disembunyikan oleh sistem keamanan browser.',
                    icon: 'error',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-slate-100',
                        title: 'text-xl font-black text-slate-800',
                        confirmButton: 'font-bold rounded-xl px-6 py-2.5 shadow-sm'
                    }
                });
            }
        }

        // Toggle readonly untuk jam absen berdasarkan status absen
        function toggleTimeInputs() {
            const status = document.getElementById('select-status-absen').value;
            const isAuto = status === 'auto';
            
            const timeInputs = document.querySelectorAll('.time-input');
            timeInputs.forEach(el => {
                // If it's not auto, we disable them visually
                el.readOnly = !isAuto;
                if (!isAuto) {
                    el.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                } else {
                    el.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                }
            });
            
            // Handle libur toggles
            if(isAuto) {
                document.querySelectorAll('.status-libur-checkbox').forEach(cb => {
                    const row = cb.closest('tr');
                    const inputs = row.querySelectorAll('.time-input');
                    const labelText = cb.parentElement.querySelector('.toggle-text');
                    
                    if(cb.checked) {
                        labelText.textContent = 'Libur';
                        inputs.forEach(el => {
                            el.classList.add('opacity-40', 'cursor-not-allowed');
                            el.readOnly = true;
                        });
                    } else {
                        labelText.textContent = 'Masuk';
                        inputs.forEach(el => {
                            el.classList.remove('opacity-40', 'cursor-not-allowed');
                            el.readOnly = false;
                        });
                    }
                });
            }
        }

        document.getElementById('select-status-absen').addEventListener('change', toggleTimeInputs);
        
        document.querySelectorAll('.status-libur-checkbox').forEach(cb => {
            cb.addEventListener('change', toggleTimeInputs);
        });

        window.addEventListener('load', toggleTimeInputs);

    </script>
</x-app-layout>