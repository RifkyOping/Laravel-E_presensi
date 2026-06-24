<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AbsensiGuru;
use App\Models\AbsensiMengajar;
use App\Models\MataPelajaran;
use App\Models\SchoolSetting;
use App\Models\AbsensiSiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ──────────────────────────────────────────
    //  DASHBOARD
    // ──────────────────────────────────────────

    public function dashboard()
    {
        $today = Carbon::today();

        $stats = [
            'total_siswa'    => User::where('role', 'siswa')->count(),
            'total_guru'     => User::where('role', 'guru')->count(),
            'guru_hadir'     => AbsensiGuru::whereDate('tanggal', $today)->whereNotNull('waktu_datang')->count(),
            'guru_mengajar'  => AbsensiMengajar::whereDate('tanggal', $today)->distinct('user_id')->count(),
            'total_mapel'    => MataPelajaran::count(),
            'mapel_aktif'    => MataPelajaran::where('aktif', true)->count(),
        ];

        // Guru yang sudah absen sekolah hari ini
        $guruHadir = AbsensiGuru::with('user')
            ->whereDate('tanggal', $today)
            ->orderByDesc('waktu_datang')
            ->take(5)
            ->get();

        // Aktivitas mengajar hari ini
        $aktivitasHariIni = AbsensiMengajar::with('user')
            ->whereDate('tanggal', $today)
            ->orderBy('jam_ke')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'guruHadir', 'aktivitasHariIni'));
    }

    // ──────────────────────────────────────────
    //  MANAJEMEN USER (CRUD)
    // ──────────────────────────────────────────

    public function users(Request $request)
    {
        $tab = $request->get('tab', 'semua');

        $query = User::query();

        if ($tab !== 'semua') {
            $query->where('role', $tab);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'tab'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:siswa,guru,admin,pengawas,kurikulum',
        ];

        if ($request->role === 'siswa') {
            $rules['nis']            = 'nullable|string|max:20|unique:siswa_profiles,nis';
            $rules['nisn']           = 'nullable|string|max:20|unique:siswa_profiles,nisn';
            $rules['kelas']          = 'nullable|string|max:50';
            $rules['jurusan']        = 'nullable|string|max:100';
            $rules['jenis_kelamin']  = 'nullable|in:L,P';
            $rules['tempat_lahir']   = 'nullable|string|max:100';
            $rules['tanggal_lahir']  = 'nullable|date';
            $rules['agama']          = 'nullable|string|max:50';
        }

        $request->validate($rules, [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required'      => 'Role wajib dipilih.',
            'nis.unique'         => 'NIS sudah terdaftar.',
            'nisn.unique'        => 'NISN sudah terdaftar.',
        ]);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ];

        $user = User::create($data);

        if ($request->role === 'siswa') {
            $user->siswaProfile()->create([
                'nis'           => $request->nis ?: null,
                'nisn'          => $request->nisn ?: null,
                'kelas'         => $request->kelas,
                'jurusan'       => $request->jurusan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama'         => $request->agama,
            ]);
        }

        return redirect()->route('admin.users')->with('success', "Akun {$request->name} berhasil dibuat.");
    }

    public function downloadTemplateImport()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // Header kolom
            fputcsv($file, ['name', 'email', 'role', 'password']);
            // Contoh data
            fputcsv($file, ['Siswa Contoh 1', 'siswa1@smkn1majene.sch.id', 'siswa', '12345678']);
            fputcsv($file, ['Guru Contoh 1', 'guru1@smkn1majene.sch.id', 'guru', '12345678']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file_csv.required' => 'Pilih file CSV terlebih dahulu.',
            'file_csv.mimes'    => 'File harus berupa format CSV.',
            'file_csv.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');

        // Deteksi pemisah (koma atau titik koma)
        $firstLine = fgets($handle);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 1000, $delimiter);
        if (!$header || count($header) < 3) {
            return back()->with('error', 'Format CSV tidak sesuai template.');
        }

        $berhasil = 0;
        $gagal = 0;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (count($row) < 3) continue;

            $name = trim($row[0]);
            $email = trim($row[1]);
            $role = strtolower(trim($row[2]));
            $password = isset($row[3]) && trim($row[3]) !== '' ? trim($row[3]) : '12345678';

            // Validasi role dan email
            if (!in_array($role, ['siswa', 'guru', 'admin', 'pengawas', 'kurikulum'])) {
                $gagal++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $gagal++;
                continue;
            }

            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'role'     => $role,
                'password' => Hash::make($password),
            ]);

            if ($role === 'siswa') {
                $user->siswaProfile()->create([]);
            }

            $berhasil++;
        }

        fclose($handle);

        $msg = "Import selesai! Berhasil: {$berhasil} akun.";
        if ($gagal > 0) $msg .= " Gagal/Dilewati: {$gagal} baris (email sudah ada atau role tidak valid).";

        return back()->with('success', $msg);
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:siswa,guru,admin,pengawas,kurikulum',
            'password' => 'nullable|min:6|confirmed',
        ];

        if ($request->role === 'siswa') {
            $profileId = $user->siswaProfile ? $user->siswaProfile->id : null;
            $rules['nis']           = 'nullable|string|max:20|unique:siswa_profiles,nis,' . $profileId;
            $rules['nisn']          = 'nullable|string|max:20|unique:siswa_profiles,nisn,' . $profileId;
            $rules['kelas']         = 'nullable|string|max:50';
            $rules['jurusan']       = 'nullable|string|max:100';
            $rules['jenis_kelamin'] = 'nullable|in:L,P';
            $rules['tempat_lahir']  = 'nullable|string|max:100';
            $rules['tanggal_lahir'] = 'nullable|date';
            $rules['agama']         = 'nullable|string|max:50';
        }

        $request->validate($rules, [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah digunakan.',
            'role.required'      => 'Role wajib dipilih.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nis.unique'         => 'NIS sudah digunakan.',
            'nisn.unique'        => 'NISN sudah digunakan.',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->role === 'siswa') {
            $user->siswaProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis'           => $request->nis ?: null,
                    'nisn'          => $request->nisn ?: null,
                    'kelas'         => $request->kelas,
                    'jurusan'       => $request->jurusan,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'agama'         => $request->agama,
                ]
            );
        } else {
            // Jika role diubah dari siswa ke role lain, hapus profilnya
            if ($user->siswaProfile) {
                $user->siswaProfile()->delete();
            }
        }

        return redirect()->route('admin.users')->with('success', "Data {$user->name} berhasil diperbarui.");
    }

    public function destroyUser(User $user)
    {
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('success', "Akun {$name} berhasil dihapus.");
    }

    // ──────────────────────────────────────────
    //  MONITORING ABSENSI GURU (SEKOLAH)
    // ──────────────────────────────────────────

    public function absensiGuru(Request $request)
    {
        $tanggal = $request->filled('tanggal') ? Carbon::parse($request->tanggal) : Carbon::today();

        // Semua guru
        $semuaGuru = User::where('role', 'guru')->orderBy('name')->get();

        // Record absensi pada tanggal tersebut
        $absensi = AbsensiGuru::with('user')
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('user_id');

        // Riwayat (filter berdasarkan tanggal)
        $query = AbsensiGuru::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('tanggal')
            ->orderBy('user_id');

        if ($request->filled('guru_id')) {
            $query->where('user_id', $request->guru_id);
        }

        $riwayat = $query->paginate(20)->withQueryString();

        return view('admin.absensi-guru', compact('semuaGuru', 'absensi', 'tanggal', 'riwayat'));
    }

    public function exportAbsensiGuru(Request $request)
    {
        $bulan = $request->filled('bulan') ? Carbon::parse($request->bulan) : Carbon::today();
        
        $riwayat = AbsensiGuru::with('user')
            ->whereYear('tanggal', $bulan->year)
            ->whereMonth('tanggal', $bulan->month)
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->get();

        $filename = "absensi_guru_" . $bulan->format('Y-m') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($riwayat) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Guru', 'Tanggal', 'Jam Datang', 'Jam Pulang', 'Status Kehadiran', 'Keterangan'], ';');
            
            $no = 1;
            foreach ($riwayat as $data) {
                fputcsv($file, [
                    $no++,
                    $data->user->name ?? '-',
                    $data->tanggal->format('Y-m-d'),
                    $data->waktu_datang ?? '-',
                    $data->waktu_pulang ?? '-',
                    $data->status,
                    $data->keterangan ?? '-'
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ──────────────────────────────────────────
    //  MONITORING AKTIVITAS MENGAJAR GURU
    // ──────────────────────────────────────────

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

        return view('admin.aktivitas-guru', compact('semuaGuru', 'aktivitas', 'tanggal'));
    }

    // ──────────────────────────────────────────
    //  PENGATURAN GEOFENCE SEKOLAH
    // ──────────────────────────────────────────

    public function geofenceSetting()
    {
        $setting = SchoolSetting::get();
        return view('admin.geofence', compact('setting'));
    }

    public function updateGeofence(Request $request)
    {
        $request->validate([
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:50|max:5000',
            'nama_sekolah' => 'required|string|max:255',
        ], [
            'latitude.required'     => 'Latitude wajib diisi.',
            'latitude.between'      => 'Latitude harus antara -90 dan 90.',
            'longitude.required'    => 'Longitude wajib diisi.',
            'longitude.between'     => 'Longitude harus antara -180 dan 180.',
            'radius_meter.required' => 'Radius wajib diisi.',
            'radius_meter.min'      => 'Radius minimal 50 meter.',
            'radius_meter.max'      => 'Radius maksimal 5000 meter.',
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
        ]);

        $setting = SchoolSetting::get();
        $setting->update([
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'radius_meter' => $request->radius_meter,
            'nama_sekolah' => $request->nama_sekolah,
        ]);

        return redirect()->route('admin.geofence')
            ->with('success', 'Pengaturan zona absensi berhasil disimpan.');
    }

    // ──────────────────────────────────────────
    //  MONITORING ABSENSI SISWA
    // ──────────────────────────────────────────

    public function absensiSiswa(Request $request)
    {
        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)
            : Carbon::today();

        // Semua siswa (dengan opsional filter nama)
        $siswaQuery = User::where('role', 'siswa')->orderBy('name');
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

        // Riwayat semua absensi siswa (paginated) pada tanggal tersebut
        $riwayatQuery = AbsensiSiswa::with('user')
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('tanggal')
            ->orderBy('user_id');
        if ($request->filled('search')) {
            $riwayatQuery->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        $riwayat = $riwayatQuery->paginate(20)->withQueryString();

        $stats = [
            'total'  => User::where('role', 'siswa')->count(),
            'hadir'  => $absensi->count(),
            'belum'  => $semuaSiswa->count() - $absensi->count(),
        ];

        return view('admin.absensi-siswa', compact(
            'semuaSiswa', 'absensi', 'siswaHadir', 'siswaBelum',
            'riwayat', 'stats', 'tanggal'
        ));
    }

    public function exportAbsensiSiswa(Request $request)
    {
        $bulan = $request->filled('bulan') ? Carbon::parse($request->bulan) : Carbon::today();
        
        $riwayat = AbsensiSiswa::with('user')
            ->whereYear('tanggal', $bulan->year)
            ->whereMonth('tanggal', $bulan->month)
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->get();

        $filename = "absensi_siswa_" . $bulan->format('Y-m') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($riwayat) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Siswa', 'Tanggal', 'Waktu Datang', 'Waktu Pulang', 'Status Kehadiran', 'Keterangan'], ';');
            
            $no = 1;
            foreach ($riwayat as $data) {
                fputcsv($file, [
                    $no++,
                    $data->user->name ?? '-',
                    $data->tanggal->format('Y-m-d'),
                    $data->waktu_datang ?? '-',
                    $data->waktu_pulang ?? '-',
                    $data->status,
                    $data->keterangan ?? '-'
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
