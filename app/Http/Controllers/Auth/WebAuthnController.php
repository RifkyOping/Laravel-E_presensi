<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebAuthnController extends Controller
{
    /**
     * Tampilkan halaman registrasi perangkat WebAuthn (Login pertama)
     */
    public function registerPage(Request $request)
    {
        if (!$request->session()->has('webauthn_pending_registration')) {
            return redirect()->route('dashboard');
        }
        return view('auth.webauthn-register');
    }

    /**
     * Dapatkan options untuk navigator.credentials.create()
     */
    public function registerOptions(Request $request)
    {
        $user = Auth::user();
        
        // Generate random challenge
        $challenge = Str::random(32);
        $request->session()->put('webauthn_challenge', $challenge);

        $options = [
            'challenge' => base64_encode($challenge),
            'rp' => [
                'name' => 'E-Presensi SMKN 1 Majene',
                'id' => $request->getHost(),
            ],
            'user' => [
                'id' => base64_encode((string)$user->id),
                'name' => $user->nomor_induk,
                'displayName' => $user->name,
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7], // ES256
                ['type' => 'public-key', 'alg' => -257], // RS256
            ],
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',
                'userVerification' => 'preferred', // Bisa didaftarkan tanpa PIN di device tanpa PIN
            ],
            'timeout' => 60000,
            'attestation' => 'none'
        ];

        return response()->json($options);
    }

    /**
     * Verifikasi pendaftaran WebAuthn dan simpan ke DB
     */
    public function registerVerify(Request $request)
    {
        $user = Auth::user();
        $expectedChallenge = $request->session()->pull('webauthn_challenge');

        $request->validate([
            'id' => 'required|string',
            'clientDataJSON' => 'required|string',
            'authenticatorData' => 'required|string',
            'publicKey' => 'required|string',
        ]);

        // Verifikasi Challenge
        $clientData = json_decode(base64_decode($request->clientDataJSON), true);
        if (!$clientData || base64_decode($clientData['challenge']) !== $expectedChallenge) {
            return response()->json(['success' => false, 'message' => 'Challenge tidak valid.'], 400);
        }

        // Cek origin
        $expectedOrigin = url('/');
        if ($clientData['origin'] !== $expectedOrigin) {
            return response()->json(['success' => false, 'message' => 'Origin tidak sesuai.'], 400);
        }

        // Parse Authenticator Data
        $authData = base64_decode($request->authenticatorData);
        if (strlen($authData) < 37) {
            return response()->json(['success' => false, 'message' => 'Authenticator data tidak valid.'], 400);
        }

        // Cek flag Backup Eligible (BE)
        $flags = ord($authData[32]);
        $backupEligible = ($flags >> 3) & 1; // bit BE (bit ke-3 dari kanan)

        if ($backupEligible) {
            return response()->json([
                'success' => false, 
                'message' => 'Passkey ini dapat disinkronkan ke cloud (contoh: Google Password Manager / iCloud). Sistem hanya mengizinkan passkey yang terikat pada perangkat ini secara fisik. Harap gunakan Windows Hello, Touch ID lokal, atau matikan sinkronisasi passkey.'
            ], 422);
        }

        // Simpan ke DB dalam format PEM agar valid disimpan di kolom TEXT (bukan binary)
        $rawPubKey = base64_decode($request->publicKey); // SPKI dari getPublicKey()
        $pubKeyPem = "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($rawPubKey), 64, "\n") . "-----END PUBLIC KEY-----\n";

        DB::table('users')->where('id', $user->id)->update([
            'device_id' => $request->id,
            'webauthn_public_key' => $pubKeyPem,
            'webauthn_sign_count' => 0,
        ]);

        $request->session()->forget('webauthn_pending_registration');
        $request->session()->put('webauthn_verified', true);

        return response()->json(['success' => true]);
    }

    /**
     * Tampilkan halaman autentikasi perangkat WebAuthn (Login ulang)
     */
    public function authenticatePage(Request $request)
    {
        if (!$request->session()->has('webauthn_pending_verification')) {
            return redirect()->route('dashboard');
        }
        return view('auth.webauthn-authenticate');
    }

    /**
     * Dapatkan options untuk navigator.credentials.get()
     */
    public function authenticateOptions(Request $request)
    {
        $user = Auth::user();
        
        $challenge = Str::random(32);
        $request->session()->put('webauthn_challenge', $challenge);

        $options = [
            'challenge' => base64_encode($challenge),
            'timeout' => 60000,
            'rpId' => $request->getHost(),
            'allowCredentials' => [
                [
                    'type' => 'public-key',
                    'id' => base64_encode(base64_decode(strtr($user->device_id, '-_', '+/'))), // Base64url to base64
                ]
            ],
            'userVerification' => 'preferred',
        ];

        return response()->json($options);
    }

    /**
     * Verifikasi autentikasi WebAuthn (Signature)
     */
    public function authenticateVerify(Request $request)
    {
        $user = Auth::user();
        $expectedChallenge = $request->session()->pull('webauthn_challenge');

        $request->validate([
            'id' => 'required|string',
            'clientDataJSON' => 'required|string',
            'authenticatorData' => 'required|string',
            'signature' => 'required|string',
        ]);

        if ($request->id !== $user->device_id) {
            return response()->json(['success' => false, 'message' => 'Credential ID tidak cocok dengan perangkat ini.'], 400);
        }

        $clientData = json_decode(base64_decode($request->clientDataJSON), true);
        if (!$clientData || base64_decode($clientData['challenge']) !== $expectedChallenge) {
            return response()->json(['success' => false, 'message' => 'Challenge tidak valid.'], 400);
        }

        $expectedOrigin = url('/');
        if ($clientData['origin'] !== $expectedOrigin) {
            return response()->json(['success' => false, 'message' => 'Origin tidak sesuai.'], 400);
        }

        // Verifikasi Signature
        $clientDataHash = hash('sha256', base64_decode($request->clientDataJSON), true);
        $authData = base64_decode($request->authenticatorData);
        
        $signatureBase = $authData . $clientDataHash;
        $signature = base64_decode($request->signature);
        
        // Kunci publik sudah berformat PEM saat registrasi
        $pubKeyPem = $user->webauthn_public_key;
        // Fallback jika secara historis tersimpan sebagai binary (meski seharusnya tidak lagi)
        if (strpos($pubKeyPem, 'BEGIN PUBLIC KEY') === false) {
            $pubKeyPem = "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($pubKeyPem), 64, "\n") . "-----END PUBLIC KEY-----\n";
        }
        
        $publicKey = openssl_pkey_get_public($pubKeyPem);
        if (!$publicKey) {
            return response()->json(['success' => false, 'message' => 'Public key tidak valid di database.'], 500);
        }

        // Signature ECDSA / RSA bergantung pada kunci (Algoritma otomatis dikenali oleh openssl_verify jika format DER benar)
        // openssl_verify menggunakan SHA256 secara default untuk ES256 dan RS256 WebAuthn
        $verified = openssl_verify($signatureBase, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        if ($verified !== 1) {
            return response()->json(['success' => false, 'message' => 'Verifikasi signature gagal.'], 400);
        }

        $request->session()->forget('webauthn_pending_verification');
        $request->session()->put('webauthn_verified', true);

        return response()->json(['success' => true]);
    }
}
