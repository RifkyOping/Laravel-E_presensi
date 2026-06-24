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

        return view('guru.absensi', compact('absensiHariIni', 'riwayat', 'setting'));
    }

    /**
     * Simpan absen datang — dengan validasi geofence.
     */
    public function absenDatang(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ], [
            'latitude.required'  => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi di perangkat Anda.',
            'longitude.required' => 'Lokasi GPS tidak terdeteksi. Aktifkan izin lokasi di perangkat Anda.',
        ]);

        $setting = SchoolSetting::get();
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

        $existing = AbsensiGuru::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing && $existing->waktu_datang) {
            return back()->with('error', 'Anda sudah melakukan absen datang hari ini.');
        }

        AbsensiGuru::updateOrCreate(
            ['user_id' => $user->id, 'tanggal' => $today->toDateString()],
            [
                'waktu_datang' => Carbon::now()->format('H:i:s'),
                'status'       => 'hadir',
            ]
        );

        return back()->with('success', 'Absen datang berhasil dicatat pada pukul ' . Carbon::now()->format('H:i') . ' WITA.');
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
