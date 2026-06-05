<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'barang_id', 'jumlah', 'harga_beli',
        'tanggal', 'keterangan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
