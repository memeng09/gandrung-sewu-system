<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sertifikat_templates', function (Blueprint $t) {
            $t->id();
            $t->string('gambar')->nullable();
            $t->decimal('nama_y', 5, 1)->default(48);
            $t->unsignedSmallInteger('nama_size')->default(42);
            $t->decimal('info_y', 5, 1)->default(62);
            $t->decimal('nomor_y', 5, 1)->default(88);
            $t->string('warna', 7)->default('#5E1220');
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sertifikat_templates'); }
};