<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JobTracker;
use App\Models\User;
use App\Models\SiswaProfile;
use Illuminate\Support\Facades\Hash;
use Shuchkin\SimpleXLSX;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filePath;
    public $trackerId;
    public $extension;

    public function __construct($filePath, $trackerId, $extension)
    {
        $this->filePath = $filePath;
        $this->trackerId = $trackerId;
        $this->extension = $extension;
    }

    public function handle()
    {
        $tracker = JobTracker::find($this->trackerId);
        if (!$tracker) return;

        $tracker->update(['status' => 'running']);

        try {
            $rows = [];
            $realPath = storage_path('app/' . $this->filePath);

            if ($this->extension === 'xlsx') {
                if ($xlsx = SimpleXLSX::parse($realPath)) {
                    $rows = $xlsx->rows();
                } else {
                    throw new \Exception('Gagal membaca file XLSX: ' . SimpleXLSX::parseError());
                }
            } else {
                $handle = fopen($realPath, 'r');
                $firstLine = fgets($handle);
                $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
                rewind($handle);

                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }

            if (count($rows) < 2) {
                throw new \Exception('File kosong atau format tidak sesuai.');
            }

            $berhasil = 0;
            $gagalRows = [];

            // Proses baris per baris (persis seperti di AdminController)
            for ($i = 1; $i < count($rows); $i++) {
                $rowNum = $i + 1;
                $row = $rows[$i];
                
                if (empty($row) || count(array_filter($row, fn($val) => trim((string)$val) !== '')) === 0) {
                    continue;
                }

                if (count($row) < 4) {
                    $gagalRows[] = "Baris $rowNum: Data kurang dari 4 kolom.";
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

                $user = null;
                if ($nomor_induk !== '') {
                    $user = User::where('nomor_induk', $nomor_induk)->first();
                }
                if (!$user && $nis) {
                    $siswaProfile = SiswaProfile::where('nis', $nis)->first();
                    if ($siswaProfile) $user = $siswaProfile->user;
                }

                if ($user) {
                    $role = $user->role;
                    if ($email && $email !== $user->email && User::where('email', $email)->exists()) {
                        $gagalRows[] = "Baris $rowNum: Email sudah digunakan.";
                        continue;
                    }
                    if ($role === 'murid' && $nis) {
                        $profile = $user->siswaProfile;
                        if ($profile && $profile->nis !== $nis && SiswaProfile::where('nis', $nis)->exists()) {
                            $gagalRows[] = "Baris $rowNum: NIS sudah digunakan.";
                            continue;
                        }
                    }
                    try {
                        if ($name !== '') $user->name = $name;
                        if ($email !== null) $user->email = $email;
                        if ($password !== null) $user->password = Hash::make($password);
                        $user->save();

                        if ($role === 'murid') {
                            $profile = $user->siswaProfile ?: new SiswaProfile(['user_id' => $user->id]);
                            if ($nis !== null) $profile->nis = $nis;
                            if ($agama !== null) $profile->agama = $agama;
                            if ($kelasStr !== null) {
                                $parts = explode(' ', $kelasStr);
                                $profile->kelas = $parts[0] ?? null;
                                $profile->rombel = end($parts) ?: null;
                                if (count($parts) > 2) $profile->jurusan = implode(' ', array_slice($parts, 1, -1));
                                else $profile->jurusan = null;
                            }
                            $profile->save();
                        }
                        $berhasil++;
                    } catch (\Exception $e) {
                        $gagalRows[] = "Baris $rowNum: Gagal update - " . $e->getMessage();
                    }
                } else {
                    $role = $roleExcel;
                    if ($role !== 'murid' && $nomor_induk === '') {
                        $gagalRows[] = "Baris $rowNum: Nomor Induk wajib diisi.";
                        continue;
                    }
                    if ($role === 'murid' && $nomor_induk === '' && !$nis) {
                        $gagalRows[] = "Baris $rowNum: Murid minimal butuh NIS.";
                        continue;
                    }
                    if (!in_array($role, ['murid', 'guru', 'admin', 'pengawas'])) {
                        $gagalRows[] = "Baris $rowNum: Role tidak valid.";
                        continue;
                    }
                    if ($email && User::where('email', $email)->exists()) {
                        $gagalRows[] = "Baris $rowNum: Email sudah digunakan.";
                        continue;
                    }
                    if ($role === 'murid' && $nis && SiswaProfile::where('nis', $nis)->exists()) {
                        $gagalRows[] = "Baris $rowNum: NIS sudah terdaftar.";
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
                                if (count($parts) > 2) $profileData['jurusan'] = implode(' ', array_slice($parts, 1, -1));
                            }
                            $newUser->siswaProfile()->create($profileData);
                        }
                        $berhasil++;
                    } catch (\Exception $e) {
                        $gagalRows[] = "Baris $rowNum: Gagal insert - " . $e->getMessage();
                    }
                }
            }

            // Hapus file temporary
            @unlink($realPath);

            $resultUrl = null;
            if (count($gagalRows) > 0) {
                // Buat file log txt untuk rincian kegagalan
                $logContent = "Laporan Import User\nBerhasil: $berhasil akun\nGagal: " . count($gagalRows) . " baris\n\nRincian Gagal:\n" . implode("\n", $gagalRows);
                $logFilename = 'import_log_' . time() . '.txt';
                if (!file_exists(storage_path('app/public/exports'))) {
                    mkdir(storage_path('app/public/exports'), 0755, true);
                }
                file_put_contents(storage_path('app/public/exports/' . $logFilename), $logContent);
                $resultUrl = $logFilename;
            }

            $tracker->update([
                'status' => 'completed',
                'result_url' => $resultUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Import Users Job failed: ' . $e->getMessage());
            $tracker->update(['status' => 'failed']);
        }
    }
}
