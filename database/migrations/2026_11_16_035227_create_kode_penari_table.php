<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kode_penari', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kecamatan_id')->constrained('kecamatan');
            $t->string('kode')->unique();
            $t->enum('status', ['belum_dipakai', 'dipakai', 'dicabut'])->default('belum_dipakai');
            $t->foreignId('dipakai_oleh')->nullable()->constrained('users');
            $t->timestamp('dipakai_pada')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kode_penari'); }
};