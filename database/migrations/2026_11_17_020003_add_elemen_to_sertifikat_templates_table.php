<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sertifikat_templates', function (Blueprint $t) {
            $t->string('orientasi')->default('landscape')->after('gambar');
            $t->json('elemen')->nullable()->after('orientasi');
        });
    }
    public function down(): void {
        Schema::table('sertifikat_templates', fn (Blueprint $t) => $t->dropColumn(['orientasi', 'elemen']));
    }
};