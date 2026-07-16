<?php

namespace App\Http\Controllers;

use App\Models\BukuManual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BukuManualController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil semua buku manual yang diupload user
        $bukuManuals = BukuManual::where('user_id', $user->id)
            ->orderBy('level')
            ->get()
            ->keyBy('level');

        // Level 1 selalu terbuka
        // Level 2 terbuka jika Level 1 ada dan status_selesai = true
        // Level 3 terbuka jika Level 2 ada dan status_selesai = true

        $levels = collect([1, 2, 3])->map(function ($lvl) use ($bukuManuals) {
            $buku = $bukuManuals->get($lvl);
            
            $terbuka = false;
            if ($lvl === 1) {
                $terbuka = true;
            } else {
                $bukuSebelumnya = $bukuManuals->get($lvl - 1);
                if ($bukuSebelumnya && $bukuSebelumnya->status_selesai) {
                    $terbuka = true;
                }
            }

            return (object) [
                'level' => $lvl,
                'buku' => $buku,
                'terbuka' => $terbuka
            ];
        });

        return view('siswa.ebook.manual.index', compact('levels'));
    }

    public function create($level)
    {
        // Pastikan level valid
        $user = Auth::user();
        $bukuManuals = BukuManual::where('user_id', $user->id)->get()->keyBy('level');

        // Jika sudah ada buku di level ini, tidak bisa upload lagi
        if ($bukuManuals->has($level)) {
            return redirect()->route('ebook.manual.index')->with('error', 'Anda sudah mengupload buku untuk level ini.');
        }

        return view('siswa.ebook.manual.create', compact('level'));
    }

    public function store(Request $request, $level)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'kota_terbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|numeric|digits:4',
            'jumlah_halaman' => 'required|integer|min:1',
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.max' => 'Judul buku maksimal 255 karakter.',
            'penulis.required' => 'Nama penulis/pengarang wajib diisi.',
            'penulis.max' => 'Nama penulis/pengarang maksimal 255 karakter.',
            'penerbit.required' => 'Nama penerbit wajib diisi.',
            'penerbit.max' => 'Nama penerbit maksimal 255 karakter.',
            'kota_terbit.required' => 'Kota terbit wajib diisi.',
            'kota_terbit.max' => 'Kota terbit maksimal 255 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.numeric' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.digits' => 'Tahun terbit harus terdiri dari 4 angka (misal: 2023).',
            'jumlah_halaman.required' => 'Jumlah halaman wajib diisi.',
            'jumlah_halaman.integer' => 'Jumlah halaman harus berupa angka bulat.',
            'jumlah_halaman.min' => 'Jumlah halaman minimal 1.',
            'foto_sampul.required' => 'Foto sampul wajib diunggah.',
            'foto_sampul.image' => 'File yang diupload harus berupa gambar.',
            'foto_sampul.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto_sampul.max' => 'Ukuran foto sampul tidak boleh lebih dari 2MB.',
        ]);

        $user = Auth::user();

        if ($request->hasFile('foto_sampul')) {
            $path = $request->file('foto_sampul')->store('buku_manual/sampul', 'public');
        } else {
            return back()->with('error', 'Gagal mengupload foto sampul.');
        }

        BukuManual::create([
            'user_id' => $user->id,
            'level' => $level,
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'kota_terbit' => $request->kota_terbit,
            'tahun_terbit' => $request->tahun_terbit,
            'jumlah_halaman' => $request->jumlah_halaman,
            'foto_sampul' => $path,
            'status_selesai' => false,
        ]);

        return redirect()->route('ebook.manual.index')->with('success', 'Buku Manual berhasil diupload. Silakan baca dan isi pertanyaan indikator.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $buku = BukuManual::where('user_id', $user->id)->findOrFail($id);
        $catatan = \App\Models\CatatanMembaca::where('user_id', $user->id)
            ->where('jenis_buku', 'manual')
            ->where('buku_id', $buku->id)
            ->first();

        return view('siswa.ebook.manual.show', compact('buku', 'catatan'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $buku = BukuManual::where('user_id', $user->id)->findOrFail($id);
        
        return view('siswa.ebook.manual.edit', compact('buku'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $buku = BukuManual::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'kota_terbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|numeric|digits:4',
            'jumlah_halaman' => 'required|integer|min:1',
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.max' => 'Judul buku maksimal 255 karakter.',
            'penulis.required' => 'Nama penulis/pengarang wajib diisi.',
            'penulis.max' => 'Nama penulis/pengarang maksimal 255 karakter.',
            'penerbit.required' => 'Nama penerbit wajib diisi.',
            'penerbit.max' => 'Nama penerbit maksimal 255 karakter.',
            'kota_terbit.required' => 'Kota terbit wajib diisi.',
            'kota_terbit.max' => 'Kota terbit maksimal 255 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.numeric' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.digits' => 'Tahun terbit harus terdiri dari 4 angka (misal: 2023).',
            'jumlah_halaman.required' => 'Jumlah halaman wajib diisi.',
            'jumlah_halaman.integer' => 'Jumlah halaman harus berupa angka bulat.',
            'jumlah_halaman.min' => 'Jumlah halaman minimal 1.',
            'foto_sampul.image' => 'File yang diupload harus berupa gambar.',
            'foto_sampul.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto_sampul.max' => 'Ukuran foto sampul tidak boleh lebih dari 2MB.',
        ]);

        if ($request->hasFile('foto_sampul')) {
            // Delete old photo
            if ($buku->foto_sampul) {
                Storage::disk('public')->delete($buku->foto_sampul);
            }
            $path = $request->file('foto_sampul')->store('buku_manual/sampul', 'public');
            $buku->foto_sampul = $path;
        }

        $buku->judul = $request->judul;
        $buku->penulis = $request->penulis;
        $buku->penerbit = $request->penerbit;
        $buku->kota_terbit = $request->kota_terbit;
        $buku->tahun_terbit = $request->tahun_terbit;
        $buku->jumlah_halaman = $request->jumlah_halaman;
        
        $buku->save();

        return redirect()->route('ebook.manual.show', $buku->id)->with('success', 'Informasi buku berhasil diperbarui.');
    }
}
