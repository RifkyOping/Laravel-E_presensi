<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AbsensiSiswa;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScanQrController extends Controller
{
    /**
     * Tampilkan halaman scanner QR Code.
     */
    public function index()
    {
        if (!auth()->user()->guruProfile || !auth()->user()->guruProfile->is_piket_absen_qr) {
            abort(403, 'Anda tidak memiliki akses ke fitur Scan Absen QR.');
        }

        return view('guru.scan-qr.index');
    }

    /**
     * Proses hasil scan QR Code.
     */
    public function processScan(Request $request)
    {
        if (!auth()->user()->guruProfile || !auth()->user()->guruProfile->is_piket_absen_qr) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses piket untuk fitur ini.'
            ], 403);
        }

        $request->validate([
            'qr_data' => 'required|string',
            'tipe_absen' => 'required|in:datang,pulang' // Jenis absensi yang dipilih guru
        ]);

        $qrData = $request->input('qr_data');
        $tipeAbsen = $request->input('tipe_absen');
        
        // Cari siswa berdasarkan NISN (nomor_induk)
        $siswa = User::where('nomor_induk', $qrData)
            ->where('role', 'murid')
            ->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa dengan NISN ' . $qrData . ' tidak ditemukan.'
            ], 404);
        }

        $setting = SchoolSetting::get();
        $today = Carbon::today();

        // Cek Jadwal Buka/Tutup Absen
        [$isOpen, $reason] = $setting->isAbsensiTerbuka($tipeAbsen, 'hadir');
        if (!$isOpen) {
            return response()->json([
                'success' => false,
                'message' => $reason
            ], 422);
        }

        $existing = AbsensiSiswa::where('user_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($tipeAbsen === 'datang') {
            if ($existing) {
                if (in_array($existing->status, ['sakit', 'izin'])) {
                    $existing->update([
                        'status' => 'hadir',
                        'waktu_datang' => now()->format('H:i:s'),
                        'keterangan' => null,
                        'file_bukti' => null,
                        'status_pengajuan' => null,
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => "Masa Sakit/Izin dihentikan. Absen datang {$siswa->name} berhasil dicatat."
                    ]);
                }
                
                if ($existing->waktu_datang) {
                    return response()->json([
                        'success' => false,
                        'message' => "{$siswa->name} sudah melakukan absen datang hari ini."
                    ], 422);
                }
            } else {
                AbsensiSiswa::create([
                    'user_id'      => $siswa->id,
                    'tanggal'      => $today,
                    'waktu_datang' => now()->format('H:i:s'),
                    'status'       => 'hadir',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Absen datang {$siswa->name} berhasil dicatat."
            ]);

        } else { // Pulang
            if (!$existing || !$existing->waktu_datang) {
                return response()->json([
                    'success' => false,
                    'message' => "{$siswa->name} belum melakukan absen datang. Tidak dapat absen pulang.",
                    'show_popup' => true
                ], 422);
            }

            if ($existing->waktu_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => "{$siswa->name} sudah melakukan absen pulang hari ini."
                ], 422);
            }

            $existing->update([
                'waktu_pulang' => now()->format('H:i:s'),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Absen pulang {$siswa->name} berhasil dicatat."
            ]);
        }
    }
}
