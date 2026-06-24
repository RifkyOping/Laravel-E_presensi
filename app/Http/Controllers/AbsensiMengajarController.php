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

        return view('guru.aktivitas', compact('hariIni', 'riwayat', 'tanggalRiwayat'));
    }

    /**
     * Simpan data aktivitas mengajar baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran'    => 'required|string|max:100',
            'kelas'             => 'required|string|max:20',
            'jam_ke'            => 'required|integer|min:1|max:12',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'nullable',
            'materi'            => 'required|string|max:500',
            'metode'            => 'required|in:daring,luring',
            'jumlah_siswa_hadir'=> 'required|integer|min:0',
            'keterangan'        => 'nullable|string|max:500',
        ], [
            'mata_pelajaran.required' => 'Mata pelajaran wajib diisi.',
            'kelas.required'          => 'Kelas wajib diisi.',
            'jam_ke.required'         => 'Jam ke- wajib diisi.',
            'jam_mulai.required'      => 'Jam mulai wajib diisi.',
            'materi.required'         => 'Materi wajib diisi.',
            'metode.required'         => 'Metode mengajar wajib dipilih.',
            'jumlah_siswa_hadir.required' => 'Jumlah siswa hadir wajib diisi.',
        ]);

        AbsensiMengajar::create([
            'user_id'            => Auth::id(),
            'tanggal'            => Carbon::today()->toDateString(),
            'mata_pelajaran'     => $request->mata_pelajaran,
            'kelas'              => $request->kelas,
            'jam_ke'             => $request->jam_ke,
            'jam_mulai'          => $request->jam_mulai,
            'jam_selesai'        => $request->jam_selesai,
            'materi'             => $request->materi,
            'metode'             => $request->metode,
            'jumlah_siswa_hadir' => $request->jumlah_siswa_hadir,
            'keterangan'         => $request->keterangan,
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
