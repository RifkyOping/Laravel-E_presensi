<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\CatatanLiterasiQuran;
use Illuminate\Support\Facades\Auth;

class SiswaLiterasiQuranController extends Controller
{
    public function index()
    {
        $siswa = Auth::user();

        // Ambil semua catatan untuk siswa ini, grup per jenis
        $catatan = CatatanLiterasiQuran::with('guru')
            ->where('siswa_id', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCatatan = CatatanLiterasiQuran::where('siswa_id', $siswa->id)->count();

        return view('siswa.literasi_quran_siswa', compact('siswa', 'catatan', 'totalCatatan'));
    }
}
