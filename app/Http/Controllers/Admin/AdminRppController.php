<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RppGuru;
use Illuminate\Http\Request;

class AdminRppController extends Controller
{
    /**
     * Tampilkan rekap semua RPP dari semua guru.
     */
    public function index(Request $request)
    {
        $query = RppGuru::with('user');

        if ($request->filled('nama')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama . '%');
            });
        }
        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }
        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }
        if ($request->filled('periode')) {
            $query->where('rpp_periode', $request->periode);
        }
        if ($request->filled('status')) {
            $query->where('rpp_status', $request->status);
        }

        $rppList = $query->orderByDesc('rpp_periode')
            ->orderBy('tingkat')
            ->orderBy('jurusan')
            ->paginate(20)
            ->withQueryString();

        // Data untuk filter dropdown
        $periodeList = RppGuru::select('rpp_periode')->distinct()->orderByDesc('rpp_periode')->pluck('rpp_periode');
        $tingkatList = RppGuru::select('tingkat')->distinct()->orderBy('tingkat')->pluck('tingkat');
        $jurusanList = RppGuru::select('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');

        return view('admin.rekap-rpp', compact('rppList', 'periodeList', 'tingkatList', 'jurusanList'));
    }
}
