<?php
namespace App\Services;

use App\Models\Kecamatan;
use App\Models\KelompokLuar;
use App\Models\KodePenari;
use App\Models\KuotaJalur;
use Illuminate\Support\Facades\DB;

class KodeGenerator {
    // Tanpa 0/O/1/I/L supaya tidak salah baca saat diketik
    private const HURUF = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /** Buat $jumlah kode. $penerima = daftar nama (opsional), satu nama untuk tiap kode. */
    public function buat(string $jalur, ?Kecamatan $kec, ?KelompokLuar $kel, int $jumlah, array $penerima = []): array {
        $prefix = ($kec?->kode_singkat ?? $kel->kode_singkat) . '-' . KodePenari::JALUR[$jalur]['huruf'];
        $hasil = [];
        for ($i = 0; $i < $jumlah; $i++) {
            do {
                $kode = $prefix . '-' . $this->acak(4) . '-' . $this->acak(4);
            } while (KodePenari::where('kode', $kode)->exists());

            $hasil[] = KodePenari::create([
                'jalur' => $jalur, 'kecamatan_id' => $kec?->id, 'kelompok_luar_id' => $kel?->id,
                'kode' => $kode, 'nama_penerima' => $penerima[$i] ?? null,
            ]);
        }
        return $hasil;
    }

    public function hitung(Kecamatan $kec, string $jalur): int {
        return KodePenari::where('kecamatan_id', $kec->id)->where('jalur', $jalur)->where('status', '!=', 'dicabut')->count();
    }

    /** Buat kode sebanyak sisa kuota. Kuota kosong = tidak membuat apa pun. */
       /** Buat kode sebanyak sisa kuota. Mengembalikan daftar kode baru (kosong kalau kuota kosong atau sudah penuh). */
    public function generateSisa(Kecamatan $kec, string $jalur): array {
        return DB::transaction(function () use ($kec, $jalur) {
            $kuota = KuotaJalur::where('kecamatan_id', $kec->id)->where('jalur', $jalur)->lockForUpdate()->value('kuota');
            if ($kuota === null) return [];
            $sisa = max(0, $kuota - $this->hitung($kec, $jalur));
            return $this->buat($jalur, $kec, null, $sisa);
        });
    }

    /** Tambah kode di luar kuota (untuk jalur yang jumlahnya baru pasti setelah seleksi). */
    public function tambah(Kecamatan $kec, string $jalur, int $jumlah): int {
        DB::transaction(fn () => $this->buat($jalur, $kec, null, $jumlah));
        return $jumlah;
    }

    /** Kuota diturunkan: cabut kode BELUM DIPAKAI yang berlebih. Kode terpakai tidak disentuh. */
    public function cabutKelebihan(Kecamatan $kec, string $jalur): int {
        return DB::transaction(function () use ($kec, $jalur) {
            $kuota = KuotaJalur::where('kecamatan_id', $kec->id)->where('jalur', $jalur)->value('kuota');
            if ($kuota === null) return 0;
            $lebih = $this->hitung($kec, $jalur) - $kuota;
            if ($lebih <= 0) return 0;
            $ids = KodePenari::where('kecamatan_id', $kec->id)->where('jalur', $jalur)->where('status', 'belum_dipakai')
                ->latest('id')->limit($lebih)->pluck('id');
            KodePenari::whereIn('id', $ids)->update(['status' => 'dicabut']);
            return $ids->count();
        });
    }

    private function acak(int $n): string {
        $out = '';
        $max = strlen(self::HURUF) - 1;
        for ($i = 0; $i < $n; $i++) $out .= self::HURUF[random_int(0, $max)];
        return $out;
    }
}