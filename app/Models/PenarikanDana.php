<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenarikanDana extends Model
{
    use HasFactory;

    protected $table = 'penarikan_dana';

    protected $fillable = [
        'anggota_id',
        'no_penarikan',
        'nominal',
        'metode_pembayaran',
        'rekening_bank',
        'keterangan',
        'bukti_transfer',
        'status',
        'catatan_bendahara',
        'catatan_ketua',
        'verified_by',
        'approved_by',
        'processed_by',
        'tanggal_proses',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'rekening_bank' => 'array',
            'tanggal_proses' => 'date',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function generateNoPenarikan(): string
    {
        $prefix = 'PRK' . date('Ymd');
        $last = static::where('no_penarikan', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->no_penarikan, -4);
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
            'disetujui_ketua' => 'Menunggu ACC Ketua',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }

    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu_bendahara' => 'warning',
            'disetujui_ketua' => 'info',
            'diproses' => 'primary',
            'selesai' => 'success',
            'ditolak' => 'danger',
            default => 'secondary',
        };
    }
}
