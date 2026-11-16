<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model {
    public const TARGET = 1300; // total penari yang diharapkan
    protected $table = 'kecamatan';
    protected $fillable = ['nama', 'kode_singkat', 'kuota'];

    public function kodes() { return $this->hasMany(KodePenari::class); }
    public function penari() { return $this->hasMany(Penari::class); }
}