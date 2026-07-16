<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiMengajar;
use App\Models\AbsensiSiswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengawasController extends Controller
{
    // ────────────────────────────────────────────
    //  DASHBOARD — Ringkasan hari ini
    // ────────────────────────────────────────────
    public function dashboard()
    {
        $today = Carbon::today();

        $stats = [
            'total_guru'    => User::where('role', 'guru')->count(),
            'total_siswa'   => User::where('role', 'murid')->count(),
            'guru_hadir'    => AbsensiGuru::whereDate('tanggal', $today)
                                ->whereNotNull('waktu_datang')->count(),
            'guru_izin'     => AbsensiGuru::whereDate('tanggal', $today)
                                ->where('status', 'izin')->count(),
            'guru_sakit'    => AbsensiGuru::whereDate('tanggal', $today)
                                ->where('status', 'sakit')->count(),
            'guru_alpa'    => AbsensiGuru::whereDate('tanggal', $today)
                                ->where('status', 'alpa')->count(),
            'siswa_hadir'   => AbsensiSiswa::whereDate('tanggal', $today)
                                ->where('status', 'hadir')->count(),
        ];

        // Guru yang sudah absen hari ini (5 terbaru)
        $guruHadirHariIni = AbsensiGuru::with('user')
            ->whereDate('tanggal', $today)
            ->orderByDesc('waktu_datang')
            ->take(5)
            ->get();

        // Guru yang BELUM absen hari ini
        $semuaGuru = User::where('role', 'guru')->orderBy('name')->get();
        $sudahAbsen = AbsensiGuru::whereDate('tanggal', $today)->pluck('user_id');
        $belumAbsen = $semuaGuru->whereNotIn('id', $sudahAbsen)->take(5);

        // Aktivitas mengajar hari ini
        $aktivitasHariIni = AbsensiMengajar::with('user')
            ->whereDate('tanggal', $today)
            ->orderBy('jam_ke')
            ->take(5)
            ->get();

        return view('pengawas.dashboard', compact(
            'stats', 'guruHadirHariIni', 'belumAbsen', 'aktivitasHariIni'
        ));
    }

    // ────────────────────────────────────────────
    //  MONITORING ABSENSI GURU
    // ────────────────────────────────────────────
    public function absensiGuru(Request $request)
    {
        $tanggal   = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)
            : Carbon::today();

        $semuaGuru = User::where('role', 'guru')->orderBy('name')->get();

        // Record kehadiran pada tanggal tersebut
        $absensi = AbsensiGuru::with('user')
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('user_id');

        // Riwayat dengan filter opsional (berdasarkan tanggal)
        $query = AbsensiGuru::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('tanggal')
            ->orderBy('user_id');

        if ($request->filled('guru_id')) {
            $query->where('user_id', $request->guru_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $riwayat = $query->paginate(20)->withQueryString();

        return view('pengawas.absensi-guru', compact('semuaGuru', 'absensi', 'tanggal', 'riwayat'));
    }

    // ────────────────────────────────────────────
    //  MONITORING AKTIVITAS MENGAJAR
    // ────────────────────────────────────────────
    public function aktivitasGuru(Request $request)
    {
        $semuaGuru = User::where('role', 'guru')->orderBy('name')->get();
        $tanggal = $request->filled('tanggal') ? Carbon::parse($request->tanggal) : Carbon::today();

        $query = AbsensiMengajar::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('tanggal')
            ->orderBy('jam_ke');

        if ($request->filled('guru_id')) {
            $query->where('user_id', $request->guru_id);
        }

        $aktivitas = $query->paginate(20)->withQueryString();

        return view('pengawas.aktivitas-guru', compact('semuaGuru', 'aktivitas', 'tanggal'));
    }

    // ────────────────────────────────────────────
    //  MONITORING SISWA (daftar + rekap)
    // ────────────────────────────────────────────
    public function absensiSiswa(Request $request)
    {
        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)
            : Carbon::today();

        // Semua siswa (dengan opsional filter nama)
        $siswaQuery = User::where('role', 'murid')->orderBy('name');
        if ($request->filled('search')) {
            $siswaQuery->where('name', 'like', '%' . $request->search . '%');
        }
        $semuaSiswa = $siswaQuery->get();

        // Absensi siswa pada tanggal yang dipilih (keyBy user_id untuk lookup O(1))
        $absensi = AbsensiSiswa::with('user')
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('user_id');

        // Pisahkan siswa hadir vs belum
        $siswaHadir   = $semuaSiswa->filter(fn($s) => $absensi->has($s->id));
        $siswaBelum   = $semuaSiswa->filter(fn($s) => !$absensi->has($s->id));

        // Riwayat semua absensi siswa pada tanggal tersebut (paginated)
        $riwayatQuery = AbsensiSiswa::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('tanggal')
            ->orderBy('user_id');
        if ($request->filled('search')) {
            $riwayatQuery->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        $riwayat = $riwayatQuery->paginate(20)->withQueryString();

        $stats = [
            'total'  => User::where('role', 'murid')->count(),
            'hadir'  => $absensi->where('status', 'hadir')->count(),
            'izin'   => $absensi->where('status', 'izin')->count(),
            'sakit'  => $absensi->where('status', 'sakit')->count(),
            'belum'  => User::where('role', 'murid')->count() - $absensi->count(),
        ];

        return view('pengawas.absensi-siswa', compact(
            'semuaSiswa', 'absensi', 'siswaHadir', 'siswaBelum',
            'riwayat', 'stats', 'tanggal'
        ));
    }
}
