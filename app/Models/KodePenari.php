<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodePenari extends Model {
    // Huruf jalur ikut menjadi bagian kode, contoh: CLR-D-7K4M-X9QP
    public const JALUR = [
        'sekolah' => ['label' => 'Sekolah', 'huruf' => 'S'],
        'desa'    => ['label' => 'Desa', 'huruf' => 'D'],
        'umum'    => ['label' => 'Umum', 'huruf' => 'U'],
        'luar'    => ['label' => 'Luar Banyuwangi', 'huruf' => 'L'],
    ];

    protected $table = 'kode_penari';
    protected $fillable = ['jalur', 'kecamatan_id', 'kelompok_luar_id', 'kode', 'nama_penerima', 'status', 'dipakai_oleh', 'dipakai_pada'];
    protected $casts = ['dipakai_pada' => 'datetime'];

    public function kecamatan() { return $this->belongsTo(Kecamatan::class); }
    public function kelompok() { return $this->belongsTo(KelompokLuar::class, 'kelompok_luar_id'); }
    public function pemakai() { return $this->belongsTo(User::class, 'dipakai_oleh'); }

    public function getLabelJalurAttribute(): string { return self::JALUR[$this->jalur]['label']; }

    public function getAsalAttribute(): string {
        return $this->kecamatan ? 'Kecamatan ' . $this->kecamatan->nama : 'Kelompok ' . ($this->kelompok?->nama ?? '-');
    }
}