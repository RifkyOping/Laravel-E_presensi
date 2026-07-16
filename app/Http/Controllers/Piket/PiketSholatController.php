<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSholatSiswa;
use App\Models\User;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PiketSholatController extends Controller
{
    public function index(Request $request)
    {
        // Daftar kelas dari tabel kelas yang aktif
        $kelasList = \App\Models\Kelas::where('status', true)
            ->orderByRaw("FIELD(tingkat,'X','XI','XII')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();

        $tanggal         = $request->filled('tanggal') ? Carbon::parse($request->tanggal) : Carbon::today();
        $selectedKelasId = $request->input('kelas_id');
        $selectedKelas   = null;
        $siswas          = collect();
        $absensi         = collect();

        if ($selectedKelasId) {
            $selectedKelas = \App\Models\Kelas::find($selectedKelasId);
        }

        if ($selectedKelas) {
            $tingkat = $selectedKelas->tingkat;
            $jurusan = $selectedKelas->jurusan;
            $rombel  = $selectedKelas->rombel;

            $siswas = User::where('role', 'murid')
                ->whereHas('siswaProfile', function ($q) use ($tingkat, $jurusan, $rombel) {
                    $q->where('kelas', $tingkat)
                      ->where('jurusan', $jurusan)
                      ->where('rombel', $rombel);
                })
                ->with('siswaProfile')
                ->orderBy('name')
                ->get();

            $absensi = AbsensiSholatSiswa::where('tanggal', $tanggal->format('Y-m-d'))
                ->whereIn('user_id', $siswas->pluck('id'))
                ->get()
                ->keyBy('user_id');
        }

        return view('piket.sholat.index', compact('kelasList', 'tanggal', 'selectedKelasId', 'selectedKelas', 'siswas', 'absensi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'in:sholat,tidak_sholat,udzur',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $pencatatId = Auth::id();

        foreach ($request->status as $userId => $status) {
            AbsensiSholatSiswa::updateOrCreate(
                [
                    'user_id' => $userId,
                    'tanggal' => $tanggal,
                ],
                [
                    'status' => $status,
                    'pencatat_id' => $pencatatId,
                ]
            );
        }

        return back()->with('success', 'Data absensi sholat berhasil disimpan.');
    }
}
