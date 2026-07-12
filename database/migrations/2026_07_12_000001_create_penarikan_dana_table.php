<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penarikan_dana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->string('no_penarikan', 30)->unique();
            $table->decimal('nominal', 15, 2);
            $table->enum('metode_pembayaran', ['cash', 'transfer']);
            $table->json('rekening_bank')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->enum('status', [
                'menunggu_bendahara',
                'disetujui_ketua',
                'diproses',
                'selesai',
                'ditolak',
            ])->default('menunggu_bendahara');
            $table->text('catatan_bendahara')->nullable();
            $table->text('catatan_ketua')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_proses')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penarikan_dana');
    }
};
