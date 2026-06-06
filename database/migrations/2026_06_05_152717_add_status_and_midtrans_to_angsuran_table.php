<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('angsuran', function (Blueprint $table) {
            $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('berhasil')->after('keterangan');
            $table->string('snap_token')->nullable()->after('status');
        });

        // Alter enum to add 'midtrans'
        DB::statement("ALTER TABLE angsuran MODIFY COLUMN metode_pembayaran ENUM('tunai', 'transfer', 'qris', 'midtrans') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum
        DB::statement("ALTER TABLE angsuran MODIFY COLUMN metode_pembayaran ENUM('tunai', 'transfer', 'qris') NOT NULL");
        
        Schema::table('angsuran', function (Blueprint $table) {
            $table->dropColumn(['status', 'snap_token']);
        });
    }
};
