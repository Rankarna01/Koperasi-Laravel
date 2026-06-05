<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barang';

    protected $fillable = [
        'kategori_id', 'kode_barang', 'nama', 'satuan',
        'harga_beli', 'harga_jual', 'stok', 'stok_minimal',
        'gambar', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    /**
     * Cek apakah stok menipis
     */
    public function isStokMenipis(): bool
    {
        return $this->stok <= $this->stok_minimal;
    }

    /**
     * Laba per unit
     */
    public function getLabaPerUnitAttribute(): float
    {
        return $this->harga_jual - $this->harga_beli;
    }

    /**
     * Generate kode barang
     */
    public static function generateKodeBarang(): string
    {
        $last = static::withTrashed()->orderBy('id', 'desc')->first();
        $number = $last ? ((int) substr($last->kode_barang, 3)) + 1 : 1;
        return 'BRG' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStokMenipis($query)
    {
        return $query->whereColumn('stok', '<=', 'stok_minimal');
    }
}
