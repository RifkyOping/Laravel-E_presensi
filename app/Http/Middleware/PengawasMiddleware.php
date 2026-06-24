<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengawasMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['pengawas', 'admin'])) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Pengawas.');
        }

        return $next($request);
    }
}
