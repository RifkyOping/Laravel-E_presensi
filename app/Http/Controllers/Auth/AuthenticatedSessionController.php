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
        // Langkah 1: Autentikasi kredensial (email + password)
        $request->authenticate();

        $user = Auth::user();

        // Langkah 2: Baca cookie device_uuid dari browser
        // Cookie ini tidak terenkripsi (dikecualikan di bootstrap/app.php)
        // sehingga nilainya bisa dibaca secara langsung dan andal
        $cookieDeviceId = $request->cookie('device_uuid');

        // Langkah 3: Cek kunci perangkat — ambil fresh dari DB agar pasti akurat
        $freshDeviceId = DB::table('users')->where('id', $user->id)->value('device_id');

        if (!empty($freshDeviceId) && $user->role !== 'admin') {
            // Akun sudah terikat ke perangkat tertentu.
            // Jika cookie di browser ini BERBEDA atau TIDAK ADA → TOLAK LOGIN
            if ($cookieDeviceId !== $freshDeviceId) {
                // Batalkan login sepenuhnya
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'nomor_induk' => 'Akun ini hanya dapat digunakan di perangkat yang pertama kali didaftarkan. Hubungi Admin untuk mereset perangkat Anda.',
                ]);
            }
        }

        // Langkah 4: Perangkat valid (atau pertama kali login) → lanjutkan
        $request->session()->regenerate();

        // Jika ini perangkat pertama (device_id masih kosong), ikat sekarang
        $cookie = null;
        if (empty($freshDeviceId)) {
            $newDeviceId = (string) Str::uuid();

            // Simpan ke database menggunakan Query Builder langsung (lebih andal)
            DB::table('users')
                ->where('id', $user->id)
                ->update(['device_id' => $newDeviceId]);

            // Set cookie browser (tidak terenkripsi, berlaku 10 tahun)
            // Parameter: nama, nilai, menit, path, domain, secure, httpOnly
            $cookie = cookie('device_uuid', $newDeviceId, 60 * 24 * 365 * 10, '/', null, false, false);
        } else {
            // Perbarui masa berlaku cookie agar tidak kedaluwarsa
            $cookie = cookie('device_uuid', $freshDeviceId, 60 * 24 * 365 * 10, '/', null, false, false);
        }

        // Update session token (untuk SingleSessionMiddleware)
        $token = Str::random(60);
        DB::table('users')
            ->where('id', $user->id)
            ->update(['session_token' => $token]);
        session(['session_token' => $token]);

        $response = match($user->role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'guru'       => redirect()->route('guru.dashboard'),
            'pengawas'   => redirect()->route('pengawas.dashboard'),
            'kurikulum'  => redirect()->route('kurikulum.dashboard'),
            default      => redirect()->route('murid.dashboard'),
        };

        return $response->withCookie($cookie);
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
