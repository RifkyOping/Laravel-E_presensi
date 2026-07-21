<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use App\Models\SchoolSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiSiswaController extends Controller
{
    /**
     * Tampilkan halaman absensi + riwayat siswa yang sedang login.
     */
    public function index()
    {
        $today = Carbon::today();
        $user  = Auth::user();

        $absensiHariIni = AbsensiSiswa::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $riwayat = AbsensiSiswa::where('user_id', $user->id)
            ->orderByDesc('tanggal')
            ->take(30)
            ->get();

        // Check for unread approval/rejection notifications
        $notif = AbsensiSiswa::where('user_id', $user->id)
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
                           : 'Pengajuan izin/sakit Anda ditolak oleh Admin, sehingga status Anda menjadi Alpa.',
                'icon'  => $isApproved ? 'success' : 'error'
            ]);
            
            // Mark all as notified to avoid repeated popups
            AbsensiSiswa::where('user_id', $user->id)
                ->where('is_notified', false)
                ->update(['is_notified' => true]);
        }
        
        $sedangMasaSakitIzin = false;
        $jenisMasaAktif = null; // 'sakit' atau 'izin'
        if ($absensiHariIni && in_array($absensiHariIni->status, ['sakit', 'izin'])) {
            $sedangMasaSakitIzin = true;
            $jenisMasaAktif = $absensiHariIni->status;
        } elseif (!$absensiHariIni) {
            $activeIzin = AbsensiSiswa::where('user_id', $user->id)
                ->where('status', 'izin')
                ->where('status_pengajuan', 'approved')
                ->whereDate('tanggal', '<=', $today)
                ->whereDate('tanggal_selesai', '>=', $today)
                ->exists();
            if ($activeIzin) {
                $sedangMasaSakitIzin = true;
                $jenisMasaAktif = 'izin';
            } else {
                $lastAbsen = AbsensiSiswa::where('user_id', $user->id)
                    ->whereDate('tanggal', '<', $today)
                    ->orderByDesc('tanggal')
                    ->first();
                if ($lastAbsen && $lastAbsen->status === 'sakit') {
                    $sedangMasaSakitIzin = true;
                    $jenisMasaAktif = 'sakit';
                }
            }
        }
        
        $setting = SchoolSetting::get();
        $semuaGuru = \App\Models\User::where('role', 'guru')->orderBy('name')->get();

        return view('siswa.absensi', compact('absensiHariIni', 'riwayat', 'setting', 'sedangMasaSakitIzin', 'jenisMasaAktif', 'semuaGuru'));
    }

    /**
     * Catat absen datang (check-in) — dengan validasi geofence.
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
                'guru_id'    => 'required|exists:users,id',
                'keterangan' => 'required|string',
                'file_bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];
            $messages = [
                'guru_id.required'    => 'Silakan pilih guru tujuan.',
                'keterangan.required' => 'Keterangan sakit wajib diisi.',
                'file_bukti.required' => 'File bukti (surat sakit) wajib diunggah.',
                'file_bukti.mimes'    => 'File bukti harus berupa gambar (JPG, PNG) atau PDF.',
                'file_bukti.max'      => 'Ukuran file bukti maksimal 2MB.',
            ];
        } elseif ($jenis === 'izin') {
            $rules = [
                'guru_id'         => 'required|exists:users,id',
                'tanggal_selesai' => 'required|date|after_or_equal:today',
                'keterangan'      => 'required|string',
                'file_bukti'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];
            $messages = [
                'guru_id.required'               => 'Silakan pilih guru tujuan.',
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

            // --- Server-side Fake GPS & Timestamp Validation ---
            if ($request->filled('timestamp')) {
                $clientTimestamp = (int) $request->input('timestamp'); // in milliseconds
                $gpsTime = Carbon::createFromTimestampMs($clientTimestamp);
                if (now()->diffInSeconds($gpsTime) > 300) { // Lebih dari 5 menit
                    return back()->with('error', 'Waktu lokasi tidak valid atau kadaluarsa. Silakan refresh dan coba lagi.');
                }
            }

            if ($request->filled('accuracy')) {
                $acc = (float) $request->input('accuracy');
                $speed = (float) $request->input('speed');
                
                $isRoundAccuracy = floor($acc) == $acc && ($acc % 10 === 0 || $acc == 65);
                if ($isRoundAccuracy || $acc < 5 || ($speed > 0)) {
                    return back()->with('error', 'Terdeteksi manipulasi lokasi (Fake GPS) dari server.');
                }
            }

            $jarak = SchoolSetting::hitungJarak($setting->latitude, $setting->longitude, $lat, $lng);

            if ($jarak > $setting->radius_meter) {
                $jarakTampil = number_format($jarak, 0, ',', '.');
                return back()->with('error', "Anda berada di luar area {$setting->nama_sekolah}. Jarak Anda: {$jarakTampil} m (batas: {$setting->radius_meter} m).");
            }
            
            $existing = AbsensiSiswa::where('user_id', $user->id)->whereDate('tanggal', $today)->first();
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

            AbsensiSiswa::create([
                'user_id'      => $user->id,
                'tanggal'      => $today,
                'waktu_datang' => now()->format('H:i:s'),
                'status'       => 'hadir',
            ]);

            return back()->with('success', 'Absen datang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.');
            
        } elseif ($jenis === 'sakit') {
            $existing = AbsensiSiswa::where('user_id', $user->id)->whereDate('tanggal', $today)->first();

            AbsensiSiswa::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $today->toDateString()],
                [
                    'guru_id'          => $request->guru_id,
                    'status'           => 'sakit',
                    'keterangan'       => $request->keterangan,
                    'file_bukti'       => $filePath,
                    'status_pengajuan' => 'pending',
                    'is_notified'      => true,
                ]
            );

            return back()->with('success', 'Pengajuan sakit Anda sedang menunggu konfirmasi guru.');
            
        } elseif ($jenis === 'izin') {
            $existing = AbsensiSiswa::where('user_id', $user->id)->whereDate('tanggal', $today)->first();

            AbsensiSiswa::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $today->toDateString()],
                [
                    'guru_id'          => $request->guru_id,
                    'tanggal_selesai'  => $request->tanggal_selesai,
                    'status'           => 'izin',
                    'keterangan'       => $request->keterangan,
                    'file_bukti'       => $filePath,
                    'status_pengajuan' => 'pending',
                    'is_notified'      => true,
                ]
            );

            return back()->with('success', "Pengajuan Izin Anda sedang menunggu konfirmasi guru.");
        }
    }

    /**
     * Catat absen pulang (check-out) — dengan validasi geofence.
     */
    public function absenPulang(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'latitude.required'  => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi.',
            'longitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi.',
        ]);

        $setting = SchoolSetting::get();

        // Cek Jadwal Buka/Tutup Absen
        [$isOpen, $reason] = $setting->isAbsensiTerbuka('pulang');
        if (!$isOpen) {
            return redirect()->route('absensi')->with('error', $reason);
        }

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // --- Server-side Fake GPS & Timestamp Validation ---
        if ($request->filled('timestamp')) {
            $clientTimestamp = (int) $request->input('timestamp'); // in milliseconds
            $gpsTime = Carbon::createFromTimestampMs($clientTimestamp);
            if (now()->diffInSeconds($gpsTime) > 300) { // Lebih dari 5 menit
                return redirect()->route('absensi')->with('error', 'Waktu lokasi tidak valid atau kadaluarsa. Silakan refresh dan coba lagi.');
            }
        }

        if ($request->filled('accuracy')) {
            $acc = (float) $request->input('accuracy');
            $speed = (float) $request->input('speed');
            
            $isRoundAccuracy = floor($acc) == $acc && ($acc % 10 === 0 || $acc == 65);
            if ($isRoundAccuracy || $acc < 5 || ($speed > 0)) {
                return redirect()->route('absensi')->with('error', 'Terdeteksi manipulasi lokasi (Fake GPS) dari server.');
            }
        }

        $jarak = SchoolSetting::hitungJarak($setting->latitude, $setting->longitude, $lat, $lng);

        if ($jarak > $setting->radius_meter) {
            $jarakTampil = number_format($jarak, 0, ',', '.');
            return redirect()->route('absensi')
                ->with('error', "Anda berada di luar area {$setting->nama_sekolah}. Jarak Anda: {$jarakTampil} m (batas: {$setting->radius_meter} m).");
        }

        $today = Carbon::today();
        $user  = Auth::user();

        $existing = AbsensiSiswa::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$existing) {
            return redirect()->route('absensi')
                ->with('error', 'Anda belum melakukan absen datang hari ini.');
        }

        if ($existing->waktu_pulang) {
            return redirect()->route('absensi')
                ->with('error', 'Anda sudah melakukan absen pulang hari ini pukul '
                    . Carbon::parse($existing->waktu_pulang)->format('H:i') . ' WITA.');
        }

        $existing->update([
            'waktu_pulang' => now()->format('H:i:s'),
        ]);

        return redirect()->route('absensi')
            ->with('success', 'Absen pulang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.');
    }

    /**
     * Tampilkan riwayat sholat siswa.
     */
    public function riwayatSholat()
    {
        $riwayatSholat = \App\Models\AbsensiSholatSiswa::where('user_id', Auth::id())
            ->orderByDesc('tanggal')
            ->paginate(20);

        return view('siswa.sholat', compact('riwayatSholat'));
    }
}
