<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setor_simpanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->string('no_setor', 30)->unique();
            $table->enum('jenis_simpanan', ['pokok', 'wajib', 'sukarela', 'deposito']);
            $table->decimal('nominal', 15, 2);
            $table->enum('metode_pembayaran', ['transfer', 'cash']);
            $table->json('bukti_transfer')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['menunggu_bendahara', 'selesai', 'ditolak'])->default('menunggu_bendahara');
            $table->text('catatan_bendahara')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setor_simpanan');
    }
};
