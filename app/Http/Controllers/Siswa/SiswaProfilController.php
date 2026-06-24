<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SiswaProfilController extends Controller
{
    public function show()
    {
        return view('siswa.profil', ['siswa' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $siswa = Auth::user();

        $request->validate([
            'name'          => 'required|string|max:255',
            'nis'           => ['nullable', 'string', 'max:20', Rule::unique('users', 'nis')->ignore($siswa->id)],
            'nisn'          => ['nullable', 'string', 'max:20', Rule::unique('users', 'nisn')->ignore($siswa->id)],
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date|before:today',
            'agama'         => 'nullable|string|max:50',
            'password'      => 'nullable|string|min:6|confirmed',
        ], [
            'name.required'          => 'Nama lengkap wajib diisi.',
            'nis.unique'             => 'NIS ini sudah digunakan oleh siswa lain.',
            'nisn.unique'            => 'NISN ini sudah digunakan oleh siswa lain.',
            'tanggal_lahir.before'   => 'Tanggal lahir tidak valid.',
            'password.min'           => 'Password minimal 6 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
        ]);

        $data = [
            'name'          => $request->name,
            'nis'           => $request->nis,
            'nisn'          => $request->nisn,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'agama'         => $request->agama,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $siswa->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
