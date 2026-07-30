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
                           ? 'Pengajuan izin/sakit Anda telah disetujui.' 
                           : 'Pengajuan izin/sakit Anda ditolak, sehingga status Anda menjadi Alpa.' . ($notif->alasan_ditolak ? ' Alasan penolakan: ' . $notif->alasan_ditolak : ''),
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
        } elseif (in_array($jenis, ['sakit', 'izin'])) {
            $rules = [
                'guru_id'         => 'required|exists:users,id',
                'tanggal_mulai'   => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'keterangan'      => 'required|string',
                'file_bukti'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];
            $messages = [
                'guru_id.required'               => 'Silakan pilih guru tujuan.',
                'tanggal_mulai.required'         => 'Tanggal mulai wajib diisi.',
                'tanggal_selesai.required'       => 'Tanggal selesai wajib diisi.',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
                'keterangan.required'            => 'Keterangan wajib diisi.',
                'file_bukti.required'            => 'File bukti wajib diunggah.',
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
            
        } elseif (in_array($jenis, ['sakit', 'izin'])) {
            $tanggalMulai = $request->tanggal_mulai;
            AbsensiSiswa::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $tanggalMulai],
                [
                    'guru_id'          => $request->guru_id,
                    'tanggal_selesai'  => $request->tanggal_selesai,
                    'status'           => $jenis,
                    'keterangan'       => $request->keterangan,
                    'file_bukti'       => $filePath,
                    'status_pengajuan' => 'pending',
                    'is_notified'      => true,
                ]
            );

            return back()->with('success', 'Pengajuan ' . ucfirst($jenis) . ' Anda sedang menunggu konfirmasi guru.');
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
            $existing = AbsensiSiswa::create([
                'user_id' => $user->id,
                'tanggal' => $today,
                'status'  => 'hadir',
                'kategori'=> 'bolos',
            ]);
        } elseif (!$existing->waktu_datang) {
            $existing->status = 'hadir';
            $existing->kategori = 'bolos';
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

    /**
     * Tampilkan halaman jadwal & monitoring kelas murid
     */
    public function monitoringKelas(Request $request)
    {
        $user = Auth::user();
        $profile = $user->siswaProfile;
        
        if (!$profile) {
            return redirect()->route('murid.dashboard')->with('error', 'Profil siswa tidak lengkap.');
        }

        $kelasStr = trim("{$profile->kelas} {$profile->jurusan} {$profile->rombel}");

        $jadwalList = \App\Models\JadwalMengajar::with('user')
            ->where('kelas', $kelasStr)
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        $hariIniStr = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ][now()->format('l')] ?? 'Senin';
        
        $activeTab = $request->query('hari', $hariIniStr);
        $today = Carbon::today()->toDateString();

        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        // Get this week's active AbsensiMengajar for this class
        $absensiMengajar = \App\Models\AbsensiMengajar::whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->where('kelas', $kelasStr)
            ->get()
            ->keyBy(function($item) {
                $dayName = [
                    'Monday'    => 'Senin',
                    'Tuesday'   => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday'  => 'Kamis',
                    'Friday'    => 'Jumat',
                    'Saturday'  => 'Sabtu',
                    'Sunday'    => 'Minggu'
                ][\Carbon\Carbon::parse($item->tanggal)->format('l')] ?? 'Senin';
                
                return $dayName . '_' . $item->user_id . '_' . $item->mata_pelajaran . '_' . $item->jam_ke;
            });

        // Get student's class attendance for this week
        $absensiKelas = \App\Models\AbsensiKelasSiswa::whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->where('siswa_id', $user->id)
            ->get()
            ->keyBy(function($item) {
                $dayName = [
                    'Monday'    => 'Senin',
                    'Tuesday'   => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday'  => 'Kamis',
                    'Friday'    => 'Jumat',
                    'Saturday'  => 'Sabtu',
                    'Sunday'    => 'Minggu'
                ][\Carbon\Carbon::parse($item->tanggal)->format('l')] ?? 'Senin';
                
                return $dayName . '_' . $item->jadwal_mengajar_id;
            });

        return view('siswa.monitoring-kelas', compact('jadwalList', 'activeTab', 'hariIniStr', 'absensiMengajar', 'absensiKelas'));
    }
}
