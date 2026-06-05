<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu_periode', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->decimal('total_pendapatan', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('shu_bersih', 15, 2)->default(0);
            $table->decimal('dana_cadangan_persen', 5, 2)->default(20);
            $table->decimal('dana_pengurus_persen', 5, 2)->default(10);
            $table->decimal('dana_pendidikan_persen', 5, 2)->default(10);
            $table->decimal('dana_sosial_persen', 5, 2)->default(10);
            $table->decimal('dana_anggota_persen', 5, 2)->default(50);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('shu_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shu_periode_id')->constrained('shu_periode')->cascadeOnDelete();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->decimal('kontribusi_simpanan', 15, 2)->default(0);
            $table->decimal('kontribusi_pinjaman', 15, 2)->default(0);
            $table->decimal('kontribusi_penjualan', 15, 2)->default(0);
            $table->decimal('total_shu', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_anggota');
        Schema::dropIfExists('shu_periode');
    }
};
