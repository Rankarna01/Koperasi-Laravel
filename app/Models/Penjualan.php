<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'no_nota', 'tanggal', 'anggota_id',
        'total', 'bayar', 'kembalian',
        'metode_pembayaran', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'bayar' => 'decimal:2',
            'kembalian' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class);
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
     * Generate nomor nota penjualan
     */
    public static function generateNoNota(): string
    {
        $date = date('ymd');
        $prefix = 'PJ-' . $date;
        $last = static::where('no_nota', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $last ? ((int) substr($last->no_nota, -3)) + 1 : 1;
        return $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Total laba dari penjualan ini
     */
    public function getLabaAttribute(): float
    {
        return $this->detail->sum(function ($item) {
            $barang = $item->barang;
            return ($item->harga_jual - $barang->harga_beli) * $item->jumlah;
        });
    }
}
