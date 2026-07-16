<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\CatatanMembaca;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanLiterasiController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua catatan, dikelompokkan berdasarkan siswa
        $query = CatatanMembaca::with('user')
            ->orderBy('updated_at', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_buku', $request->jenis);
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

        return view('guru.literasi.catatan', compact('catatans'));
    }
}
