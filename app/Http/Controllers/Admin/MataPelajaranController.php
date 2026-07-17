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
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
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
            'aktif'     => 'nullable|boolean',
        ], [
            'nama.required'    => 'Nama mata pelajaran wajib diisi.',
        ]);

        MataPelajaran::create([
            'nama'      => $request->nama,
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
            'aktif'     => 'nullable|boolean',
        ], [
            'nama.required'    => 'Nama mata pelajaran wajib diisi.',
        ]);

        $mataPelajaran->update([
            'nama'      => $request->nama,
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
