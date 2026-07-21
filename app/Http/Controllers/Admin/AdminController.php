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
            'total_siswa'    => User::where('role', 'murid')->count(),
            'total_guru'     => User::where('role', 'guru')->count(),
            'guru_hadir'     => AbsensiGuru::whereDate('tanggal', $today)->whereNotNull('waktu_datang')->count(),
            'guru_mengajar'  => AbsensiMengajar::whereDate('tanggal', $today)->distinct('user_id')->count(),
            'total_mapel'    => MataPelajaran::count(),
            'mapel_aktif'    => MataPelajaran::where('aktif', true)->count(),
            'siswa_hadir'    => AbsensiSiswa::whereDate('tanggal', $today)->where('status', 'hadir')->count(),
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
        $kelasList = \App\Models\Kelas::where('status', true)
            ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();

        $tingkats  = $kelasList->pluck('tingkat')->unique()->values();
        $jurusans  = $kelasList->pluck('jurusan')->unique()->values();
        $rombels   = $kelasList->pluck('rombel')->unique()->values();

        return view('admin.users.create', compact('kelasList', 'tingkats', 'jurusans', 'rombels'));
    }

    public function storeUser(Request $request)
    {
        $rules = [
            'name'        => 'required|string|max:255',
            'nomor_induk' => 'required|string|max:255|unique:users,nomor_induk',
            'email'       => 'nullable|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
            'role'        => 'required|in:murid,guru,admin,pengawas,kurikulum',
        ];

        if ($request->role === 'murid') {
            $rules['kelas_id']       = 'nullable|exists:kelas,id';
            $rules['jenis_kelamin']  = 'nullable|in:L,P';
            $rules['tempat_lahir']   = 'nullable|string|max:100';
            $rules['tanggal_lahir']  = 'nullable|date';
            $rules['agama']          = 'nullable|string|max:50';
        }

        $request->validate($rules, [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'nomor_induk.required' => 'Nomor induk (NISN/NIP) wajib diisi.',
            'nomor_induk.unique'   => 'Nomor induk ini sudah terdaftar.',
            'email.required'       => 'Email wajib diisi.',
            'email.unique'         => 'Email sudah terdaftar.',
            'password.required'    => 'Password wajib diisi.',
            'password.min'         => 'Password minimal 6 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
            'role.required'        => 'Role wajib dipilih.',
            'role.in'              => 'Role yang dipilih tidak valid.',
        ]);

        $data = [
            'name'        => $request->name,
            'nomor_induk' => $request->nomor_induk,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
        ];

        $user = User::create($data);

        if ($request->role === 'guru') {
            $user->guruProfile()->create([
                'is_piket_sholat'   => $request->has('is_piket_sholat'),
                'is_piket_mengajar' => $request->has('is_piket_mengajar'),
                'is_piket_rpp'      => $request->has('is_piket_rpp'),
                'is_guru_bahasa'    => $request->has('is_guru_bahasa'),
            ]);
        }

        if ($request->role === 'murid') {
            $kelas = $request->kelas_id ? \App\Models\Kelas::find($request->kelas_id) : null;
            $user->siswaProfile()->create([
                'kelas'         => $kelas ? $kelas->tingkat : null,
                'jurusan'       => $kelas ? $kelas->jurusan : null,
                'rombel'        => $kelas ? $kelas->rombel : null,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama'         => $request->agama,
            ]);
        }

        return redirect()->route('admin.users')->with('success', "Akun {$request->name} berhasil dibuat.");
    }

    public function downloadTemplateImport(Request $request)
    {
        $delimiter = $request->query('delimiter', ',');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
        ];

        $callback = function () use ($delimiter) {
            $file = fopen('php://output', 'w');
            // Header kolom
            fputcsv($file, ['nama', 'nomor_induk (NISN/NIP)', 'email', 'role', 'password', 'kelas', 'agama'], $delimiter);
            // Contoh data
            fputcsv($file, ['Murid Contoh 1', '0012345678', 'murid1@smkn1majene.sch.id', 'murid', '12345678', 'X RPL 1', 'Islam'], $delimiter);
            fputcsv($file, ['Guru Contoh 1', '198001012010011001', '', 'guru', '12345678', '', ''], $delimiter);
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
        if (!$header || count($header) < 4) {
            return back()->with('error', 'Format CSV tidak sesuai template.');
        }

        $berhasil = 0;
        $gagal = 0;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (count($row) < 4) continue;

            $name = trim($row[0]);
            $nomor_induk = trim($row[1]);
            $email = trim($row[2]) === '' ? null : trim($row[2]);
            $role = strtolower(trim($row[3]));
            $password = isset($row[4]) && trim($row[4]) !== '' ? trim($row[4]) : '12345678';
            $kelasStr = isset($row[5]) ? trim($row[5]) : null;
            $agama = isset($row[6]) && trim($row[6]) !== '' ? trim($row[6]) : null;

            if ($nomor_induk === '') {
                $gagal++;
                continue;
            }

            // Validasi role dan email
            if (!in_array($role, ['murid', 'guru', 'admin', 'pengawas', 'kurikulum'])) {
                $gagal++;
                continue;
            }

            if (User::where('nomor_induk', $nomor_induk)->exists()) {
                $gagal++;
                continue;
            }

            if ($email && User::where('email', $email)->exists()) {
                $gagal++;
                continue;
            }

            $user = User::create([
                'name'        => $name,
                'nomor_induk' => $nomor_induk,
                'email'       => $email,
                'role'        => $role,
                'password'    => Hash::make($password),
            ]);

            if ($role === 'murid') {
                $profileData = [];
                if ($agama) {
                    $profileData['agama'] = $agama;
                }
                if ($kelasStr) {
                    $parts = explode(' ', $kelasStr);
                    $profileData['kelas'] = $parts[0] ?? null;
                    $profileData['rombel'] = end($parts) ?: null;
                    if (count($parts) > 2) {
                        $profileData['jurusan'] = implode(' ', array_slice($parts, 1, -1));
                    }
                }
                $user->siswaProfile()->create($profileData);
            }

            $berhasil++;
        }

        fclose($handle);

        $msg = "Import selesai! Berhasil: {$berhasil} akun.";
        if ($gagal > 0) $msg .= " Gagal/Dilewati: {$gagal} baris (nomor_induk sudah ada, email sudah ada, role tidak valid, atau data tidak lengkap).";

        return back()->with('success', $msg);
    }

    public function editUser(User $user)
    {
        $kelasList = \App\Models\Kelas::where('status', true)
            ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();

        $tingkats  = $kelasList->pluck('tingkat')->unique()->values();
        $jurusans  = $kelasList->pluck('jurusan')->unique()->values();
        $rombels   = $kelasList->pluck('rombel')->unique()->values();

        return view('admin.users.edit', compact('user', 'kelasList', 'tingkats', 'jurusans', 'rombels'));
    }

    public function updateUser(Request $request, User $user)
    {
        $rules = [
            'name'        => 'required|string|max:255',
            'nomor_induk' => 'required|string|max:255|unique:users,nomor_induk,' . $user->id,
            'email'       => 'nullable|email|unique:users,email,' . $user->id,
            'role'        => 'required|in:murid,guru,admin,pengawas,kurikulum',
            'password'    => 'nullable|min:6|confirmed',
        ];

        if ($request->role === 'murid') {
            $profileId = $user->siswaProfile ? $user->siswaProfile->id : null;
            $rules['kelas_id']      = 'nullable|exists:kelas,id';
            $rules['jenis_kelamin'] = 'nullable|in:L,P';
            $rules['tempat_lahir']  = 'nullable|string|max:100';
            $rules['tanggal_lahir'] = 'nullable|date';
            $rules['agama']         = 'nullable|string|max:50';
        }

        $request->validate($rules, [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'nomor_induk.required' => 'Nomor induk (NISN/NIP) wajib diisi.',
            'nomor_induk.unique'   => 'Nomor induk ini sudah digunakan.',
            'email.required'       => 'Email wajib diisi.',
            'email.unique'         => 'Email sudah digunakan.',
            'role.required'        => 'Role wajib dipilih.',
            'role.in'              => 'Role yang dipilih tidak valid.',
            'password.min'         => 'Password minimal 6 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
        ]);

        $data = [
            'name'        => $request->name,
            'nomor_induk' => $request->nomor_induk,
            'email'       => $request->email,
            'role'        => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->role === 'guru') {
            $user->guruProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'is_piket_sholat'   => $request->has('is_piket_sholat'),
                    'is_piket_mengajar' => $request->has('is_piket_mengajar'),
                    'is_piket_rpp'      => $request->has('is_piket_rpp'),
                    'is_guru_bahasa'    => $request->has('is_guru_bahasa'),
                ]
            );
        }

        if ($request->role === 'murid') {
            $kelas = $request->kelas_id ? \App\Models\Kelas::find($request->kelas_id) : null;
            $user->siswaProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'kelas'         => $kelas ? $kelas->tingkat : null,
                    'jurusan'       => $kelas ? $kelas->jurusan : null,
                    'rombel'        => $kelas ? $kelas->rombel : null,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'agama'         => $request->agama,
                ]
            );
        } else {
            // Jika role diubah dari murid ke role lain, hapus profilnya
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

    public function resetDevice(User $user)
    {
        $name = $user->name;
        $user->update(['device_id' => null]);

        return redirect()->route('admin.users')->with('success', "Perangkat untuk akun {$name} berhasil direset.");
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
        $tanggalMulai = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai) : Carbon::today()->startOfMonth();
        $tanggalAkhir = $request->filled('tanggal_akhir') ? Carbon::parse($request->tanggal_akhir) : Carbon::today();
        $delimiter = $request->input('delimiter', ';');
        
        $riwayat = AbsensiGuru::with('user')
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->get();

        $filename = "absensi_guru_" . $tanggalMulai->format('Y-m-d') . "_sd_" . $tanggalAkhir->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($riwayat, $delimiter) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Guru', 'Tanggal', 'Jam Datang', 'Jam Pulang', 'Status Kehadiran', 'Kategori', 'Keterangan'], $delimiter);
            
            $no = 1;
            foreach ($riwayat as $data) {
                fputcsv($file, [
                    $no++,
                    $data->user->name ?? '-',
                    $data->tanggal->format('Y-m-d'),
                    $data->waktu_datang ?? '-',
                    $data->waktu_pulang ?? '-',
                    $data->status,
                    $data->kategori ?? '-',
                    $data->keterangan ?? '-'
                ], $delimiter);
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

        // Cari JadwalMengajar yang cocok per record aktivitas (untuk tombol detail absen kelas)
        // Match berdasarkan user_id + kelas + jam_ke
        $jadwalIds = [];
        foreach ($aktivitas as $item) {
            $jadwal = \App\Models\JadwalMengajar::where('user_id', $item->user_id)
                ->where('kelas', $item->kelas)
                ->where('jam_ke', $item->jam_ke)
                ->whereHas('absensiKelas', function ($q) use ($item) {
                    $q->whereDate('tanggal', $item->tanggal);
                })
                ->first();
            $jadwalIds[$item->id] = $jadwal ? $jadwal->id : null;
        }

        return view('admin.aktivitas-guru', compact('semuaGuru', 'aktivitas', 'tanggal', 'jadwalIds'));
    }

    public function exportAktivitasGuru(Request $request)
    {
        $tanggalMulai = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai) : Carbon::today()->startOfMonth();
        $tanggalAkhir = $request->filled('tanggal_akhir') ? Carbon::parse($request->tanggal_akhir) : Carbon::today();
        $delimiter = $request->input('delimiter', ';');
        
        $riwayat = \App\Models\AbsensiMengajar::with(['user', 'verifier'])
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->orderBy('jam_ke')
            ->get();

        $filename = "aktivitas_mengajar_" . $tanggalMulai->format('Y-m-d') . "_sd_" . $tanggalAkhir->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($riwayat, $delimiter) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Guru', 'Tanggal', 'Mata Pelajaran', 'Kelas', 'Jam Ke', 'Jam Mulai', 'Jam Selesai', 'Masuk', 'Keluar', 'Kategori', 'Status Verifikasi', 'Diverifikasi Oleh'], $delimiter);
            
            $no = 1;
            foreach ($riwayat as $data) {
                fputcsv($file, [
                    $no++,
                    $data->user->name ?? '-',
                    Carbon::parse($data->tanggal)->format('Y-m-d'),
                    $data->mata_pelajaran,
                    $data->kelas,
                    $data->jam_ke,
                    $data->jam_mulai,
                    $data->jam_selesai,
                    $data->waktu_absen_masuk ?? '-',
                    $data->waktu_absen_keluar ?? '-',
                    $data->kategori ?? '-',
                    $data->status_verifikasi,
                    $data->verifier->name ?? '-'
                ], $delimiter);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ──────────────────────────────────────────
    //  REKAP ABSENSI KELAS (diinput oleh Guru)
    // ──────────────────────────────────────────

    public function rekapAbsensiKelas(Request $request)
    {
        $semuaGuru = User::where('role', 'guru')->orderBy('name')->get();
        $tanggal   = $request->filled('tanggal') ? Carbon::parse($request->tanggal) : Carbon::today();

        // Ambil jadwal yang sudah punya data absensi kelas pada tanggal ini
        $query = \App\Models\JadwalMengajar::with(['user'])
            ->whereHas('absensiKelas', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            })
            ->withCount(['absensiKelas as total_hadir' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal)->where('status', 'hadir');
            }])
            ->withCount(['absensiKelas as total_alpa' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal)->where('status', 'alpa');
            }])
            ->withCount(['absensiKelas as total_sakit' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal)->where('status', 'sakit');
            }])
            ->withCount(['absensiKelas as total_izin' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal)->where('status', 'izin');
            }])
            ->withCount(['absensiKelas as total_siswa' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            }]);

        if ($request->filled('guru_id')) {
            $query->where('user_id', $request->guru_id);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', 'like', '%' . $request->kelas . '%');
        }

        $jadwals = $query->orderBy('jam_ke')->paginate(20)->withQueryString();

        return view('admin.rekap-absensi-kelas', compact('semuaGuru', 'jadwals', 'tanggal'));
    }

    public function rekapAbsensiKelasDetail(\App\Models\JadwalMengajar $jadwal, Request $request)
    {
        $tanggal = $request->filled('tanggal') ? Carbon::parse($request->tanggal) : Carbon::today();

        $absensi = \App\Models\AbsensiKelasSiswa::with('siswa.siswaProfile')
            ->where('jadwal_mengajar_id', $jadwal->id)
            ->whereDate('tanggal', $tanggal)
            ->orderBy('status')
            ->get();

        $jadwal->load('user');

        return view('admin.rekap-absensi-kelas-detail', compact('jadwal', 'absensi', 'tanggal'));
    }

    // ──────────────────────────────────────────
    //  PENGATURAN GEOFENCE SEKOLAH
    // ──────────────────────────────────────────

    public function geofenceSetting()
    {
        $setting = SchoolSetting::get();
        $jadwalAbsensi = \App\Models\JadwalAbsensi::all();
        return view('admin.geofence', compact('setting', 'jadwalAbsensi'));
    }

    public function updateGeofence(Request $request)
    {
        $request->validate([
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:1|max:500',
            'nama_sekolah' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:100',
            'status_absen' => 'required|in:auto,buka,tutup',
            'jadwal'       => 'required|array',
            'jadwal.*.absen_datang_buka'  => 'required|date_format:H:i',
            'jadwal.*.absen_datang_tutup' => 'required|date_format:H:i',
            'jadwal.*.absen_pulang_buka'  => 'required|date_format:H:i',
            'jadwal.*.absen_pulang_tutup' => 'required|date_format:H:i',
            'jadwal.*.batas_waktu_terlambat' => 'required|date_format:H:i',
            'jadwal.*.batas_pulang_cepat' => 'required|date_format:H:i',
        ], [
            'latitude.required'     => 'Latitude wajib diisi.',
            'latitude.between'      => 'Latitude harus antara -90 dan 90.',
            'longitude.required'    => 'Longitude wajib diisi.',
            'longitude.between'     => 'Longitude harus antara -180 dan 180.',
            'radius_meter.required' => 'Radius wajib diisi.',
            'radius_meter.min'      => 'Radius minimal 1 meter.',
            'radius_meter.max'      => 'Radius maksimal 500 meter.',
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'status_absen.required' => 'Status absen wajib dipilih.',
            'status_absen.in'       => 'Status absen tidak valid.',
            'jadwal.*.absen_datang_buka.required' => 'Jam buka absen datang wajib diisi.',
            'jadwal.*.absen_datang_tutup.required' => 'Jam tutup absen datang wajib diisi.',
            'jadwal.*.absen_pulang_buka.required' => 'Jam buka absen pulang wajib diisi.',
            'jadwal.*.absen_pulang_tutup.required' => 'Jam tutup absen pulang wajib diisi.',
        ]);

        $setting = SchoolSetting::first();
        $setting->update([
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'radius_meter' => $request->radius_meter,
            'nama_sekolah' => $request->nama_sekolah,
            'tahun_ajaran' => $request->tahun_ajaran,
            'status_absen' => $request->status_absen,
        ]);

        foreach ($request->jadwal as $hari => $data) {
            \App\Models\JadwalAbsensi::where('hari', $hari)->update([
                'absen_datang_buka'     => $data['absen_datang_buka'],
                'absen_datang_tutup'    => $data['absen_datang_tutup'],
                'batas_waktu_terlambat' => $data['batas_waktu_terlambat'],
                'absen_pulang_buka'     => $data['absen_pulang_buka'],
                'absen_pulang_tutup'    => $data['absen_pulang_tutup'],
                'batas_pulang_cepat'    => $data['batas_pulang_cepat'],
                'is_libur'              => isset($data['is_libur']) ? true : false,
            ]);
        }

        if ($request->status_absen === 'tutup') {
            \Illuminate\Support\Facades\Artisan::call('presensi:cek-alpha');
            \Illuminate\Support\Facades\Artisan::call('presensi:cek-lupa-pulang');
        }

        return redirect()->route('admin.geofence')
            ->with('success', 'Pengaturan absensi berhasil disimpan.');
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
            'total'  => User::where('role', 'murid')->count(),
            'hadir'  => $absensi->where('status', 'hadir')->count(),
            'izin'   => $absensi->where('status', 'izin')->count(),
            'sakit'  => $absensi->where('status', 'sakit')->count(),
            'belum'  => $semuaSiswa->count() - $absensi->count(),
        ];

        return view('admin.absensi-siswa', compact(
            'semuaSiswa', 'absensi', 'siswaHadir', 'siswaBelum',
            'riwayat', 'stats', 'tanggal'
        ));
    }

    public function exportAbsensiSiswa(Request $request)
    {
        $tanggalMulai = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai) : Carbon::today()->startOfMonth();
        $tanggalAkhir = $request->filled('tanggal_akhir') ? Carbon::parse($request->tanggal_akhir) : Carbon::today();
        $delimiter = $request->input('delimiter', ';');
        
        $riwayat = AbsensiSiswa::with('user')
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->get();

        $filename = "absensi_murid_" . $tanggalMulai->format('Y-m-d') . "_sd_" . $tanggalAkhir->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($riwayat, $delimiter) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Siswa', 'Tanggal', 'Waktu Datang', 'Waktu Pulang', 'Status Kehadiran', 'Keterangan'], $delimiter);
            
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
                ], $delimiter);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ──────────────────────────────────────────
    //  PERSETUJUAN IZIN / SAKIT (ADMIN)
    // ──────────────────────────────────────────

    public function persetujuanAbsensi()
    {
        $pengajuanSiswa = AbsensiSiswa::with('user')
            ->where('status_pengajuan', 'pending')
            ->orderByDesc('tanggal')
            ->get();
            
        $pengajuanGuru = AbsensiGuru::with('user')
            ->where('status_pengajuan', 'pending')
            ->orderByDesc('tanggal')
            ->get();
            
        $riwayatSiswa = AbsensiSiswa::with('user')
            ->whereNotNull('status_pengajuan')
            ->where('status_pengajuan', '!=', 'pending')
            ->orderByDesc('updated_at')
            ->take(30)
            ->get();
            
        $riwayatGuru = AbsensiGuru::with('user')
            ->whereNotNull('status_pengajuan')
            ->where('status_pengajuan', '!=', 'pending')
            ->orderByDesc('updated_at')
            ->take(30)
            ->get();
            
        return view('admin.persetujuan-absensi', compact('pengajuanSiswa', 'pengajuanGuru', 'riwayatSiswa', 'riwayatGuru'));
    }

    public function approvePengajuan($type, $id)
    {
        if ($type === 'murid') {
            $model = AbsensiSiswa::findOrFail($id);
            $model->update([
                'status_pengajuan' => 'approved',
                'is_notified' => false,
            ]);
        } else {
            // Guru
            $model = AbsensiGuru::findOrFail($id);
            $model->update([
                'status_pengajuan' => 'approved',
                'is_notified' => false,
            ]);

            // Jika status adalah cuti atau tugas, otomatis isi presensi
            // untuk setiap tanggal dari tanggal mulai s/d tanggal selesai
            if (in_array($model->status, ['cuti', 'tugas']) && $model->tanggal_selesai) {
                $start = \Carbon\Carbon::parse($model->tanggal);
                $end   = \Carbon\Carbon::parse($model->tanggal_selesai);

                $current = $start->copy()->addDay(); // hari pertama sudah ada record
                while ($current->lte($end)) {
                    AbsensiGuru::updateOrCreate(
                        [
                            'user_id' => $model->user_id,
                            'tanggal' => $current->toDateString(),
                        ],
                        [
                            'tanggal_selesai'  => $model->tanggal_selesai,
                            'status'           => $model->status,
                            'judul_pengajuan'  => $model->judul_pengajuan,
                            'keterangan'       => $model->keterangan,
                            'file_bukti'       => $model->file_bukti,
                            'status_pengajuan' => 'approved',
                            'is_notified'      => true,
                        ]
                    );
                    $current->addDay();
                }
            }
        }
        
        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function rejectPengajuan($type, $id)
    {
        $model = $type === 'murid' ? AbsensiSiswa::findOrFail($id) : AbsensiGuru::findOrFail($id);
        
        $model->update([
            'status_pengajuan' => 'rejected',
            'status' => 'alpa',
            'is_notified' => false,
        ]);
        
        return back()->with('success', 'Pengajuan ditolak. Status kehadiran diubah menjadi Alpa.');
    }
}
