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

        $setting = \App\Models\SchoolSetting::get();
        $blokAktif = $setting->blok_jadwal_aktif;

        if ($blokAktif === 'TEFA') {
            $jadwals = collect();
        } else {
            $jadwals = JadwalMengajar::where('user_id', $guru->id)
                ->where('hari', $hariIni)
                ->whereIn('tipe_blok', ['Semua', $blokAktif])
                ->orderBy('jam_ke')
                ->get();
        }

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

        // Cek apakah RPP sudah diupload (pending atau disetujui) untuk bulan ini
        $rppStatus = $guru->rpp_status;
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

        // Ambil pengajuan sakit/izin harian yang SUDAH DISETUJUI untuk default status (termasuk multi-hari)
        $absensiHarianSiswa = \App\Models\AbsensiSiswa::whereIn('user_id', $siswas->pluck('id'))
            ->whereIn('status', ['sakit', 'izin'])
            ->where('status_pengajuan', 'approved')
            ->whereDate('tanggal', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->get()
            ->keyBy('user_id');

        $sudahDiabsen = $absensiHariIni->isNotEmpty();

        // Cari atau buat Jurnal/Aktivitas Mengajar untuk kelas ini hari ini
        $aktivitas = \App\Models\AbsensiMengajar::firstOrCreate([
            'user_id' => $guru->id,
            'tanggal' => $today,
            'kelas'   => $jadwal->kelas,
            'jam_ke'  => $jadwal->jam_ke,
        ], [
            'mata_pelajaran' => $jadwal->mata_pelajaran,
            'jam_mulai'      => $jadwal->jam_mulai,
            'jam_selesai'    => $jadwal->jam_selesai,
        ]);

        $aktivitas->load('verifier');

        return view('guru.absen-kelas.show', compact(
            'jadwal', 'siswas', 'absensiHariIni', 'absensiHarianSiswa', 'sudahDiabsen', 'today', 'aktivitas'
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

        // Cek apakah RPP sudah diupload (pending atau disetujui) untuk bulan ini
        $rppStatus = $guru->rpp_status;
        if (!in_array($rppStatus, ['pending', 'disetujui'])) {
            return redirect()->route('guru.absen-kelas.index')->with('error', 'Anda tidak dapat mengisi absensi kelas karena Anda belum mengunggah RPP atau RPP ditolak.');
        }

        // Allow update by removing the early return for $sudahAda
        // We will use updateOrCreate to handle both insert and update.

        $request->validate([
            'materi'               => 'required|string',
            'absensi'              => 'required|array',
            'absensi.*.status'     => 'required|in:hadir,alpa,sakit,izin',
            'absensi.*.keterangan' => 'nullable|string|max:255',
        ], [
            'materi.required' => 'Materi pembelajaran harus diisi sebelum menyimpan absensi kelas.',
        ]);

        foreach ($request->absensi as $siswaId => $data) {
            AbsensiKelasSiswa::updateOrCreate([
                'jadwal_mengajar_id' => $jadwal->id,
                'guru_id'            => $guru->id,
                'siswa_id'           => (int) $siswaId,
                'tanggal'            => $today,
            ], [
                'status'             => $data['status'],
                'keterangan'         => $data['keterangan'] ?? null,
                'materi'             => $request->materi,
            ]);
        }

        return redirect()->route('guru.absen-kelas.show', $jadwal->id)
            ->with('success', "Absensi kelas {$jadwal->kelas} - {$jadwal->mata_pelajaran} berhasil disimpan.");
    }

    /**
     * Download rekap absensi kelas hari ini.
     */
    public function export(Request $request, JadwalMengajar $jadwal)
    {
        $guru  = Auth::user();
        $today = Carbon::today()->toDateString();

        if ($jadwal->user_id !== $guru->id) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $absensiHariIni = AbsensiKelasSiswa::where('jadwal_mengajar_id', $jadwal->id)
            ->where('tanggal', $today)
            ->with('siswa')
            ->get();

        if ($absensiHariIni->isEmpty()) {
            return back()->with('error', 'Belum ada data absensi untuk diunduh.');
        }

        $materi = $absensiHariIni->first()->materi ?? '-';
        $fileName = 'Rekap_Absensi_Kelas_' . str_replace(' ', '_', $jadwal->kelas) . '_' . date('Y-m-d') . '.xlsx';

        $rows = [];
        $rows[] = ['Mata Pelajaran:', $jadwal->mata_pelajaran];
        $rows[] = ['Kelas:', $jadwal->kelas];
        $rows[] = ['Tanggal:', Carbon::parse($today)->translatedFormat('d F Y')];
        $rows[] = ['Materi:', $materi];
        $rows[] = [];
        
        $columns = ['No', 'Nama Siswa', 'Nomor Induk', 'Status', 'Keterangan'];
        $rows[] = $columns;

        $no = 1;
        foreach ($absensiHariIni as $absen) {
            $rows[] = [
                $no++,
                $absen->siswa->name ?? '-',
                $absen->siswa->nomor_induk ?? '-',
                ucfirst($absen->status),
                $absen->keterangan ?? '-'
            ];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=$fileName",
        ]);
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
                'rpp_file'    => $filePath,
                'rpp_status'  => 'pending',
                'rpp_pesan'   => null,
                'rpp_periode' => date('Y-m'),
            ]
        );

        return back()->with('success', 'File RPP berhasil diunggah dan sedang menunggu persetujuan dari Guru Piket.');
    }

    /**
     * Tampilkan form cetak buku kemajuan kelas.
     */
    public function bukuKemajuan(Request $request)
    {
        $kelasList = \App\Models\Kelas::where('status', true)
            ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();

        $jadwalHariIni = null;
        $filterKelas = $request->get('filter_kelas');
        
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
        $today = Carbon::today()->toDateString();

        if ($filterKelas) {
            $setting = \App\Models\SchoolSetting::get();
            $blokAktif = $setting->blok_jadwal_aktif;

            if ($blokAktif === 'TEFA') {
                $jadwalHariIni = collect();
            } else {
                $jadwalHariIni = \App\Models\JadwalMengajar::with('user')
                    ->where('hari', $hariIni)
                    ->where('kelas', $filterKelas)
                    ->whereIn('tipe_blok', ['Semua', $blokAktif])
                    ->orderBy('jam_ke')
                    ->get();
            }
                
            foreach ($jadwalHariIni as $jadwal) {
                // Status Absensi Masuk & Pulang Guru (dari Aktivitas Mengajar)
                $absenMengajar = \App\Models\AbsensiMengajar::where('user_id', $jadwal->user_id)
                    ->where('tanggal', $today)
                    ->where('kelas', $jadwal->kelas)
                    ->where('jam_ke', $jadwal->jam_ke)
                    ->first();
                $jadwal->waktu_datang = $absenMengajar ? $absenMengajar->waktu_absen_masuk : null;
                $jadwal->waktu_pulang = $absenMengajar ? $absenMengajar->waktu_absen_keluar : null;
                
                // Status Absen Kelas Siswa
                $jadwal->sudah_absen_kelas = \App\Models\AbsensiKelasSiswa::where('jadwal_mengajar_id', $jadwal->id)
                    ->where('tanggal', $today)
                    ->exists();
            }
        }

        return view('guru.buku-kemajuan.index', compact('kelasList', 'jadwalHariIni', 'filterKelas', 'hariIni', 'today'));
    }

    /**
     * Cetak buku kemajuan kelas.
     */
    public function cetakBukuKemajuan(Request $request)
    {
        $request->validate([
            'kelas' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $kelas = $request->kelas;
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir);

        $aktivitas = \App\Models\AbsensiMengajar::with(['user'])
            ->where('kelas', $kelas)
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->orderBy('tanggal')
            ->orderBy('jam_ke')
            ->get();

        foreach ($aktivitas as $item) {
            $absenSiswa = \App\Models\AbsensiKelasSiswa::where('guru_id', $item->user_id)
                ->where('tanggal', $item->tanggal)
                ->whereHas('jadwalMengajar', function($q) use ($kelas, $item) {
                    $q->where('kelas', $kelas)
                      ->where('jam_ke', $item->jam_ke);
                })->first();
                
            $item->materi_pembelajaran = $absenSiswa ? $absenSiswa->materi : '';
        }

        // Group by week
        $aktivitasPerMinggu = $aktivitas->groupBy(function($date) {
            return Carbon::parse($date->tanggal)->startOfWeek()->format('Y-m-d');
        });

        return view('guru.buku-kemajuan.cetak', compact('kelas', 'tanggalMulai', 'tanggalAkhir', 'aktivitasPerMinggu'));
    }
}
