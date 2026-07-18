<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuranController extends Controller
{
    /**
     * Tampilkan halaman daftar surah.
     */
    public function index()
    {
        return view('siswa.quran.index');
    }

    /**
     * Tampilkan halaman baca surah murni Arab.
     */
    public function show($nomor)
    {
        return view('siswa.quran.show', compact('nomor'));
    }

    /**
     * Tampilkan halaman baca per juz.
     */
    public function juz($nomor)
    {
        return view('siswa.quran.juz', compact('nomor'));
    }
}
