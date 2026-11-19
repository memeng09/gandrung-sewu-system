<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kecamatan', function (Blueprint $t) {
            $t->id();
            $t->string('nama')->unique();
            $t->string('kode_singkat', 5)->unique();
            $t->timestamps();
        });

        // Daftar desa per kecamatan (pilihan untuk jalur Desa)
        Schema::create('desa', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $t->string('nama');
            $t->timestamps();
            $t->unique(['kecamatan_id', 'nama']);
        });

        // Kuota per kecamatan dan jalur. kuota kosong (NULL) = belum ditentukan
        Schema::create('kuota_jalur', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $t->enum('jalur', ['sekolah', 'desa', 'umum']);
            $t->unsignedInteger('kuota')->nullable();
            $t->timestamps();
            $t->unique(['kecamatan_id', 'jalur']);
        });

        // Kelompok peserta luar Banyuwangi (berubah tiap tahun, tanpa kuota)
        Schema::create('kelompok_luar', function (Blueprint $t) {
            $t->id();
            $t->string('nama')->unique();
            $t->string('kode_singkat', 5)->unique();
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('kelompok_luar');
        Schema::dropIfExists('kuota_jalur');
        Schema::dropIfExists('desa');
        Schema::dropIfExists('kecamatan');
    }
};