<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penari extends Model {
    protected $table = 'penari';
    protected $fillable = ['user_id', 'kode_penari_id', 'kecamatan_id', 'nama', 'tanggal_lahir', 'alamat',
        'no_wa', 'sanggar', 'foto', 'status', 'catatan'];
    protected $casts = ['tanggal_lahir' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function kecamatan() { return $this->belongsTo(Kecamatan::class); }
    public function kode() { return $this->belongsTo(KodePenari::class, 'kode_penari_id'); }

    public function getNomorSertifikatAttribute(): string {
        return sprintf('GS26/%04d/%s', $this->id, $this->created_at->format('Y'));
    }
        // Mengganti penanda {nama}, {kecamatan}, dst. dengan data penari
    public function isiTeks(string $teks): string {
        return strtr($teks, [
            '{nama}'          => $this->nama,
            '{kecamatan}'     => $this->kecamatan->nama,
            '{sanggar}'       => $this->sanggar ?: '-',
            '{tanggal_lahir}' => $this->tanggal_lahir->locale('id')->translatedFormat('d F Y'),
            '{nomor}'         => $this->nomor_sertifikat,
            '{tanggal}'       => now()->locale('id')->translatedFormat('d F Y'),
        ]);
    }
}