<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Langkah 1: Autentikasi kredensial (nomor_induk + password)
        $request->authenticate();

        $user = Auth::user();

        // Langkah 2: Cek device_id yang tersimpan di DB
        $freshDeviceId = DB::table('users')->where('id', $user->id)->value('device_id');

        $request->session()->regenerate();

        // Update session token (untuk SingleSessionMiddleware)
        $token = Str::random(60);
        DB::table('users')
            ->where('id', $user->id)
            ->update(['session_token' => $token]);
        session(['session_token' => $token]);

        // Jika bukan admin dan bukan piket rpp, wajib WebAuthn
        if ($user->role !== 'admin' && !$user->is_piket_rpp) {
            if (empty($freshDeviceId)) {
                $request->session()->put('webauthn_pending_registration', true);
                return redirect()->route('webauthn.register');
            } else {
                $request->session()->put('webauthn_pending_verification', true);
                return redirect()->route('webauthn.authenticate');
            }
        } else {
            $request->session()->put('webauthn_verified', true);
        }

        return match($user->role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'guru'       => redirect()->route('guru.dashboard'),
            'pengawas'   => redirect()->route('pengawas.dashboard'),
            'kurikulum'  => redirect()->route('kurikulum.dashboard'),
            default      => redirect()->route('murid.dashboard'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

