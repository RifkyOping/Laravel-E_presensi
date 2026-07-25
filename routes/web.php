<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiSiswaController;
use App\Http\Controllers\AbsensiGuruController;
use App\Http\Controllers\AbsensiMengajarController;
use App\Http\Controllers\EBookController;
use App\Http\Controllers\LiterasiQuranController;
use App\Http\Controllers\Guru\IndikatorLiterasiController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminEBookController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\AdminJadwalMengajarController;
use App\Http\Controllers\Admin\IndikatorLiterasiController as AdminIndikatorController;
use App\Http\Controllers\Pengawas\PengawasController;
use App\Http\Controllers\Kurikulum\KurikulumController;
use App\Http\Controllers\Piket\PiketSholatController;
use App\Http\Controllers\Piket\PiketMengajarController;
use App\Http\Controllers\BukuManualController;
use App\Http\Controllers\IndikatorLiterasiController as SiswaIndikatorController;
use App\Http\Controllers\CatatanMembacaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// NPSN Verification
Route::post('/npsn/verify', function (\Illuminate\Http\Request $request) {
    $npsn = $request->input('npsn');
    if ($npsn === '40601489') {
        session(['npsn_verified' => true]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'message' => 'NPSN salah. Silakan coba lagi.'], 422);
})->middleware('auth')->name('npsn.verify');

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
    return redirect()->route('murid.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard Murid
Route::get('/murid/dashboard', function () {
    return view('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('murid.dashboard');

// Dashboard Guru
Route::get('/guru/dashboard', function () {
    return view('guru.dashboard');
})->middleware(['auth', 'verified'])->name('guru.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Murid - Absensi
    Route::get('/absensi', [AbsensiSiswaController::class, 'index'])->name('absensi');
    Route::post('/absensi/datang', [AbsensiSiswaController::class, 'absenDatang'])->name('absensi.datang');
    Route::post('/absensi/pulang', [AbsensiSiswaController::class, 'absenPulang'])->name('absensi.pulang');

    // Murid - Profil (redirect ke /profile agar tidak ambigu)
    Route::get('/murid/profil', fn() => redirect()->route('profile.edit'))->name('murid.profil');

    // Murid - Lihat catatan Literasi Al-Qur'an dari guru
    Route::get('/murid/literasi/quran', [\App\Http\Controllers\Siswa\SiswaLiterasiQuranController::class, 'index'])->name('murid.quran');

    // Riwayat Sholat Murid
    Route::get('/murid/sholat', [AbsensiSiswaController::class, 'riwayatSholat'])->name('murid.sholat');

    // Monitoring Kelas Murid
    Route::get('/murid/monitoring-kelas', [AbsensiSiswaController::class, 'monitoringKelas'])->name('murid.monitoring-kelas');

    // Baca Al-Qur'an (Pure Arabic)
    Route::get('/murid/baca-quran', [\App\Http\Controllers\QuranController::class, 'index'])->name('murid.baca-quran.index');
    Route::get('/murid/baca-quran/surah/{nomor}', [\App\Http\Controllers\QuranController::class, 'show'])->name('murid.baca-quran.show');
    Route::get('/murid/baca-quran/juz/{nomor}', [\App\Http\Controllers\QuranController::class, 'juz'])->name('murid.baca-quran.juz');

    // E-Book Literasi

    Route::get('/literasi/ebook', [EBookController::class, 'index'])->name('ebook.index');
    Route::get('/literasi/ebook/manual', [BukuManualController::class, 'index'])->name('ebook.manual.index');
    Route::get('/literasi/ebook/manual/create/{level}', [BukuManualController::class, 'create'])->name('ebook.manual.create');
    Route::post('/literasi/ebook/manual/store/{level}', [BukuManualController::class, 'store'])->name('ebook.manual.store');
    Route::get('/literasi/ebook/manual/{id}/detail', [BukuManualController::class, 'show'])->name('ebook.manual.show');
    Route::get('/literasi/ebook/manual/{id}/edit', [BukuManualController::class, 'edit'])->name('ebook.manual.edit');
    Route::put('/literasi/ebook/manual/{id}', [BukuManualController::class, 'update'])->name('ebook.manual.update');

    Route::get('/literasi/ebook/{jenis}/{id}/indikator', [SiswaIndikatorController::class, 'show'])->name('ebook.indikator.show');
    Route::post('/literasi/ebook/{jenis}/{id}/indikator', [SiswaIndikatorController::class, 'store'])->name('ebook.indikator.store');
    Route::post('/literasi/catatan', [CatatanMembacaController::class, 'store'])->name('catatan.store');
    Route::get('/literasi/ebook/{ebook}', [EBookController::class, 'read'])->name('ebook.read');
    Route::get('/literasi/ebook/{ebook}/kuis-page', [EBookController::class, 'quizPage'])->name('ebook.quiz.page');
    Route::get('/literasi/ebook/{ebook}/pdf', [EBookController::class, 'streamPdf'])->name('ebook.pdf');
    Route::post('/literasi/ebook/{ebook}/voice-save', [EBookController::class, 'saveVoiceProgress'])->name('ebook.voice-save');
    Route::post('/literasi/ebook/{ebook}/voice-check', [EBookController::class, 'checkVoice'])->name('ebook.voice-check');
    Route::post('/literasi/ebook/{ebook}/voice-skip', [EBookController::class, 'skipVoiceVerification'])->name('ebook.voice-skip');
    Route::get('/literasi/ebook/{ebook}/kuis', [EBookController::class, 'getKuis'])->name('ebook.kuis.get');
    Route::post('/literasi/ebook/{ebook}/kuis', [EBookController::class, 'submitKuis'])->name('ebook.kuis.submit');

    // Guru - Absen Sekolah (datang & pulang)
    Route::get('/guru/absensi', [AbsensiGuruController::class, 'index'])->name('guru.absensi');
    Route::post('/guru/absensi/datang', [AbsensiGuruController::class, 'absenDatang'])->name('guru.absensi.datang');
    Route::post('/guru/absensi/pulang', [AbsensiGuruController::class, 'absenPulang'])->name('guru.absensi.pulang');

    // Guru - Aktivitas Mengajar
    Route::get('/guru/aktivitas', [AbsensiMengajarController::class, 'index'])->name('guru.aktivitas');
    Route::post('/guru/aktivitas', [AbsensiMengajarController::class, 'store'])->name('guru.aktivitas.store');
    Route::patch('/guru/aktivitas/{aktivitas}/masuk', [AbsensiMengajarController::class, 'absenMasuk'])->name('guru.aktivitas.masuk');
    Route::patch('/guru/aktivitas/{aktivitas}/keluar', [AbsensiMengajarController::class, 'absenKeluar'])->name('guru.aktivitas.keluar');
    Route::delete('/guru/aktivitas/{aktivitas}', [AbsensiMengajarController::class, 'destroy'])->name('guru.aktivitas.destroy');

    // Guru - Literasi Al-Qur'an
    Route::get('/guru/literasi/quran', [LiterasiQuranController::class, 'index'])->name('guru.literasi.quran');
    Route::post('/guru/literasi/quran/catatan', [LiterasiQuranController::class, 'store'])->name('guru.literasi.quran.store');
    Route::put('/guru/literasi/quran/catatan/{catatan}', [LiterasiQuranController::class, 'update'])->name('guru.literasi.quran.update');
    Route::delete('/guru/literasi/quran/catatan/{catatan}', [LiterasiQuranController::class, 'destroy'])->name('guru.literasi.quran.destroy');

    // Guru - Catatan Membaca (Digital/Manual)
    Route::get('/guru/literasi/catatan-membaca', [\App\Http\Controllers\Guru\CatatanLiterasiController::class, 'index'])->name('guru.literasi.catatan');

    // Guru - Review Jawaban Indikator E-Book
    Route::get('/guru/literasi/jawaban-indikator', [\App\Http\Controllers\Guru\ReviewIndikatorController::class, 'index'])->name('guru.literasi.jawaban-indikator');
    Route::post('/guru/literasi/jawaban-indikator/nilai', [\App\Http\Controllers\Guru\ReviewIndikatorController::class, 'storeNilai'])->name('guru.literasi.jawaban-indikator.nilai');

    // Guru - Manajemen Pertanyaan Indikator Literasi
    Route::resource('/guru/literasi/manajemen-indikator', IndikatorLiterasiController::class)->parameters([
        'manajemen-indikator' => 'indikator'
    ])->names('guru.indikator');

    // Guru - Jadwal Mengajar (read-only, dikelola oleh admin)
    Route::get('/guru/jadwal', [\App\Http\Controllers\JadwalMengajarController::class, 'index'])->name('guru.jadwal.index');

    // Guru - Persetujuan Absensi Siswa
    Route::get('/guru/persetujuan-absensi', [\App\Http\Controllers\GuruPersetujuanAbsensiController::class, 'index'])->name('guru.persetujuan-absensi');
    Route::post('/guru/persetujuan-absensi/{id}/approve', [\App\Http\Controllers\GuruPersetujuanAbsensiController::class, 'approve'])->name('guru.persetujuan-absensi.approve');
    Route::post('/guru/persetujuan-absensi/{id}/reject', [\App\Http\Controllers\GuruPersetujuanAbsensiController::class, 'reject'])->name('guru.persetujuan-absensi.reject');

    // Guru - Absen Kelas Siswa
    Route::get('/guru/absen-kelas', [App\Http\Controllers\Guru\AbsensiKelasController::class, 'index'])->name('guru.absen-kelas.index');
    Route::get('/guru/absen-kelas/{jadwal}', [App\Http\Controllers\Guru\AbsensiKelasController::class, 'show'])->name('guru.absen-kelas.show');
    Route::get('/guru/absen-kelas/{jadwal}/export', [App\Http\Controllers\Guru\AbsensiKelasController::class, 'export'])->name('guru.absen-kelas.export');
    Route::post('/guru/absen-kelas/{jadwal}', [App\Http\Controllers\Guru\AbsensiKelasController::class, 'store'])->name('guru.absen-kelas.store');
    Route::post('/guru/upload-rpp', [App\Http\Controllers\Guru\AbsensiKelasController::class, 'uploadRpp'])->name('guru.upload-rpp');
    
    // Guru - Buku Kemajuan Kelas
    Route::get('/guru/buku-kemajuan', [App\Http\Controllers\Guru\AbsensiKelasController::class, 'bukuKemajuan'])->name('guru.buku-kemajuan');
    Route::get('/guru/buku-kemajuan/cetak', [App\Http\Controllers\Guru\AbsensiKelasController::class, 'cetakBukuKemajuan'])->name('guru.buku-kemajuan.cetak');
});

require __DIR__ . '/auth.php';

// ──────────────────────────────────────────
// ADMIN ROUTES
// ──────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Manajemen User
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/template-import', [AdminController::class, 'downloadTemplateImport'])->name('users.template-import');
    Route::post('/users/import', [AdminController::class, 'importUsers'])->name('users.import');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::delete('/users/bulk-delete', [AdminController::class, 'bulkDestroyUsers'])->name('users.bulk-delete');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('/users/{user}/reset-device', [AdminController::class, 'resetDevice'])->name('users.reset-device');

    // Monitoring
    Route::get('/absensi-guru', [AdminController::class, 'absensiGuru'])->name('absensi-guru');
    Route::get('/absensi-guru/export', [AdminController::class, 'exportAbsensiGuru'])->name('absensi-guru.export');
    Route::get('/aktivitas-guru', [AdminController::class, 'aktivitasGuru'])->name('aktivitas-guru');
    Route::get('/aktivitas-guru/export', [AdminController::class, 'exportAktivitasGuru'])->name('aktivitas-guru.export');
    Route::get('/absensi-siswa', [AdminController::class, 'absensiSiswa'])->name('absensi-siswa');
    Route::get('/absensi-siswa/export', [AdminController::class, 'exportAbsensiSiswa'])->name('absensi-siswa.export');

    // Rekap Absensi Kelas (absensi siswa yang diinput guru)
    Route::get('/rekap-absensi-kelas', [AdminController::class, 'rekapAbsensiKelas'])->name('rekap-absensi-kelas');
    Route::get('/rekap-absensi-kelas/{jadwal}/detail', [AdminController::class, 'rekapAbsensiKelasDetail'])->name('rekap-absensi-kelas.detail');

    // Persetujuan Izin/Sakit
    Route::get('/persetujuan-absensi', [AdminController::class, 'persetujuanAbsensi'])->name('persetujuan-absensi');
    Route::post('/persetujuan-absensi/{type}/{id}/approve', [AdminController::class, 'approvePengajuan'])->name('persetujuan-absensi.approve');
    Route::post('/persetujuan-absensi/{type}/{id}/reject', [AdminController::class, 'rejectPengajuan'])->name('persetujuan-absensi.reject');

    // Mata Pelajaran
    Route::resource('/mata-pelajaran', MataPelajaranController::class)->names('mata-pelajaran');
    Route::patch('/mata-pelajaran/{mataPelajaran}/toggle', [MataPelajaranController::class, 'toggleAktif'])->name('mata-pelajaran.toggle');

    // Kelas
    Route::resource('/kelas', \App\Http\Controllers\Admin\KelasController::class)->names('kelas');
    Route::patch('/kelas/{kela}/toggle', [\App\Http\Controllers\Admin\KelasController::class, 'toggleAktif'])->name('kelas.toggle');

    // Pengaturan Geofence
    Route::get('/geofence', [AdminController::class, 'geofenceSetting'])->name('geofence');
    Route::post('/geofence', [AdminController::class, 'updateGeofence'])->name('geofence.update');

    // Manajemen E-Book Literasi
    Route::get('/ebook', [AdminEBookController::class, 'index'])->name('ebook.index');
    Route::get('/ebook/students', [AdminEBookController::class, 'studentsVoiceAccess'])->name('ebook.students');
    Route::post('/ebook/students/{user}/toggle', [AdminEBookController::class, 'toggleVoiceAccess'])->name('ebook.students.toggle');
    Route::get('/ebook/create', [AdminEBookController::class, 'create'])->name('ebook.create');
    Route::post('/ebook', [AdminEBookController::class, 'store'])->name('ebook.store');
    Route::get('/ebook/{ebook}/edit', [AdminEBookController::class, 'edit'])->name('ebook.edit');
    Route::put('/ebook/{ebook}', [AdminEBookController::class, 'update'])->name('ebook.update');
    Route::delete('/ebook/{ebook}', [AdminEBookController::class, 'destroy'])->name('ebook.destroy');
    Route::patch('/ebook/{ebook}/toggle', [AdminEBookController::class, 'toggleAktif'])->name('ebook.toggle');

    // Manajemen Jadwal Mengajar
    Route::get('/jadwal-mengajar', [AdminJadwalMengajarController::class, 'index'])->name('jadwal-mengajar.index');
    Route::get('/jadwal-mengajar/template', [AdminJadwalMengajarController::class, 'template'])->name('jadwal-mengajar.template');
    Route::post('/jadwal-mengajar/import', [AdminJadwalMengajarController::class, 'import'])->name('jadwal-mengajar.import');
    Route::get('/jadwal-mengajar/{user}/edit', [AdminJadwalMengajarController::class, 'edit'])->name('jadwal-mengajar.edit');
    Route::post('/jadwal-mengajar/{user}', [AdminJadwalMengajarController::class, 'update'])->name('jadwal-mengajar.update');

});

// ──────────────────────────────────────────
// PENGAWAS ROUTES
// ──────────────────────────────────────────
Route::middleware(['auth', 'pengawas'])->prefix('pengawas')->name('pengawas.')->group(function () {
    Route::get('/dashboard', [PengawasController::class, 'dashboard'])->name('dashboard');
    Route::get('/absensi-guru', [PengawasController::class, 'absensiGuru'])->name('absensi-guru');
    Route::get('/aktivitas-guru', [PengawasController::class, 'aktivitasGuru'])->name('aktivitas-guru');
    Route::get('/absensi-siswa', [PengawasController::class, 'absensiSiswa'])->name('absensi-siswa');
});

// ──────────────────────────────────────────
// KURIKULUM ROUTES
// ──────────────────────────────────────────
Route::middleware(['auth', 'kurikulum'])->prefix('kurikulum')->name('kurikulum.')->group(function () {
    Route::get('/dashboard', [KurikulumController::class, 'dashboard'])->name('dashboard');
    Route::get('/monitoring-mengajar', [KurikulumController::class, 'monitoringMengajar'])->name('monitoring-mengajar');
    Route::get('/verifikasi/{aktivitas}', [KurikulumController::class, 'verifikasi'])->name('verifikasi');
    Route::put('/verifikasi/{aktivitas}', [KurikulumController::class, 'storeVerifikasi'])->name('store-verifikasi');
    Route::delete('/verifikasi/{aktivitas}', [KurikulumController::class, 'hapusVerifikasi'])->name('hapus-verifikasi');

});

// ──────────────────────────────────────────
// PIKET ROUTES
// ──────────────────────────────────────────
Route::middleware(['auth'])->prefix('piket')->name('piket.')->group(function () {
    // Piket Absen Sholat Siswa
    Route::get('/sholat', [PiketSholatController::class, 'index'])->name('sholat.index');
    Route::get('/sholat/export', [PiketSholatController::class, 'export'])->name('sholat.export');
    Route::post('/sholat', [PiketSholatController::class, 'store'])->name('sholat.store');

    // Piket Verifikasi Mengajar
    Route::get('/mengajar', [PiketMengajarController::class, 'index'])->name('mengajar.index');
    Route::get('/mengajar/verifikasi/{aktivitas}', [PiketMengajarController::class, 'verifikasi'])->name('mengajar.verifikasi');
    Route::put('/mengajar/verifikasi/{aktivitas}', [PiketMengajarController::class, 'storeVerifikasi'])->name('mengajar.store-verifikasi');
    Route::delete('/mengajar/verifikasi/{aktivitas}', [PiketMengajarController::class, 'hapusVerifikasi'])->name('mengajar.hapus-verifikasi');

    // Piket Persetujuan RPP
    Route::get('/persetujuan-rpp', [PiketMengajarController::class, 'persetujuanRpp'])->name('persetujuan-rpp');
    Route::post('/persetujuan-rpp/{user}/approve', [PiketMengajarController::class, 'approveRpp'])->name('persetujuan-rpp.approve');
    Route::post('/persetujuan-rpp/{user}/reject', [PiketMengajarController::class, 'rejectRpp'])->name('persetujuan-rpp.reject');
});

// ──────────────────────────────────────────
// CRON JOB ROUTES (Endpoint Terpisah)
// ──────────────────────────────────────────
// Route ini digunakan untuk menjalankan command khusus presensi melalui URL.

Route::get('/cron/cek-alpha', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== env('CRON_SECRET', 'rahasia123'))
        abort(403, 'Akses ditolak.');
    try {
        \Illuminate\Support\Facades\Artisan::call('presensi:cek-alpha');
        return response()->json(['status' => 'success', 'message' => 'Cek Alpha berhasil dijalankan.', 'output' => \Illuminate\Support\Facades\Artisan::output()]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/cron/cek-lupa-pulang', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== env('CRON_SECRET', 'rahasia123'))
        abort(403, 'Akses ditolak.');
    try {
        \Illuminate\Support\Facades\Artisan::call('presensi:cek-lupa-pulang');
        return response()->json(['status' => 'success', 'message' => 'Cek Lupa Pulang berhasil dijalankan.', 'output' => \Illuminate\Support\Facades\Artisan::output()]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/cron/generate-aktivitas-mengajar', function (\Illuminate\Http\Request $request) {
    if ($request->query('token') !== env('CRON_SECRET', 'rahasia123'))
        abort(403, 'Akses ditolak.');
    try {
        \Illuminate\Support\Facades\Artisan::call('presensi:generate-aktivitas-mengajar');
        return response()->json(['status' => 'success', 'message' => 'Generate Aktivitas Mengajar berhasil dijalankan.', 'output' => \Illuminate\Support\Facades\Artisan::output()]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});
