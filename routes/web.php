<?php
use App\Http\Controllers\Admin;
use App\Http\Controllers\Penari;
use Illuminate\Support\Facades\Route;

Route::view('/', 'beranda')->name('beranda');
Route::get('/penari-lolos', [Penari\CardController::class, 'index'])->name('penari.cards');
Route::view('/kontak', 'kontak')->name('kontak');
Route::view('/tentang', 'tentang')->name('tentang');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'penari.dashboard'))->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/kuota', [Admin\KuotaKodeController::class, 'index'])->name('kuota');
        Route::post('/kuota', [Admin\KuotaKodeController::class, 'simpan'])->name('kuota.simpan');
        Route::post('/kode/generate-semua', [Admin\KuotaKodeController::class, 'generateSemua'])->name('kode.generate-semua');
        Route::post('/kode/{kecamatan}/generate', [Admin\KuotaKodeController::class, 'generate'])->name('kode.generate');
        Route::post('/kode/{kecamatan}/cabut-kelebihan', [Admin\KuotaKodeController::class, 'cabutKelebihan'])->name('kode.cabut');
        Route::get('/kode/{kecamatan}/cetak', [Admin\KuotaKodeController::class, 'cetak'])->name('kode.cetak');
        Route::get('/penari', [Admin\PenariController::class, 'index'])->name('penari');
        Route::post('/penari/{penari}/setujui', [Admin\PenariController::class, 'setujui'])->name('penari.setujui');
        Route::post('/penari/{penari}/cabut', [Admin\PenariController::class, 'cabut'])->name('penari.cabut');
        Route::get('/template', [Admin\TemplateController::class, 'edit'])->name('template');
        Route::post('/template', [Admin\TemplateController::class, 'simpan'])->name('template.simpan');
    });

    Route::middleware('role:penari')->prefix('penari')->name('penari.')->group(function () {
        Route::get('/', [Penari\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/daftar', [Penari\FormController::class, 'create'])->name('form');
        // throttle: batasi tebak-tebakan kode (10 percobaan per menit)
        Route::post('/daftar', [Penari\FormController::class, 'store'])->middleware('throttle:10,1')->name('simpan');
        Route::get('/sertifikat', [Penari\SertifikatController::class, 'download'])->name('sertifikat');
    });

    // Route profil bawaan Breeze
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';