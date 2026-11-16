<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatTemplate extends Model {
    protected $fillable = ['gambar', 'nama_y', 'nama_size', 'info_y', 'nomor_y', 'warna'];
}