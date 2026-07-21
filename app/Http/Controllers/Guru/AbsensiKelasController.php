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

        return view('guru.absen-kelas.show', compact(
            'jadwal', 'siswas', 'absensiHariIni', 'sudahDiabsen', 'today', 'aktivitas'
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
            'materi'               => 'required|string',
            'absensi'              => 'required|array',
            'absensi.*.status'     => 'required|in:hadir,alpa,sakit,izin',
            'absensi.*.keterangan' => 'nullable|string|max:255',
        ], [
            'materi.required' => 'Materi pembelajaran harus diisi sebelum menyimpan absensi kelas.',
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
                'materi'             => $request->materi,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        AbsensiKelasSiswa::insert($records);

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
        $fileName = 'Rekap_Absensi_Kelas_' . str_replace(' ', '_', $jadwal->kelas) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Nama Siswa', 'Nomor Induk', 'Status', 'Keterangan'];

        $delimiter = $request->input('delimiter', ';');

        $callback = function() use($absensiHariIni, $columns, $materi, $jadwal, $today, $delimiter) {
            $file = fopen('php://output', 'w');
            
            // Tulis header info
            fputcsv($file, ['Mata Pelajaran:', $jadwal->mata_pelajaran], $delimiter);
            fputcsv($file, ['Kelas:', $jadwal->kelas], $delimiter);
            fputcsv($file, ['Tanggal:', Carbon::parse($today)->translatedFormat('d F Y')], $delimiter);
            fputcsv($file, ['Materi:', $materi], $delimiter);
            fputcsv($file, [], $delimiter);
            
            // Tulis kolom
            fputcsv($file, $columns, $delimiter);

            $no = 1;
            foreach ($absensiHariIni as $absen) {
                $row['No']          = $no++;
                $row['Nama Siswa']  = $absen->siswa->name ?? '-';
                $row['Nomor Induk'] = $absen->siswa->nomor_induk ?? '-';
                $row['Status']      = ucfirst($absen->status);
                $row['Keterangan']  = $absen->keterangan ?? '-';

                fputcsv($file, $row, $delimiter);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            $jadwalHariIni = \App\Models\JadwalMengajar::with('user')
                ->where('hari', $hariIni)
                ->where('kelas', $filterKelas)
                ->orderBy('jam_ke')
                ->get();
                
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
