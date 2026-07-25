<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JawabanIndikator;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewIndikatorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_guru_bahasa) {
            abort(403, 'Anda tidak memiliki akses sebagai Guru Bahasa Indonesia.');
        }

        $kelasList = Kelas::where('status', true)->orderBy('tingkat')->orderBy('jurusan')->orderBy('rombel')->get();

        $query = JawabanIndikator::with(['user.siswaProfile', 'indikator', 'user.progresEbook'])
            ->join('users', 'jawaban_indikators.user_id', '=', 'users.id')
            ->join('siswa_profiles', 'users.id', '=', 'siswa_profiles.user_id')
            ->select('jawaban_indikators.*');

        if ($request->filled('kelas_id')) {
            $kelas = Kelas::find($request->kelas_id);
            if ($kelas) {
                $query->where('siswa_profiles.kelas', $kelas->tingkat)
                      ->where('siswa_profiles.jurusan', $kelas->jurusan)
                      ->where('siswa_profiles.rombel', $kelas->rombel);
            }
        }

        // Ambil data terbaru dan kelompokkan per siswa per buku jika perlu, 
        // tapi karena ini melihat jawaban per indikator, kita bisa tampilkan list jawaban.
        // Untuk mempermudah, lebih baik dikelompokkan berdasarkan user dan buku.
        $jawabans = $query->orderBy('jawaban_indikators.created_at', 'desc')->paginate(20);

        // Group by user and book id
        $groupedJawabans = $jawabans->groupBy(function($item) {
            return $item->user_id . '-' . $item->buku_id . '-' . $item->jenis_buku;
        });

        return view('guru.literasi.jawaban-indikator', compact('jawabans', 'groupedJawabans', 'kelasList'));
    }

    public function storeNilai(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_guru_bahasa) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'buku_id' => 'required',
            'jenis_buku' => 'required',
            'nilai_guru' => 'required|array',
            'catatan_guru' => 'required|array',
        ]);

        foreach ($request->nilai_guru as $indikator_id => $nilai) {
            JawabanIndikator::where('user_id', $request->user_id)
                ->where('buku_id', $request->buku_id)
                ->where('jenis_buku', $request->jenis_buku)
                ->where('indikator_id', $indikator_id)
                ->update([
                    'nilai_guru' => $nilai,
                    'catatan_guru' => $request->catatan_guru[$indikator_id] ?? null,
                ]);
        }

        return back()->with('success', 'Penilaian berhasil disimpan.');
    }
}
