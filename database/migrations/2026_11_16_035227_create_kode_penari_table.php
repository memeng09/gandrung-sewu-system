<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kode_penari', function (Blueprint $t) {
            $t->id();
            $t->enum('jalur', ['sekolah', 'desa', 'umum', 'luar']);
            $t->foreignId('kecamatan_id')->nullable()->constrained('kecamatan');
            $t->foreignId('kelompok_luar_id')->nullable()->constrained('kelompok_luar');
            $t->string('kode')->unique();
            $t->string('nama_penerima')->nullable();   // opsional: kode dikunci ke nama tertentu
            $t->enum('status', ['belum_dipakai', 'dipakai', 'dicabut'])->default('belum_dipakai');
            $t->foreignId('dipakai_oleh')->nullable()->constrained('users');
            $t->timestamp('dipakai_pada')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kode_penari'); }
};