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
        // Daftar kelas dari tabel kelas yang aktif
        $kelasList = \App\Models\Kelas::where('status', true)
            ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();

        $jurusanList = $kelasList->pluck('jurusan')->unique()->filter()->values();
        $rombelList  = $kelasList->pluck('rombel')->unique()->filter()->values();

        $siswaList        = collect();
        $selectedKelas    = $request->input('kelas');
        $selectedJurusan  = $request->input('jurusan');
        $selectedRombel   = $request->input('rombel');
        $selectedKelasId  = null;

        if ($selectedKelas || $selectedJurusan || $selectedRombel) {
            $query = User::where('role', 'murid')
                ->whereHas('siswaProfile', function ($q) use ($selectedKelas, $selectedJurusan, $selectedRombel) {
                    if ($selectedKelas)   $q->where('kelas', $selectedKelas);
                    if ($selectedJurusan) $q->where('jurusan', $selectedJurusan);
                    if ($selectedRombel)  $q->where('rombel', $selectedRombel);
                })
                ->orderBy('name')
                ->with(['siswaProfile', 'catatanQuran' => function ($q) {
                    $q->orderByDesc('created_at');
                }]);

            $siswaList = $query->get();
        }

        return view('guru.literasi_quran.index', compact(
            'kelasList',
            'jurusanList',
            'rombelList',
            'siswaList',
            'selectedKelas',
            'selectedJurusan',
            'selectedRombel',
            'selectedKelasId'
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
