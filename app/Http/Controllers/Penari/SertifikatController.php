<?php
namespace App\Http\Controllers\Penari;

use App\Http\Controllers\Controller;
use App\Models\SertifikatTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SertifikatController extends Controller {
    // Data diambil otomatis dari form penari, ditempel ke template buatan admin
    public function download() {
        $penari = auth()->user()->penari?->load('kecamatan');
        abort_unless($penari && $penari->status === 'aktif', 403);

        $tpl = SertifikatTemplate::first();
        if (!$tpl || !$tpl->gambar) return back()->with('err', 'Template sertifikat belum diunggah admin.');

        return Pdf::loadView('sertifikat.pdf', [
            'penari' => $penari, 'tpl' => $tpl, 'bg' => storage_path('app/public/' . $tpl->gambar),
        ])->setPaper('a4', 'landscape')->download('Sertifikat-' . Str::slug($penari->nama) . '.pdf');
    }
}