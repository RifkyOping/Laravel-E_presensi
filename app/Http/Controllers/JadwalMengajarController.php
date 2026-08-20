<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalMengajar;
use Illuminate\Support\Facades\Auth;

class JadwalMengajarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cacheKey = 'guru_jadwal_' . $user->id;
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 43200, function() use ($user) {
            $setting = \App\Models\SchoolSetting::get();
            $blokAktif = $setting->blok_jadwal_aktif ?? 'A';

            if ($blokAktif === 'TEFA') {
                $jadwalRaw = collect();
            } else {
                $jadwalRaw = $user->jadwalMengajars()
                    ->whereIn('tipe_blok', ['Semua', $blokAktif])
                    ->orderBy('jam_ke')
                    ->get();
            }

            // Kelompokkan berdasarkan hari
            $jadwal = [
                'Senin'  => [],
                'Selasa' => [],
                'Rabu'   => [],
                'Kamis'  => [],
                'Jumat'  => [],
            ];

            foreach ($jadwalRaw as $j) {
                $jadwal[$j->hari][] = $j;
            }
            return compact('jadwal', 'blokAktif');
        });
        extract($data);

        return view('guru.jadwal.index', compact('jadwal', 'blokAktif'));
    }

    public function store(Request $request)
    {
        // Validasi struktur dinamis
        $request->validate([
            'jadwal' => 'nullable|array',
            'jadwal.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jadwal.*.mata_pelajaran' => 'required|string',
            'jadwal.*.tingkat' => 'required|string',
            'jadwal.*.jurusan' => 'required|string',
            'jadwal.*.rombel' => 'required|string',
            'jadwal.*.jam_ke' => 'required|integer|min:1',
            'jadwal.*.jam_mulai' => 'required',
            'jadwal.*.jam_selesai' => 'nullable',
        ]);

        $user = Auth::user();
        \Illuminate\Support\Facades\Cache::forget('guru_jadwal_' . $user->id);

        // Hapus semua jadwal lama
        $user->jadwalMengajars()->delete();

        // Insert jadwal baru (jika ada)
        if ($request->has('jadwal') && is_array($request->jadwal)) {
            foreach ($request->jadwal as $j) {
                $kelasStr = $j['tingkat'] . ' ' . $j['jurusan'] . ' ' . $j['rombel'];
                
                JadwalMengajar::create([
                    'user_id' => $user->id,
                    'hari' => $j['hari'],
                    'mata_pelajaran' => $j['mata_pelajaran'],
                    'kelas' => $kelasStr,
                    'jam_ke' => $j['jam_ke'],
                    'jam_mulai' => $j['jam_mulai'],
                    'jam_selesai' => $j['jam_selesai'] ?? null,
                ]);
            }
        }

        // Tandai bahwa guru sudah mengatur jadwal
        $user->guruProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['is_jadwal_set' => true]
        );

        return redirect()->route('guru.dashboard')->with('success', 'Jadwal mengajar berhasil disimpan.');
    }
}
