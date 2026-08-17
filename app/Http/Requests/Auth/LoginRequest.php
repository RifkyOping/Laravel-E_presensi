<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nomor_induk' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Cek apakah input adalah Nomor Induk/NIP/NISN di tabel users
        $inputNomorInduk = $this->input('nomor_induk');
        $user = \App\Models\User::where('nomor_induk', $inputNomorInduk)->first();

        // Jika tidak ditemukan di tabel users, cek apakah itu NIS di tabel siswa_profiles
        if (!$user) {
            $profile = \App\Models\SiswaProfile::where('nis', $inputNomorInduk)->first();
            if ($profile) {
                $user = $profile->user;
            }
        }

        if (!$user) {
            // Nomor Induk/NIP/NIS tidak ditemukan sama sekali
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'nomor_induk' => 'Nomor Induk/NIP/NISN/NIS tidak terdaftar.',
            ]);
        }

        // Akun ditemukan, cek password
        if (!\Illuminate\Support\Facades\Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => 'Kata sandi yang Anda masukkan salah.',
            ]);
        }

        Auth::login($user, true); // always remember=true

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nomor_induk' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('nomor_induk')) . '|' . $this->ip());
    }
}
