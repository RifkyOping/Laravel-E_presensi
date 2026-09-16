<?php

namespace App\Http\Controllers\Staf;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiMengajar;
use Carbon\Carbon;

class StafController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        // Aktivitas mengajar hari ini yang belum diverifikasi
        $belumVerifikasi = AbsensiMengajar::whereDate('tanggal', $today)
            ->whereNull('verified_at')
            ->count();

        // Aktivitas mengajar hari ini yang sudah diverifikasi
        $sudahVerifikasi = AbsensiMengajar::whereDate('tanggal', $today)
            ->whereNotNull('verified_at')
            ->count();

        // Guru yang sudah absen datang hari ini
        $guruHadir = AbsensiGuru::whereDate('tanggal', $today)
            ->whereNotNull('waktu_datang')
            ->where(function ($q) {
                $q->whereNull('status_pengajuan')
                  ->orWhere('status_pengajuan', '!=', 'pending');
            })
            ->count();

        // 5 aktivitas mengajar terbaru hari ini
        $aktivitasTerbaru = AbsensiMengajar::with('user', 'verifier')
            ->whereDate('tanggal', $today)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('staf.dashboard', compact(
            'belumVerifikasi',
            'sudahVerifikasi',
            'guruHadir',
            'aktivitasTerbaru',
            'today'
        ));
    }
}
