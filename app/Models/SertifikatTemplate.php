<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatTemplate extends Model {
    protected $fillable = ['gambar', 'orientasi', 'elemen', 'nama_y', 'nama_size', 'info_y', 'nomor_y', 'warna'];
    protected $casts = ['elemen' => 'array'];

    // Font bawaan PDF + font tambahan dari public/fonts/sertifikat/*.ttf
    public static function daftarFont(): array {
        return array_merge(['Times', 'Helvetica', 'DejaVu Serif', 'DejaVu Sans'], self::fontCustom());
    }

    public static function fontCustom(): array {
        return collect(glob(public_path('fonts/sertifikat/*.ttf')) ?: [])
            ->map(fn ($p) => pathinfo($p, PATHINFO_FILENAME))->values()->all();
    }

    public function elemenAktif(): array {
        return $this->elemen ?: self::elemenDefault();
    }

    // Contoh awal supaya editor tidak kosong. Posisi dalam persen halaman.
    public static function elemenDefault(): array {
        $e = fn ($teks, $y, $ukuran, $o = []) => array_merge([
            'teks' => $teks, 'x' => 10, 'y' => $y, 'w' => 80, 'ukuran' => $ukuran, 'font' => 'Times',
            'tebal' => false, 'miring' => false, 'rata' => 'center', 'warna' => '#2A1A14',
        ], $o);

        return [
            $e('SERTIFIKAT', 14, 40, ['tebal' => true, 'warna' => '#5E1220']),
            $e('Nomor: {nomor}', 27, 13, ['font' => 'Helvetica']),
            $e('Diberikan kepada', 37, 14, ['miring' => true]),
            $e('{nama}', 44, 38, ['tebal' => true, 'warna' => '#8B1E2D']),
            $e("Atas partisipasinya sebagai penari dalam Festival Gandrung Sewu 2026\nmewakili Kecamatan {kecamatan}.", 60, 15, ['x' => 15, 'w' => 70]),
            $e('Banyuwangi, {tanggal}', 76, 13, ['x' => 55, 'w' => 35]),
            $e('Ketua Panitia', 81, 13, ['x' => 55, 'w' => 35, 'tebal' => true]),
            $e('(Nama Penandatangan)', 90, 13, ['x' => 55, 'w' => 35]),
        ];
    }
}