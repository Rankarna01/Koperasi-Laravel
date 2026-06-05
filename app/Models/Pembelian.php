<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    protected $fillable = [
        'no_nota', 'supplier_id', 'tanggal',
        'total', 'keterangan', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detail()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate nomor nota pembelian
     */
    public static function generateNoNota(): string
    {
        $date = date('ymd');
        $prefix = 'NB-' . $date;
        $last = static::where('no_nota', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $last ? ((int) substr($last->no_nota, -3)) + 1 : 1;
        return $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
