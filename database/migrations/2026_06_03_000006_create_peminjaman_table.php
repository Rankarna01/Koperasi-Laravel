<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->string('no_pinjaman', 30)->unique();
            $table->date('tanggal_pengajuan');
            $table->decimal('nominal', 15, 2);
            $table->integer('lama_cicilan'); // dalam bulan
            $table->decimal('bunga_persen', 5, 2)->default(1.00);
            $table->decimal('total_bunga', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2)->default(0);
            $table->decimal('angsuran_per_bulan', 15, 2)->default(0);
            $table->string('tujuan_pinjaman')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->enum('status', ['menunggu_bendahara', 'menunggu_ketua', 'disetujui', 'ditolak', 'lunas'])->default('menunggu_bendahara');
            $table->text('catatan_bendahara')->nullable();
            $table->text('catatan_ketua')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->date('tanggal_pencairan')->nullable();
            $table->date('tanggal_lunas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
