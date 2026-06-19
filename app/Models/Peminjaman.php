<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Peminjaman extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'peminjaman';

    protected $fillable = [
        'anggota_id', 'no_pinjaman', 'tanggal_pengajuan',
        'nominal', 'lama_cicilan', 'bunga_persen',
        'total_bunga', 'total_bayar', 'angsuran_per_bulan',
        'tujuan_pinjaman', 'keterangan', 'dokumen_pendukung',
        'status', 'catatan_bendahara', 'catatan_ketua',
        'verified_by', 'approved_by', 'verified_at', 'approved_at',
        'tanggal_pencairan', 'tanggal_lunas',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_pencairan' => 'date',
            'tanggal_lunas' => 'date',
            'nominal' => 'decimal:2',
            'bunga_persen' => 'decimal:2',
            'total_bunga' => 'decimal:2',
            'total_bayar' => 'decimal:2',
            'angsuran_per_bulan' => 'decimal:2',
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'verified_by', 'approved_by'])
            ->logOnlyDirty();
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function angsuran()
    {
        return $this->hasMany(Angsuran::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Hitung bunga dan total bayar (flat)
     */
    public function hitungBunga(): void
    {
        $this->total_bunga = $this->nominal * ($this->bunga_persen / 100) * $this->lama_cicilan;
        $this->total_bayar = $this->nominal + $this->total_bunga;
        $this->angsuran_per_bulan = $this->total_bayar / $this->lama_cicilan;
    }

    /**
     * Total yang sudah dibayar
     */
    public function getTotalDibayarAttribute(): float
    {
        return $this->angsuran()->sum('nominal');
    }

    /**
     * Sisa pinjaman
     */
    public function getSisaPinjamanAttribute(): float
    {
        return $this->total_bayar - $this->total_dibayar;
    }

    /**
     * Jumlah angsuran yang sudah dibayar
     */
    public function getJumlahAngsuranDibayarAttribute(): int
    {
        return $this->angsuran()->count();
    }

    /**
     * Cek apakah sudah lunas
     */
    public function isLunas(): bool
    {
        return $this->jumlah_angsuran_dibayar >= $this->lama_cicilan;
    }

    /**
     * Generate nomor pinjaman
     */
    public static function generateNoPinjaman(): string
    {
        $date = date('Ymd');
        $prefix = 'PNJ' . $date;
        $last = static::where('no_pinjaman', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $last ? ((int) substr($last->no_pinjaman, -4)) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Label status
     */
    public function getLabelStatusAttribute(): string
    {
        $labels = [
            'menunggu_bendahara' => 'Menunggu Verifikasi',
            'menunggu_ketua' => 'Menunggu ACC Ketua',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'lunas' => 'Lunas',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Warna badge status
     */
    public function getBadgeStatusAttribute(): string
    {
        $badges = [
            'menunggu_bendahara' => 'warning',
            'menunggu_ketua' => 'info',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'lunas' => 'primary',
        ];
        return $badges[$this->status] ?? 'secondary';
    }
}
