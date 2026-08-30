<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JobTracker;
use App\Models\User;
use App\Models\JadwalMengajar;
use App\Models\GuruProfile;
use Shuchkin\SimpleXLSX;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportJadwalJob implements ShouldQueue
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

            $header = array_shift($rows); 
            $header = array_map(function($h) {
                return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h)));
            }, $header);
            
            $identifierColumn = null;
            if (in_array('nip', $header)) {
                $identifierColumn = 'nip';
            } elseif (in_array('nama', $header)) {
                $identifierColumn = 'nama';
            } elseif (in_array('email_guru', $header)) {
                $identifierColumn = 'email_guru';
            }

            if (!$identifierColumn || !in_array('hari', $header) || !in_array('jam_ke', $header)) {
                throw new \Exception('Format header file tidak sesuai template. Pastikan ada kolom nip, hari, dan jam_ke.');
            }

            $berhasil = 0;
            $usersUpdated = [];
            $gagalRows = [];

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2; 

                if (empty($row) || count(array_filter($row, fn($val) => trim((string)$val) !== '')) === 0) {
                    continue;
                }
                
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
                    $gagalRows[] = "Baris $rowNum: Kolom identitas (NIP/Nama/Email) kosong.";
                    continue;
                }
                
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
                    $gagalRows[] = "Baris $rowNum: Guru '$namaOrEmail' tidak ditemukan.";
                    continue;
                }

                if ($hari === '' || $jamKe === '') {
                    $gagalRows[] = "Baris $rowNum: Hari atau Jam ke- kosong.";
                    continue;
                }

                try {
                    if (!in_array($guru->id, $usersUpdated)) {
                        $guru->jadwalMengajars()->delete();
                        $usersUpdated[] = $guru->id;
                    }
                    
                    JadwalMengajar::create([
                        'user_id' => $guru->id,
                        'hari' => ucfirst(strtolower($hari)), 
                        'tipe_blok' => in_array($tipeBlok, ['A', 'B', 'Semua']) ? $tipeBlok : 'Semua',
                        'mata_pelajaran' => $mapel,
                        'kelas' => $kelasStr,
                        'jam_ke' => (int)$jamKe,
                        'jam_mulai' => $jamMulai ?: '07:30',
                        'jam_selesai' => $jamSelesai,
                    ]);
                    $berhasil++;
                } catch (\Exception $e) {
                    $gagalRows[] = "Baris $rowNum: Gagal menyimpan - " . $e->getMessage();
                }
            }
            
            foreach ($usersUpdated as $userId) {
                GuruProfile::updateOrCreate(
                    ['user_id' => $userId],
                    ['is_jadwal_set' => true]
                );
            }

            @unlink($realPath);

            $resultUrl = null;
            if (count($gagalRows) > 0) {
                $logContent = "Laporan Import Jadwal\nBerhasil: $berhasil jadwal\nGagal: " . count($gagalRows) . " baris\n\nRincian Gagal:\n" . implode("\n", $gagalRows);
                $logFilename = 'import_jadwal_log_' . time() . '.txt';
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
            Log::error('Import Jadwal Job failed: ' . $e->getMessage());
            $tracker->update(['status' => 'failed']);
        }
    }
}
