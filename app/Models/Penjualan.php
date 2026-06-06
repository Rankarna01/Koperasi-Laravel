<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'bulan', 'tahun', 'total_omset',
        'total_laba', 'keterangan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_omset' => 'decimal:2',
            'total_laba' => 'decimal:2',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
