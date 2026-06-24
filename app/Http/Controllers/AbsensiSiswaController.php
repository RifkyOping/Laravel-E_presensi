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

        $setting = SchoolSetting::get();

        return view('siswa.absensi', compact('absensiHariIni', 'riwayat', 'setting'));
    }

    /**
     * Catat absen datang (check-in) — dengan validasi geofence.
     */
    public function absenDatang(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'latitude.required'  => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi.',
            'longitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi.',
        ]);

        $setting = SchoolSetting::get();
        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // Hitung jarak dari sekolah
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

        if ($existing) {
            return redirect()->route('absensi')
                ->with('error', 'Anda sudah melakukan absen datang hari ini pukul '
                    . Carbon::parse($existing->waktu_datang)->format('H:i') . ' WITA.');
        }

        AbsensiSiswa::create([
            'user_id'      => $user->id,
            'tanggal'      => $today,
            'waktu_datang' => now()->format('H:i:s'),
            'status'       => 'hadir',
        ]);

        return redirect()->route('absensi')
            ->with('success', 'Absen datang berhasil dicatat pukul ' . now()->format('H:i') . ' WITA.');
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
        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

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
}
