<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruPersetujuanAbsensiController extends Controller
{
    public function index()
    {
        $pengajuanSiswa = AbsensiSiswa::with('user')
            ->where('guru_id', Auth::id())
            ->where('status_pengajuan', 'pending')
            ->orderByDesc('tanggal')
            ->get();
            
        $riwayatSiswa = AbsensiSiswa::with('user')
            ->where('guru_id', Auth::id())
            ->whereNotNull('status_pengajuan')
            ->where('status_pengajuan', '!=', 'pending')
            ->orderByDesc('updated_at')
            ->take(30)
            ->get();

        return view('guru.persetujuan-absensi', compact('pengajuanSiswa', 'riwayatSiswa'));
    }

    public function approve($id)
    {
        $pengajuan = AbsensiSiswa::where('guru_id', Auth::id())->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $pengajuan->update([
            'status_pengajuan' => 'approved',
            'is_notified'      => false,
        ]);

        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:500'
        ], [
            'alasan.required' => 'Alasan penolakan wajib diisi.'
        ]);

        $pengajuan = AbsensiSiswa::where('guru_id', Auth::id())->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $pengajuan->update([
            'status_pengajuan' => 'rejected',
            'alasan_ditolak'   => $request->alasan,
            'is_notified'      => false,
        ]);

        return back()->with('success', 'Pengajuan ditolak. Murid masih dapat melakukan absen sekolah biasa.');
    }
}
