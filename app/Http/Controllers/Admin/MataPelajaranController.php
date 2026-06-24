<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    // ──────────────────────────────────────────
    //  INDEX — Daftar semua mata pelajaran
    // ──────────────────────────────────────────

    public function index(Request $request)
    {
        $query = MataPelajaran::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%')
                  ->orWhere('jurusan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('status')) {
            $query->where('aktif', $request->status === 'aktif');
        }

        $mapel = $query->orderBy('nama')->paginate(15)->withQueryString();

        $stats = [
            'total'  => MataPelajaran::count(),
            'aktif'  => MataPelajaran::where('aktif', true)->count(),
            'nonaktif' => MataPelajaran::where('aktif', false)->count(),
        ];

        return view('admin.mata-pelajaran.index', compact('mapel', 'stats'));
    }

    // ──────────────────────────────────────────
    //  CREATE
    // ──────────────────────────────────────────

    public function create()
    {
        return view('admin.mata-pelajaran.create');
    }

    // ──────────────────────────────────────────
    //  STORE
    // ──────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:150',
            'kode'      => 'required|string|max:20|unique:mata_pelajaran,kode',
            'tingkat'   => 'required|in:X,XI,XII,Semua',
            'jurusan'   => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'aktif'     => 'nullable|boolean',
        ], [
            'nama.required'    => 'Nama mata pelajaran wajib diisi.',
            'kode.required'    => 'Kode mata pelajaran wajib diisi.',
            'kode.unique'      => 'Kode mata pelajaran sudah digunakan.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
        ]);

        MataPelajaran::create([
            'nama'      => $request->nama,
            'kode'      => strtoupper($request->kode),
            'tingkat'   => $request->tingkat,
            'jurusan'   => $request->jurusan,
            'deskripsi' => $request->deskripsi,
            'aktif'     => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', "Mata pelajaran \"{$request->nama}\" berhasil ditambahkan.");
    }

    // ──────────────────────────────────────────
    //  EDIT
    // ──────────────────────────────────────────

    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata-pelajaran.edit', compact('mataPelajaran'));
    }

    // ──────────────────────────────────────────
    //  UPDATE
    // ──────────────────────────────────────────

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama'      => 'required|string|max:150',
            'kode'      => 'required|string|max:20|unique:mata_pelajaran,kode,' . $mataPelajaran->id,
            'tingkat'   => 'required|in:X,XI,XII,Semua',
            'jurusan'   => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'aktif'     => 'nullable|boolean',
        ], [
            'nama.required'    => 'Nama mata pelajaran wajib diisi.',
            'kode.required'    => 'Kode mata pelajaran wajib diisi.',
            'kode.unique'      => 'Kode mata pelajaran sudah digunakan.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
        ]);

        $mataPelajaran->update([
            'nama'      => $request->nama,
            'kode'      => strtoupper($request->kode),
            'tingkat'   => $request->tingkat,
            'jurusan'   => $request->jurusan,
            'deskripsi' => $request->deskripsi,
            'aktif'     => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', "Mata pelajaran \"{$mataPelajaran->nama}\" berhasil diperbarui.");
    }

    // ──────────────────────────────────────────
    //  DESTROY
    // ──────────────────────────────────────────

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $nama = $mataPelajaran->nama;
        $mataPelajaran->delete();

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', "Mata pelajaran \"{$nama}\" berhasil dihapus.");
    }

    // ──────────────────────────────────────────
    //  TOGGLE STATUS AKTIF (via AJAX/quick action)
    // ──────────────────────────────────────────

    public function toggleAktif(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->update(['aktif' => !$mataPelajaran->aktif]);

        $status = $mataPelajaran->aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Mata pelajaran \"{$mataPelajaran->nama}\" berhasil {$status}.");
    }
}
