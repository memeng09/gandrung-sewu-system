<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('penari', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('kode_penari_id')->unique()->constrained('kode_penari');
            $t->enum('jalur', ['sekolah', 'desa', 'umum', 'luar']);
            $t->foreignId('kecamatan_id')->nullable()->constrained('kecamatan');
            $t->string('desa')->nullable();
            $t->string('provinsi')->nullable();
            $t->string('kota')->nullable();
            $t->string('nama');
            $t->date('tanggal_lahir');
            $t->text('alamat');
            $t->string('no_wa', 20)->nullable();
            $t->string('sekolah')->nullable();
            $t->string('kelas', 20)->nullable();
            $t->string('instansi')->nullable();
            $t->string('sanggar')->nullable();
            $t->string('foto');
            $t->enum('status', ['aktif', 'perlu_ditinjau', 'dicabut'])->default('aktif');
            $t->string('catatan')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('penari'); }
};