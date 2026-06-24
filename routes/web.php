<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiSiswaController;
use App\Http\Controllers\AbsensiGuruController;
use App\Http\Controllers\AbsensiMengajarController;
use App\Http\Controllers\EBookController;
use App\Http\Controllers\LiterasiQuranController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminEBookController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Pengawas\PengawasController;
use App\Http\Controllers\Kurikulum\KurikulumController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Generic /dashboard redirect berdasarkan role
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role === 'guru') {
        return redirect()->route('guru.dashboard');
    }
    if ($user->role === 'pengawas') {
        return redirect()->route('pengawas.dashboard');
    }
    if ($user->role === 'kurikulum') {
        return redirect()->route('kurikulum.dashboard');
    }
    return redirect()->route('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard Siswa
Route::get('/siswa/dashboard', function () {
    return view('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('siswa.dashboard');

// Dashboard Guru
Route::get('/guru/dashboard', function () {
    return view('guru.dashboard');
})->middleware(['auth', 'verified'])->name('guru.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Siswa - Absensi
    Route::get('/absensi',             [AbsensiSiswaController::class, 'index'])->name('absensi');
    Route::post('/absensi/datang',     [AbsensiSiswaController::class, 'absenDatang'])->name('absensi.datang');
    Route::post('/absensi/pulang',     [AbsensiSiswaController::class, 'absenPulang'])->name('absensi.pulang');

    // Siswa - Profil (redirect ke /profile agar tidak ambigu)
    Route::get('/siswa/profil', fn() => redirect()->route('profile.edit'))->name('siswa.profil');

    // Siswa - Lihat catatan Literasi Al-Qur'an dari guru
    Route::get('/siswa/literasi/quran', [\App\Http\Controllers\Siswa\SiswaLiterasiQuranController::class, 'index'])->name('siswa.quran');

    // E-Book Literasi
    Route::get('/literasi/ebook',                      [EBookController::class, 'index'])->name('ebook.index');
    Route::get('/literasi/ebook/{ebook}',              [EBookController::class, 'read'])->name('ebook.read');
    Route::get('/literasi/ebook/{ebook}/pdf',          [EBookController::class, 'streamPdf'])->name('ebook.pdf');
    Route::post('/literasi/ebook/{ebook}/voice-check', [EBookController::class, 'checkVoice'])->name('ebook.voice-check');
    Route::get('/literasi/ebook/{ebook}/kuis',         [EBookController::class, 'getKuis'])->name('ebook.kuis.get');
    Route::post('/literasi/ebook/{ebook}/kuis',        [EBookController::class, 'submitKuis'])->name('ebook.kuis.submit');

    // Guru - Absen Sekolah (datang & pulang)
    Route::get('/guru/absensi', [AbsensiGuruController::class, 'index'])->name('guru.absensi');
    Route::post('/guru/absensi/datang', [AbsensiGuruController::class, 'absenDatang'])->name('guru.absensi.datang');
    Route::post('/guru/absensi/pulang', [AbsensiGuruController::class, 'absenPulang'])->name('guru.absensi.pulang');

    // Guru - Aktivitas Mengajar
    Route::get('/guru/aktivitas', [AbsensiMengajarController::class, 'index'])->name('guru.aktivitas');
    Route::post('/guru/aktivitas', [AbsensiMengajarController::class, 'store'])->name('guru.aktivitas.store');
    Route::delete('/guru/aktivitas/{aktivitas}', [AbsensiMengajarController::class, 'destroy'])->name('guru.aktivitas.destroy');

    // Guru - Literasi Al-Qur'an
    Route::get('/guru/literasi/quran',                         [LiterasiQuranController::class, 'index'])->name('guru.literasi.quran');
    Route::post('/guru/literasi/quran/catatan',                [LiterasiQuranController::class, 'store'])->name('guru.literasi.quran.store');
    Route::put('/guru/literasi/quran/catatan/{catatan}',       [LiterasiQuranController::class, 'update'])->name('guru.literasi.quran.update');
    Route::delete('/guru/literasi/quran/catatan/{catatan}',    [LiterasiQuranController::class, 'destroy'])->name('guru.literasi.quran.destroy');
});

require __DIR__.'/auth.php';

// ──────────────────────────────────────────
// ADMIN ROUTES
// ──────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',         [AdminController::class, 'dashboard'])->name('dashboard');

    // Manajemen User
    Route::get('/users',             [AdminController::class, 'users'])->name('users');
    Route::get('/users/template-import', [AdminController::class, 'downloadTemplateImport'])->name('users.template-import');
    Route::post('/users/import',     [AdminController::class, 'importUsers'])->name('users.import');
    Route::get('/users/create',      [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users',            [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}',      [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}',   [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Monitoring
    Route::get('/absensi-guru',   [AdminController::class, 'absensiGuru'])->name('absensi-guru');
    Route::get('/absensi-guru/export', [AdminController::class, 'exportAbsensiGuru'])->name('absensi-guru.export');
    Route::get('/aktivitas-guru', [AdminController::class, 'aktivitasGuru'])->name('aktivitas-guru');
    Route::get('/absensi-siswa',  [AdminController::class, 'absensiSiswa'])->name('absensi-siswa');
    Route::get('/absensi-siswa/export', [AdminController::class, 'exportAbsensiSiswa'])->name('absensi-siswa.export');

    // Mata Pelajaran
    Route::resource('/mata-pelajaran', MataPelajaranController::class)->names('mata-pelajaran');
    Route::patch('/mata-pelajaran/{mataPelajaran}/toggle', [MataPelajaranController::class, 'toggleAktif'])->name('mata-pelajaran.toggle');

    // Pengaturan Geofence
    Route::get('/geofence',  [AdminController::class, 'geofenceSetting'])->name('geofence');
    Route::post('/geofence', [AdminController::class, 'updateGeofence'])->name('geofence.update');

    // Manajemen E-Book Literasi
    Route::get('/ebook',                       [AdminEBookController::class, 'index'])->name('ebook.index');
    Route::get('/ebook/create',                [AdminEBookController::class, 'create'])->name('ebook.create');
    Route::post('/ebook',                      [AdminEBookController::class, 'store'])->name('ebook.store');
    Route::get('/ebook/{ebook}/edit',          [AdminEBookController::class, 'edit'])->name('ebook.edit');
    Route::put('/ebook/{ebook}',               [AdminEBookController::class, 'update'])->name('ebook.update');
    Route::delete('/ebook/{ebook}',            [AdminEBookController::class, 'destroy'])->name('ebook.destroy');
    Route::patch('/ebook/{ebook}/toggle',      [AdminEBookController::class, 'toggleAktif'])->name('ebook.toggle');
});

// ──────────────────────────────────────────
// PENGAWAS ROUTES
// ──────────────────────────────────────────
Route::middleware(['auth', 'pengawas'])->prefix('pengawas')->name('pengawas.')->group(function () {
    Route::get('/dashboard',       [PengawasController::class, 'dashboard'])->name('dashboard');
    Route::get('/absensi-guru',    [PengawasController::class, 'absensiGuru'])->name('absensi-guru');
    Route::get('/aktivitas-guru',  [PengawasController::class, 'aktivitasGuru'])->name('aktivitas-guru');
    Route::get('/absensi-siswa',   [PengawasController::class, 'absensiSiswa'])->name('absensi-siswa');
});

// ──────────────────────────────────────────
// KURIKULUM ROUTES
// ──────────────────────────────────────────
Route::middleware(['auth', 'kurikulum'])->prefix('kurikulum')->name('kurikulum.')->group(function () {
    Route::get('/dashboard',             [KurikulumController::class, 'dashboard'])->name('dashboard');
    Route::get('/monitoring-mengajar',   [KurikulumController::class, 'monitoringMengajar'])->name('monitoring-mengajar');
    Route::get('/verifikasi/{aktivitas}', [KurikulumController::class, 'verifikasi'])->name('verifikasi');
    Route::put('/verifikasi/{aktivitas}', [KurikulumController::class, 'storeVerifikasi'])->name('store-verifikasi');
    Route::delete('/verifikasi/{aktivitas}', [KurikulumController::class, 'hapusVerifikasi'])->name('hapus-verifikasi');
});
