<?php

namespace App\Models;

use App\Services\KodeVerifikasiGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Penari extends Model
{
    protected $table = 'penari';

    protected $fillable = ['user_id', 'kode_penari_id', 'kecamatan_id', 'nama', 'tanggal_lahir', 'alamat',
        'no_wa', 'sanggar', 'foto', 'status', 'catatan'];

    protected $casts = ['tanggal_lahir' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kode()
    {
        return $this->belongsTo(KodePenari::class, 'kode_penari_id');
    }

    public function getNomorSertifikatAttribute(): string
    {
        return sprintf('GS26/%04d/%s', $this->id, $this->created_at->format('Y'));
    }

    public function pastikanKodeVerifikasi(): string
    {
        if ($this->kode_verifikasi) {
            return $this->kode_verifikasi;
        }

        $generator = app(KodeVerifikasiGenerator::class);

        for ($percobaan = 0; $percobaan < 5; $percobaan++) {
            $kode = $generator->generate();

            try {
                $terubah = static::whereKey($this->id)
                    ->whereNull('kode_verifikasi')
                    ->update(['kode_verifikasi' => $kode]);
            } catch (QueryException $e) {
                if (! $this->adalahBentrokKodeUnik($e)) {
                    throw $e;
                }

                $tersimpan = $this->fresh()?->kode_verifikasi;
                if ($tersimpan) {
                    return $this->kode_verifikasi = $tersimpan;
                }

                continue;
            }

            if ($terubah === 1) {
                return $this->kode_verifikasi = $kode;
            }

            $tersimpan = $this->fresh()?->kode_verifikasi;
            if ($tersimpan) {
                return $this->kode_verifikasi = $tersimpan;
            }
        }

        throw new \RuntimeException('Gagal membuat kode verifikasi unik.');
    }

    private function adalahBentrokKodeUnik(QueryException $e): bool
    {
        $sqlState = (string) $e->getCode();

        if ($sqlState === '23505') {
            return true;
        }

        if ($sqlState !== '23000') {
            return false;
        }

        return preg_match('/(UNIQUE constraint failed|Duplicate entry|duplicate key)/i', $e->getMessage()) === 1;
    }

    // Mengganti penanda {nama}, {kecamatan}, dst. dengan data penari
    public function isiTeks(string $teks): string
    {
        return strtr($teks, [
            '{nama}' => $this->nama,
            '{kecamatan}' => $this->kecamatan->nama,
            '{sanggar}' => $this->sanggar ?: '-',
            '{tanggal_lahir}' => $this->tanggal_lahir->locale('id')->translatedFormat('d F Y'),
            '{nomor}' => $this->nomor_sertifikat,
            '{tanggal}' => now()->locale('id')->translatedFormat('d F Y'),
        ]);
    }
}
