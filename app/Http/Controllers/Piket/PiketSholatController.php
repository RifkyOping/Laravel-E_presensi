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
                      ->where('rombel', $rombel)
                      ->whereRaw('LOWER(agama) = ?', ['islam']);
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

    public function export(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
            'kelas_id'      => 'required|exists:kelas,id',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir);
        $delimiter    = $request->input('delimiter', ';');

        if ($tanggalMulai->gt($tanggalAkhir)) {
            return back()->with('error', 'Tanggal mulai tidak boleh lebih dari tanggal akhir.');
        }

        $kelas = \App\Models\Kelas::findOrFail($request->kelas_id);

        $siswas = User::where('role', 'murid')
            ->whereHas('siswaProfile', function ($q) use ($kelas) {
                $q->where('kelas', $kelas->tingkat)
                  ->where('jurusan', $kelas->jurusan)
                  ->where('rombel', $kelas->rombel)
                  ->whereRaw('LOWER(agama) = ?', ['islam']);
            })
            ->with('siswaProfile')
            ->orderBy('name')
            ->get();

        $absensi = AbsensiSholatSiswa::whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->whereIn('user_id', $siswas->pluck('id'))
            ->orderBy('tanggal')
            ->get()
            ->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        $namaKelas = "{$kelas->tingkat} {$kelas->jurusan} {$kelas->rombel}";
        $fileName = 'Rekap_Sholat_' . str_replace(' ', '_', $namaKelas) . '_' . $tanggalMulai->format('Ymd') . '-' . $tanggalAkhir->format('Ymd') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Tanggal', 'Nama Siswa', 'NISN', 'Status'];

        $callback = function() use($siswas, $absensi, $columns, $namaKelas, $tanggalMulai, $tanggalAkhir, $delimiter) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Kelas:', $namaKelas], $delimiter);
            fputcsv($file, ['Periode:', $tanggalMulai->translatedFormat('d M Y') . ' - ' . $tanggalAkhir->translatedFormat('d M Y')], $delimiter);
            fputcsv($file, [], $delimiter);
            
            fputcsv($file, $columns, $delimiter);

            $no = 1;
            $period = \Carbon\CarbonPeriod::create($tanggalMulai, $tanggalAkhir);
            
            foreach ($period as $date) {
                $tanggal = $date->format('Y-m-d');
                $records = $absensi->get($tanggal, collect());
                $recordsBySiswa = $records->keyBy('user_id');
                
                foreach ($siswas as $siswa) {
                    $statusData = $recordsBySiswa->get($siswa->id);
                    $statusText = $statusData ? ucfirst(str_replace('_', ' ', $statusData->status)) : 'Belum Diabsen';
                    
                    $row['No']          = $no++;
                    $row['Tanggal']     = $date->translatedFormat('d/m/Y');
                    $row['Nama Siswa']  = $siswa->name;
                    $row['NISN']        = $siswa->nomor_induk ?? '-';
                    $row['Status']      = $statusText;

                    fputcsv($file, $row, $delimiter);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
