<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shu_anggota', function (Blueprint $table) {
            $table->decimal('bunga_simpanan', 15, 2)->default(0)->after('kontribusi_penjualan');
        });
    }

    public function down(): void
    {
        Schema::table('shu_anggota', function (Blueprint $table) {
            $table->dropColumn('bunga_simpanan');
        });
    }
};
