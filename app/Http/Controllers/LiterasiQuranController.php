<?php

namespace App\Http\Controllers;

use App\Models\CatatanLiterasiQuran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiterasiQuranController extends Controller
{
    /**
     * Halaman utama literasi Al-Quran — filter kelas & jurusan.
     */
    public function index(Request $request)
    {
        // Daftar kelas & jurusan unik dari tabel siswa_profiles
        $kelasList   = \App\Models\SiswaProfile::whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas')
            ->sort()
            ->values();

        $jurusanList = \App\Models\SiswaProfile::whereNotNull('jurusan')
            ->distinct()
            ->pluck('jurusan')
            ->sort()
            ->values();

        $siswaList = collect();
        $selectedKelas   = $request->input('kelas');
        $selectedJurusan = $request->input('jurusan');

        if ($selectedKelas && $selectedJurusan) {
            $siswaList = User::where('role', 'siswa')
                ->whereHas('siswaProfile', function ($q) use ($selectedKelas, $selectedJurusan) {
                    $q->where('kelas', $selectedKelas)
                      ->where('jurusan', $selectedJurusan);
                })
                ->orderBy('name')
                ->with(['siswaProfile', 'catatanQuran' => function ($q) {
                    $q->orderByDesc('created_at');
                }])
                ->get();
        }

        return view('guru.literasi_quran.index', compact(
            'kelasList',
            'jurusanList',
            'siswaList',
            'selectedKelas',
            'selectedJurusan'
        ));
    }

    /**
     * Simpan catatan baru untuk siswa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:users,id',
            'catatan'  => 'required|string|max:1000',
        ]);

        CatatanLiterasiQuran::create([
            'siswa_id' => $request->siswa_id,
            'guru_id'  => Auth::id(),
            'catatan'  => $request->catatan,
        ]);

        return back()->with('success', 'Catatan berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit catatan.
     */
    public function edit(CatatanLiterasiQuran $catatan)
    {
        $this->authorize('update', $catatan);
        return response()->json($catatan);
    }

    /**
     * Update catatan.
     */
    public function update(Request $request, CatatanLiterasiQuran $catatan)
    {
        // Hanya guru yang membuat catatan yang bisa edit
        if ($catatan->guru_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        $catatan->update([
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    /**
     * Hapus catatan.
     */
    public function destroy(CatatanLiterasiQuran $catatan)
    {
        if ($catatan->guru_id !== Auth::id()) {
            abort(403);
        }
        $catatan->delete();
        return back()->with('success', 'Catatan berhasil dihapus.');
    }
}
