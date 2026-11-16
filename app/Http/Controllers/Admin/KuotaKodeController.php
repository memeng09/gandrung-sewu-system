<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Services\KodeGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KuotaKodeController extends Controller {
    public function index() {
        $kec = Kecamatan::withCount([
            'kodes as kode_dibuat' => fn ($q) => $q->where('status', '!=', 'dicabut'),
            'kodes as kode_dipakai' => fn ($q) => $q->where('status', 'dipakai'),
        ])->orderBy('nama')->get();
        return view('admin.kuota', ['kec' => $kec, 'target' => Kecamatan::TARGET]);
    }

    // Admin mengisi kuota tiap kecamatan
    public function simpan(Request $r) {
        $r->validate(['kuota' => 'required|array', 'kuota.*' => 'required|integer|min:0|max:500']);
        foreach ($r->kuota as $id => $jumlah) {
            Kecamatan::whereKey($id)->update(['kuota' => $jumlah]);
        }
        return back()->with('ok', 'Kuota disimpan. Klik Generate untuk membuat kodenya.');
    }

    // Satu klik: kode dibuat otomatis sesuai sisa kuota kecamatan
    public function generate(Kecamatan $kecamatan, KodeGenerator $gen) {
        $n = $gen->generate($kecamatan);
        return back()->with('ok', $n ? "$n kode untuk {$kecamatan->nama} berhasil dibuat." : "Kuota {$kecamatan->nama} sudah penuh.");
    }

    public function generateSemua(KodeGenerator $gen) {
        $total = 0;
        foreach (Kecamatan::where('kuota', '>', 0)->get() as $kec) $total += $gen->generate($kec);
        return back()->with('ok', "$total kode baru dibuat untuk semua kecamatan.");
    }

    public function cabutKelebihan(Kecamatan $kecamatan, KodeGenerator $gen) {
        $n = $gen->cabutKelebihan($kecamatan);
        return back()->with('ok', "$n kode belum dipakai dicabut dari {$kecamatan->nama}.");
    }

    // PDF kartu kode siap potong (hanya kode yang belum dipakai)
    public function cetak(Kecamatan $kecamatan) {
        $kodes = $kecamatan->kodes()->where('status', 'belum_dipakai')->orderBy('id')->get();
        return Pdf::loadView('admin.kartu', compact('kecamatan', 'kodes'))
            ->setPaper('a4')->stream("kartu-kode-{$kecamatan->kode_singkat}.pdf");
    }
}