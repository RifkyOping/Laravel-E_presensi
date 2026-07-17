<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\IndikatorLiterasi;
use Illuminate\Http\Request;

class IndikatorLiterasiController extends Controller
{
    public function __construct()
    {
        if (!auth()->check() || !auth()->user()->is_guru_bahasa) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $indikators = IndikatorLiterasi::all();
        return view('guru.indikator-literasi.index', compact('indikators'));
    }

    public function create()
    {
        return view('guru.indikator-literasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'aktif' => 'boolean',
        ]);

        IndikatorLiterasi::create([
            'pertanyaan' => $request->pertanyaan,
            'aktif' => $request->has('aktif') ? true : false,
        ]);

        return redirect()->route('guru.indikator.index')->with('success', 'Pertanyaan Indikator berhasil ditambahkan.');
    }

    public function edit(IndikatorLiterasi $indikator)
    {
        return view('guru.indikator-literasi.edit', compact('indikator'));
    }

    public function update(Request $request, IndikatorLiterasi $indikator)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'aktif' => 'boolean',
        ]);

        $indikator->update([
            'pertanyaan' => $request->pertanyaan,
            'aktif' => $request->has('aktif') ? true : false,
        ]);

        return redirect()->route('guru.indikator.index')->with('success', 'Pertanyaan Indikator berhasil diperbarui.');
    }

    public function destroy(IndikatorLiterasi $indikator)
    {
        $indikator->delete();
        return redirect()->route('guru.indikator.index')->with('success', 'Pertanyaan Indikator berhasil dihapus.');
    }
}
