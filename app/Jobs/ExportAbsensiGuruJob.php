<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JobTracker;
use App\Models\AbsensiGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExportAbsensiGuruJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tanggalMulai;
    public $tanggalAkhir;
    public $trackerId;

    public function __construct($tanggalMulai, $tanggalAkhir, $trackerId)
    {
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->trackerId = $trackerId;
    }

    public function handle()
    {
        $tracker = JobTracker::find($this->trackerId);
        if (!$tracker) return;

        $tracker->update(['status' => 'running']);

        try {
            $riwayat = AbsensiGuru::with('user')
                ->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalAkhir])
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

            $filename = "absensi_guru_" . $this->tanggalMulai . "_sd_" . $this->tanggalAkhir . "_" . time() . ".xlsx";
            $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($rows);

            if (!file_exists(storage_path('app/public/exports'))) {
                mkdir(storage_path('app/public/exports'), 0755, true);
            }
            
            $xlsx->saveAs(storage_path('app/public/exports/' . $filename));

            $tracker->update([
                'status' => 'completed',
                'result_url' => $filename
            ]);

        } catch (\Exception $e) {
            Log::error('Export Absensi Guru failed: ' . $e->getMessage());
            $tracker->update(['status' => 'failed']);
        }
    }
}
