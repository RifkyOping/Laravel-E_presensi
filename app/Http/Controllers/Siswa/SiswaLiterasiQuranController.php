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

        $cacheKey = 'siswa_lit_quran_' . $siswa->id;
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 43200, function() use ($siswa) {
            $catatan = CatatanLiterasiQuran::with('guru')
                ->where('siswa_id', $siswa->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalCatatan = CatatanLiterasiQuran::where('siswa_id', $siswa->id)->count();
            return compact('catatan', 'totalCatatan');
        });
        extract($data);

        return view('siswa.literasi_quran_siswa', compact('siswa', 'catatan', 'totalCatatan'));
    }
}
