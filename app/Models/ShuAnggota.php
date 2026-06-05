<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShuAnggota extends Model
{
    use HasFactory;

    protected $table = 'shu_anggota';

    protected $fillable = [
        'shu_periode_id', 'anggota_id',
        'kontribusi_simpanan', 'kontribusi_pinjaman',
        'kontribusi_penjualan', 'total_shu',
    ];

    protected function casts(): array
    {
        return [
            'kontribusi_simpanan' => 'decimal:2',
            'kontribusi_pinjaman' => 'decimal:2',
            'kontribusi_penjualan' => 'decimal:2',
            'total_shu' => 'decimal:2',
        ];
    }

    public function shuPeriode()
    {
        return $this->belongsTo(ShuPeriode::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
