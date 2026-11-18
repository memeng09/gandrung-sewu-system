<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikat_templates', function (Blueprint $t) {
            $t->decimal('qr_x', 5, 1)->default(78)->after('elemen');
            $t->decimal('qr_y', 5, 1)->default(86)->after('qr_x');
            $t->decimal('qr_size', 5, 1)->default(12)->after('qr_y');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat_templates', fn (Blueprint $t) => $t->dropColumn(['qr_x', 'qr_y', 'qr_size']));
    }
};
