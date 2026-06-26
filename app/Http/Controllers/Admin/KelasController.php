<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('jurusan', 'like', "%{$search}%")
                  ->orWhere('rombel', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tingkat') && $request->tingkat !== 'Semua') {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'aktif');
        }

        $kelas = $query->orderBy('tingkat')
                       ->orderBy('jurusan')
                       ->orderBy('rombel')
                       ->paginate(15)
                       ->withQueryString();

        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat' => 'required|string',
            'jurusan' => 'required|string',
            'rombel'  => 'required|string',
        ]);

        $exists = Kelas::where('tingkat', $request->tingkat)
            ->where('jurusan', $request->jurusan)
            ->where('rombel', $request->rombel)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kombinasi Tingkat, Jurusan, dan Rombel sudah ada.');
        }

        Kelas::create([
            'tingkat' => $request->tingkat,
            'jurusan' => strtoupper($request->jurusan),
            'rombel'  => strtoupper($request->rombel),
            'status'  => true,
        ]);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'tingkat' => 'required|string',
            'jurusan' => 'required|string',
            'rombel'  => 'required|string',
        ]);

        $exists = Kelas::where('tingkat', $request->tingkat)
            ->where('jurusan', $request->jurusan)
            ->where('rombel', $request->rombel)
            ->where('id', '!=', $kela->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kombinasi Tingkat, Jurusan, dan Rombel sudah digunakan oleh kelas lain.');
        }

        $kela->update([
            'tingkat' => $request->tingkat,
            'jurusan' => strtoupper($request->jurusan),
            'rombel'  => strtoupper($request->rombel),
        ]);

        return back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    public function toggleAktif(Kelas $kela)
    {
        $kela->update(['status' => !$kela->status]);
        return back()->with('success', 'Status kelas berhasil diubah.');
    }
}
