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
        'keterangan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal_bayar' => 'date',
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
        $date = date('Ymd');
        $prefix = 'TRK' . $date;
        $last = static::where('no_referensi', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $last ? ((int) substr($last->no_referensi, -4)) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
