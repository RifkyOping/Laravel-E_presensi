<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SingleSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        $currentToken = session('session_token');

        \Illuminate\Support\Facades\Log::info("SingleSessionMiddleware Debug: User ID: {$user->id}, User Token: {$user->session_token}, Current Token: {$currentToken}");

        // Jika belum ada token di session, berarti baru login tanpa token — paksa logout
        if (!$currentToken) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['nomor_induk' => 'Sesi Anda tidak valid. Silakan login kembali.']);
        }

        // Bandingkan dengan token yang tersimpan di database
        if ($user->session_token !== $currentToken) {
            // Token tidak cocok — ada login dari perangkat lain
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['nomor_induk' => 'Akun Anda telah login di perangkat lain. Silakan login kembali untuk melanjutkan.']);
        }

        return $next($request);
    }
}
