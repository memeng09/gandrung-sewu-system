<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Penari extends Model {
    public const PROVINSI = ['Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi', 'Sumatera Selatan',
        'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung', 'DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Tengah', 'DI Yogyakarta',
        'Jawa Timur', 'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 'Kalimantan Barat', 'Kalimantan Tengah',
        'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 'Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah',
        'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat',
        'Papua Barat Daya', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan'];

    protected $table = 'penari';
    protected $fillable = ['user_id', 'kode_penari_id', 'jalur', 'kecamatan_id', 'desa', 'provinsi', 'kota', 'nama',
        'tanggal_lahir', 'alamat', 'no_wa', 'sekolah', 'kelas', 'instansi', 'sanggar', 'foto', 'status', 'catatan'];
    protected $casts = ['tanggal_lahir' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function kecamatan() { return $this->belongsTo(Kecamatan::class); }
    public function kode() { return $this->belongsTo(KodePenari::class, 'kode_penari_id'); }

    // Aturan isian khusus tiap jalur (dipakai form pendaftaran dan form edit admin)
    public static function aturanJalur(string $jalur): array {
        return match ($jalur) {
            'sekolah' => ['sekolah' => 'required|string|max:100', 'kelas' => 'required|string|max:20'],
             'desa'    => ['desa' => ['required', 'string', 'max:100', "regex:/^[\\pL][\\pL\\s.'-]*$/u"],
                          'sekolah' => 'nullable|string|max:100',
                          'kelas' => 'nullable|required_with:sekolah|string|max:20'],
            'umum'    => ['instansi' => 'nullable|string|max:100'],
            'luar'    => ['provinsi' => ['required', Rule::in(self::PROVINSI)], 'kota' => 'required|string|max:100',
                          'instansi' => 'nullable|string|max:100'],
        };
    }
        // Nama desa selalu diseragamkan: tanpa awalan "Desa/Kelurahan", spasi rapi, huruf besar di awal tiap kata
    public function setDesaAttribute($value): void {
        $v = trim(preg_replace('/\s+/u', ' ', (string) $value));
        $v = preg_replace('/^(desa|ds\.?|kelurahan|kel\.?)\s+/iu', '', $v);
        $this->attributes['desa'] = $v === '' ? null : mb_convert_case($v, MB_CASE_TITLE, 'UTF-8');
    }

    public function getLabelJalurAttribute(): string { return KodePenari::JALUR[$this->jalur]['label']; }

    // Kecamatan, atau "Kota, Provinsi" untuk peserta luar Banyuwangi
    public function getWilayahAttribute(): string {
        return $this->jalur === 'luar' ? trim($this->kota . ', ' . $this->provinsi, ', ') : ($this->kecamatan?->nama ?? '-');
    }

    // Teks asal untuk kartu dan sertifikat
    public function getAsalAttribute(): string {
        return match ($this->jalur) {
            'sekolah' => $this->sekolah ?: $this->wilayah,
            'desa'    => 'Desa ' . $this->desa . ', Kec. ' . $this->wilayah,
            'umum'    => $this->instansi ?: 'Kec. ' . $this->wilayah,
            default   => $this->wilayah,
        };
    }

    // Nomor mengikuti format yang diatur admin di Template Sertifikat
    public function getNomorSertifikatAttribute(): string {
        $format = SertifikatTemplate::first()?->format_nomor ?: 'GS26/{urut}/{tahun}';
        return strtr($format, ['{urut}' => sprintf('%04d', $this->id), '{tahun}' => $this->created_at->format('Y')]);
    }

    // Token QR: id + tanda tangan rahasia, tidak bisa ditebak
    public function getTokenVerifikasiAttribute(): string {
        return $this->id . '-' . substr(hash_hmac('sha256', (string) $this->id, config('app.key')), 0, 12);
    }

    // Penanda huruf kecil = apa adanya, HURUF BESAR = dikapitalkan
    public function isiTeks(string $teks): string {
        $sekolah = $this->sekolah ?: ($this->instansi ?: '-');
        $sanggar = $this->sanggar ?: '-';
        $desa = $this->desa ?: '-';
        $up = fn ($s) => mb_strtoupper($s);
        return strtr($teks, [
            '{nama}' => $this->nama, '{NAMA}' => $up($this->nama),
            '{kecamatan}' => $this->wilayah, '{KECAMATAN}' => $up($this->wilayah),
            '{asal}' => $this->asal, '{ASAL}' => $up($this->asal),
            '{sekolah}' => $sekolah, '{SEKOLAH}' => $up($sekolah),
            '{sanggar}' => $sanggar, '{SANGGAR}' => $up($sanggar),
            '{desa}' => $desa, '{DESA}' => $up($desa),
            '{tanggal_lahir}' => $this->tanggal_lahir->locale('id')->translatedFormat('d F Y'),
            '{nomor}' => $this->nomor_sertifikat,
            '{tanggal}' => now()->locale('id')->translatedFormat('d F Y'),
        ]);
    }

    // Untuk PDF: **tebal**, __miring__, dan baris baru
    public function teksPdf(string $teks): string {
        $h = e($this->isiTeks($teks));
        $h = preg_replace('/\*\*(.+?)\*\*/su', '<b>$1</b>', $h);
        $h = preg_replace('/__(.+?)__/su', '<i>$1</i>', $h);
        return nl2br($h);
    }
}