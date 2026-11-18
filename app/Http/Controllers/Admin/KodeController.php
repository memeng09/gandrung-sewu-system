<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\KodePenari;
use Illuminate\Http\Request;

class KodeController extends Controller {
    public function index(Request $r) {
        $q = KodePenari::with('kecamatan', 'pemakai.penari')->orderBy('kecamatan_id')->orderBy('id');
        if ($r->filled('status')) $q->where('status', $r->status);
        if ($r->filled('kecamatan')) $q->where('kecamatan_id', $r->kecamatan);
        if ($r->filled('q')) $q->where('kode', 'like', '%' . strtoupper(trim($r->q)) . '%');

        return view('admin.kode', [
            'kodes'     => $q->paginate(25)->withQueryString(),
            'kecamatan' => Kecamatan::orderBy('nama')->get(['id', 'nama']),
            'hitung'    => KodePenari::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    // Cabut satu kode (kartu hilang atau bocor). Hanya kode yang belum dipakai.
    public function cabut(KodePenari $kode) {
        if ($kode->status !== 'belum_dipakai') {
            return back()->with('err', 'Hanya kode yang belum dipakai yang bisa dicabut.');
        }
        $kode->update(['status' => 'dicabut']);
        return back()->with('ok', "Kode {$kode->kode} dicabut. Klik Generate di halaman Kuota untuk membuat kode pengganti.");
    }

    // Batalkan pencabutan (kalau salah klik)
    public function aktifkan(KodePenari $kode) {
        if ($kode->status !== 'dicabut' || $kode->dipakai_oleh) {
            return back()->with('err', 'Kode ini tidak bisa diaktifkan kembali.');
        }
        $kec = $kode->kecamatan;
        if ($kec->kodes()->where('status', '!=', 'dicabut')->count() >= $kec->kuota) {
            return back()->with('err', "Kuota {$kec->nama} sudah penuh, kode tidak bisa diaktifkan kembali.");
        }
        $kode->update(['status' => 'belum_dipakai']);
        return back()->with('ok', "Kode {$kode->kode} aktif kembali.");
    }
}