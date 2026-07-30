<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    /**
     * Tampilkan halaman QR Code untuk absensi offline murid.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Pastikan murid memiliki nomor induk (NISN)
        if (!$user->nomor_induk) {
            return redirect()->route('murid.dashboard')->with('error', 'Nomor Induk (NISN) Anda belum diatur.');
        }

        // Generate QR Code berisi nomor induk
        // Anda dapat mengatur ukurannya sesuai kebutuhan
        $qrCode = QrCode::size(300)
            ->style('round')
            ->margin(1)
            ->generate($user->nomor_induk);

        return view('siswa.qr-code.index', compact('qrCode', 'user'));
    }
}
