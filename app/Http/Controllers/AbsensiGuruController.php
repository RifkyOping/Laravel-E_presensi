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
        $user = Auth::user();
        $today = Carbon::today();

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
                'text' => $isApproved
                    ? 'Pengajuan izin/sakit Anda telah disetujui.'
                    : 'Pengajuan izin/sakit Anda ditolak, sehingga status Anda menjadi Alpa.' . ($notif->alasan_ditolak ? ' Alasan penolakan: ' . $notif->alasan_ditolak : ''),
                'icon' => $isApproved ? 'success' : 'error'
            ]);

            // Mark all as notified to avoid repeated popups
            AbsensiGuru::where('user_id', $user->id)
                ->where('is_notified', false)
                ->update(['is_notified' => true]);
        }
        $sedangMasaCutiTugas = false;
        $jenisMasaAktif = null; // 'cuti' atau 'tugas'
        if ($absensiHariIni && in_array($absensiHariIni->status, ['cuti', 'tugas']) && $absensiHariIni->status_pengajuan === 'approved') {
            $sedangMasaCutiTugas = true;
            $jenisMasaAktif = $absensiHariIni->status;
        } elseif (!$absensiHariIni) {
            $activeCuti = AbsensiGuru::where('user_id', $user->id)
                ->whereIn('status', ['cuti', 'tugas'])
                ->where('status_pengajuan', 'approved')
                ->whereDate('tanggal', '<=', $today)
                ->whereDate('tanggal_selesai', '>=', $today)
                ->first();
            if ($activeCuti) {
                $sedangMasaCutiTugas = true;
                $jenisMasaAktif = $activeCuti->status;
            }
        }

        return view('guru.absensi', compact('absensiHariIni', 'riwayat', 'setting', 'sedangMasaCutiTugas', 'jenisMasaAktif'));
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
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ];
            $messages = [
                'latitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi.',
                'longitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi.',
            ];
        } elseif (in_array($jenis, ['cuti', 'tugas'])) {
            $rules = [
                'judul_pengajuan' => 'required|string|max:255',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'keterangan' => 'required|string',
                'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ];
            $messages = [
                'judul_pengajuan.required' => 'Judul pengajuan wajib diisi.',
                'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
                'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
                'keterangan.required' => 'Keterangan/kepentingan wajib diisi.',
                'file_bukti.mimes' => 'Dokumen harus berupa gambar (JPG, PNG) atau PDF.',
                'file_bukti.max' => 'Ukuran dokumen maksimal 5MB.',
            ];
        }

        $request->validate($rules, $messages);

        $setting = SchoolSetting::get();
        $user = Auth::user();
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

                $isRoundAccuracy = floor($acc) == $acc && ($acc % 10 === 0 || $acc == 65);
                if ($isRoundAccuracy || $acc < 5) {
                    return back()->with('error', 'Terdeteksi manipulasi lokasi (Fake GPS) dari server.');
                }
            }

            $jarak = SchoolSetting::hitungJarak($setting->latitude, $setting->longitude, $lat, $lng);

            if ($jarak > $setting->radius_meter) {
                $jarakTampil = number_format($jarak, 0, ',', '.');
                return back()->with('error', "Anda berada di luar area {$setting->nama_sekolah}. Jarak Anda: {$jarakTampil} m (batas: {$setting->radius_meter} m).");
            }

            $kategori = 'tepat waktu';
            $hariMap = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'
            ];
            $hariIni = $hariMap[Carbon::now()->format('l')] ?? 'Senin';
            $jadwal = \App\Models\JadwalAbsensi::where('hari', $hariIni)->first();
            if ($jadwal && now()->format('H:i:s') > Carbon::parse($jadwal->batas_waktu_terlambat)->format('H:i:s')) {
                $kategori = 'terlambat';
            }

            $existing = AbsensiGuru::where('user_id', $user->id)->whereDate('tanggal', $today)->first();
            if ($existing) {
                if (in_array($existing->status, ['cuti', 'tugas']) || $existing->status_pengajuan === 'rejected' || !$existing->waktu_datang) {
                    $pesan = ($existing->status_pengajuan === 'rejected')
                        ? 'Absen datang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.'
                        : (in_array($existing->status, ['cuti', 'tugas'])
                            ? 'Masa Cuti/Tugas dihentikan. Absen datang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.'
                            : 'Absen datang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.');

                    $existing->update([
                        'status' => 'hadir',
                        'waktu_datang' => now()->format('H:i:s'),
                        'kategori' => $kategori,
                        'keterangan' => null,
                        'file_bukti' => null,
                        'status_pengajuan' => null,
                        'judul_pengajuan' => null,
                        'alasan_ditolak' => null,
                    ]);
                    return back()->with('success', $pesan);
                }
                return back()->with('error', 'Anda sudah melakukan absen hari ini.');
            }

            AbsensiGuru::create([
                'user_id' => $user->id,
                'tanggal' => $today,
                'waktu_datang' => now()->format('H:i:s'),
                'status' => 'hadir',
                'kategori' => $kategori,
            ]);

            return back()->with('success', 'Absen datang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.');

        } elseif (in_array($jenis, ['cuti', 'tugas'])) {
            $existing = AbsensiGuru::where('user_id', $user->id)->whereDate('tanggal', $today)->first();

            $tanggalMulai = $request->tanggal_mulai;
            $tanggalSelesai = $request->tanggal_selesai;

            AbsensiGuru::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $tanggalMulai],
                [
                    'tanggal_selesai' => $tanggalSelesai,
                    'status' => $jenis,
                    'judul_pengajuan' => $request->judul_pengajuan,
                    'keterangan' => $request->keterangan,
                    'file_bukti' => $filePath,
                    'status_pengajuan' => 'pending',
                    'is_notified' => true,
                ]
            );

            $label = $jenis === 'cuti' ? 'Cuti' : 'Tugas';
            return back()->with('success', "Pengajuan {$label} Anda sedang menunggu konfirmasi admin.");
        }
    }

    /**
     * Simpan absen pulang — dengan validasi geofence.
     */
    public function absenPulang(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'latitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi di perangkat Anda.',
            'longitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi di perangkat Anda.',
        ]);

        $setting = SchoolSetting::get();

        // Cek Jadwal Buka/Tutup Absen
        [$isOpen, $reason] = $setting->isAbsensiTerbuka('pulang', 'hadir');
        if (!$isOpen) {
            return back()->with('error', $reason);
        }

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

            $isRoundAccuracy = floor($acc) == $acc && ($acc % 10 === 0 || $acc == 65);
            if ($isRoundAccuracy || $acc < 5) {
                return back()->with('error', 'Terdeteksi manipulasi lokasi (Fake GPS) dari server.');
            }
        }

        $jarak = SchoolSetting::hitungJarak($setting->latitude, $setting->longitude, $lat, $lng);

        if ($jarak > $setting->radius_meter) {
            $jarakTampil = number_format($jarak, 0, ',', '.');
            return back()->with(
                'error',
                "Anda berada di luar area {$setting->nama_sekolah}. Jarak Anda: {$jarakTampil} m (batas: {$setting->radius_meter} m)."
            );
        }

        $user = Auth::user();
        $today = Carbon::today();

        $absensi = AbsensiGuru::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($absensi && $absensi->waktu_pulang) {
            return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        $hariMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        $hariIni = $hariMap[Carbon::now()->format('l')] ?? 'Senin';
        $jadwal = \App\Models\JadwalAbsensi::where('hari', $hariIni)->first();

        if (!$absensi) {
            // Lupa / belum absen datang, langsung absen pulang
            $kategori = 'terlambat';
            if ($jadwal && now()->format('H:i:s') < Carbon::parse($jadwal->batas_pulang_cepat)->format('H:i:s')) {
                $kategori = 'terlambat dan pulang lebih awal';
            }

            AbsensiGuru::create([
                'user_id' => $user->id,
                'tanggal' => $today,
                'waktu_pulang' => Carbon::now()->format('H:i:s'),
                'status' => 'hadir',
                'kategori' => $kategori,
            ]);

            return back()->with('success', 'Absen pulang berhasil dicatat pada pukul ' . Carbon::now()->format('H:i') . ' WITA.');
        }

        if (!$absensi->waktu_datang) {
            // Sudah ada record (misal izin/cuti ditolak atau dibuat tanpa jam datang), langsung catat pulang
            $kategori = 'terlambat';
            if ($jadwal && now()->format('H:i:s') < Carbon::parse($jadwal->batas_pulang_cepat)->format('H:i:s')) {
                $kategori = 'terlambat dan pulang lebih awal';
            }

            $absensi->update([
                'status' => 'hadir',
                'waktu_pulang' => Carbon::now()->format('H:i:s'),
                'kategori' => $kategori,
                'status_pengajuan' => null,
                'alasan_ditolak' => null,
                'keterangan' => null,
                'file_bukti' => null,
                'judul_pengajuan' => null,
            ]);

            return back()->with('success', 'Absen pulang berhasil dicatat pada pukul ' . Carbon::now()->format('H:i') . ' WITA.');
        }

        // Sudah ada waktu datang normal
        $kategori = $absensi->kategori ?? 'tepat waktu';
        if ($jadwal && now()->format('H:i:s') < Carbon::parse($jadwal->batas_pulang_cepat)->format('H:i:s')) {
            if (str_contains(strtolower($kategori), 'terlambat')) {
                $kategori = 'terlambat dan pulang lebih awal';
            } else {
                $kategori = 'pulang lebih awal';
            }
        }

        $absensi->update([
            'waktu_pulang' => Carbon::now()->format('H:i:s'),
            'kategori' => $kategori,
        ]);

        return back()->with('success', 'Absen pulang berhasil dicatat pada pukul ' . Carbon::now()->format('H:i') . ' WITA.');
    }
}
