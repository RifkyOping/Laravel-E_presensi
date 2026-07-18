<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalMengajar;
use App\Models\AbsensiKelasSiswa;
use App\Models\SiswaProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiKelasController extends Controller
{
    /**
     * Tampilkan jadwal mengajar guru hari ini.
     */
    public function index()
    {
        $guru = Auth::user();

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
        $today   = Carbon::today()->toDateString();

        $jadwals = JadwalMengajar::where('user_id', $guru->id)
            ->where('hari', $hariIni)
            ->orderBy('jam_ke')
            ->get();

        // Cek jadwal mana yang sudah pernah diabsen hari ini
        foreach ($jadwals as $jadwal) {
            $jadwal->sudah_diabsen = AbsensiKelasSiswa::where('jadwal_mengajar_id', $jadwal->id)
                ->where('tanggal', $today)
                ->exists();
        }

        return view('guru.absen-kelas.index', compact('jadwals', 'hariIni'));
    }

    /**
     * Tampilkan form absen murid untuk jadwal tertentu.
     */
    public function show(JadwalMengajar $jadwal)
    {
        $guru  = Auth::user();
        $today = Carbon::today()->toDateString();

        // Pastikan guru ini yang punya jadwal ini
        if ($jadwal->user_id !== $guru->id) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        // Cek apakah RPP sudah diupload (pending atau disetujui)
        $rppStatus = $guru->guruProfile?->rpp_status ?? null;
        if (!in_array($rppStatus, ['pending', 'disetujui'])) {
            return redirect()->route('guru.absen-kelas.index')->with('error', 'Anda tidak dapat mengisi absensi kelas karena Anda belum mengunggah RPP atau RPP ditolak.');
        }

        // Ambil murid berdasarkan rombel/kelas yang sesuai dengan jadwal
        $siswas = User::where('role', 'murid')
            ->whereHas('siswaProfile', function ($q) use ($jadwal) {
                $q->whereRaw("CONCAT(kelas, ' ', jurusan, ' ', rombel) = ?", [$jadwal->kelas]);
            })
            ->with('siswaProfile')
            ->orderBy('name')
            ->get();

        // Ambil absensi yang sudah ada hari ini (jika sudah pernah disubmit)
        $absensiHariIni = AbsensiKelasSiswa::where('jadwal_mengajar_id', $jadwal->id)
            ->where('tanggal', $today)
            ->get()
            ->keyBy('siswa_id');

        $sudahDiabsen = $absensiHariIni->isNotEmpty();

        return view('guru.absen-kelas.show', compact(
            'jadwal', 'siswas', 'absensiHariIni', 'sudahDiabsen', 'today'
        ));
    }

    /**
     * Simpan absensi kelas.
     */
    public function store(Request $request, JadwalMengajar $jadwal)
    {
        $guru  = Auth::user();
        $today = Carbon::today()->toDateString();

        // Pastikan guru ini yang punya jadwal ini
        if ($jadwal->user_id !== $guru->id) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        // Cek apakah RPP sudah diupload (pending atau disetujui)
        $rppStatus = $guru->guruProfile?->rpp_status ?? null;
        if (!in_array($rppStatus, ['pending', 'disetujui'])) {
            return redirect()->route('guru.absen-kelas.index')->with('error', 'Anda tidak dapat mengisi absensi kelas karena Anda belum mengunggah RPP atau RPP ditolak.');
        }

        // Cek jika sudah pernah disubmit hari ini
        $sudahAda = AbsensiKelasSiswa::where('jadwal_mengajar_id', $jadwal->id)
            ->where('tanggal', $today)
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Absensi kelas ini sudah pernah disimpan hari ini.');
        }

        $request->validate([
            'absensi'              => 'required|array',
            'absensi.*.status'     => 'required|in:hadir,alpa,sakit,izin',
            'absensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        $records = [];
        foreach ($request->absensi as $siswaId => $data) {
            $records[] = [
                'jadwal_mengajar_id' => $jadwal->id,
                'guru_id'            => $guru->id,
                'siswa_id'           => (int) $siswaId,
                'tanggal'            => $today,
                'status'             => $data['status'],
                'keterangan'         => $data['keterangan'] ?? null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        AbsensiKelasSiswa::insert($records);

        return redirect()->route('guru.absen-kelas.index')
            ->with('success', "Absensi kelas {$jadwal->kelas} – {$jadwal->mata_pelajaran} berhasil disimpan.");
    }

    /**
     * Upload RPP guru (dilakukan 1 kali).
     */
    public function uploadRpp(Request $request)
    {
        $guru = Auth::user();

        $request->validate([
            'rpp_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // Maks 5MB
        ], [
            'rpp_file.required' => 'File RPP wajib diunggah.',
            'rpp_file.mimes'    => 'Format file harus berupa PDF, DOC, atau DOCX.',
            'rpp_file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $filePath = $request->file('rpp_file')->store('rpp', 'public');

        $guru->guruProfile()->updateOrCreate(
            ['user_id' => $guru->id],
            [
                'rpp_file'   => $filePath,
                'rpp_status' => 'pending',
                'rpp_pesan'  => null,
            ]
        );

        return back()->with('success', 'File RPP berhasil diunggah dan sedang menunggu persetujuan dari Guru Piket.');
    }
}
