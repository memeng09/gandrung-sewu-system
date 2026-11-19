<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\KodePenari;
use App\Models\KuotaJalur;
use Illuminate\Http\Request;

class KodeController extends Controller {
    public function index(Request $r) {
        $q = KodePenari::with('kecamatan', 'kelompok', 'pemakai.penari')->orderBy('jalur')->orderBy('kecamatan_id')->orderBy('id');
        if ($r->filled('status')) $q->where('status', $r->status);
        if ($r->filled('jalur')) $q->where('jalur', $r->jalur);
        if ($r->filled('kecamatan')) $q->where('kecamatan_id', $r->kecamatan);
        if ($t = $r->q) $q->where(fn ($w) => $w->where('kode', 'like', '%' . strtoupper(trim($t)) . '%')->orWhere('nama_penerima', 'like', "%$t%"));

        return view('admin.kode', [
            'kodes'     => $q->paginate(25)->withQueryString(),
            'kecamatan' => Kecamatan::orderBy('nama')->get(['id', 'nama']),
            'hitung'    => KodePenari::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    // Cabut satu kode (kartu hilang atau bocor). Hanya yang belum dipakai.
    public function cabut(KodePenari $kode) {
        if ($kode->status !== 'belum_dipakai') return back()->with('err', 'Hanya kode yang belum dipakai yang bisa dicabut.');
        $kode->update(['status' => 'dicabut']);
        return back()->with('ok', "Kode {$kode->kode} dicabut. Buat kode pengganti lewat halaman Kuota & Kode.");
    }
        // Kode terkunci akun tetapi datanya belum diisi (misalnya salah memasukkan kode): lepaskan supaya bisa dipakai lagi
    public function lepas(KodePenari $kode) {
        if ($kode->status !== 'dipakai' || $kode->pemakai?->penari) {
            return back()->with('err', 'Hanya kode yang terkunci akun tetapi belum dipakai mendaftar yang bisa dilepas.');
        }
        $kode->update(['status' => 'belum_dipakai', 'dipakai_oleh' => null, 'dipakai_pada' => null]);
        return back()->with('ok', "Kode {$kode->kode} dilepas dan bisa dipakai lagi.");
    }

    public function aktifkan(KodePenari $kode) {
        if ($kode->status !== 'dicabut' || $kode->dipakai_oleh) return back()->with('err', 'Kode ini tidak bisa diaktifkan kembali.');

        if ($kode->jalur !== 'luar') {
            $kuota = KuotaJalur::where('kecamatan_id', $kode->kecamatan_id)->where('jalur', $kode->jalur)->value('kuota');
            $ada = KodePenari::where('kecamatan_id', $kode->kecamatan_id)->where('jalur', $kode->jalur)->where('status', '!=', 'dicabut')->count();
            if ($kuota !== null && $ada >= $kuota) return back()->with('err', 'Kuota jalur ini sudah penuh, kode tidak bisa diaktifkan kembali.');
        }
        $kode->update(['status' => 'belum_dipakai']);
        return back()->with('ok', "Kode {$kode->kode} aktif kembali.");
    }
}