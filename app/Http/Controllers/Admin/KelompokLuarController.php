<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\KelompokLuar;
use App\Models\KodePenari;
use App\Services\KodeGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KelompokLuarController extends Controller {
    public function index() {
        return view('admin.kelompok', [
            'kelompok' => KelompokLuar::with(['kodes' => fn ($q) => $q->where('status', '!=', 'dicabut')->orderBy('id')])->orderBy('nama')->get(),
            'totalDibuat' => KodePenari::where('status', '!=', 'dicabut')->count(),
            'target' => Kecamatan::TARGET,
        ]);
    }

    public function simpan(Request $r) {
        $r->merge(['kode_singkat' => strtoupper((string) $r->kode_singkat)]);
        $r->validate([
            'nama' => 'required|string|max:60|unique:kelompok_luar,nama',
            'kode_singkat' => ['required', 'regex:/^[A-Z]{2,5}$/', 'unique:kelompok_luar,kode_singkat', 'unique:kecamatan,kode_singkat'],
        ], ['kode_singkat.regex' => 'Singkatan harus 2 sampai 5 huruf, tanpa angka atau spasi.']);

        KelompokLuar::create($r->only('nama', 'kode_singkat'));
        return back()->with('ok', "Kelompok {$r->nama} dibuat. Sekarang tambahkan kodenya.");
    }

    // Satu nama per baris = satu kode terkunci ke nama itu. Tanpa nama: isi jumlahnya saja.
    public function tambah(Request $r, KelompokLuar $kelompok, KodeGenerator $gen) {
        $r->validate(['nama' => 'nullable|string|max:3000', 'jumlah' => 'nullable|integer|min:1|max:100']);
        $nama = collect(preg_split('/\R/', (string) $r->nama))->map(fn ($n) => trim($n))->filter()->values()->all();
        $jumlah = $nama ? count($nama) : (int) $r->jumlah;

        if ($jumlah < 1) return back()->with('err', 'Isi jumlah kode atau daftar nama penerima.');
        if ($jumlah > 100) return back()->with('err', 'Maksimal 100 kode sekali tambah.');

        $gen->buat('luar', null, $kelompok, $jumlah, $nama);

        $total = KodePenari::where('status', '!=', 'dicabut')->count();
        $pesan = "$jumlah kode {$kelompok->nama} dibuat.";
        if ($total > Kecamatan::TARGET) $pesan .= ' Perhatian: total kode sekarang ' . number_format($total) . ', melebihi target ' . number_format(Kecamatan::TARGET) . '.';
        return back()->with('ok', $pesan);
    }

    public function cetak(KelompokLuar $kelompok) {
        $daftar = $kelompok->kodes()->where('status', 'belum_dipakai')->orderBy('id')->get();
        abort_if($daftar->isEmpty(), 404, 'Tidak ada kode yang belum dipakai untuk dicetak.');
        $daftar->load('kelompok');
        $kelompokKartu = collect(['Luar Banyuwangi - ' . $kelompok->nama => $daftar]);

        return Pdf::loadView('admin.kartu', ['kelompok' => $kelompokKartu])->setPaper('a4')->stream("kartu-kode-{$kelompok->kode_singkat}.pdf");
    }
}