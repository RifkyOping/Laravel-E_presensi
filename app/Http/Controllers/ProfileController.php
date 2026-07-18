<?php

namespace App\Http\Controllers;

use App\Models\CatatanLiterasiQuran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     * Siswa → custom profil page (nama, email, NIS, NISN, dll)
     * Others → standard Breeze edit page
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'murid') {
            $totalCatatan = CatatanLiterasiQuran::where('siswa_id', $user->id)->count();
            return view('siswa.profil', ['siswa' => $user, 'totalCatatan' => $totalCatatan]);
        }

        return view('profile.edit', ['user' => $user]);
    }

    /**
     * Update the user's profile information.
     * Siswa → update semua field (nama, email, password, dll)
     * Others → standard name + email update
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // ── Siswa ──────────────────────────────────────────────────
        if ($user->role === 'murid') {
            $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => ['required', 'string', 'email', 'max:255',
                                    Rule::unique('users', 'email')->ignore($user->id)],
                'jenis_kelamin' => 'nullable|in:L,P',
                'tempat_lahir'  => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date|before:today',
                'agama'         => 'nullable|string|max:50',
                'password'      => 'nullable|string|min:6|confirmed',
            ], [
                'name.required'        => 'Nama lengkap wajib diisi.',
                'email.required'       => 'Email wajib diisi.',
                'email.unique'         => 'Email ini sudah digunakan akun lain.',
                'tanggal_lahir.before' => 'Tanggal lahir tidak valid.',
                'password.min'         => 'Password minimal 6 karakter.',
                'password.confirmed'   => 'Konfirmasi password tidak cocok.',
            ]);

            $data = [
                'name'          => $request->name,
                'email'         => $request->email,
            ];

            // Reset verifikasi email jika email berubah
            if ($user->email !== $request->email) {
                $data['email_verified_at'] = null;
            }

            // Hash password baru jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            $user->siswaProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir'  => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'agama'         => $request->agama,
                ]
            );

            return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
        }

        // ── Non-siswa (Guru, Admin, dll) ────────────────────────────
        $request->user()->fill($request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255',
                        Rule::unique('users')->ignore($user->id)],
        ]));

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

}
