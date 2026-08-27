<?php

namespace App\Http\Controllers;

use App\Models\AbsensiMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiMengajarController extends Controller
{
    /**
     * Tampilkan halaman aktivitas mengajar.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Filter tanggal untuk riwayat (default hari ini jika tidak ada query 'all')
        $tanggalRiwayat = $request->has('tanggal_riwayat') 
            ? $request->input('tanggal_riwayat') 
            : Carbon::today()->format('Y-m-d');

        $cacheKey = 'guru_aktivitas_base_' . $user->id . '_' . Carbon::today()->toDateString();
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 43200, function() use ($user) {
            // Daftar hari ini (selalu hari ini untuk widget atas)
            $hariIni = AbsensiMengajar::with('verifier')
                ->where('user_id', $user->id)
                ->whereDate('tanggal', Carbon::today())
                ->orderBy('jam_ke')
                ->get();

            // Ambil data unik untuk dropdown kelas
            $kelasList = \App\Models\Kelas::where('status', true)->orderBy('tingkat')->orderBy('jurusan')->orderBy('rombel')->get();
            $mapels   = \App\Models\MataPelajaran::where('aktif', true)->orderBy('nama')->pluck('nama');

            return compact('hariIni', 'kelasList', 'mapels');
        });
        extract($data);

        // Query Riwayat (Diluar cache karena menggunakan pagination)
        $riwayatQuery = AbsensiMengajar::with('verifier')->where('user_id', $user->id);
        
        if (!empty($tanggalRiwayat)) {
            $riwayatQuery->whereDate('tanggal', $tanggalRiwayat);
        }

        $riwayat = $riwayatQuery->orderByDesc('tanggal')
            ->orderBy('jam_ke')
            ->paginate(15)
            ->withQueryString();

        return view('guru.aktivitas', compact('hariIni', 'riwayat', 'tanggalRiwayat', 'kelasList', 'mapels'));
    }

    /**
     * Simpan data aktivitas mengajar baru.
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Cache::forget('guru_aktivitas_base_' . Auth::id() . '_' . Carbon::today()->toDateString());

        $request->validate([
            'mata_pelajaran'    => 'required|string|max:100',
            'kelas'             => 'required|string',
            'jam_ke'            => 'required|integer|min:1',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'nullable',
        ], [
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
            'kelas.required'          => 'Kelas wajib dipilih.',
            'jam_ke.required'         => 'Mapel ke- wajib diisi.',
            'jam_mulai.required'      => 'Jam mulai wajib diisi.',
        ]);

        AbsensiMengajar::create([
            'user_id'            => Auth::id(),
            'tanggal'            => Carbon::today()->toDateString(),
            'mata_pelajaran'     => $request->mata_pelajaran,
            'kelas'              => $request->kelas,
            'jam_ke'             => $request->jam_ke,
            'jam_mulai'          => $request->jam_mulai,
            'jam_selesai'        => $request->jam_selesai,
        ]);

        return back()->with('success', 'Aktivitas mengajar berhasil dicatat!');
    }

    /**
     * Hapus data aktivitas mengajar.
     */
    public function destroy(AbsensiMengajar $aktivitas)
    {
        \Illuminate\Support\Facades\Cache::forget('guru_aktivitas_base_' . Auth::id() . '_' . Carbon::today()->toDateString());

        // Hanya bisa menghapus milik sendiri
        if ($aktivitas->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $aktivitas->delete();
        return back()->with('success', 'Data aktivitas mengajar berhasil dihapus.');
    }

    /**
     * Catat waktu masuk (mulai) mengajar.
     */
    public function absenMasuk(AbsensiMengajar $aktivitas)
    {
        \Illuminate\Support\Facades\Cache::forget('guru_aktivitas_base_' . Auth::id() . '_' . Carbon::today()->toDateString());

        if ($aktivitas->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke sesi ini.');
        }

        if ($aktivitas->waktu_absen_masuk) {
            return back()->with('info', 'Absen masuk mengajar sudah tercatat.');
        }

        $now = Carbon::now();
        $jamMulai = Carbon::parse($aktivitas->jam_mulai);
        
        // Toleransi 15 menit
        $batasToleransi = $jamMulai->copy()->addMinutes(15);
        $kategori = $now->format('H:i:s') <= $batasToleransi->format('H:i:s') ? 'tepat_waktu' : 'terlambat';

        $aktivitas->update([
            'waktu_absen_masuk' => $now->format('H:i:s'),
            'kategori' => $kategori,
        ]);

        return back()->with('success', 'Absen masuk mengajar berhasil dicatat pukul ' . $now->format('H:i') . ' WITA.');
    }

    /**
     * Catat waktu keluar (selesai) mengajar.
     */
    public function absenKeluar(AbsensiMengajar $aktivitas)
    {
        \Illuminate\Support\Facades\Cache::forget('guru_aktivitas_base_' . Auth::id() . '_' . Carbon::today()->toDateString());

        if ($aktivitas->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke sesi ini.');
        }

        if (!$aktivitas->waktu_absen_masuk) {
            return back()->with('error', 'Silakan absen masuk terlebih dahulu.');
        }

        if ($aktivitas->waktu_absen_keluar) {
            return back()->with('info', 'Absen keluar mengajar sudah tercatat.');
        }

        $aktivitas->update([
            'waktu_absen_keluar' => Carbon::now()->format('H:i:s'),
        ]);

        return back()->with('success', 'Absen keluar mengajar berhasil dicatat pukul ' . Carbon::now()->format('H:i') . ' WITA.');
    }
}
