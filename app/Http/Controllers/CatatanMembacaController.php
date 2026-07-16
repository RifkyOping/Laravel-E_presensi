<?php

namespace App\Http\Controllers;

use App\Models\CatatanMembaca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatatanMembacaController extends Controller
{
    /**
     * Simpan atau update catatan progres membaca
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis_buku' => 'required|in:digital,manual', // Pastikan 'digital' atau 'manual'
            'buku_id'    => 'required|integer',
            'catatan'    => 'required|string|max:2000',
        ], [
            'catatan.required' => 'Catatan tidak boleh kosong.',
            'catatan.max'      => 'Catatan maksimal 2000 karakter.',
        ]);

        $user = Auth::user();

        // Menyimpan atau memperbarui catatan (1 catatan per user per buku)
        CatatanMembaca::updateOrCreate(
            [
                'user_id'    => $user->id,
                'jenis_buku' => $request->jenis_buku,
                'buku_id'    => $request->buku_id,
            ],
            [
                'catatan' => $request->catatan
            ]
        );

        return back()->with('success_catatan', 'Catatan progres berhasil disimpan.');
    }
}
