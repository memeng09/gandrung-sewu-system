<?php

namespace App\Http\Controllers\Penari;

use App\Http\Controllers\Controller;
use App\Models\SertifikatTemplate;
use App\Services\QrCodeGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SertifikatController extends Controller
{
    public function download(QrCodeGenerator $qr)
    {
        $penari = auth()->user()->penari?->load('kecamatan');
        abort_unless($penari && $penari->status === 'aktif', 403);
        $penari->pastikanKodeVerifikasi();

        $tpl = SertifikatTemplate::first();
        if (! $tpl || ! $tpl->gambar) {
            return back()->with('err', 'Template sertifikat belum diunggah admin.');
        }

        $land = $tpl->orientasi !== 'portrait';

        return Pdf::loadView('sertifikat.pdf', [
            'penari' => $penari, 'tpl' => $tpl,
            'bg' => str_replace('\\', '/', storage_path('app/public/'.$tpl->gambar)),
            'W' => $land ? 841.89 : 595.28,
            'H' => $land ? 595.28 : 841.89,
            'fontCustom' => SertifikatTemplate::fontCustom(),
            'qrDataUri' => $qr->dataUri(url('/verifikasi/'.$penari->kode_verifikasi)),
        ])->setPaper('a4', $land ? 'landscape' : 'portrait')
            ->download('Sertifikat-'.Str::slug($penari->nama).'.pdf');
    }
}
