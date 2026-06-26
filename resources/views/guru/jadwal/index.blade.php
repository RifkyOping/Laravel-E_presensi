<x-app-layout pageTitle="Atur Jadwal Mengajar" pageSubtitle="Tetapkan jadwal tetap mingguan Anda">

<div class="max-w-5xl mx-auto space-y-6" x-data="jadwalApp()">
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Jadwal Mengajar Mingguan</h2>
                <p class="text-sm text-slate-500 mt-1">Sistem akan secara otomatis membuatkan jurnal absensi mengajar Anda setiap harinya tepat pada Jam Mulai kelas.</p>
            </div>
            @if(!auth()->user()->is_jadwal_set)
                <div class="bg-amber-50 text-amber-700 px-4 py-2 rounded-lg text-sm font-semibold border border-amber-200">
                    ⚠️ Anda wajib mengatur jadwal perdana
                </div>
            @endif
        </div>

        <form action="{{ route('guru.jadwal.store') }}" method="POST" id="formJadwal">
            @csrf
            
            {{-- Tabs --}}
            <div class="flex flex-wrap gap-2 mb-6 border-b border-slate-200 pb-3">
                <template x-for="hari in days" :key="hari">
                    <button type="button" 
                            @click="activeTab = hari"
                            :class="activeTab === hari ? 'bg-[#1e3a6e] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-colors duration-200"
                            x-text="hari">
                    </button>
                </template>
            </div>

            {{-- Tab Contents --}}
            <div class="min-h-[300px]">
                <template x-for="hari in days" :key="hari">
                    <div x-show="activeTab === hari" class="space-y-4">
                        
                        {{-- Data Kosong --}}
                        <div x-show="getJadwalByHari(hari).length === 0" class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                            <p class="text-slate-500 font-medium mb-3">Tidak ada jadwal di hari <span x-text="hari"></span></p>
                            <button type="button" @click="tambahJadwal(hari)" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50">
                                + Tambah Jam Mengajar
                            </button>
                        </div>

                        {{-- Tabel Dinamis --}}
                        <div x-show="getJadwalByHari(hari).length > 0" class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600">
                                        <th class="p-3 font-semibold rounded-tl-lg">Mata Pelajaran</th>
                                        <th class="p-3 font-semibold">Tingkat</th>
                                        <th class="p-3 font-semibold">Jurusan</th>
                                        <th class="p-3 font-semibold">Rombel</th>
                                        <th class="p-3 font-semibold w-24 text-center">Jam Ke-</th>
                                        <th class="p-3 font-semibold w-32">Jam Mulai</th>
                                        <th class="p-3 font-semibold w-32">Jam Selesai</th>
                                        <th class="p-3 font-semibold rounded-tr-lg w-16 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in getJadwalByHari(hari)" :key="item.id">
                                        <tr class="border-b border-slate-100 group">
                                            <td class="p-2">
                                                <input type="hidden" :name="`jadwal[${item.id}][hari]`" :value="hari">
                                                <select :name="`jadwal[${item.id}][mata_pelajaran]`" x-model="item.mata_pelajaran" required class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                    <option value="">Mapel</option>
                                                    @foreach($mapels as $mapel) <option value="{{$mapel}}">{{$mapel}}</option> @endforeach
                                                </select>
                                            </td>
                                            <td class="p-2">
                                                <select :name="`jadwal[${item.id}][tingkat]`" x-model="item.tingkat" required class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                    <option value="">Tingkat</option>
                                                    @foreach($tingkats as $t) <option value="{{$t}}">{{$t}}</option> @endforeach
                                                </select>
                                            </td>
                                            <td class="p-2">
                                                <select :name="`jadwal[${item.id}][jurusan]`" x-model="item.jurusan" required class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                    <option value="">Jurusan</option>
                                                    @foreach($jurusans as $j) <option value="{{$j}}">{{$j}}</option> @endforeach
                                                </select>
                                            </td>
                                            <td class="p-2">
                                                <select :name="`jadwal[${item.id}][rombel]`" x-model="item.rombel" required class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                                    <option value="">Rombel</option>
                                                    @foreach($rombels as $r) <option value="{{$r}}">{{$r}}</option> @endforeach
                                                </select>
                                            </td>
                                            <td class="p-2">
                                                <input type="number" :name="`jadwal[${item.id}][jam_ke]`" x-model="item.jam_ke" required min="1" class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e] text-center">
                                            </td>
                                            <td class="p-2">
                                                <input type="time" :name="`jadwal[${item.id}][jam_mulai]`" x-model="item.jam_mulai" required class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                            </td>
                                            <td class="p-2">
                                                <input type="time" :name="`jadwal[${item.id}][jam_selesai]`" x-model="item.jam_selesai" class="w-full text-sm border-slate-300 rounded-lg focus:border-[#1e3a6e] focus:ring-[#1e3a6e]">
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" @click="hapusJadwal(item.id)" class="text-red-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <div class="mt-4">
                                <button type="button" @click="tambahJadwal(hari)" class="px-4 py-2 text-[#1e3a6e] bg-blue-50 border border-blue-100 rounded-lg text-sm font-semibold hover:bg-blue-100 transition">
                                    + Tambah Baris
                                </button>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    Catatan: Jika Anda Guru BK atau Kepala Sekolah dan tidak memiliki jadwal tetap, biarkan kosong lalu tekan Simpan.
                </p>
                <button type="submit" class="px-6 py-3 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('jadwalApp', () => ({
            days: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            activeTab: 'Senin',
            jadwalList: [],
            counter: 0,
            
            init() {
                // Load data existing dari server
                const existingData = @json($jadwal);
                
                for(let hari in existingData) {
                    existingData[hari].forEach(j => {
                        // Split kelas string into parts: "X RPL 1" -> ["X", "RPL", "1"]
                        let parts = j.kelas.split(' ');
                        let t = parts[0] || '';
                        let r = parts[parts.length - 1] || '';
                        let jur = parts.slice(1, -1).join(' ') || '';
                        
                        this.jadwalList.push({
                            id: ++this.counter,
                            hari: hari,
                            mata_pelajaran: j.mata_pelajaran,
                            tingkat: t,
                            jurusan: jur,
                            rombel: r,
                            jam_ke: j.jam_ke,
                            jam_mulai: j.jam_mulai.substring(0, 5), // potong detik (H:i)
                            jam_selesai: j.jam_selesai ? j.jam_selesai.substring(0, 5) : ''
                        });
                    });
                }
            },
            
            getJadwalByHari(hari) {
                return this.jadwalList.filter(j => j.hari === hari).sort((a, b) => a.jam_ke - b.jam_ke);
            },
            
            tambahJadwal(hari) {
                this.jadwalList.push({
                    id: ++this.counter,
                    hari: hari,
                    mata_pelajaran: '',
                    tingkat: '',
                    jurusan: '',
                    rombel: '',
                    jam_ke: 1,
                    jam_mulai: '',
                    jam_selesai: ''
                });
            },
            
            hapusJadwal(id) {
                this.jadwalList = this.jadwalList.filter(j => j.id !== id);
            }
        }));
    });
</script>

</x-app-layout>
