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
         
         $rows = [];
 
         if ($extension === 'xlsx') {
             if ($xlsx = SimpleXLSX::parse($file->getRealPath())) {
                 $rows = $xlsx->rows();
             } else {
                 return back()->with('error', 'Gagal membaca file XLSX: ' . SimpleXLSX::parseError());
             }
         } else {
             $handle = fopen($file->getRealPath(), 'r');
             $firstLine = fgets($handle);
             $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
             rewind($handle);
 
             while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                 $rows[] = $row;
             }
             fclose($handle);
         }
 
         if (count($rows) < 2) {
             return redirect()->back()->with('error', 'File kosong atau format tidak sesuai.');
         }
 
         $header = array_shift($rows); // Hapus baris header
         
         // Membersihkan header jika ada karakter tidak terlihat (BOM, spasi, dll)
         $header = array_map(function($h) {
             return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h)));
         }, $header);
         
         // Mapping kolom standar
         // Validasi header minimum
         $identifierColumn = null;
         if (in_array('nip', $header)) {
             $identifierColumn = 'nip';
         } elseif (in_array('nama', $header)) {
             $identifierColumn = 'nama';
         } elseif (in_array('email_guru', $header)) {
             $identifierColumn = 'email_guru';
         }
 
         if (!$identifierColumn || !in_array('hari', $header) || !in_array('jam_ke', $header)) {
             return redirect()->back()->with('error', 'Format header file tidak sesuai template. Pastikan ada kolom nip, hari, dan jam_ke.');
         }
 
         $berhasil = 0;
         $usersUpdated = [];
         $gagalRows = [];

         foreach ($rows as $index => $row) {
             $rowNum = $index + 2; // Baris riil di file Excel / CSV (karena baris 1 adalah header)

             // Abaikan jika baris kosong
             if (empty($row) || count(array_filter($row, fn($val) => trim((string)$val) !== '')) === 0) {
                 continue;
             }
             
             // Samakan jumlah kolom dengan header
             if (count($row) < count($header)) {
                 $row = array_pad($row, count($header), '');
             } elseif (count($row) > count($header)) {
                 $row = array_slice($row, 0, count($header));
             }
             
             $rowAssoc = array_combine($header, $row);

             $namaOrEmail = trim((string)($rowAssoc[$identifierColumn] ?? ''));
             $hari = trim((string)($rowAssoc['hari'] ?? ''));
             $mapel = trim((string)($rowAssoc['mata_pelajaran'] ?? ''));
             $kelasStr = trim((string)($rowAssoc['kelas'] ?? ''));
             $jamKe = trim((string)($rowAssoc['jam_ke'] ?? ''));
             $jamMulai = trim((string)($rowAssoc['jam_mulai'] ?? ''));
             $jamSelesai = isset($rowAssoc['jam_selesai']) && trim((string)$rowAssoc['jam_selesai']) !== '' ? trim((string)$rowAssoc['jam_selesai']) : null;
             $tipeBlok = isset($rowAssoc['tipe_blok']) && trim((string)$rowAssoc['tipe_blok']) !== '' ? ucfirst(strtolower(trim((string)$rowAssoc['tipe_blok']))) : 'Semua';

             if ($namaOrEmail === '') {
                 $gagalRows[] = [
                     'baris' => $rowNum,
                     'nama' => '(NIP/Nama/Email Kosong)',
                     'detail' => "Mapel: " . ($mapel ?: '-') . " | Kelas: " . ($kelasStr ?: '-') . " | Hari: " . ($hari ?: '-'),
                     'alasan' => 'Kolom identitas guru (NIP/Nama/Email) tidak diisi.'
                 ];
                 continue;
             }
             
             // Cari user (guru) berdasarkan NIP, nama atau email
             if ($identifierColumn === 'nip') {
                 $guru = User::where('nomor_induk', $namaOrEmail)->where('role', 'guru')->first();
             } elseif ($identifierColumn === 'nama') {
                 $guru = User::where('name', $namaOrEmail)->where('role', 'guru')->first();
                 if (!$guru) {
                     $guru = User::whereRaw('LOWER(name) = ?', [strtolower($namaOrEmail)])->where('role', 'guru')->first();
                 }
             } else {
                 $guru = User::where('email', $namaOrEmail)->where('role', 'guru')->first();
             }
             
             if (!$guru) {
                 $gagalRows[] = [
                     'baris' => $rowNum,
                     'nama' => $namaOrEmail,
                     'detail' => "Mapel: " . ($mapel ?: '-') . " | Kelas: " . ($kelasStr ?: '-') . " | Hari: " . ($hari ?: '-') . " (Mapel ke-" . ($jamKe ?: '-') . ")",
                     'alasan' => "Akun guru \"{$namaOrEmail}\" tidak ditemukan di sistem."
                 ];
                 continue;
             }

             if ($hari === '' || $jamKe === '') {
                 $gagalRows[] = [
                     'baris' => $rowNum,
                     'nama' => $guru->name,
                     'detail' => "Mapel: " . ($mapel ?: '-') . " | Kelas: " . ($kelasStr ?: '-'),
                     'alasan' => 'Kolom Hari atau Mapel ke- tidak boleh kosong.'
                 ];
                 continue;
             }

             try {
                 // Hapus jadwal sebelumnya hanya jika user ini baru pertama kali diproses di file ini
                 if (!in_array($guru->id, $usersUpdated)) {
                     $guru->jadwalMengajars()->delete();
                     $usersUpdated[] = $guru->id;
                 }
                 
                 JadwalMengajar::create([
                     'user_id' => $guru->id,
                     'hari' => ucfirst(strtolower($hari)), // Senin, Selasa...
                     'tipe_blok' => in_array($tipeBlok, ['A', 'B', 'Semua']) ? $tipeBlok : 'Semua',
                     'mata_pelajaran' => $mapel,
                     'kelas' => $kelasStr,
                     'jam_ke' => (int)$jamKe,
                     'jam_mulai' => $jamMulai ?: '07:30',
                     'jam_selesai' => $jamSelesai,
                 ]);
                 $berhasil++;
             } catch (\Exception $e) {
                 $gagalRows[] = [
                     'baris' => $rowNum,
                     'nama' => $guru->name,
                     'detail' => "Mapel: " . ($mapel ?: '-') . " | Kelas: " . ($kelasStr ?: '-'),
                     'alasan' => 'Gagal menyimpan: ' . $e->getMessage()
                 ];
             }
         }
         
         // Update status is_jadwal_set untuk guru-guru yang berhasil diimport
         foreach ($usersUpdated as $userId) {
             \App\Models\GuruProfile::updateOrCreate(
                 ['user_id' => $userId],
                 ['is_jadwal_set' => true]
             );
         }

         $totalGagal = count($gagalRows);

         if ($totalGagal > 0) {
             $msg = "Import selesai. Berhasil: {$berhasil} jadwal. Terdapat {$totalGagal} baris yang gagal diimport.";
             return redirect()->back()
                 ->with($berhasil > 0 ? 'warning' : 'error', $msg)
                 ->with('import_errors', $gagalRows);
         }

         return redirect()->back()->with('success', "Import berhasil! Sebanyak {$berhasil} jadwal mengajar berhasil dimasukkan.");
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
