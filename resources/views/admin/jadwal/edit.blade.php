<x-app-layout pageTitle="Atur Jadwal Mengajar" pageSubtitle="Tetapkan jadwal tetap mingguan untuk guru">

    <div class="max-w-5xl mx-auto space-y-6" x-data="jadwalApp()">

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.jadwal-mengajar.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 hover:bg-slate-50 transition text-slate-500 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-xl font-black text-slate-800">Edit Jadwal: {{ $user->name }}</h1>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Jadwal Mengajar</h2>
                    <p class="text-sm text-slate-500 mt-1">Atur jadwal mengajar untuk guru bersangkutan.</p>
                </div>
                @if(!$user->is_jadwal_set)
                    <div
                        class="bg-amber-50 text-amber-700 px-4 py-2 rounded-lg text-sm font-semibold border border-amber-200">
                        ⚠️ Jadwal belum diatur
                    </div>
                @endif
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-amber-50 border border-amber-300 text-amber-800 px-5 py-4 rounded-xl text-sm flex gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    <div>
                        <p class="font-bold">Jadwal belum bisa disimpan</p>
                        <p class="mt-1">Pastikan semua baris jadwal sudah diisi dengan lengkap: <strong>Mata Pelajaran</strong>, <strong>Kelas</strong>, dan <strong>Jam Mulai</strong>. Hapus baris yang tidak digunakan sebelum menyimpan.</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.jadwal-mengajar.update', $user->id) }}" method="POST" id="formJadwal">
                @csrf

                {{-- Tabs --}}
                <div class="-mx-1 overflow-x-auto pb-1 mb-6 border-b border-slate-200">
                    <div class="flex gap-2 px-1 min-w-max">
                        <template x-for="hari in days" :key="hari">
                            <button type="button" @click="activeTab = hari"
                                :class="activeTab === hari ? 'bg-[#1e3a6e] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-4 py-2 rounded-xl font-semibold text-sm transition-colors duration-200 whitespace-nowrap flex-shrink-0"
                                x-text="hari">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Tab Contents --}}
                <div class="min-h-[300px]">
                    <template x-for="hari in days" :key="hari">
                        <div x-show="activeTab === hari" class="space-y-4">

                            {{-- Data Kosong --}}
                            <div x-show="getJadwalByHari(hari).length === 0"
                                class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                                <p class="text-slate-500 font-medium mb-3">Tidak ada jadwal di hari <span
                                        x-text="hari"></span></p>
                                <button type="button" @click="tambahJadwal(hari)"
                                    class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50">
                                    + Tambah Jam Mengajar
                                </button>
                            </div>

                            {{-- Mobile: Card per item --}}
                            <div x-show="getJadwalByHari(hari).length > 0" class="block sm:hidden space-y-3">
                                <template x-for="(item, index) in getJadwalByHari(hari)" :key="item.id">
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                                        <input type="hidden" :name="`jadwal[${item.id}][hari]`" :value="hari">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sesi</span>
                                            <button type="button" @click="hapusJadwal(item.id)" class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                        {{-- Mata Pelajaran --}}
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Mata Pelajaran</label>
                                            <select :name="`jadwal[${item.id}][mata_pelajaran]`" x-model="item.mata_pelajaran" class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                <option value="">Mapel</option>
                                                @foreach($mapels as $mapel) <option value="{{$mapel}}">{{$mapel}}</option> @endforeach
                                            </select>
                                        </div>
                                        {{-- Kelas 1 dropdown --}}
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Kelas</label>
                                            <select :name="`jadwal[${item.id}][kelas_id]`" x-model="item.kelas_id" class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                <option value="">-- Pilih Kelas --</option>
                                                @foreach($kelasList as $k)
                                                <option value="{{$k->id}}">{{$k->tingkat}} {{$k->jurusan}} {{$k->rombel}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- Tipe Blok --}}
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1">Tipe Blok</label>
                                            <select :name="`jadwal[${item.id}][tipe_blok]`" x-model="item.tipe_blok" class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                <option value="Semua">Semua Blok</option>
                                                <option value="A">Blok A</option>
                                                <option value="B">Blok B</option>
                                            </select>
                                        </div>
                                        {{-- Jam --}}
                                        <div class="grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-500 mb-1">Mapel Ke-</label>
                                                <input type="number" :name="`jadwal[${item.id}][jam_ke]`" x-model="item.jam_ke" min="1" class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e] text-center">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-500 mb-1">Mulai</label>
                                                <input type="time" :name="`jadwal[${item.id}][jam_mulai]`" x-model="item.jam_mulai" class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-500 mb-1">Selesai</label>
                                                <input type="time" :name="`jadwal[${item.id}][jam_selesai]`" x-model="item.jam_selesai" class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <button type="button" @click="tambahJadwal(hari)" class="w-full px-4 py-2.5 text-[#1e3a6e] bg-blue-50 border border-blue-100 rounded-xl text-sm font-semibold hover:bg-blue-100 transition">
                                    + Tambah Baris
                                </button>
                            </div>

                            {{-- Desktop: Table --}}
                            <div x-show="getJadwalByHari(hari).length > 0" class="hidden sm:block overflow-x-auto">
                                <table class="w-full text-left text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 text-slate-600">
                                            <th class="p-3 font-semibold rounded-tl-lg">Mata Pelajaran</th>
                                            <th class="p-3 font-semibold">Kelas</th>
                                            <th class="p-3 font-semibold w-28">Tipe Blok</th>
                                            <th class="p-3 font-semibold w-24 text-center">Mapel Ke-</th>
                                            <th class="p-3 font-semibold w-32">Jam Mulai</th>
                                            <th class="p-3 font-semibold w-32">Jam Selesai</th>
                                            <th class="p-3 font-semibold rounded-tr-lg w-16 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item, index) in getJadwalByHari(hari)" :key="item.id">
                                            <tr class="border-b border-slate-100 group">
                                                <td class="p-2">
                                                    <input type="hidden" :name="`jadwal[${item.id}][hari]`"
                                                        :value="hari">
                                                    <select :name="`jadwal[${item.id}][mata_pelajaran]`"
                                                        x-model="item.mata_pelajaran"
                                                        class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                        <option value="">Mapel</option>
                                                        @foreach($mapels as $mapel) <option value="{{$mapel}}">
                                                        {{$mapel}}</option> @endforeach
                                                    </select>
                                                </td>
                                                <td class="p-2" colspan="1">
                                                    <select :name="`jadwal[${item.id}][kelas_id]`" x-model="item.kelas_id"
                                                        
                                                        class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                        <option value="">-- Pilih Kelas --</option>
                                                        @foreach($kelasList as $k)
                                                        <option value="{{$k->id}}">{{$k->tingkat}} {{$k->jurusan}} {{$k->rombel}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="p-2">
                                                    <select :name="`jadwal[${item.id}][tipe_blok]`" x-model="item.tipe_blok"
                                                        class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                        <option value="Semua">Semua</option>
                                                        <option value="A">Blok A</option>
                                                        <option value="B">Blok B</option>
                                                    </select>
                                                </td>
                                                <td class="p-2">
                                                    <input type="number" :name="`jadwal[${item.id}][jam_ke]`"
                                                        x-model="item.jam_ke" min="1"
                                                        class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e] text-center">
                                                </td>
                                                <td class="p-2">
                                                    <input type="time" :name="`jadwal[${item.id}][jam_mulai]`"
                                                        x-model="item.jam_mulai"
                                                        class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                </td>
                                                <td class="p-2">
                                                    <input type="time" :name="`jadwal[${item.id}][jam_selesai]`"
                                                        x-model="item.jam_selesai"
                                                        class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                </td>
                                                <td class="p-2 text-center">
                                                    <button type="button" @click="hapusJadwal(item.id)"
                                                        class="text-red-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <div class="mt-4">
                                    <button type="button" @click="tambahJadwal(hari)"
                                        class="px-4 py-2 text-[#1e3a6e] bg-blue-50 border border-blue-100 rounded-lg text-sm font-semibold hover:bg-blue-100 transition">
                                        + Tambah Baris
                                    </button>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-slate-500">
                        Pastikan untuk mengisi jadwal secara runut berdasarkan jam_ke.
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.jadwal-mengajar.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                            Batal
                        </a>
                        <button type="button" @click="submitJadwal()" class="px-6 py-3 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Jadwal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('jadwalApp', () => ({
                days: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                activeTab: 'Senin',
                jadwalList: [],
                counter: 0,
                @php
                    $mappedKelas = $kelasList->map(fn($k) => [
                        'id' => $k->id, 
                        'nama' => $k->tingkat.' '.$k->jurusan.' '.$k->rombel, 
                        'tingkat' => $k->tingkat, 
                        'jurusan' => $k->jurusan, 
                        'rombel' => $k->rombel
                    ]);
                @endphp
                kelasList: {!! $mappedKelas->toJson() !!},

                init() {
                    // Load data existing dari server
                    const existingData = @json($jadwal);

                    for (let hari in existingData) {
                        // Urutkan data dari database berdasarkan jam_ke sebelum dimasukkan
                        existingData[hari].sort((a, b) => a.jam_ke - b.jam_ke).forEach(j => {
                            // Cari kelas_id yang cocok berdasarkan nama kelas yang disimpan ("X RPL 1")
                            let matchedKelas = this.kelasList.find(k => k.nama === j.kelas);

                            this.jadwalList.push({
                                id: ++this.counter,
                                hari: hari,
                                mata_pelajaran: j.mata_pelajaran,
                                kelas_id: matchedKelas ? String(matchedKelas.id) : '',
                                jam_ke: j.jam_ke,
                                jam_mulai: j.jam_mulai.substring(0, 5), // potong detik (H:i)
                                jam_selesai: j.jam_selesai ? j.jam_selesai.substring(0, 5) : '',
                                tipe_blok: j.tipe_blok || 'Semua'
                            });
                        });
                    }
                },

                getJadwalByHari(hari) {
                    return this.jadwalList.filter(j => j.hari === hari);
                },

                tambahJadwal(hari) {
                    this.jadwalList.push({
                        id: ++this.counter,
                        hari: hari,
                        mata_pelajaran: '',
                        kelas_id: '',
                        tipe_blok: 'Semua',
                        jam_ke: 1,
                        jam_mulai: '',
                        jam_selesai: ''
                    });
                },

                hapusJadwal(id) {
                    this.jadwalList = this.jadwalList.filter(j => j.id !== id);
                },

                submitJadwal() {
                    const form = document.getElementById('formJadwal');

                    // Hapus semua input lama (kecuali _token)
                    form.querySelectorAll('input:not([name="_token"]), select').forEach(el => el.remove());

                    // Bangun hidden input bersih dari jadwalList Alpine
                    this.jadwalList.forEach((item, index) => {
                        const fields = {
                            hari:           item.hari,
                            mata_pelajaran: item.mata_pelajaran,
                            kelas_id:       item.kelas_id,
                            jam_ke:         item.jam_ke,
                            jam_mulai:      item.jam_mulai,
                            jam_selesai:    item.jam_selesai || '',
                            tipe_blok:      item.tipe_blok || 'Semua',
                        };
                        Object.entries(fields).forEach(([key, value]) => {
                            const input = document.createElement('input');
                            input.type  = 'hidden';
                            input.name  = `jadwal[${index}][${key}]`;
                            input.value = value;
                            form.appendChild(input);
                        });
                    });

                    form.submit();
                }
            }));
        });
    </script>

</x-app-layout>
