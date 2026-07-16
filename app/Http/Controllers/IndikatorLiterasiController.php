<?php

namespace App\Http\Controllers;

use App\Models\BukuManual;
use App\Models\EBook;
use App\Models\IndikatorLiterasi;
use App\Models\JawabanIndikator;
use App\Models\ProgresEbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndikatorLiterasiController extends Controller
{
    public function show($jenis, $id)
    {
        $user = Auth::user();

        // Validasi akses
        if ($jenis === 'digital') {
            $buku = EBook::findOrFail($id);
            $progres = ProgresEbook::where('user_id', $user->id)->where('e_book_id', $buku->id)->first();
            
            // Harus lulus suara dan kuis dulu
            if (!$progres || !$progres->lulus_suara || ($buku->questions()->count() > 0 && !$progres->lulus_kuis)) {
                return redirect()->route('ebook.index')->with('error', 'Anda harus menyelesaikan bacaan dan kuis terlebih dahulu.');
            }
            
            if ($progres->selesai) {
                return redirect()->route('ebook.index')->with('success', 'Anda sudah menyelesaikan buku ini.');
            }

            $judulBuku = $buku->judul;
        } elseif ($jenis === 'manual') {
            $buku = BukuManual::where('user_id', $user->id)->where('id', $id)->firstOrFail();
            
            if ($buku->status_selesai) {
                return redirect()->route('ebook.manual.index')->with('success', 'Anda sudah menyelesaikan buku ini.');
            }

            $judulBuku = $buku->judul;
        } else {
            abort(404);
        }

        $indikators = IndikatorLiterasi::where('aktif', true)->get();

        if ($indikators->isEmpty()) {
            // Jika tidak ada indikator sama sekali, otomatis selesaikan
            if ($jenis === 'digital') {
                $progres->update(['selesai' => true, 'selesai_pada' => now()]);
                return redirect()->route('ebook.index')->with('success', 'Selamat, Anda telah menyelesaikan buku ini.');
            } else {
                $buku->update(['status_selesai' => true]);
                return redirect()->route('ebook.manual.index')->with('success', 'Selamat, Anda telah menyelesaikan buku manual ini.');
            }
        }

        return view('siswa.ebook.indikator', compact('buku', 'jenis', 'judulBuku', 'indikators'));
    }

    public function store(Request $request, $jenis, $id)
    {
        $user = Auth::user();
        $indikators = IndikatorLiterasi::where('aktif', true)->get();

        $rules = [];
        foreach ($indikators as $indikator) {
            $rules['jawaban.' . $indikator->id] = 'required|string';
        }

        $request->validate($rules, [
            'jawaban.*.required' => 'Semua pertanyaan indikator wajib diisi.'
        ]);

        if ($jenis === 'digital') {
            $buku = EBook::findOrFail($id);
            $progres = ProgresEbook::where('user_id', $user->id)->where('e_book_id', $buku->id)->firstOrFail();
        } elseif ($jenis === 'manual') {
            $buku = BukuManual::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        } else {
            abort(404);
        }

        // Simpan jawaban
        foreach ($request->jawaban as $indikator_id => $jawaban) {
            JawabanIndikator::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'jenis_buku' => $jenis,
                    'buku_id' => $buku->id,
                    'indikator_id' => $indikator_id,
                ],
                [
                    'jawaban' => $jawaban,
                ]
            );
        }

        // Tandai selesai
        if ($jenis === 'digital') {
            $progres->update(['selesai' => true, 'selesai_pada' => now()]);
            return redirect()->route('ebook.index')->with('success', 'Jawaban indikator berhasil disimpan. Anda telah menyelesaikan buku ini.');
        } else {
            $buku->update(['status_selesai' => true]);
            return redirect()->route('ebook.manual.index')->with('success', 'Jawaban indikator berhasil disimpan. Anda telah menyelesaikan buku manual ini.');
        }
    }
}
