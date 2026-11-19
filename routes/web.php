<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Penari;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

$jalurKec = ['sekolah', 'desa', 'umum'];

Route::view('/', 'beranda')->name('beranda');
Route::get('/penari-lolos', [Penari\CardController::class, 'index'])->name('penari.cards');
Route::get('/verifikasi/{kode}', [VerifikasiController::class, 'show'])->name('verifikasi.sertifikat');
Route::view('/kontak', 'kontak')->name('kontak');
Route::view('/tentang', 'tentang')->name('tentang');

Route::middleware('auth')->group(function () use ($jalurKec) {
    Route::get('/dashboard', fn () => redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'penari.dashboard'))->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () use ($jalurKec) {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Kuota dan kode jalur Sekolah / Desa / Umum
        Route::get('/kuota', [Admin\KuotaKodeController::class, 'index'])->name('kuota');
        Route::post('/kuota', [Admin\KuotaKodeController::class, 'simpan'])->name('kuota.simpan');
        Route::post('/kuota/cetak', [Admin\KuotaKodeController::class, 'buatCetak'])->name('kuota.buat-cetak');
        Route::get('/kuota/cetak', [Admin\KuotaKodeController::class, 'cetak'])->name('kuota.cetak');
        Route::post('/kuota/{kecamatan}/{jalur}/cabut', [Admin\KuotaKodeController::class, 'cabutKelebihan'])->whereIn('jalur', $jalurKec)->name('kuota.cabut');

        // Kelompok luar Banyuwangi
        Route::get('/kuota-luar', [Admin\KelompokLuarController::class, 'index'])->name('kuota.luar');
        Route::post('/kuota-luar', [Admin\KelompokLuarController::class, 'simpan'])->name('kuota.luar.simpan');
        Route::post('/kuota-luar/{kelompok}/tambah', [Admin\KelompokLuarController::class, 'tambah'])->name('kuota.luar.tambah');
        Route::get('/kuota-luar/{kelompok}/cetak', [Admin\KelompokLuarController::class, 'cetak'])->name('kuota.luar.cetak');

        // Daftar semua kode
        Route::get('/kode', [Admin\KodeController::class, 'index'])->name('kode');
        Route::post('/kode/{kode}/cabut', [Admin\KodeController::class, 'cabut'])->name('kode.cabut-satu');
        Route::post('/kode/{kode}/aktifkan', [Admin\KodeController::class, 'aktifkan'])->name('kode.aktifkan');
        Route::post('/kode/{kode}/lepas', [Admin\KodeController::class, 'lepas'])->name('kode.lepas');

        // Data penari
        Route::get('/penari', [Admin\PenariController::class, 'index'])->name('penari');
        Route::get('/penari-export/excel', [Admin\PenariController::class, 'excel'])->name('penari.excel');
        Route::get('/penari-export/pdf', [Admin\PenariController::class, 'pdf'])->name('penari.pdf');
        Route::get('/penari/{penari}/edit', [Admin\PenariController::class, 'edit'])->name('penari.edit');
        Route::put('/penari/{penari}', [Admin\PenariController::class, 'update'])->name('penari.update');
        Route::post('/penari/{penari}/setujui', [Admin\PenariController::class, 'setujui'])->name('penari.setujui');
        Route::post('/penari/{penari}/cabut', [Admin\PenariController::class, 'cabut'])->name('penari.cabut');
        Route::post('/penari/{penari}/reset-password', [Admin\PenariController::class, 'resetPassword'])->name('penari.reset-password');

        // Template sertifikat
        Route::get('/template', [Admin\TemplateController::class, 'edit'])->name('template');
        Route::post('/template', [Admin\TemplateController::class, 'simpan'])->name('template.simpan');
    });

    Route::middleware('role:penari')->prefix('penari')->name('penari.')->group(function () {
        Route::get('/', [Penari\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/daftar', [Penari\FormController::class, 'create'])->name('form');
        Route::post('/daftar/kode', [Penari\FormController::class, 'cekKode'])->middleware('throttle:10,1')->name('kode');
        Route::post('/daftar', [Penari\FormController::class, 'store'])->name('simpan');
        Route::get('/sertifikat', [Penari\SertifikatController::class, 'download'])->name('sertifikat');
    });

    // Route profil bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';