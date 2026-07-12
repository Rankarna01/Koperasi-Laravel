<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetorSimpanan extends Model
{
    use HasFactory;

    protected $table = 'setor_simpanan';

    protected $fillable = [
        'anggota_id',
        'no_setor',
        'jenis_simpanan',
        'nominal',
        'metode_pembayaran',
        'bukti_transfer',
        'keterangan',
        'status',
        'catatan_bendahara',
        'verified_by',
        'tanggal_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'bukti_transfer' => 'array',
            'tanggal_verifikasi' => 'datetime',
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function generateNoSetor(): string
    {
        $prefix = 'STR' . date('Ymd');
        $last = static::where('no_setor', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->no_setor, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu_bendahara' => 'Menunggu Verifikasi',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }

    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu_bendahara' => 'warning',
            'selesai' => 'success',
            'ditolak' => 'danger',
            default => 'secondary',
        };
    }

    public function getLabelJenisAttribute(): string
    {
        return match ($this->jenis_simpanan) {
            'pokok' => 'Simpanan Pokok',
            'wajib' => 'Simpanan Wajib',
            'sukarela' => 'Simpanan Sukarela',
            'deposito' => 'Deposito',
            default => $this->jenis_simpanan,
        };
    }
}
