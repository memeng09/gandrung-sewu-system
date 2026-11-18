<?php

namespace App\Http\Controllers;

use App\Models\Penari;
use Illuminate\Contracts\View\View;

class VerifikasiController extends Controller
{
    public function show(string $kode): View
    {
        $penari = Penari::where('kode_verifikasi', strtoupper($kode))
            ->with('kecamatan')
            ->first();

        return view('verifikasi.show', [
            'penari' => $penari,
            'kode' => strtoupper($kode),
            'tahun' => $penari?->created_at?->year ?? now()->year,
        ]);
    }
}
