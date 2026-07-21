@php 
    use Carbon\Carbon;
    
    $userId = Auth::id();
    $today = Carbon::today()->toDateString();
    
    $hariMap = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];
    $hariIni = $hariMap[Carbon::now()->format('l')];

    // 1. Data Absensi Pribadi Guru (Masuk/Pulang)
    $absenPribadi = \App\Models\AbsensiGuru::where('user_id', $userId)
        ->where('tanggal', $today)
        ->first();

    // 2. Data Jadwal Mengajar Hari Ini
    $jadwalHariIni = \App\Models\JadwalMengajar::where('user_id', $userId)
        ->where('hari', $hariIni)
        ->orderBy('jam_ke')
        ->get();
    
    // Cek status absen tiap kelas (apakah guru sudah absen siswa di kelas tsb)
    foreach ($jadwalHariIni as $jadwal) {
        $jadwal->sudah_absen_kelas = \App\Models\AbsensiKelasSiswa::where('jadwal_mengajar_id', $jadwal->id)
            ->where('tanggal', $today)
            ->exists();
    }
    
    $totalKelas = $jadwalHariIni->count();
    $kelasSelesai = $jadwalHariIni->where('sudah_absen_kelas', true)->count();

    // 3. Persetujuan Tertunda
    $pendingCount = \App\Models\AbsensiSiswa::where('guru_id', $userId)
        ->where('status_pengajuan', 'pending')
        ->count();

    // 4. Data Chart: Aktivitas 7 Hari Terakhir
    $tujuhHariLalu = Carbon::today()->subDays(6);
    // Kita kumpulkan tanggal 7 hari terakhir
    $labels = [];
    $dataAktivitas = [];
    for ($i = 0; $i < 7; $i++) {
        $date = $tujuhHariLalu->copy()->addDays($i);
        $labels[] = $date->format('d M');
        $count = \App\Models\AbsensiMengajar::where('user_id', $userId)
                    ->where('tanggal', $date->toDateString())
                    ->count();
        $dataAktivitas[] = $count;
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <span class="text-sm font-bold text-slate-800">Dashboard Guru</span>
    </x-slot>

<div class="space-y-6">

    {{-- Welcome Strip --}}
    <div class="relative overflow-hidden bg-[#1e3a6e] rounded-2xl px-5 py-5 sm:px-8 sm:py-7 shadow-xl"
         style="box-shadow: 0 8px 32px rgba(30,58,110,.3)">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-blue-300 text-sm font-semibold mb-1">Selamat datang,</p>
                <h1 class="text-white text-2xl font-black leading-tight">{{ Auth::user()->name }}</h1>
                <p class="text-blue-300/80 text-sm mt-1">
                    {{ Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex flex-col items-start sm:items-end gap-1 shrink-0">
                <span class="text-white/40 text-xs uppercase tracking-widest font-bold">Guru</span>
                <span class="text-white text-sm font-semibold">E-Presensi {{ \App\Models\SchoolSetting::get()->nama_sekolah }}</span>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full border-[40px] border-white/5 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-12 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

    {{-- Metric Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Card 1: Status Absensi --}}
        <div class="bg-white rounded-xl border border-blue-100 p-5 shadow-sm flex items-center gap-4 border-l-4 border-l-blue-600">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Absensi Pribadi</p>
                @if($absenPribadi && $absenPribadi->waktu_datang)
                    <div class="flex flex-col">
                        <span class="text-blue-600 font-bold text-lg leading-none">Sudah Hadir</span>
                        <span class="text-xs text-slate-500 mt-1">Masuk: {{ Carbon::parse($absenPribadi->waktu_datang)->format('H:i') }}</span>
                    </div>
                @else
                    <div class="flex flex-col">
                        <span class="text-slate-700 font-bold text-lg leading-none">Belum Hadir</span>
                        <a href="{{ route('guru.absensi') }}" class="text-xs text-blue-600 font-semibold hover:underline mt-1">Absen sekarang &rarr;</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Total Kelas --}}
        <div class="bg-white rounded-xl border border-blue-100 p-5 shadow-sm flex items-center gap-4 border-l-4 border-l-blue-500">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Jadwal Mengajar</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-[#1e3a6e]">{{ $totalKelas }}</span>
                    <span class="text-sm font-semibold text-slate-500">Kelas</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">{{ $kelasSelesai }} kelas sudah diabsen</p>
            </div>
        </div>

        {{-- Card 3: Persetujuan --}}
        <div class="bg-white rounded-xl border border-blue-100 p-5 shadow-sm flex items-center gap-4 border-l-4 border-l-blue-400 relative overflow-hidden">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Persetujuan Siswa</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-[#1e3a6e]">{{ $pendingCount }}</span>
                    <span class="text-sm font-semibold text-slate-500">Menunggu</span>
                </div>
            </div>
            @if($pendingCount > 0)
                <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-blue-100 rounded-full animate-ping opacity-20 pointer-events-none"></div>
            @endif
        </div>
    </div>

    {{-- Main Content: Chart & Timeline --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Chart Section (Left, 2/3 width) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col h-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-[#1e3a6e]">Aktivitas Mengajar (7 Hari Terakhir)</h3>
            </div>
            <div class="flex-1 w-full min-h-[250px] relative">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        {{-- Timeline Section (Right, 1/3 width) --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col h-full">
            <h3 class="font-bold text-[#1e3a6e] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Jadwal Hari Ini
            </h3>
            
            <div class="flex-1 overflow-y-auto pr-2">
                @if($jadwalHariIni->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-blue-50 text-blue-300 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-500">Tidak ada jadwal mengajar hari ini.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($jadwalHariIni as $idx => $jadwal)
                            <div class="relative pl-6 pb-2 {{ !$loop->last ? 'border-l-2 border-blue-100' : '' }}">
                                <div class="absolute -left-[5px] top-1.5 w-2 h-2 rounded-full {{ $jadwal->sudah_absen_kelas ? 'bg-blue-600 ring-4 ring-blue-100' : 'bg-slate-300' }}"></div>
                                
                                <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 hover:border-blue-200 hover:shadow-sm transition group">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-xs font-bold text-blue-600 uppercase">Jam ke-{{ $jadwal->jam_ke }}</span>
                                        <span class="text-xs font-semibold text-slate-400">{{ Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-sm group-hover:text-[#1e3a6e]">{{ $jadwal->kelas }}</h4>
                                    <p class="text-xs text-slate-500 mt-1 truncate">{{ $jadwal->mata_pelajaran }}</p>
                                    
                                    <div class="mt-2 pt-2 border-t border-slate-200 flex justify-between items-center">
                                        <span class="text-[0.65rem] font-bold {{ $jadwal->sudah_absen_kelas ? 'text-blue-600' : 'text-slate-400' }}">
                                            {{ $jadwal->sudah_absen_kelas ? '✔ Sudah Diabsen' : '⏳ Belum Diabsen' }}
                                        </span>
                                        <a href="{{ route('guru.absen-kelas.show', $jadwal->id) }}" class="text-[0.65rem] font-bold text-white bg-[#1e3a6e] hover:bg-blue-800 py-1 px-2.5 rounded-full transition">
                                            Buka Kelas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Quick Access --}}
    <div>
        <p class="text-[.7rem] font-black uppercase tracking-widest text-slate-400 mb-3">Akses Cepat</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">

            {{-- Absen Sekolah --}}
            <a href="{{ route('guru.absensi') }}"
               class="group bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40">
                <h3 class="font-bold text-slate-800 text-sm group-hover:text-[#1e3a6e] transition-colors">Absen Sekolah</h3>
                <p class="text-xs text-slate-500 leading-relaxed flex-1">Catat kehadiran datang & pulang sekolah.</p>
            </a>

            {{-- Aktivitas Mengajar --}}
            <a href="{{ route('guru.aktivitas') }}"
               class="group bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40">
                <h3 class="font-bold text-slate-800 text-sm group-hover:text-[#1e3a6e] transition-colors">Jurnal Mengajar</h3>
                <p class="text-xs text-slate-500 leading-relaxed flex-1">Isi aktivitas dan materi pembelajaran.</p>
            </a>

            {{-- Literasi Keagamaan --}}
            <a href="{{ route('guru.literasi.quran') }}"
               class="group bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40">
                <h3 class="font-bold text-slate-800 text-sm group-hover:text-[#1e3a6e] transition-colors">Literasi Agama</h3>
                <p class="text-xs text-slate-500 leading-relaxed flex-1">Pantau perkembangan literasi agama siswa.</p>
            </a>

            {{-- Persetujuan Izin/Sakit Siswa --}}
            <a href="{{ route('guru.persetujuan-absensi') }}"
               class="group bg-white rounded-xl border border-slate-200 p-5 flex flex-col gap-3
                      transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-[#1e3a6e]/40 relative">
                @if($pendingCount > 0)
                <div class="absolute top-4 right-4 bg-blue-600 text-white w-5 h-5 rounded-full flex items-center justify-center font-bold text-[10px] shadow-sm">
                    {{ $pendingCount }}
                </div>
                @endif
                <h3 class="font-bold text-slate-800 text-sm group-hover:text-[#1e3a6e] transition-colors">Persetujuan Siswa</h3>
                <p class="text-xs text-slate-500 leading-relaxed flex-1">Kelola perizinan ketidakhadiran murid.</p>
            </a>

        </div>
    </div>

</div>
</x-app-layout>

{{-- Modal Wajib Isi Jadwal --}}
@if(!Auth::user()->is_jadwal_set)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl text-center border-4 border-[#1e3a6e]">
        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-5 text-blue-600">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-2xl font-black text-[#1e3a6e] mb-3">Atur Jadwal Mengajar</h2>
        <p class="text-slate-600 mb-8 leading-relaxed">
            Sistem E-Presensi sekarang dilengkapi <strong>Otomatisasi Jurnal Mengajar</strong>. Anda wajib mengatur jadwal mengajar mingguan Anda terlebih dahulu agar sistem dapat membuatkan jurnal harian secara otomatis.
        </p>
        <a href="{{ route('guru.jadwal.index') }}" class="block w-full py-3.5 px-6 bg-[#1e3a6e] hover:bg-[#162d57] text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition-all text-lg">
            Atur Jadwal Sekarang
        </a>
    </div>
</div>
@endif

{{-- Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Chart.js Initialization ---
    const ctx = document.getElementById('activityChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Jumlah Kelas yang Diisi Jurnal',
                    data: {!! json_encode($dataAktivitas) !!},
                    backgroundColor: '#1e3a6e',
                    borderRadius: 6,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e3a6e',
                        titleFont: { size: 13, family: 'Inter' },
                        bodyFont: { size: 14, family: 'Inter', weight: 'bold' },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Inter', size: 11 },
                            color: '#94a3b8'
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false,
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#64748b'
                        },
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // --- SweetAlert Notification ---
    @if($pendingCount > 0)
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                iconColor: '#1e3a6e',
                title: 'Ada Pengajuan Baru!',
                text: 'Terdapat {{ $pendingCount }} pengajuan izin/sakit siswa yang menunggu persetujuan Anda.',
                confirmButtonColor: '#1e3a6e',
                confirmButtonText: 'Lihat Sekarang',
                showCancelButton: true,
                cancelButtonColor: '#f1f5f9',
                cancelButtonText: '<span style="color:#64748b">Tutup</span>',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-6 font-bold',
                    cancelButton: 'rounded-lg px-6 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('guru.persetujuan-absensi') }}";
                }
            });
        }
    @endif
});
</script>
