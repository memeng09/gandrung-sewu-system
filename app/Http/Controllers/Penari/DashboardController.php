<?php
namespace App\Http\Controllers\Penari;

use App\Http\Controllers\Controller;
use App\Models\KodePenari;

class DashboardController extends Controller {
    public function index() {
        return view('penari.dashboard', [
            'penari'    => auth()->user()->penari?->load('kecamatan', 'kode'),
            // kode sudah terkunci ke akun ini (langkah 1 selesai), tetapi data diri belum dikirim
            'punyaKode' => KodePenari::where('dipakai_oleh', auth()->id())->where('status', 'dipakai')->exists(),
        ]);
    }
}