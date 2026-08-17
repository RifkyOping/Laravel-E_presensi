<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KurikulumMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || (!Auth::user()->is_kurikulum && Auth::user()->role !== 'admin')) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Tim Kurikulum.');
        }

        return $next($request);
    }
}
