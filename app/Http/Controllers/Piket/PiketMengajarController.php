<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\AbsensiMengajar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PiketMengajarController extends Controller
{
    public function index(Request $request)
    {
        $semuaGuru = User::where('role', 'guru')->orderBy('name')->get();
        $tanggal = $request->filled('tanggal') ? Carbon::parse($request->tanggal) : Carbon::today();

        $query = AbsensiMengajar::with('user', 'verifier')
            ->whereDate('tanggal', $tanggal)
            ->orderByDesc('tanggal')
            ->orderBy('jam_ke');

        if ($request->filled('guru_id')) {
            $query->where('user_id', $request->guru_id);
        }
        if ($request->filled('status_verif')) {
            if ($request->status_verif === 'belum') {
                $query->whereNull('verified_at');
            } elseif ($request->status_verif === 'mengajar') {
                $query->where('status_verifikasi', 'mengajar');
            } elseif ($request->status_verif === 'tidak_mengajar') {
                $query->where('status_verifikasi', 'tidak_mengajar');
            }
        }

        $aktivitas = $query->paginate(20)->withQueryString();

        return view('piket.mengajar.index', compact('semuaGuru', 'aktivitas', 'tanggal'));
    }

    public function verifikasi(AbsensiMengajar $aktivitas)
    {
        $aktivitas->load('user', 'verifier');
        return view('piket.mengajar.verifikasi', compact('aktivitas'));
    }

    public function storeVerifikasi(Request $request, AbsensiMengajar $aktivitas)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:mengajar,tidak_mengajar',
            'foto_verifikasi' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'catatan_kurikulum' => 'required|string|min:5|max:1000',
        ], [
            'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
            'status_verifikasi.in'       => 'Status verifikasi tidak valid.',
            'catatan_kurikulum.required' => 'Catatan verifikasi wajib diisi.',
            'catatan_kurikulum.min'      => 'Catatan minimal 5 karakter.',
            'foto_verifikasi.image'      => 'File harus berupa gambar.',
            'foto_verifikasi.max'        => 'Ukuran foto maksimal 5 MB.',
        ]);

        $fotoPath = $aktivitas->foto_verifikasi;

        if ($request->hasFile('foto_verifikasi')) {
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto_verifikasi')->store('verifikasi-mengajar', 'public');
        }

        $aktivitas->update([
            'status_verifikasi' => $request->status_verifikasi,
            'foto_verifikasi'   => $fotoPath,
            'catatan_kurikulum' => $request->catatan_kurikulum, // Piket is filling the same notes field
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
        ]);

        return redirect()->route('piket.mengajar.index')
            ->with('success', 'Verifikasi berhasil disimpan untuk ' . $aktivitas->user->name . '.');
    }

    public function hapusVerifikasi(AbsensiMengajar $aktivitas)
    {
        if ($aktivitas->foto_verifikasi && Storage::disk('public')->exists($aktivitas->foto_verifikasi)) {
            Storage::disk('public')->delete($aktivitas->foto_verifikasi);
        }

        $aktivitas->update([
            'status_verifikasi' => null,
            'foto_verifikasi'   => null,
            'catatan_kurikulum' => null,
            'verified_by'       => null,
            'verified_at'       => null,
        ]);

        return back()->with('success', 'Verifikasi berhasil dihapus.');
    }

    // ─────────────────────────────────────────────────
    //  PERSETUJUAN RPP GURU
    // ─────────────────────────────────────────────────
    public function persetujuanRpp(Request $request)
    {
        $query = \App\Models\RppGuru::with('user')
            ->where('rpp_periode', '>=', date('Y-m'));
        
        if ($request->filled('status')) {
            $query->where('rpp_status', $request->status);
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }
        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $rppList = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Ambil daftar tingkat dan jurusan unik untuk filter
        $tingkatList = \App\Models\RppGuru::where('rpp_periode', '>=', date('Y-m'))
            ->select('tingkat')->distinct()->orderBy('tingkat')->pluck('tingkat');
        $jurusanList = \App\Models\RppGuru::where('rpp_periode', '>=', date('Y-m'))
            ->select('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');

        return view('piket.persetujuan-rpp', compact('rppList', 'tingkatList', 'jurusanList'));
    }

    public function approveRpp(\App\Models\RppGuru $rppGuru)
    {
        $rppGuru->update([
            'rpp_status' => 'disetujui',
            'rpp_pesan'  => null,
        ]);
        return back()->with('success', 'RPP milik ' . $rppGuru->user->name . ' (Kelas ' . $rppGuru->tingkat . ' ' . $rppGuru->jurusan . ') telah disetujui.');
    }

    public function rejectRpp(Request $request, \App\Models\RppGuru $rppGuru)
    {
        $request->validate([
            'pesan' => 'required|string|max:500'
        ]);

        $rppGuru->update([
            'rpp_status' => 'ditolak',
            'rpp_pesan'  => $request->pesan,
        ]);
        return back()->with('success', 'RPP milik ' . $rppGuru->user->name . ' (Kelas ' . $rppGuru->tingkat . ' ' . $rppGuru->jurusan . ') telah ditolak.');
    }
}
