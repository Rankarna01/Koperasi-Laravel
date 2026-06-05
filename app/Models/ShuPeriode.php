<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShuPeriode extends Model
{
    use HasFactory;

    protected $table = 'shu_periode';

    protected $fillable = [
        'tahun', 'total_pendapatan', 'total_biaya', 'shu_bersih',
        'dana_cadangan_persen', 'dana_pengurus_persen',
        'dana_pendidikan_persen', 'dana_sosial_persen',
        'dana_anggota_persen', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_pendapatan' => 'decimal:2',
            'total_biaya' => 'decimal:2',
            'shu_bersih' => 'decimal:2',
        ];
    }

    public function shuAnggota()
    {
        return $this->hasMany(ShuAnggota::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Hitung dana cadangan
     */
    public function getDanaCadanganAttribute(): float
    {
        return $this->shu_bersih * ($this->dana_cadangan_persen / 100);
    }

    /**
     * Hitung dana pengurus
     */
    public function getDanaPengurusAttribute(): float
    {
        return $this->shu_bersih * ($this->dana_pengurus_persen / 100);
    }

    /**
     * Hitung dana pendidikan
     */
    public function getDanaPendidikanAttribute(): float
    {
        return $this->shu_bersih * ($this->dana_pendidikan_persen / 100);
    }

    /**
     * Hitung dana sosial
     */
    public function getDanaSosialAttribute(): float
    {
        return $this->shu_bersih * ($this->dana_sosial_persen / 100);
    }

    /**
     * Hitung dana untuk anggota
     */
    public function getDanaAnggotaAttribute(): float
    {
        return $this->shu_bersih * ($this->dana_anggota_persen / 100);
    }
}
