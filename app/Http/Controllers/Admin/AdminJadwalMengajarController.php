<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JadwalMengajar;
use Illuminate\Support\Facades\Response;

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
        $gurus = $query->withCount('jadwalMengajars')->paginate(15);
        
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
            'jadwal.*.jam_ke.required'         => 'Jam ke- wajib diisi.',
            'jadwal.*.jam_ke.integer'          => 'Jam ke- harus berupa angka.',
            'jadwal.*.jam_ke.min'              => 'Jam ke- minimal 1.',
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
        $delimiter = $request->query('delimiter', ',');
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=template_jadwal_mengajar.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['nama', 'hari', 'tipe_blok', 'mata_pelajaran', 'kelas', 'jam_ke', 'jam_mulai', 'jam_selesai'];

        $callback = function() use ($columns, $delimiter) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, $delimiter);
            // Contoh isi
            fputcsv($file, ['NAMA GURU', 'Senin', 'A', 'Mata Pelajaran 1', 'X JURUSAN 1', '1', '07:30', '08:15'], $delimiter);
            fputcsv($file, ['NAMA GURU', 'Senin', 'B', 'Mata Pelajaran 2', 'X JURUSAN 1', '1', '07:30', '08:15'], $delimiter);
            fputcsv($file, ['NAMA GURU', 'Selasa', 'Semua', 'Mata Pelajaran Umum', 'X JURUSAN 1', '2', '08:15', '09:00'], $delimiter);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Memproses upload file CSV untuk Jadwal Mengajar.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();
        
        $handle = fopen($path, 'r');
        $firstLine = fgets($handle);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        fclose($handle);
        
        $data = array_map(function($v) use ($delimiter) { return str_getcsv($v, $delimiter); }, file($path));
        $header = array_shift($data); // Hapus baris header
        
        // Membersihkan header jika ada karakter tidak terlihat (BOM, dll)
        $header = array_map('trim', $header);
        // Pastikan huruf kecil semua
        $header = array_map('strtolower', $header);
        
        // Mapping kolom standar
        // Validasi header minimum
        $identifierColumn = null;
        if (in_array('nama', $header)) {
            $identifierColumn = 'nama';
        } elseif (in_array('email_guru', $header)) {
            $identifierColumn = 'email_guru';
        }

        if (!$identifierColumn || !in_array('hari', $header) || !in_array('jam_ke', $header)) {
            return redirect()->back()->with('error', 'Format header CSV tidak sesuai template. Pastikan ada kolom nama, hari, dan jam_ke.');
        }

        $berhasil = 0;
        $gagal = 0;
        $usersUpdated = [];

        foreach ($data as $row) {
            // Abaikan jika baris kosong
            if (empty($row) || count($row) < count($header)) {
                continue;
            }
            
            $rowAssoc = array_combine($header, $row);
            
            // Cari user (guru) berdasarkan nama atau email
            if ($identifierColumn === 'nama') {
                $guru = User::where('name', trim($rowAssoc['nama']))->where('role', 'guru')->first();
            } else {
                $guru = User::where('email', trim($rowAssoc['email_guru']))->where('role', 'guru')->first();
            }
            
            if ($guru) {
                // Hapus jadwal sebelumnya hanya jika user ini baru pertama kali diproses di file ini
                if (!in_array($guru->id, $usersUpdated)) {
                    $guru->jadwalMengajars()->delete();
                    $usersUpdated[] = $guru->id;
                }
                
                $kelasStr = trim($rowAssoc['kelas'] ?? '');
                
                JadwalMengajar::create([
                    'user_id' => $guru->id,
                    'hari' => ucfirst($rowAssoc['hari']), // Senin, Selasa...
                    'tipe_blok' => isset($rowAssoc['tipe_blok']) ? ucfirst(strtolower($rowAssoc['tipe_blok'])) : 'Semua',
                    'mata_pelajaran' => $rowAssoc['mata_pelajaran'],
                    'kelas' => $kelasStr,
                    'jam_ke' => $rowAssoc['jam_ke'],
                    'jam_mulai' => $rowAssoc['jam_mulai'],
                    'jam_selesai' => $rowAssoc['jam_selesai'] ?? null,
                ]);
                $berhasil++;
            } else {
                $gagal++;
            }
        }
        
        // Update status is_jadwal_set untuk guru-guru yang berhasil diimport
        foreach ($usersUpdated as $userId) {
            \App\Models\GuruProfile::updateOrCreate(
                ['user_id' => $userId],
                ['is_jadwal_set' => true]
            );
        }

        return redirect()->back()->with('success', "Import selesai. Berhasil memasukkan $berhasil jadwal. Gagal: $gagal baris (karena nama/email guru tidak ditemukan di sistem).");
    }

    /**
     * Mengubah status Blok Jadwal Aktif (A <-> B).
     */
    public function toggleBlok(Request $request)
    {
        $setting = \App\Models\SchoolSetting::get();
        
        if ($setting->blok_jadwal_aktif === 'A') {
            $setting->blok_jadwal_aktif = 'B';
        } else {
            $setting->blok_jadwal_aktif = 'A';
        }
        
        $setting->save();
        
        return redirect()->back()->with('success', "Blok jadwal aktif berhasil diubah menjadi Blok {$setting->blok_jadwal_aktif}.");
    }
}
