<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAuthnVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user belum login, biarkan auth middleware yang menangani
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Admin dan Piket RPP dikecualikan
        if ($user->role === 'admin' || $user->is_piket_rpp) {
            return $next($request);
        }

        // Hindari infinite loop jika sedang berada di halaman webauthn atau logout
        if ($request->is('webauthn/*') || $request->is('logout')) {
            return $next($request);
        }

        // Cek jika butuh verifikasi perangkat (login ulang)
        if ($request->session()->has('webauthn_pending_verification')) {
            return redirect()->route('webauthn.authenticate');
        }

        // Cek jika butuh mendaftarkan perangkat (pertama kali login)
        if ($request->session()->has('webauthn_pending_registration')) {
            return redirect()->route('webauthn.register');
        }

        // Pastikan session sudah terverifikasi
        if (!$request->session()->has('webauthn_verified')) {
            // Fallback, jika tidak ada pending tapi juga belum verified
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Silakan login kembali.');
        }

        return $next($request);
    }
}
