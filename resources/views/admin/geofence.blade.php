<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Pengaturan Absensi & Zona Geofencing</span>
    </x-slot>

    <div class="space-y-6">

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
            <div class="app-card p-6 anim-up" x-data="{ activeTab: 'Senin', days: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] }">
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

                {{-- Tabs --}}
                <div class="-mx-1 overflow-x-auto pb-1 mb-6 border-b border-slate-200 custom-scrollbar">
                    <div class="flex gap-2 px-1 min-w-max pb-2">
                        <template x-for="hari in days" :key="hari">
                            <button type="button" @click="activeTab = hari"
                                :class="activeTab === hari ? 'bg-[#1e3a6e] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 whitespace-nowrap flex-shrink-0"
                                x-text="hari">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Tab Contents --}}
                <div class="min-h-[250px]">
                    @foreach($jadwalAbsensi as $jadwal)
                    <div x-show="activeTab === '{{ $jadwal->hari }}'" class="space-y-6" style="display: none;" x-init="if(activeTab === '{{ $jadwal->hari }}') $el.style.display = 'block'">
                        
                        {{-- Status Hari Ini --}}
                        <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-xl">
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Status Hari {{ $jadwal->hari }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Tentukan apakah hari ini masuk sekolah atau libur</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="jadwal[{{ $jadwal->hari }}][is_libur]" value="1" class="sr-only peer status-libur-checkbox" {{ $jadwal->is_libur ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-blue-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                                <span class="ml-3 text-sm font-bold peer-checked:text-red-500 text-blue-600 toggle-text w-12">{{ $jadwal->is_libur ? 'Libur' : 'Masuk' }}</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Kedatangan --}}
                            <div class="border border-slate-200 rounded-xl p-5 relative overflow-hidden group hover:border-blue-300 transition-colors bg-white">
                                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                                <h4 class="font-bold text-slate-800 flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                    Absen Datang
                                </h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[0.7rem] font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Jam Masuk Buka</label>
                                        <input type="time" name="jadwal[{{ $jadwal->hari }}][absen_datang_buka]" class="app-input w-full time-input font-medium" value="{{ \Carbon\Carbon::parse($jadwal->absen_datang_buka)->format('H:i') }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-[0.7rem] font-bold text-amber-600 mb-1.5 uppercase tracking-wider flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Batas Tepat Waktu</label>
                                        <input type="time" name="jadwal[{{ $jadwal->hari }}][batas_waktu_terlambat]" class="app-input w-full time-input border-amber-200 focus:border-amber-500 bg-amber-50/50 font-medium text-amber-900" value="{{ \Carbon\Carbon::parse($jadwal->batas_waktu_terlambat)->format('H:i') }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-[0.7rem] font-bold text-red-500 mb-1.5 uppercase tracking-wider flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Jam Masuk Tutup</label>
                                        <input type="time" name="jadwal[{{ $jadwal->hari }}][absen_datang_tutup]" class="app-input w-full time-input border-red-200 focus:border-red-500 bg-red-50/50 font-medium text-red-900" value="{{ \Carbon\Carbon::parse($jadwal->absen_datang_tutup)->format('H:i') }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Kepulangan --}}
                            <div class="border border-slate-200 rounded-xl p-5 relative overflow-hidden group hover:border-emerald-300 transition-colors bg-white">
                                <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
                                <h4 class="font-bold text-slate-800 flex items-center gap-2 mb-4">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Absen Pulang
                                </h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[0.7rem] font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Jam Pulang Buka</label>
                                        <input type="time" name="jadwal[{{ $jadwal->hari }}][absen_pulang_buka]" class="app-input w-full time-input font-medium" value="{{ \Carbon\Carbon::parse($jadwal->absen_pulang_buka)->format('H:i') }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-[0.7rem] font-bold text-amber-600 mb-1.5 uppercase tracking-wider flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Batas Tepat Waktu</label>
                                        <input type="time" name="jadwal[{{ $jadwal->hari }}][batas_pulang_cepat]" class="app-input w-full time-input border-amber-200 focus:border-amber-500 bg-amber-50/50 font-medium text-amber-900" value="{{ \Carbon\Carbon::parse($jadwal->batas_pulang_cepat)->format('H:i') }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-[0.7rem] font-bold text-red-500 mb-1.5 uppercase tracking-wider flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Jam Pulang Tutup</label>
                                        <input type="time" name="jadwal[{{ $jadwal->hari }}][absen_pulang_tutup]" class="app-input w-full time-input border-red-200 focus:border-red-500 bg-red-50/50 font-medium text-red-900" value="{{ \Carbon\Carbon::parse($jadwal->absen_pulang_tutup)->format('H:i') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
                
                <style>
                    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
                    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
                </style>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Form Setting --}}
                <div class="app-card p-6 anim-up d1 order-2 lg:order-1">
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
                <div class="app-card overflow-hidden anim-up d2 order-1 lg:order-2">
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
                { enableHighAccuracy: true, timeout: 30000 }
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