<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokLuar extends Model {
    protected $table = 'kelompok_luar';
    protected $fillable = ['nama', 'kode_singkat'];

    public function kodes() { return $this->hasMany(KodePenari::class); }
}