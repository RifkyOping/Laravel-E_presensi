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

        // Daftar hari ini (selalu hari ini untuk widget atas)
        $hariIni = AbsensiMengajar::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today())
            ->orderBy('jam_ke')
            ->get();

        // Query Riwayat
        $riwayatQuery = AbsensiMengajar::where('user_id', $user->id);
        
        if (!empty($tanggalRiwayat)) {
            $riwayatQuery->whereDate('tanggal', $tanggalRiwayat);
        }

        $riwayat = $riwayatQuery->orderByDesc('tanggal')
            ->orderBy('jam_ke')
            ->paginate(15)
            ->withQueryString();

        // Ambil data unik untuk dropdown kelas
        $tingkats = \App\Models\Kelas::where('status', true)->select('tingkat')->distinct()->pluck('tingkat');
        $jurusans = \App\Models\Kelas::where('status', true)->select('jurusan')->distinct()->pluck('jurusan');
        $rombels  = \App\Models\Kelas::where('status', true)->select('rombel')->distinct()->pluck('rombel');
        $mapels   = \App\Models\MataPelajaran::where('aktif', true)->orderBy('nama')->pluck('nama');

        return view('guru.aktivitas', compact('hariIni', 'riwayat', 'tanggalRiwayat', 'tingkats', 'jurusans', 'rombels', 'mapels'));
    }

    /**
     * Simpan data aktivitas mengajar baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran'    => 'required|string|max:100',
            'tingkat'           => 'required|string',
            'jurusan'           => 'required|string',
            'rombel'            => 'required|string',
            'jam_ke'            => 'required|integer|min:1',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'nullable',
        ], [
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
            'tingkat.required'        => 'Tingkat wajib dipilih.',
            'jurusan.required'        => 'Jurusan wajib dipilih.',
            'rombel.required'         => 'Rombel wajib dipilih.',
            'jam_ke.required'         => 'Jam ke- wajib diisi.',
            'jam_mulai.required'      => 'Jam mulai wajib diisi.',
        ]);

        $kelasStr = $request->tingkat . ' ' . $request->jurusan . ' ' . $request->rombel;

        AbsensiMengajar::create([
            'user_id'            => Auth::id(),
            'tanggal'            => Carbon::today()->toDateString(),
            'mata_pelajaran'     => $request->mata_pelajaran,
            'kelas'              => $kelasStr,
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
        // Hanya bisa menghapus milik sendiri
        if ($aktivitas->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $aktivitas->delete();
        return back()->with('success', 'Data aktivitas mengajar berhasil dihapus.');
    }
}
