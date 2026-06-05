<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    use HasFactory;

    protected $table = 'simpanan';

    protected $fillable = [
        'anggota_id', 'no_transaksi', 'jenis',
        'nominal', 'tanggal', 'keterangan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate nomor transaksi simpanan
     */
    public static function generateNoTransaksi(): string
    {
        $date = date('Ymd');
        $prefix = 'SMP' . $date;
        $last = static::where('no_transaksi', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $last ? ((int) substr($last->no_transaksi, -4)) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Label jenis simpanan
     */
    public function getLabelJenisAttribute(): string
    {
        $labels = [
            'pokok' => 'Simpanan Pokok',
            'wajib' => 'Simpanan Wajib',
            'sukarela' => 'Simpanan Sukarela',
            'deposito' => 'Deposito',
        ];
        return $labels[$this->jenis] ?? $this->jenis;
    }
}
