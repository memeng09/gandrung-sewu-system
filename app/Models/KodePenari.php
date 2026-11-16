<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodePenari extends Model {
    protected $table = 'kode_penari';
    protected $fillable = ['kecamatan_id', 'kode', 'status', 'dipakai_oleh', 'dipakai_pada'];
    protected $casts = ['dipakai_pada' => 'datetime'];

    public function kecamatan() { return $this->belongsTo(Kecamatan::class); }
    public function pemakai() { return $this->belongsTo(User::class, 'dipakai_oleh'); }
}