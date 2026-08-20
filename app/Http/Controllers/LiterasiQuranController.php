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
        $cacheKey = 'guru_lit_quran_base';
        $kelasList = \Illuminate\Support\Facades\Cache::remember($cacheKey, 43200, function() {
            return \App\Models\Kelas::where('status', true)
                ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
                ->orderBy('jurusan')
                ->orderBy('rombel')
                ->get();
        });

        $siswaList = collect();
        $selectedKelasId = $request->input('kelas_id');
        $selectedKelasModel = null;

        if ($selectedKelasId) {
            $cacheKeyData = 'guru_lit_quran_data_' . $selectedKelasId;
            $data = \Illuminate\Support\Facades\Cache::remember($cacheKeyData, 43200, function() use ($selectedKelasId) {
                $selectedKelasModel = \App\Models\Kelas::find($selectedKelasId);
                $siswaList = collect();
                
                if ($selectedKelasModel) {
                    $query = User::where('role', 'murid')
                        ->whereHas('siswaProfile', function ($q) use ($selectedKelasModel) {
                            $q->where('kelas', $selectedKelasModel->tingkat)
                              ->where('jurusan', $selectedKelasModel->jurusan)
                              ->where('rombel', $selectedKelasModel->rombel);
                        })
                        ->orderBy('name')
                        ->with(['siswaProfile', 'catatanQuran' => function ($q) {
                            $q->orderByDesc('created_at');
                        }]);

                    $siswaList = $query->get();
                }
                return compact('selectedKelasModel', 'siswaList');
            });
            extract($data);
        }

        return view('guru.literasi_quran.index', compact(
            'kelasList',
            'siswaList',
            'selectedKelasId',
            'selectedKelasModel'
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

        $this->invalidateCacheForSiswa($request->siswa_id);

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

        $this->invalidateCacheForSiswa($catatan->siswa_id);

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
        $siswaId = $catatan->siswa_id;
        $catatan->delete();
        $this->invalidateCacheForSiswa($siswaId);
        return back()->with('success', 'Catatan berhasil dihapus.');
    }

    private function invalidateCacheForSiswa($siswaId)
    {
        // Invalidate Murid's personal cache
        \Illuminate\Support\Facades\Cache::forget('siswa_lit_quran_' . $siswaId);

        $siswa = User::with('siswaProfile')->find($siswaId);
        if($siswa && $siswa->siswaProfile) {
            $kelasModel = \App\Models\Kelas::where('tingkat', $siswa->siswaProfile->kelas)
                ->where('jurusan', $siswa->siswaProfile->jurusan)
                ->where('rombel', $siswa->siswaProfile->rombel)
                ->first();
            if($kelasModel) {
                \Illuminate\Support\Facades\Cache::forget('guru_lit_quran_data_' . $kelasModel->id);
            }
        }
    }
}
