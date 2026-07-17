<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\CatatanMembaca;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanLiterasiController extends Controller
{
    public function index(Request $request)
    {
        // Daftar kelas untuk filter
        $kelasList = Kelas::where('status', true)
            ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();

        // Ambil semua catatan, dikelompokkan berdasarkan siswa
        $query = CatatanMembaca::with('user.siswaProfile')
            ->orderBy('updated_at', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_buku', $request->jenis);
        }

        if ($request->filled('kelas_id')) {
            $kelas = Kelas::find($request->kelas_id);
            if ($kelas) {
                $query->whereHas('user.siswaProfile', function ($q) use ($kelas) {
                    $q->where('kelas', $kelas->tingkat)
                      ->where('jurusan', $kelas->jurusan)
                      ->where('rombel', $kelas->rombel);
                });
            }
        }

        $catatans = $query->paginate(20)->withQueryString();

        // Resolve judul buku
        $catatans->getCollection()->transform(function ($catatan) {
            if ($catatan->jenis_buku === 'digital') {
                $buku = \App\Models\EBook::find($catatan->buku_id);
                $catatan->judul_buku = $buku ? $buku->judul : '(Buku tidak ditemukan)';
            } else {
                $buku = \App\Models\BukuManual::find($catatan->buku_id);
                $catatan->judul_buku = $buku ? $buku->judul : '(Buku tidak ditemukan)';
            }
            return $catatan;
        });
        
        $selectedKelasId = $request->kelas_id;

        return view('guru.literasi.catatan', compact(
            'catatans',
            'kelasList',
            'selectedKelasId'
        ));
    }
}
