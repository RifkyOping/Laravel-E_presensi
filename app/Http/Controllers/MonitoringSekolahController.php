<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\AbsensiMengajar;
use App\Models\AbsensiKelasSiswa;
use App\Models\AbsensiSholatSiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringSekolahController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!auth()->user()->is_kepsek && !auth()->user()->is_kurikulum) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $today = Carbon::today();
        
        $filter = $request->query('filter', 'harian');
        $bulan = $request->query('bulan', $today->format('m'));
        $tahun = $request->query('tahun', $today->format('Y'));

        // 1. Data Hari Ini (Ringkasan)
        $totalGuru = User::where('role', 'guru')->count();
        $totalSiswa = User::where('role', 'murid')->count();
        $totalSiswaIslam = User::whereHas('siswaProfile', function($q) {
            $q->where('agama', 'Islam');
        })->count();

        $guruHadirHariIni = AbsensiGuru::whereDate('tanggal', $today)->whereNotNull('waktu_datang')->count();
        $siswaHadirHariIni = AbsensiSiswa::whereDate('tanggal', $today)->where('status', 'hadir')->count();
        $guruMengajarHariIni = AbsensiMengajar::whereDate('tanggal', $today)->whereNotNull('waktu_absen_masuk')->distinct('user_id')->count();
        $siswaHadirKelasHariIni = AbsensiKelasSiswa::whereDate('tanggal', $today)->where('status', 'hadir')->distinct('siswa_id')->count();
        $siswaSholatHariIni = AbsensiSholatSiswa::whereDate('tanggal', $today)->whereIn('status', ['hadir', 'berjamaah'])->distinct('user_id')->count();

        $stats = [
            'guru' => [
                'total' => $totalGuru,
                'hadir' => $guruHadirHariIni,
                'mengajar' => $guruMengajarHariIni,
                'persen_hadir' => $totalGuru > 0 ? round(($guruHadirHariIni / $totalGuru) * 100) : 0,
            ],
            'siswa' => [
                'total' => $totalSiswa,
                'hadir' => $siswaHadirHariIni,
                'hadir_kelas' => $siswaHadirKelasHariIni,
                'persen_hadir' => $totalSiswa > 0 ? round(($siswaHadirHariIni / $totalSiswa) * 100) : 0,
            ],
            'sholat' => [
                'total' => $totalSiswaIslam,
                'hadir' => $siswaSholatHariIni,
                'persen_hadir' => $totalSiswaIslam > 0 ? round(($siswaSholatHariIni / $totalSiswaIslam) * 100) : 0,
            ]
        ];

        // 2. Data Grafik
        $chartData = [
            'labels' => [],
            'guru' => [],
            'siswa' => [],
            'sholat' => [],
        ];

        if ($filter === 'harian') {
            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($tahun, $bulan, $day);
                if ($date->isSunday()) continue;
                if ($date > Carbon::today()) break;

                $chartData['labels'][] = $date->translatedFormat('d M');
                $chartData['guru'][] = AbsensiGuru::whereDate('tanggal', $date)->whereNotNull('waktu_datang')->count();
                $chartData['siswa'][] = AbsensiSiswa::whereDate('tanggal', $date)->where('status', 'hadir')->count();
                $chartData['sholat'][] = AbsensiSholatSiswa::whereDate('tanggal', $date)->whereIn('status', ['hadir', 'berjamaah'])->distinct('user_id')->count();
            }
        } elseif ($filter === 'mingguan') {
            $firstDay = Carbon::createFromDate($tahun, $bulan, 1);
            $lastDay = $firstDay->copy()->endOfMonth();
            
            $currentDate = $firstDay->copy()->startOfWeek();
            $weekNum = 1;
            while ($currentDate <= $lastDay) {
                if ($currentDate > Carbon::today()) break;
                
                $endOfWeek = $currentDate->copy()->endOfWeek()->subDay(); // sampai Sabtu
                if ($endOfWeek > $lastDay) $endOfWeek = $lastDay;

                $chartData['labels'][] = "Minggu $weekNum";
                
                $workDays = 0;
                for ($d = $currentDate->copy(); $d <= $endOfWeek; $d->addDay()) {
                    if (!$d->isSunday()) $workDays++;
                }

                $g = AbsensiGuru::whereBetween('tanggal', [$currentDate, $endOfWeek])->whereNotNull('waktu_datang')->count();
                $s = AbsensiSiswa::whereBetween('tanggal', [$currentDate, $endOfWeek])->where('status', 'hadir')->count();
                $sh = AbsensiSholatSiswa::whereBetween('tanggal', [$currentDate, $endOfWeek])->whereIn('status', ['hadir', 'berjamaah'])->count();

                $chartData['guru'][] = $workDays > 0 ? round($g / $workDays) : 0;
                $chartData['siswa'][] = $workDays > 0 ? round($s / $workDays) : 0;
                $chartData['sholat'][] = $workDays > 0 ? round($sh / $workDays) : 0;
                
                $currentDate->addWeek();
                $weekNum++;
            }
        } elseif ($filter === 'bulanan') {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::createFromDate($tahun, $m, 1);
                if ($date > Carbon::today()->endOfMonth()) break;
                
                $chartData['labels'][] = $date->translatedFormat('M Y');
                
                $daysInMonth = $date->daysInMonth;
                $workDays = 0;
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    if (!Carbon::createFromDate($tahun, $m, $d)->isSunday()) $workDays++;
                }
                
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();
                if ($endOfMonth > Carbon::today()) {
                    $endOfMonth = Carbon::today();
                    $workDays = 0;
                    for ($d = 1; $d <= Carbon::today()->day; $d++) {
                        if (!Carbon::createFromDate($tahun, $m, $d)->isSunday()) $workDays++;
                    }
                }
                
                $g = AbsensiGuru::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->whereNotNull('waktu_datang')->count();
                $s = AbsensiSiswa::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->where('status', 'hadir')->count();
                $sh = AbsensiSholatSiswa::whereBetween('tanggal', [$startOfMonth, $endOfMonth])->whereIn('status', ['hadir', 'berjamaah'])->count();

                $chartData['guru'][] = $workDays > 0 ? round($g / $workDays) : 0;
                $chartData['siswa'][] = $workDays > 0 ? round($s / $workDays) : 0;
                $chartData['sholat'][] = $workDays > 0 ? round($sh / $workDays) : 0;
            }
        }

        // 3. Data Tabel Detail
        $tanggal_detail = $request->query('tanggal', $today->format('Y-m-d'));
        $detailDate = Carbon::parse($tanggal_detail);

        $listGuru = User::with(['absensiGuru' => function($q) use ($detailDate) {
                $q->whereDate('tanggal', $detailDate);
            }, 'aktivitasMengajar' => function($q) use ($detailDate) {
                $q->whereDate('tanggal', $detailDate);
            }])
            ->where('role', 'guru')
            ->orderBy('name')
            ->get();

        $listSiswa = User::with(['siswaProfile', 'absensiSiswa' => function($q) use ($detailDate) {
                $q->whereDate('tanggal', $detailDate);
            }, 'absensiKelasSiswa' => function($q) use ($detailDate) {
                $q->whereDate('tanggal', $detailDate);
            }, 'absensiSholat' => function($q) use ($detailDate) {
                $q->whereDate('tanggal', $detailDate);
            }])
            ->where('role', 'murid')
            ->orderBy('name')
            ->get();

        $listKelas = $listSiswa->map(function($u) {
            return trim(($u->siswaProfile->kelas ?? '') . ' ' . ($u->siswaProfile->jurusan ?? '') . ' ' . ($u->siswaProfile->rombel ?? ''));
        })->unique()->filter()->sort()->values();

        $tab = $request->query('tab', 'ringkasan');

        return view('monitoring-sekolah.dashboard', compact('stats', 'chartData', 'today', 'filter', 'bulan', 'tahun', 'listGuru', 'listSiswa', 'tanggal_detail', 'detailDate', 'tab', 'listKelas'));
    }
}
