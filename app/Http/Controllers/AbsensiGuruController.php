<?php

namespace App\Http\Controllers;

use App\Models\AbsensiGuru;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiGuruController extends Controller
{
    /**
     * Tampilkan halaman absensi guru (datang & pulang).
     */
    public function index()
    {
        $user   = Auth::user();
        $today  = Carbon::today();

        $absensiHariIni = AbsensiGuru::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $riwayat = AbsensiGuru::where('user_id', $user->id)
            ->orderByDesc('tanggal')
            ->limit(30)
            ->get();

        $setting = SchoolSetting::get();

        // Check for unread approval/rejection notifications
        $notif = AbsensiGuru::where('user_id', $user->id)
            ->where('is_notified', false)
            ->whereNotNull('status_pengajuan')
            ->where('status_pengajuan', '!=', 'pending')
            ->orderByDesc('tanggal')
            ->first();

        if ($notif) {
            $isApproved = $notif->status_pengajuan === 'approved';
            session()->now('popup_notification', [
                'title' => $isApproved ? 'Pengajuan Disetujui!' : 'Pengajuan Ditolak',
                'text'  => $isApproved 
                           ? 'Pengajuan izin/sakit Anda telah disetujui oleh Admin.' 
                           : 'Pengajuan izin/sakit Anda ditolak oleh Admin, sehingga status Anda menjadi Alpha.',
                'icon'  => $isApproved ? 'success' : 'error'
            ]);
            
            // Mark all as notified to avoid repeated popups
            AbsensiGuru::where('user_id', $user->id)
                ->where('is_notified', false)
                ->update(['is_notified' => true]);
        }
        $sedangMasaSakitIzin = false;
        $jenisMasaAktif = null; // 'sakit' atau 'izin'
        if ($absensiHariIni && in_array($absensiHariIni->status, ['sakit', 'izin'])) {
            $sedangMasaSakitIzin = true;
            $jenisMasaAktif = $absensiHariIni->status;
        } elseif (!$absensiHariIni) {
            $activeIzin = AbsensiGuru::where('user_id', $user->id)
                ->where('status', 'izin')
                ->where('status_pengajuan', 'approved')
                ->whereDate('tanggal', '<=', $today)
                ->whereDate('tanggal_selesai', '>=', $today)
                ->exists();
            if ($activeIzin) {
                $sedangMasaSakitIzin = true;
                $jenisMasaAktif = 'izin';
            } else {
                $lastAbsen = AbsensiGuru::where('user_id', $user->id)
                    ->whereDate('tanggal', '<', $today)
                    ->orderByDesc('tanggal')
                    ->first();
                if ($lastAbsen && $lastAbsen->status === 'sakit') {
                    $sedangMasaSakitIzin = true;
                    $jenisMasaAktif = 'sakit';
                }
            }
        }

        return view('guru.absensi', compact('absensiHariIni', 'riwayat', 'setting', 'sedangMasaSakitIzin', 'jenisMasaAktif'));
    }

    /**
     * Simpan absen datang — dengan validasi geofence.
     */
    public function absenDatang(Request $request)
    {
        $jenis = $request->input('jenis_absen', 'hadir');
        
        $rules = [];
        $messages = [];
        
        if ($jenis === 'hadir') {
            $rules = [
                'latitude'  => 'required|numeric',
                'longitude' => 'required|numeric',
            ];
            $messages = [
                'latitude.required'  => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi.',
                'longitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi.',
            ];
        } elseif ($jenis === 'sakit') {
            $rules = [
                'keterangan' => 'required|string',
                'file_bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];
            $messages = [
                'keterangan.required' => 'Keterangan sakit wajib diisi.',
                'file_bukti.required' => 'File bukti (surat sakit) wajib diunggah.',
                'file_bukti.mimes'    => 'File bukti harus berupa gambar (JPG, PNG) atau PDF.',
                'file_bukti.max'      => 'Ukuran file bukti maksimal 2MB.',
            ];
        } elseif ($jenis === 'izin') {
            $rules = [
                'tanggal_selesai' => 'required|date|after_or_equal:today',
                'keterangan'      => 'required|string',
                'file_bukti'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];
            $messages = [
                'tanggal_selesai.required'       => 'Tanggal selesai izin wajib diisi.',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai izin tidak boleh di masa lalu.',
                'keterangan.required'            => 'Keterangan izin wajib diisi.',
                'file_bukti.required'            => 'File bukti izin wajib diunggah.',
                'file_bukti.mimes'               => 'File bukti harus berupa gambar (JPG, PNG) atau PDF.',
                'file_bukti.max'                 => 'Ukuran file bukti maksimal 2MB.',
            ];
        }
        
        $request->validate($rules, $messages);

        $setting = SchoolSetting::get();
        $user  = Auth::user();
        $today = Carbon::today();

        // Cek Jadwal Buka/Tutup Absen
        [$isOpen, $reason] = $setting->isAbsensiTerbuka('datang', $jenis);
        if (!$isOpen) {
            return back()->with('error', $reason);
        }

        $filePath = null;
        if ($request->hasFile('file_bukti')) {
            $filePath = $request->file('file_bukti')->store('bukti_absen', 'public');
        }

        if ($jenis === 'hadir') {
            $lat = (float) $request->latitude;
            $lng = (float) $request->longitude;

            $jarak = SchoolSetting::hitungJarak($setting->latitude, $setting->longitude, $lat, $lng);

            if ($jarak > $setting->radius_meter) {
                $jarakTampil = number_format($jarak, 0, ',', '.');
                return back()->with('error', "Anda berada di luar area {$setting->nama_sekolah}. Jarak Anda: {$jarakTampil} m (batas: {$setting->radius_meter} m).");
            }
            
            $existing = AbsensiGuru::where('user_id', $user->id)->whereDate('tanggal', $today)->first();
            if ($existing) {
                if (in_array($existing->status, ['sakit', 'izin'])) {
                    $existing->update([
                        'status' => 'hadir',
                        'waktu_datang' => now()->format('H:i:s'),
                        'keterangan' => null,
                        'file_bukti' => null,
                        'status_pengajuan' => null,
                    ]);
                    return back()->with('success', 'Masa Sakit/Izin dihentikan. Absen datang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.');
                }
                return back()->with('error', 'Anda sudah melakukan absen hari ini.');
            }

            AbsensiGuru::create([
                'user_id'      => $user->id,
                'tanggal'      => $today,
                'waktu_datang' => now()->format('H:i:s'),
                'status'       => 'hadir',
            ]);

            return back()->with('success', 'Absen datang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.');
            
        } elseif ($jenis === 'sakit') {
            $existing = AbsensiGuru::where('user_id', $user->id)->whereDate('tanggal', $today)->first();
            if ($existing && $existing->status === 'hadir') {
                return back()->with('error', 'Anda sudah absen hadir hari ini.');
            }

            AbsensiGuru::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $today->toDateString()],
                [
                    'status'           => 'sakit',
                    'keterangan'       => $request->keterangan,
                    'file_bukti'       => $filePath,
                    'status_pengajuan' => 'pending',
                    'is_notified'      => true,
                ]
            );

            return back()->with('success', 'Pengajuan sakit Anda sedang menunggu konfirmasi admin.');
            
        } elseif ($jenis === 'izin') {
            $existing = AbsensiGuru::where('user_id', $user->id)->whereDate('tanggal', $today)->first();
            if ($existing && $existing->status === 'hadir') {
                return back()->with('error', 'Anda sudah absen hadir hari ini.');
            }

            AbsensiGuru::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $today->toDateString()],
                [
                    'tanggal_selesai'  => $request->tanggal_selesai,
                    'status'           => 'izin',
                    'keterangan'       => $request->keterangan,
                    'file_bukti'       => $filePath,
                    'status_pengajuan' => 'pending',
                    'is_notified'      => true,
                ]
            );

            return back()->with('success', "Pengajuan Izin Anda sedang menunggu konfirmasi admin.");
        }
    }

    /**
     * Simpan absen pulang — dengan validasi geofence.
     */
    public function absenPulang(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'latitude.required'  => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi di perangkat Anda.',
            'longitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi di perangkat Anda.',
        ]);

        $setting = SchoolSetting::get();

        // Cek Jadwal Buka/Tutup Absen
        [$isOpen, $reason] = $setting->isAbsensiTerbuka('pulang');
        if (!$isOpen) {
            return back()->with('error', $reason);
        }

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        $jarak = SchoolSetting::hitungJarak($setting->latitude, $setting->longitude, $lat, $lng);

        if ($jarak > $setting->radius_meter) {
            $jarakTampil = number_format($jarak, 0, ',', '.');
            return back()->with('error',
                "Anda berada di luar area {$setting->nama_sekolah}. Jarak Anda: {$jarakTampil} m (batas: {$setting->radius_meter} m)."
            );
        }

        $user  = Auth::user();
        $today = Carbon::today();

        $absensi = AbsensiGuru::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absensi || !$absensi->waktu_datang) {
            return back()->with('error', 'Anda belum melakukan absen datang hari ini.');
        }

        if ($absensi->waktu_pulang) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        $absensi->update([
            'waktu_pulang' => Carbon::now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Absen pulang berhasil dicatat pada pukul ' . Carbon::now()->format('H:i') . ' WITA.');
    }
}
