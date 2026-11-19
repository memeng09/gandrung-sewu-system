<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KuotaJalur extends Model {
    protected $table = 'kuota_jalur';
    protected $fillable = ['kecamatan_id', 'jalur', 'kuota'];
}