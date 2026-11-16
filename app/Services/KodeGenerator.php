<?php
namespace App\Services;

use App\Models\Kecamatan;
use App\Models\KodePenari;
use Illuminate\Support\Facades\DB;

class KodeGenerator {
    // Tanpa 0/O/1/I/L supaya tidak salah baca saat diketik
    private const HURUF = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /** Buat kode sebanyak sisa kuota kecamatan. Return jumlah kode baru. */
    public function generate(Kecamatan $kec): int {
        return DB::transaction(function () use ($kec) {
            $kec = Kecamatan::lockForUpdate()->findOrFail($kec->id);
            $ada = $kec->kodes()->where('status', '!=', 'dicabut')->count();
            $sisa = max(0, $kec->kuota - $ada);

            for ($i = 0; $i < $sisa; $i++) {
                do {
                    $kode = $kec->kode_singkat . '-' . $this->acak(4) . '-' . $this->acak(4);
                } while (KodePenari::where('kode', $kode)->exists());
                $kec->kodes()->create(['kode' => $kode]);
            }
            return $sisa;
        });
    }

    /** Kuota diturunkan: cabut kode BELUM DIPAKAI yang berlebih. */
    public function cabutKelebihan(Kecamatan $kec): int {
        return DB::transaction(function () use ($kec) {
            $lebih = $kec->kodes()->where('status', '!=', 'dicabut')->count() - $kec->kuota;
            if ($lebih <= 0) return 0;
            $ids = $kec->kodes()->where('status', 'belum_dipakai')->latest('id')->limit($lebih)->pluck('id');
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