<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penari', function (Blueprint $t) {
            $t->string('kode_verifikasi', 12)->nullable()->unique()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('penari', fn (Blueprint $t) => $t->dropColumn('kode_verifikasi'));
    }
};
