<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['cash', 'transfer'])->default('cash')->after('tujuan_pinjaman');
            $table->string('nama_bank')->nullable()->after('metode_pembayaran');
            $table->string('nomor_rekening')->nullable()->after('nama_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'nama_bank', 'nomor_rekening']);
        });
    }
};
