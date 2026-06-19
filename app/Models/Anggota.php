<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Anggota extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'anggota';

    protected $fillable = [
        'user_id',
        'no_anggota',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telepon',
        'pekerjaan',
        'foto_ktp',
        'foto_kk',
        'status',
        'catatan_verifikasi',
        'verified_by',
        'approved_by',
        'verified_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
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

    /**
     * Relasi ke user account
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User yang memverifikasi (Bendahara)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * User yang menyetujui (Ketua)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Simpanan anggota
     */
    public function simpanan()
    {
        return $this->hasMany(Simpanan::class);
    }

    /**
     * Peminjaman anggota
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * SHU anggota
     */
    public function shuAnggota()
    {
        return $this->hasMany(ShuAnggota::class);
    }

    /**
     * Total simpanan
     */
    public function getTotalSimpananAttribute(): float
    {
        return $this->simpanan()->sum('nominal');
    }

    /**
     * Total simpanan per jenis
     */
    public function getSimpananByJenis(string $jenis): float
    {
        return $this->simpanan()->where('jenis', $jenis)->sum('nominal');
    }

    /**
     * Total pinjaman aktif
     */
    public function getTotalPinjamanAktifAttribute(): float
    {
        return $this->peminjaman()
            ->where('status', 'disetujui')
            ->sum('nominal');
    }

    /**
     * Generate nomor anggota baru
     */
    public static function generateNoAnggota(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->no_anggota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'ANG' . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope: hanya anggota aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope: menunggu verifikasi
     */
    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status', 'menunggu_bendahara');
    }

    /**
     * Scope: menunggu approval
     */
    public function scopeMenungguApproval($query)
    {
        return $query->where('status', 'menunggu_ketua');
    }
}
