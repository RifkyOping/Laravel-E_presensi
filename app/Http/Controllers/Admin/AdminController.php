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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Shuchkin\SimpleXLSX;

class AdminController extends Controller
{
    // ──────────────────────────────────────────
    //  DASHBOARD
    // ──────────────────────────────────────────

    public function dashboard()
    {
        $today = Carbon::today();

        $stats = Cache::remember('admin_dashboard_stats', 600, function () use ($today) {
            return [
                'total_siswa'    => User::where('role', 'murid')->count(),
                'total_guru'     => User::where('role', 'guru')->count(),
                'guru_hadir'     => AbsensiGuru::whereDate('tanggal', $today)->whereNotNull('waktu_datang')->count(),
                'guru_mengajar'  => AbsensiMengajar::whereDate('tanggal', $today)->distinct('user_id')->count(),
                'total_mapel'    => MataPelajaran::count(),
                'mapel_aktif'    => MataPelajaran::where('aktif', true)->count(),
                'siswa_hadir'    => AbsensiSiswa::whereDate('tanggal', $today)->where('status', 'hadir')->count(),
            ];
        });

        // Guru yang sudah absen sekolah hari ini
        $guruHadir = AbsensiGuru::with('user')
            ->whereDate('tanggal', $today)
            ->orderByDesc('waktu_datang')
            ->take(5)
            ->get();

        // Mengumpulkan Aktivitas Pengguna (Tanpa Tabel Baru)
        $aktivitas = collect();

        // 1. Aktivitas Login / Sesi Aktif
        $sessions = \Illuminate\Support\Facades\DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('users.name', 'users.role', 'sessions.last_activity as time')
            ->whereNotNull('sessions.user_id')
            ->orderByDesc('sessions.last_activity')
            ->take(5)
            ->get()
            ->map(function ($s) {
                return (object)[
                    'name' => $s->name,
                    'role' => $s->role,
                    'description' => 'Akses Sistem (Online)',
                    'last_activity' => $s->time
                ];
            });
        $aktivitas = $aktivitas->merge($sessions);

        // 2. Aktivitas Absensi Guru
        $absensiGuru = \App\Models\AbsensiGuru::with('user')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function ($a) {
                $isNew = $a->created_at == $a->updated_at;
                return (object)[
                    'name' => $a->user->name ?? 'Pengguna Dihapus',
                    'role' => 'guru',
                    'description' => $isNew ? 'Absen Datang Sekolah' : 'Pembaruan Data Absensi',
                    'last_activity' => $a->updated_at->timestamp
                ];
            });
        $aktivitas = $aktivitas->merge($absensiGuru);

        // 3. Aktivitas Absensi Siswa
        $absensiSiswa = \App\Models\AbsensiSiswa::with('user')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function ($a) {
                $isNew = $a->created_at == $a->updated_at;
                return (object)[
                    'name' => $a->user->name ?? 'Pengguna Dihapus',
                    'role' => 'murid',
                    'description' => $isNew ? 'Absen Datang Sekolah' : 'Pembaruan Data Absensi',
                    'last_activity' => $a->updated_at->timestamp
                ];
            });
        $aktivitas = $aktivitas->merge($absensiSiswa);

        // 4. Aktivitas Akun (CRUD User)
        $usersCrud = \App\Models\User::orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(function ($u) {
                $isNew = $u->created_at == $u->updated_at;
                return (object)[
                    'name' => $u->name,
                    'role' => $u->role,
                    'description' => $isNew ? 'Pendaftaran Akun Baru' : 'Pembaruan Profil Akun',
                    'last_activity' => $u->updated_at->timestamp
                ];
            });
        $aktivitas = $aktivitas->merge($usersCrud);

        // Urutkan semua aktivitas berdasarkan waktu terbaru, ambil pengguna unik, dan ambil 10 teratas
        $aktivitasHariIni = $aktivitas->sortByDesc('last_activity')->unique('name')->take(10)->values();

        // Status Sistem (Job Queue)
        $systemStatus = Cache::remember('admin_dashboard_system_status', 600, function () {
            $pendingJobs = \Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('jobs') 
                ? \Illuminate\Support\Facades\DB::table('jobs')->count() 
                : 0;
                
            $failedJobs = \Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('failed_jobs') 
                ? \Illuminate\Support\Facades\DB::table('failed_jobs')->count() 
                : 0;

            return [
                'pending_jobs' => $pendingJobs,
                'failed_jobs'  => $failedJobs,
            ];
        });

        return view('admin.dashboard', compact('stats', 'guruHadir', 'aktivitasHariIni', 'systemStatus'));
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
                  ->orWhere('nomor_induk', 'like', '%' . $request->search . '%')
                  ->orWhereHas('siswaProfile', function ($profileQuery) use ($request) {
                      $profileQuery->where('nis', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(50)->withQueryString();

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
            'email'       => 'nullable|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
            'role'        => 'required|in:murid,guru,admin,pengawas',
        ];

        if ($request->role === 'murid') {
            $rules['nomor_induk']    = 'required_without:nis|nullable|string|max:255|unique:users,nomor_induk';
            $rules['nis']            = 'required_without:nomor_induk|nullable|string|max:255|unique:siswa_profiles,nis';
            $rules['kelas_id']       = 'nullable|exists:kelas,id';
            $rules['jenis_kelamin']  = 'nullable|in:L,P';
            $rules['tempat_lahir']   = 'nullable|string|max:100';
            $rules['tanggal_lahir']  = 'nullable|date';
            $rules['agama']          = 'nullable|string|max:50';
        } else {
            $rules['nomor_induk']    = 'required|string|max:255|unique:users,nomor_induk';
        }

        $request->validate($rules, [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'nomor_induk.required' => 'Nomor induk (NISN/NIP) wajib diisi.',
            'nomor_induk.required_without' => 'NISN wajib diisi jika NIS kosong.',
            'nomor_induk.unique'   => 'Nomor induk ini sudah terdaftar.',
            'nis.required_without' => 'NIS wajib diisi jika NISN kosong.',
            'nis.unique'           => 'NIS ini sudah terdaftar.',
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
            'nomor_induk' => $request->nomor_induk ?: null,
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
                'is_piket_absen_qr' => $request->has('is_piket_absen_qr'),
                'is_guru_bahasa'    => $request->has('is_guru_bahasa'),
                'is_kepsek'         => $request->has('is_kepsek'),
                'is_kurikulum'      => $request->has('is_kurikulum'),
            ]);
        }

        if ($request->role === 'murid') {
            $kelas = $request->kelas_id ? \App\Models\Kelas::find($request->kelas_id) : null;
            $user->siswaProfile()->create([
                'kelas'         => $kelas ? $kelas->tingkat : null,
                'jurusan'       => $kelas ? $kelas->jurusan : null,
                'rombel'        => $kelas ? $kelas->rombel : null,
                'nis'           => $request->nis ?: null,
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
        $rows = [
            ['nama', 'nomor_induk (NISN/NIP)', 'nis', 'email', 'role', 'password', 'kelas', 'agama'],
            ['Murid Contoh 1', '0012345678', '21221001', 'murid1@smkn1majene.sch.id', 'murid', '12345678', 'X RPL 1', 'Islam'],
            ['Guru Contoh 1', '198001012010011001', '', '', 'guru', '12345678', '', '']
        ];

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_user.xlsx"',
        ]);
    }

    public function importUsers(Request $request)
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
            return back()->with('error', 'File kosong atau format tidak sesuai.');
        }

        $berhasil = 0;
        $gagalRows = [];

        // Skip header at index 0
        for ($i = 1; $i < count($rows); $i++) {
            $rowNum = $i + 1;
            $row = $rows[$i];
            
            // Abaikan baris kosong
            if (empty($row) || count(array_filter($row, fn($val) => trim((string)$val) !== '')) === 0) {
                continue;
            }

            if (count($row) < 4) {
                $gagalRows[] = [
                    'baris' => $rowNum,
                    'nama' => trim($row[0] ?? '(Kosong)'),
                    'detail' => 'Data kolom tidak lengkap (kurang dari 4 kolom)',
                    'alasan' => 'Jumlah kolom kurang dari ketentuan minimal template.'
                ];
                continue;
            }

            $name = trim($row[0] ?? '');
            $nomor_induk = trim($row[1] ?? '');
            $nis = isset($row[2]) && trim($row[2]) !== '' ? trim($row[2]) : null;
            $email = isset($row[3]) && trim($row[3]) !== '' ? trim($row[3]) : null;
            $roleExcel = isset($row[4]) ? strtolower(trim($row[4])) : '';
            $password = isset($row[5]) && trim($row[5]) !== '' ? trim($row[5]) : null;
            $kelasStr = isset($row[6]) ? trim($row[6]) : null;
            $agama = isset($row[7]) && trim($row[7]) !== '' ? trim($row[7]) : null;

            // 1. Cari User (Upsert Logic)
            $user = null;
            if ($nomor_induk !== '') {
                $user = User::where('nomor_induk', $nomor_induk)->first();
            }
            if (!$user && $nis) {
                $siswaProfile = \App\Models\SiswaProfile::where('nis', $nis)->first();
                if ($siswaProfile) {
                    $user = $siswaProfile->user;
                }
            }

            if ($user) {
                // UPDATE LOGIC
                $role = $user->role;

                if ($email && $email !== $user->email && User::where('email', $email)->exists()) {
                    $gagalRows[] = [
                        'baris' => $rowNum,
                        'nama' => $name ?: $user->name,
                        'detail' => "Email: {$email}",
                        'alasan' => "Email '{$email}' sudah digunakan oleh pengguna lain."
                    ];
                    continue;
                }

                if ($role === 'murid' && $nis) {
                    $profile = $user->siswaProfile;
                    if ($profile && $profile->nis !== $nis && \App\Models\SiswaProfile::where('nis', $nis)->exists()) {
                        $gagalRows[] = [
                            'baris' => $rowNum,
                            'nama' => $name ?: $user->name,
                            'detail' => "NIS: {$nis}",
                            'alasan' => "NIS '{$nis}' sudah digunakan oleh pengguna lain."
                        ];
                        continue;
                    }
                }

                try {
                    if ($name !== '') $user->name = $name;
                    if ($email !== null) $user->email = $email;
                    if ($password !== null) $user->password = Hash::make($password);
                    $user->save();

                    if ($role === 'murid') {
                        $profile = $user->siswaProfile ?: new \App\Models\SiswaProfile(['user_id' => $user->id]);
                        if ($nis !== null) $profile->nis = $nis;
                        if ($agama !== null) $profile->agama = $agama;
                        if ($kelasStr !== null) {
                            $parts = explode(' ', $kelasStr);
                            $profile->kelas = $parts[0] ?? null;
                            $profile->rombel = end($parts) ?: null;
                            if (count($parts) > 2) {
                                $profile->jurusan = implode(' ', array_slice($parts, 1, -1));
                            } else {
                                $profile->jurusan = null;
                            }
                        }
                        $profile->save();
                    }
                    $berhasil++;
                } catch (\Exception $e) {
                    $gagalRows[] = [
                        'baris' => $rowNum,
                        'nama' => $name ?: $user->name,
                        'detail' => "Update ID: {$user->id}",
                        'alasan' => 'Gagal update ke database: ' . $e->getMessage()
                    ];
                }

            } else {
                // INSERT LOGIC
                $role = $roleExcel;
                if ($role !== 'murid' && $nomor_induk === '') {
                    $gagalRows[] = [
                        'baris' => $rowNum,
                        'nama' => $name ?: '(Kosong)',
                        'detail' => "Role: {$role}",
                        'alasan' => 'Nomor Induk / NIP / ID wajib diisi.'
                    ];
                    continue;
                }

                if ($role === 'murid' && $nomor_induk === '' && !$nis) {
                    $gagalRows[] = [
                        'baris' => $rowNum,
                        'nama' => $name ?: '(Kosong)',
                        'detail' => "Role: {$role}",
                        'alasan' => 'Untuk murid, minimal NIS atau NISN (Nomor Induk) harus diisi.'
                    ];
                    continue;
                }

                if (!in_array($role, ['murid', 'guru', 'admin', 'pengawas'])) {
                    $gagalRows[] = [
                        'baris' => $rowNum,
                        'nama' => $name,
                        'detail' => "Role: {$role}",
                        'alasan' => "Role '{$role}' tidak valid."
                    ];
                    continue;
                }

                if ($email && User::where('email', $email)->exists()) {
                    $gagalRows[] = [
                        'baris' => $rowNum,
                        'nama' => $name,
                        'detail' => "Email: {$email}",
                        'alasan' => "Email '{$email}' sudah digunakan."
                    ];
                    continue;
                }

                if ($role === 'murid' && $nis && \App\Models\SiswaProfile::where('nis', $nis)->exists()) {
                    $gagalRows[] = [
                        'baris' => $rowNum,
                        'nama' => $name,
                        'detail' => "NIS: {$nis}",
                        'alasan' => "NIS '{$nis}' sudah terdaftar di sistem."
                    ];
                    continue;
                }

                try {
                    $newUser = User::create([
                        'name'        => $name !== '' ? $name : 'Tanpa Nama',
                        'nomor_induk' => $nomor_induk !== '' ? $nomor_induk : null,
                        'email'       => $email,
                        'role'        => $role,
                        'password'    => Hash::make($password ?: '12345678'),
                    ]);

                    if ($role === 'murid') {
                        $profileData = [];
                        if ($nis) $profileData['nis'] = $nis;
                        if ($agama) $profileData['agama'] = $agama;
                        if ($kelasStr) {
                            $parts = explode(' ', $kelasStr);
                            $profileData['kelas'] = $parts[0] ?? null;
                            $profileData['rombel'] = end($parts) ?: null;
                            if (count($parts) > 2) {
                                $profileData['jurusan'] = implode(' ', array_slice($parts, 1, -1));
                            }
                        }
                        $newUser->siswaProfile()->create($profileData);
                    }
                    $berhasil++;
                } catch (\Exception $e) {
                    $gagalRows[] = [
                        'baris' => $rowNum,
                        'nama' => $name,
                        'detail' => "Nomor Induk: {$nomor_induk}",
                        'alasan' => 'Gagal insert ke database: ' . $e->getMessage()
                    ];
                }
            }
        }

        $totalGagal = count($gagalRows);

        if ($totalGagal > 0) {
            $msg = "Import selesai. Berhasil: {$berhasil} akun. Terdapat {$totalGagal} baris yang gagal diimport.";
            return back()
                ->with($berhasil > 0 ? 'warning' : 'error', $msg)
                ->with('import_errors', $gagalRows);
        }

        return back()->with('success', "Import selesai! Seluruh {$berhasil} akun berhasil ditambahkan.");
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
            'email'       => 'nullable|email|unique:users,email,' . $user->id,
            'role'        => 'required|in:murid,guru,admin,pengawas',
            'password'    => 'nullable|min:6|confirmed',
        ];

        if ($request->role === 'murid') {
            $profileId = $user->siswaProfile ? $user->siswaProfile->id : null;
            $rules['nomor_induk']   = 'required_without:nis|nullable|string|max:255|unique:users,nomor_induk,' . $user->id;
            $rules['nis']           = 'required_without:nomor_induk|nullable|string|max:255|unique:siswa_profiles,nis,' . $profileId;
            $rules['kelas_id']      = 'nullable|exists:kelas,id';
            $rules['jenis_kelamin'] = 'nullable|in:L,P';
            $rules['tempat_lahir']  = 'nullable|string|max:100';
            $rules['tanggal_lahir'] = 'nullable|date';
            $rules['agama']         = 'nullable|string|max:50';
        } else {
            $rules['nomor_induk']   = 'required|string|max:255|unique:users,nomor_induk,' . $user->id;
        }

        $request->validate($rules, [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'nomor_induk.required' => 'Nomor induk (NISN/NIP) wajib diisi.',
            'nomor_induk.required_without' => 'NISN wajib diisi jika NIS kosong.',
            'nomor_induk.unique'   => 'Nomor induk ini sudah digunakan.',
            'nis.required_without' => 'NIS wajib diisi jika NISN kosong.',
            'nis.unique'           => 'NIS ini sudah digunakan.',
            'email.required'       => 'Email wajib diisi.',
            'email.unique'         => 'Email sudah digunakan.',
            'role.required'        => 'Role wajib dipilih.',
            'role.in'              => 'Role yang dipilih tidak valid.',
            'password.min'         => 'Password minimal 6 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
        ]);

        $data = [
            'name'        => $request->name,
            'nomor_induk' => $request->nomor_induk ?: null,
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
                    'is_piket_absen_qr' => $request->has('is_piket_absen_qr'),
                    'is_guru_bahasa'    => $request->has('is_guru_bahasa'),
                    'is_kepsek'         => $request->has('is_kepsek'),
                    'is_kurikulum'      => $request->has('is_kurikulum'),
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
                    'nis'           => $request->nis ?: null,
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

    public function bulkDestroyUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $ids = $request->user_ids;
        // Mencegah admin menghapus akunnya sendiri
        $ids = array_diff($ids, [auth()->id()]);

        if (empty($ids)) {
            return redirect()->route('admin.users')->with('error', 'Tidak ada akun valid yang dipilih untuk dihapus.');
        }

        User::whereIn('id', $ids)->delete();

        return redirect()->route('admin.users')->with('success', count($ids) . ' akun pengguna berhasil dihapus.');
    }

    public function bulkUpdateUsers(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*.name' => 'required|string|max:255',
            'users.*.nomor_induk' => 'nullable|string|max:255',
            'users.*.nis' => 'nullable|string|max:255',
            'users.*.email' => 'nullable|email',
            'users.*.role' => 'required|in:murid,guru,admin,pengawas',
        ]);

        $users = $request->users;
        $berhasil = 0;
        $gagal = 0;
        $errorMessages = [];

        foreach ($users as $id => $data) {
            $user = User::find($id);
            if (!$user) {
                continue;
            }

            $nomor_induk = $data['nomor_induk'] ?? null;
            $newRole = $data['role'];
            $submittedNis = $data['nis'] ?? null;
            $hasNisKey = array_key_exists('nis', $data);

            if ($newRole !== 'murid' && empty($nomor_induk)) {
                $errorMessages[] = "Gagal ({$user->name}): Nomor Induk wajib diisi untuk role {$newRole}.";
                $gagal++;
                continue;
            }

            if ($newRole === 'murid' && empty($nomor_induk)) {
                $effectiveNis = $hasNisKey ? $submittedNis : $user->siswaProfile?->nis;
                if (empty($effectiveNis)) {
                    $errorMessages[] = "Gagal ({$user->name}): NISN atau NIS wajib diisi.";
                    $gagal++;
                    continue;
                }
            }

            // Validasi Unik
            if (!empty($nomor_induk) && User::where('nomor_induk', $nomor_induk)->where('id', '!=', $id)->exists()) {
                $errorMessages[] = "Gagal ({$user->name}): Nomor Induk {$nomor_induk} sudah digunakan.";
                $gagal++;
                continue;
            }

            // Validasi Unik NIS
            if ($newRole === 'murid' && $hasNisKey && !empty($submittedNis)) {
                $profileId = $user->siswaProfile?->id;
                $nisExists = \App\Models\SiswaProfile::where('nis', $submittedNis)
                    ->when($profileId, function($q) use ($profileId) {
                        return $q->where('id', '!=', $profileId);
                    })->exists();
                    
                if ($nisExists) {
                    $errorMessages[] = "Gagal ({$user->name}): NIS {$submittedNis} sudah digunakan.";
                    $gagal++;
                    continue;
                }
            }

            if (!empty($data['email']) && User::where('email', $data['email'])->where('id', '!=', $id)->exists()) {
                $errorMessages[] = "Gagal ({$user->name}): Email {$data['email']} sudah digunakan.";
                $gagal++;
                continue;
            }

            $oldRole = $user->role;

            $user->update([
                'name' => $data['name'],
                'nomor_induk' => $nomor_induk,
                'email' => $data['email'],
                'role' => $newRole,
            ]);

            // Jika role diubah dari murid ke yang lain, hapus profil siswa
            if ($oldRole === 'murid' && $newRole !== 'murid') {
                if ($user->siswaProfile) {
                    $user->siswaProfile()->delete();
                }
            } elseif ($newRole === 'murid' && $hasNisKey) {
                // Update nis jika ada di submit
                $user->siswaProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    ['nis' => $submittedNis ?: null]
                );
            }

            $berhasil++;
        }

        if ($gagal > 0) {
            $msg = "Berhasil memperbarui {$berhasil} pengguna. Terdapat {$gagal} kegagalan: " . implode(' | ', $errorMessages);
            return back()->with('warning', $msg);
        }

        return back()->with('success', "Berhasil memperbarui seluruh ({$berhasil}) pengguna di halaman ini.");
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

        // Untuk dropdown filter
        $listGuru = User::where('role', 'guru')->orderBy('name')->get();

        // Guru yang akan ditampilkan di tabel Rekap
        $guruQuery = User::where('role', 'guru')->orderBy('name');
        if ($request->filled('guru_id')) {
            $guruQuery->where('id', $request->guru_id);
        }
        $semuaGuru = $guruQuery->get();

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

        return view('admin.absensi-guru', compact('semuaGuru', 'listGuru', 'absensi', 'tanggal', 'riwayat'));
    }

    public function exportAbsensiGuru(Request $request)
    {
        $tanggalMulai = $request->filled('tanggal_mulai') ? Carbon::parse($request->tanggal_mulai) : Carbon::today()->startOfMonth();
        $tanggalAkhir = $request->filled('tanggal_akhir') ? Carbon::parse($request->tanggal_akhir) : Carbon::today();
        
        $riwayat = AbsensiGuru::with('user')
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->get();

        $rows = [
            ['No', 'Nama Guru', 'Tanggal', 'Jam Datang', 'Jam Pulang', 'Status Kehadiran', 'Kategori', 'Keterangan']
        ];
        
        $no = 1;
        foreach ($riwayat as $data) {
            $rows[] = [
                $no++,
                $data->user->name ?? '-',
                $data->tanggal->format('Y-m-d'),
                $data->waktu_datang ?? '-',
                $data->waktu_pulang ?? '-',
                $data->status,
                $data->kategori ?? '-',
                $data->keterangan ?? '-'
            ];
        }

        $filename = "absensi_guru_" . $tanggalMulai->format('Y-m-d') . "_sd_" . $tanggalAkhir->format('Y-m-d') . ".xlsx";
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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
        
        $riwayat = \App\Models\AbsensiMengajar::with(['user', 'verifier'])
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->orderBy('jam_ke')
            ->get();

        $rows = [
            ['No', 'Nama Guru', 'Tanggal', 'Mata Pelajaran', 'Kelas', 'Mapel Ke', 'Jam Mulai', 'Jam Selesai', 'Masuk', 'Keluar', 'Kategori', 'Status Verifikasi', 'Diverifikasi Oleh']
        ];
        
        $no = 1;
        foreach ($riwayat as $data) {
            $rows[] = [
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
            ];
        }

        $filename = "aktivitas_mengajar_" . $tanggalMulai->format('Y-m-d') . "_sd_" . $tanggalAkhir->format('Y-m-d') . ".xlsx";
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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
        
        $riwayat = AbsensiSiswa::with('user')
            ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->orderBy('tanggal')
            ->orderBy('user_id')
            ->get();

        $rows = [
            ['No', 'Nama Siswa', 'Tanggal', 'Waktu Datang', 'Waktu Pulang', 'Status Kehadiran', 'Keterangan']
        ];
        
        $no = 1;
        foreach ($riwayat as $data) {
            $rows[] = [
                $no++,
                $data->user->name ?? '-',
                $data->tanggal->format('Y-m-d'),
                $data->waktu_datang ?? '-',
                $data->waktu_pulang ?? '-',
                $data->status,
                $data->keterangan ?? '-'
            ];
        }

        $filename = "absensi_murid_" . $tanggalMulai->format('Y-m-d') . "_sd_" . $tanggalAkhir->format('Y-m-d') . ".xlsx";
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);

        return response((string) $xlsx, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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
            \Illuminate\Support\Facades\Cache::forget('siswa_absensi_index_' . $model->user_id . '_' . \Carbon\Carbon::parse($model->tanggal)->toDateString());
            \Illuminate\Support\Facades\Cache::forget('siswa_absensi_index_' . $model->user_id . '_' . \Carbon\Carbon::today()->toDateString());
        } else {
            // Guru
            $model = AbsensiGuru::findOrFail($id);
            $model->update([
                'status_pengajuan' => 'approved',
                'is_notified' => false,
            ]);
            \Illuminate\Support\Facades\Cache::forget('guru_absensi_index_' . $model->user_id . '_' . \Carbon\Carbon::parse($model->tanggal)->toDateString());
            \Illuminate\Support\Facades\Cache::forget('guru_absensi_index_' . $model->user_id . '_' . \Carbon\Carbon::today()->toDateString());

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

    public function rejectPengajuan(Request $request, $type, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:500'
        ], [
            'alasan.required' => 'Alasan penolakan wajib diisi.'
        ]);

        $model = $type === 'murid' ? AbsensiSiswa::findOrFail($id) : AbsensiGuru::findOrFail($id);
        
        $model->update([
            'status_pengajuan' => 'rejected',
            'alasan_ditolak' => $request->alasan,
            'is_notified' => false,
        ]);
        
        if ($type === 'murid') {
            \Illuminate\Support\Facades\Cache::forget('siswa_absensi_index_' . $model->user_id . '_' . \Carbon\Carbon::parse($model->tanggal)->toDateString());
            \Illuminate\Support\Facades\Cache::forget('siswa_absensi_index_' . $model->user_id . '_' . \Carbon\Carbon::today()->toDateString());
        } else {
            \Illuminate\Support\Facades\Cache::forget('guru_absensi_index_' . $model->user_id . '_' . \Carbon\Carbon::parse($model->tanggal)->toDateString());
            \Illuminate\Support\Facades\Cache::forget('guru_absensi_index_' . $model->user_id . '_' . \Carbon\Carbon::today()->toDateString());
        }
        
        return back()->with('success', 'Pengajuan ditolak.');
    }
}
