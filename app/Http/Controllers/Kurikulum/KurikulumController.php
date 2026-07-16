<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AbsensiMengajar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KurikulumController extends Controller
{
    // ─────────────────────────────────────────────────
    //  DASHBOARD — ringkasan aktivitas mengajar hari ini
    // ─────────────────────────────────────────────────
    public function dashboard()
    {
        $today = Carbon::today();

        $stats = [
            'total_guru'       => User::where('role', 'guru')->count(),
            'guru_hadir'       => AbsensiGuru::whereDate('tanggal', $today)
                                    ->whereNotNull('waktu_datang')->count(),
            'sesi_mengajar'    => AbsensiMengajar::whereDate('tanggal', $today)->count(),
            'sudah_diverif'    => AbsensiMengajar::whereDate('tanggal', $today)
                                    ->whereNotNull('verified_at')->count(),
            'belum_diverif'    => AbsensiMengajar::whereDate('tanggal', $today)
                                    ->whereNull('verified_at')->count(),
        ];

        // Guru yang sudah absen SEKOLAH hari ini (bisa diverifikasi mengajar)
        $guruHadirHariIni = AbsensiGuru::with('user')
            ->whereDate('tanggal', $today)
            ->whereNotNull('waktu_datang')
            ->orderByDesc('waktu_datang')
            ->get();

        // Aktivitas mengajar hari ini (belum diverifikasi — prioritas)
        $menungguVerifikasi = AbsensiMengajar::with('user')
            ->whereDate('tanggal', $today)
            ->whereNull('verified_at')
            ->orderBy('jam_ke')
            ->get();

        // Aktivitas mengajar hari ini yang sudah diverifikasi
        $sudahVerifikasi = AbsensiMengajar::with('user', 'verifier')
            ->whereDate('tanggal', $today)
            ->whereNotNull('verified_at')
            ->orderBy('jam_ke')
            ->get();

        return view('kurikulum.dashboard', compact(
            'stats', 'guruHadirHariIni', 'menungguVerifikasi', 'sudahVerifikasi'
        ));
    }

    // ─────────────────────────────────────────────────
    //  MONITORING MENGAJAR — dengan filter
    // ─────────────────────────────────────────────────
    public function monitoringMengajar(Request $request)
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

        return view('kurikulum.monitoring-mengajar', compact('semuaGuru', 'aktivitas', 'tanggal'));
    }

    // ─────────────────────────────────────────────────
    //  VERIFIKASI — tampil form upload foto + catatan
    // ─────────────────────────────────────────────────
    public function verifikasi(AbsensiMengajar $aktivitas)
    {
        // Load relasi user
        $aktivitas->load('user', 'verifier');
        return view('kurikulum.verifikasi', compact('aktivitas'));
    }

    // ─────────────────────────────────────────────────
    //  STORE VERIFIKASI — simpan foto + catatan
    // ─────────────────────────────────────────────────
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

        $fotoPath = $aktivitas->foto_verifikasi; // keep old if no new upload

        if ($request->hasFile('foto_verifikasi')) {
            // Hapus foto lama jika ada
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto_verifikasi')->store('verifikasi-mengajar', 'public');
        }

        $aktivitas->update([
            'status_verifikasi' => $request->status_verifikasi,
            'foto_verifikasi'   => $fotoPath,
            'catatan_kurikulum' => $request->catatan_kurikulum,
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
        ]);

        return redirect()->route('kurikulum.monitoring-mengajar')
            ->with('success', 'Verifikasi berhasil disimpan untuk ' . $aktivitas->user->name . '.');
    }

    // ─────────────────────────────────────────────────
    //  HAPUS VERIFIKASI — reset status verifikasi
    // ─────────────────────────────────────────────────
    public function hapusVerifikasi(AbsensiMengajar $aktivitas)
    {
        // Hapus foto jika ada
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
        $query = User::where('role', 'guru')
                     ->whereHas('guruProfile', function ($q) {
                         $q->whereNotNull('rpp_file');
                     })
                     ->with('guruProfile');
        
        if ($request->filled('status')) {
            $query->whereHas('guruProfile', function ($q) use ($request) {
                $q->where('rpp_status', $request->status);
            });
        }

        $gurus = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('kurikulum.persetujuan-rpp', compact('gurus'));
    }

    public function approveRpp(User $user)
    {
        $user->guruProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['rpp_status' => 'disetujui', 'rpp_pesan' => null]
        );
        return back()->with('success', 'RPP milik ' . $user->name . ' telah disetujui.');
    }

    public function rejectRpp(Request $request, User $user)
    {
        $request->validate([
            'pesan' => 'required|string|max:500'
        ]);

        $user->guruProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['rpp_status' => 'ditolak', 'rpp_pesan' => $request->pesan]
        );
        return back()->with('success', 'RPP milik ' . $user->name . ' telah ditolak.');
    }
}
