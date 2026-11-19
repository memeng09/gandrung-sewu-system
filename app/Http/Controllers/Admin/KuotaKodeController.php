<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\KodePenari;
use App\Models\KuotaJalur;
use App\Services\KodeGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KuotaKodeController extends Controller {
    public function index(Request $r) {
        $jalur = in_array($r->jalur, ['sekolah', 'desa', 'umum']) ? $r->jalur : 'sekolah';

        $kuota = KuotaJalur::where('jalur', $jalur)->pluck('kuota', 'kecamatan_id');
        $kode = KodePenari::where('jalur', $jalur)
            ->selectRaw("kecamatan_id, SUM(CASE WHEN status != 'dicabut' THEN 1 ELSE 0 END) AS dibuat, SUM(CASE WHEN status = 'dipakai' THEN 1 ELSE 0 END) AS dipakai")
            ->groupBy('kecamatan_id')->get()->keyBy('kecamatan_id');

        $kec = Kecamatan::orderBy('nama')->get()->map(function ($k) use ($kuota, $kode) {
            $k->kuota = $kuota[$k->id] ?? null;
            $k->dibuat = (int) ($kode[$k->id]->dibuat ?? 0);
            $k->dipakai = (int) ($kode[$k->id]->dipakai ?? 0);
            return $k;
        });

        return view('admin.kuota', [
            'kec' => $kec, 'jalur' => $jalur, 'target' => Kecamatan::TARGET,
            'totalDibuat' => KodePenari::where('status', '!=', 'dicabut')->count(),
        ]);
    }

    // Simpan jumlah kode tiap kecamatan, lalu buat kode yang kurang. Mengembalikan kode yang baru dibuat.
    private function proses(Request $r, KodeGenerator $gen) {
        $r->validate([
            'jalur' => 'required|in:sekolah,desa,umum',
            'kuota' => 'required|array', 'kuota.*' => 'nullable|integer|min:0|max:1000',
            'kecamatan' => 'nullable|array', 'kecamatan.*' => 'integer',
        ]);

        $valid = Kecamatan::pluck('id')->all();
        foreach ($r->kuota as $id => $jumlah) {
            if (!in_array((int) $id, $valid)) continue;
            KuotaJalur::updateOrCreate(
                ['kecamatan_id' => $id, 'jalur' => $r->jalur],
                ['kuota' => ($jumlah === null || $jumlah === '') ? null : (int) $jumlah]
            );
        }

        $baru = collect();
        foreach (Kecamatan::whereIn('id', array_keys($r->kuota))->get() as $kec) {
            $baru = $baru->merge($gen->generateSisa($kec, $r->jalur));
        }
        return $baru;
    }

    private function peringatan(): string {
        $total = KodePenari::where('status', '!=', 'dicabut')->count();
        return $total > Kecamatan::TARGET ? ' Perhatian: total kode ' . number_format($total) . ' melebihi target ' . number_format(Kecamatan::TARGET) . '.' : '';
    }

    // Tombol "Simpan saja"
    public function simpan(Request $r, KodeGenerator $gen) {
        $baru = $this->proses($r, $gen);
        $pesan = $baru->count() ? $baru->count() . ' kode baru dibuat. Jumlah kode tersimpan.' : 'Jumlah kode tersimpan. Tidak ada kode baru yang perlu dibuat.';
        return back()->with('ok', $pesan . $this->peringatan());
    }

    // Tombol "Buat kode & cetak kartu"
    public function buatCetak(Request $r, KodeGenerator $gen) {
        $baru = $this->proses($r, $gen);
        $dipilih = KodePenari::where('jalur', $r->jalur)->where('status', 'belum_dipakai')
            ->whereIn('kecamatan_id', $r->kecamatan ?? [])->pluck('id');
        $ids = $baru->pluck('id')->merge($dipilih)->unique();

        // Tidak ada kode baru dan tidak ada yang dicentang: cetak semua kartu belum terpakai
        // di kecamatan yang jumlah kodenya sudah diisi
        if ($ids->isEmpty()) {
            $terisi = collect($r->kuota)->filter(fn ($v) => $v !== null && $v !== '')->keys();
            $ids = KodePenari::where('jalur', $r->jalur)->where('status', 'belum_dipakai')
                ->whereIn('kecamatan_id', $terisi)->pluck('id');
        }
        return $this->kartu(KodePenari::whereIn('id', $ids));
    }

    // Cetak ulang kartu yang belum terpakai
    public function cetak(Request $r) {
        $r->validate(['kecamatan' => 'required|array', 'kecamatan.*' => 'integer', 'jalur' => 'nullable|in:sekolah,desa,umum']);
        $q = KodePenari::whereIn('kecamatan_id', $r->kecamatan)->where('status', 'belum_dipakai');
        if ($r->filled('jalur')) $q->where('jalur', $r->jalur);
        return $this->kartu($q);
    }

    private function kartu($q) {
        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $kelompok = $q->with('kecamatan')->orderBy('kecamatan_id')->orderBy('jalur')->orderBy('id')->get()
            ->groupBy(fn ($k) => $k->asal . ' - Jalur ' . $k->label_jalur);

        if ($kelompok->isEmpty()) {
            return redirect()->route('admin.kuota', ['jalur' => request('jalur')])
                ->with('err', 'Belum ada kartu untuk dicetak. Isi jumlah kode terlebih dahulu, atau semua kartu sudah terpakai.');
        }
        return Pdf::loadView('admin.kartu', compact('kelompok'))->setPaper('a4')->stream('kartu-kode.pdf');
    }

    // Jumlah diturunkan: cabut kode BELUM DIPAKAI yang berlebih. Kode terpakai tidak disentuh.
    public function cabutKelebihan(Kecamatan $kecamatan, string $jalur, KodeGenerator $gen) {
        $n = $gen->cabutKelebihan($kecamatan, $jalur);
        return back()->with('ok', "$n kode belum dipakai dicabut dari {$kecamatan->nama}.");
    }
}