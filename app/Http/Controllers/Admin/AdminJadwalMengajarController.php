<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JadwalMengajar;
use Illuminate\Support\Facades\Response;
use Shuchkin\SimpleXLSX;

class AdminJadwalMengajarController extends Controller
{
    /**
     * Menampilkan daftar guru untuk dikelola jadwalnya.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'guru');
        
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_induk', 'like', '%' . $request->search . '%');
            });
        }
        
        // Cek apakah guru sudah punya jadwal
        $gurus = $query->withCount('jadwalMengajars')->paginate(50);
        
        $setting = \App\Models\SchoolSetting::get();
        $blokAktif = $setting->blok_jadwal_aktif;
        
        return view('admin.jadwal.index', compact('gurus', 'blokAktif'));
    }

    /**
     * Menampilkan rekap keseluruhan jadwal mengajar dalam bentuk matriks.
     */
    public function rekap(Request $request)
    {
        $hariMap = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
        ];
        $hariIniStr = $hariMap[now()->format('l')] ?? 'Senin';
        $activeTab = $request->query('hari', $hariIniStr);
        $filterBlok = $request->query('tipe_blok', ''); // '', 'A', 'B', 'Semua'

        // Ambil semua kelas yang ada di tabel kelas
        $kelasList = \App\Models\Kelas::where('status', true)
            ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();

        // Ambil semua jadwal (semua blok)
        $jadwalRaw = JadwalMengajar::with('user')->get();

        $maxJam = $jadwalRaw->max('jam_ke') ?? 10;
        if ($maxJam < 10) $maxJam = 10;

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $matrix = [];

        foreach ($hariList as $hari) {
            $matrix[$hari] = [];
            foreach ($kelasList as $kelas) {
                $namaKelas = trim("{$kelas->tingkat} {$kelas->jurusan} {$kelas->rombel}");
                $matrix[$hari][$namaKelas] = [];
                for ($i = 1; $i <= $maxJam; $i++) {
                    $matrix[$hari][$namaKelas][$i] = [];
                }
            }
        }

        foreach ($jadwalRaw as $j) {
            $hari = $j->hari;
            if (!in_array($hari, $hariList)) continue;
            
            $kelasStr = $j->kelas;
            if (!isset($matrix[$hari][$kelasStr])) {
                $matrix[$hari][$kelasStr] = [];
                for ($i = 1; $i <= $maxJam; $i++) {
                    $matrix[$hari][$kelasStr][$i] = [];
                }
            }
            if (isset($matrix[$hari][$kelasStr][$j->jam_ke])) {
                $matrix[$hari][$kelasStr][$j->jam_ke][] = $j;
            }
        }

        return view('admin.jadwal.rekap', compact('kelasList', 'activeTab', 'filterBlok', 'matrix', 'maxJam', 'hariList'));
    }

    /**
     * Menampilkan form edit jadwal untuk guru tertentu.
     */
    public function edit(User $user)
    {
        if ($user->role !== 'guru') {
            return redirect()->route('admin.jadwal-mengajar.index')->with('error', 'User bukan guru.');
        }

        $jadwalRaw = $user->jadwalMengajars()->orderBy('jam_ke')->get();

        // Kelompokkan berdasarkan hari
        $jadwal = [
            'Senin'  => [],
            'Selasa' => [],
            'Rabu'   => [],
            'Kamis'  => [],
            'Jumat'  => [],
        ];

        foreach ($jadwalRaw as $j) {
            $jadwal[$j->hari][] = $j;
        }

        // Ambil opsi kelas
        $kelasList = \App\Models\Kelas::where('status', true)
            ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();
        $mapels   = \App\Models\MataPelajaran::where('aktif', true)->orderBy('nama')->pluck('nama');

        return view('admin.jadwal.edit', compact('user', 'jadwal', 'kelasList', 'mapels'));
    }

    /**
     * Menyimpan jadwal untuk guru tertentu.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== 'guru') {
            return redirect()->route('admin.jadwal-mengajar.index')->with('error', 'User bukan guru.');
        }

        // Validasi struktur dinamis
        $request->validate([
            'jadwal'                   => 'nullable|array',
            'jadwal.*.hari'            => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jadwal.*.mata_pelajaran'  => 'required|string',
            'jadwal.*.kelas_id'        => 'required|exists:kelas,id',
            'jadwal.*.tipe_blok'       => 'required|in:A,B,Semua',
            'jadwal.*.jam_ke'          => 'required|integer|min:1',
            'jadwal.*.jam_mulai'       => 'required',
            'jadwal.*.jam_selesai'     => 'nullable',
        ], [
            'jadwal.*.hari.required'           => 'Kolom hari wajib diisi.',
            'jadwal.*.hari.in'                 => 'Nama hari tidak valid.',
            'jadwal.*.mata_pelajaran.required'  => 'Mata pelajaran wajib diisi.',
            'jadwal.*.kelas_id.required'       => 'Kelas wajib dipilih.',
            'jadwal.*.kelas_id.exists'         => 'Kelas yang dipilih tidak ditemukan.',
            'jadwal.*.jam_ke.required'         => 'Mapel ke- wajib diisi.',
            'jadwal.*.jam_ke.integer'          => 'Mapel ke- harus berupa angka.',
            'jadwal.*.jam_ke.min'              => 'Mapel ke- minimal 1.',
            'jadwal.*.jam_mulai.required'      => 'Jam mulai wajib diisi.',
        ]);

        // Ubah pesan error agar tidak tampilkan nama field teknis
        // (Laravel sudah handle via custom messages di atas)

        // Hapus semua jadwal lama
        $user->jadwalMengajars()->delete();

        // Insert jadwal baru (jika ada)
        if ($request->has('jadwal') && is_array($request->jadwal)) {
            foreach ($request->jadwal as $j) {
                $kelas = \App\Models\Kelas::find($j['kelas_id']);
                $kelasStr = $kelas ? $kelas->tingkat . ' ' . $kelas->jurusan . ' ' . $kelas->rombel : '';
                
                JadwalMengajar::create([
                    'user_id' => $user->id,
                    'hari' => $j['hari'],
                    'tipe_blok' => $j['tipe_blok'] ?? 'Semua',
                    'mata_pelajaran' => $j['mata_pelajaran'],
                    'kelas' => $kelasStr,
                    'jam_ke' => $j['jam_ke'],
                    'jam_mulai' => $j['jam_mulai'],
                    'jam_selesai' => $j['jam_selesai'] ?? null,
                ]);
            }
        }

        // Tandai bahwa guru sudah diatur jadwalnya
        $user->guruProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['is_jadwal_set' => true]
        );

        return redirect()->route('admin.jadwal-mengajar.index')->with('success', "Jadwal mengajar {$user->name} berhasil disimpan.");
    }

    /**
     * Download Template CSV untuk Jadwal Mengajar.
     */
    public function template(Request $request)
    {
        $rows = [
            ['nip', 'hari', 'tipe_blok', 'mata_pelajaran', 'kelas', 'jam_ke', 'jam_mulai', 'jam_selesai'],
            ['198001012010011001', 'Senin', 'A', 'Mata Pelajaran 1', 'X JURUSAN 1', '1', '07:30', '08:15'],
            ['198001012010011001', 'Senin', 'B', 'Mata Pelajaran 2', 'X JURUSAN 1', '1', '07:30', '08:15'],
            ['198001012010011001', 'Selasa', 'Semua', 'Mata Pelajaran Umum', 'X JURUSAN 1', '2', '08:15', '09:00']
        ];

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_jadwal_mengajar.xlsx"',
        ]);
    }

    /**
     * Memproses upload file CSV / XLSX untuk Jadwal Mengajar.
     */
     public function import(Request $request)
     {
         $request->validate([
             'file_csv' => 'required|file|mimes:csv,txt,xlsx|max:2048',
         ], [
             'file_csv.required' => 'Pilih file terlebih dahulu.',
             'file_csv.mimes'    => 'File harus berupa format CSV atau XLSX.',
             'file_csv.max'      => 'Ukuran file maksimal 2MB.',
         ]);
 
         $file = $request->file('file_csv');
         $extension = strtolower($file->getClientOriginalExtension());
         
         // Simpan file sementara untuk diproses oleh Job
        $filename = 'temp_import_jadwal_' . time() . '_' . uniqid() . '.' . $extension;
        $file->storeAs('temp', $filename);

        // Buat Job Tracker
        $tracker = \App\Models\JobTracker::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'type' => 'import_jadwal',
            'status' => 'pending'
        ]);

        // Dispatch Job
        \App\Jobs\ImportJadwalJob::dispatch('temp/' . $filename, $tracker->id, $extension);

        return redirect()->back()->with('success', 'Data Jadwal sedang diproses di latar belakang. Silakan perhatikan notifikasi di pojok kanan bawah layar Anda.');
     }

    /**
     * Mengubah status Blok Jadwal Aktif (A <-> B).
     */
    public function toggleBlok(Request $request)
    {
        $request->validate([
            'blok' => 'required|in:A,B'
        ]);

        $setting = \App\Models\SchoolSetting::get();
        $setting->blok_jadwal_aktif = $request->blok;
        $setting->save();
        
        $namaBlok = "Blok {$setting->blok_jadwal_aktif}";
        return redirect()->back()->with('success', "Status jadwal aktif berhasil diubah menjadi {$namaBlok}.");
    }
}
