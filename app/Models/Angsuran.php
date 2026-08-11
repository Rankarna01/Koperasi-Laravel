<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory;

    protected $table = 'angsuran';

    protected $fillable = [
        'peminjaman_id', 'no_referensi', 'angsuran_ke',
        'nominal', 'tanggal_bayar', 'metode_pembayaran',
        'keterangan', 'created_by', 'status', 'snap_token', 'jatuh_tempo',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'date',
            'jatuh_tempo' => 'date',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate nomor referensi
     */
    public static function generateNoReferensi(): string
    {
        // TRK (3) + YmdHis (14) + Rand (4) = 21 chars (fits in string(30))
        return 'TRK' . date('YmdHis') . mt_rand(1000, 9999);
    }
}
