<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Anggota;
use App\Models\KategoriBarang;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Simpanan;
use App\Models\Peminjaman;
use App\Models\Angsuran;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // 1. USERS - Create default users for each role
        // ========================================
        $ketua = User::create([
            'name' => 'Budi Santoso',
            'email' => 'ketua@koperasi.com',
            'password' => Hash::make('password'),
            'role' => 'ketua',
            'is_active' => true,
        ]);

        $bendahara = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'bendahara@koperasi.com',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin Koperasi',
            'email' => 'admin@koperasi.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $anggota = User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@koperasi.com',
            'password' => Hash::make('password'),
            'role' => 'anggota',
            'is_active' => true,
        ]);

        echo "✅ Seeder berhasil! Akun login:\n";
        echo "   Ketua: ketua@koperasi.com / password\n";
        echo "   Bendahara: bendahara@koperasi.com / password\n";
        echo "   Admin: admin@koperasi.com / password\n";
        echo "   Anggota: andi@koperasi.com / password\n";
    }
}
