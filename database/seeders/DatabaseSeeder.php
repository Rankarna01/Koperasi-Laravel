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

        // Anggota Users
        $anggotaUsers = [];
        $anggotaData = [
            ['name' => 'Andi Pratama', 'email' => 'andi@koperasi.com', 'nik' => '3201010101900001', 'pekerjaan' => 'Karyawan Swasta', 'jk' => 'L'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@koperasi.com', 'nik' => '3201010101900002', 'pekerjaan' => 'Guru', 'jk' => 'P'],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@koperasi.com', 'nik' => '3201010101900003', 'pekerjaan' => 'Wiraswasta', 'jk' => 'L'],
            ['name' => 'Siti Aisyah', 'email' => 'siti@koperasi.com', 'nik' => '3201010101900004', 'pekerjaan' => 'PNS', 'jk' => 'P'],
            ['name' => 'Rudi Hermawan', 'email' => 'rudi@koperasi.com', 'nik' => '3201010101900005', 'pekerjaan' => 'Pedagang', 'jk' => 'L'],
            ['name' => 'Nina Marlina', 'email' => 'nina@koperasi.com', 'nik' => '3201010101900006', 'pekerjaan' => 'Guru', 'jk' => 'P'],
            ['name' => 'Joko Susilo', 'email' => 'joko@koperasi.com', 'nik' => '3201010101900007', 'pekerjaan' => 'Karyawan Swasta', 'jk' => 'L'],
            ['name' => 'Rina Dwi', 'email' => 'rina@koperasi.com', 'nik' => '3201010101900008', 'pekerjaan' => 'Ibu Rumah Tangga', 'jk' => 'P'],
            ['name' => 'Fahmi Rahman', 'email' => 'fahmi@koperasi.com', 'nik' => '3201010101900009', 'pekerjaan' => 'Teknisi', 'jk' => 'L'],
            ['name' => 'Mega Putri', 'email' => 'mega@koperasi.com', 'nik' => '3201010101900010', 'pekerjaan' => 'Perawat', 'jk' => 'P'],
        ];

        foreach ($anggotaData as $i => $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'anggota',
                'is_active' => true,
            ]);

            $anggota = Anggota::create([
                'user_id' => $user->id,
                'no_anggota' => 'ANG' . date('Y') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'nik' => $data['nik'],
                'nama_lengkap' => $data['name'],
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => Carbon::now()->subYears(rand(25, 50))->format('Y-m-d'),
                'jenis_kelamin' => $data['jk'],
                'alamat' => 'Jl. Mawar No. ' . ($i + 1) . ', Jakarta Selatan',
                'no_telepon' => '0812-3456-' . str_pad(($i + 1) * 111, 4, '0', STR_PAD_LEFT),
                'pekerjaan' => $data['pekerjaan'],
                'status' => 'aktif',
                'verified_by' => $bendahara->id,
                'approved_by' => $ketua->id,
                'verified_at' => Carbon::now()->subMonths(rand(1, 12)),
                'approved_at' => Carbon::now()->subMonths(rand(1, 12)),
            ]);

            $anggotaUsers[] = ['user' => $user, 'anggota' => $anggota];
        }

        // ========================================
        // 2. SIMPANAN - Sample savings for each member
        // ========================================
        foreach ($anggotaUsers as $i => $au) {
            $anggota = $au['anggota'];

            // Simpanan Pokok (sekali saat daftar)
            Simpanan::create([
                'anggota_id' => $anggota->id,
                'no_transaksi' => 'SMP' . date('Ymd') . str_pad(($i * 4) + 1, 4, '0', STR_PAD_LEFT),
                'jenis' => 'pokok',
                'nominal' => 500000,
                'tanggal' => Carbon::now()->subMonths(rand(6, 12)),
                'keterangan' => 'Simpanan pokok saat pendaftaran',
                'created_by' => $bendahara->id,
            ]);

            // Simpanan Wajib (beberapa bulan)
            for ($m = 0; $m < rand(3, 8); $m++) {
                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'no_transaksi' => 'SMP' . Carbon::now()->subMonths($m)->format('Ymd') . str_pad(($i * 4) + $m + 2, 4, '0', STR_PAD_LEFT),
                    'jenis' => 'wajib',
                    'nominal' => 100000,
                    'tanggal' => Carbon::now()->subMonths($m),
                    'keterangan' => 'Simpanan wajib bulanan',
                    'created_by' => $bendahara->id,
                ]);
            }

            // Simpanan Sukarela (random)
            if (rand(0, 1)) {
                Simpanan::create([
                    'anggota_id' => $anggota->id,
                    'no_transaksi' => 'SMP' . date('Ymd') . str_pad(100 + $i, 4, '0', STR_PAD_LEFT),
                    'jenis' => 'sukarela',
                    'nominal' => rand(2, 20) * 100000,
                    'tanggal' => Carbon::now()->subDays(rand(1, 60)),
                    'keterangan' => 'Simpanan sukarela',
                    'created_by' => $bendahara->id,
                ]);
            }
        }

        // ========================================
        // 3. PEMINJAMAN - Sample loans
        // ========================================
        $loanStatuses = ['menunggu_bendahara', 'menunggu_ketua', 'disetujui', 'disetujui', 'disetujui', 'lunas'];

        foreach (array_slice($anggotaUsers, 0, 6) as $i => $au) {
            $anggota = $au['anggota'];
            $status = $loanStatuses[$i];
            $nominal = rand(5, 30) * 1000000;
            $lamaCicilan = [6, 12, 18, 24][rand(0, 3)];
            $bunga = 1.00;
            $totalBunga = $nominal * ($bunga / 100) * $lamaCicilan;
            $totalBayar = $nominal + $totalBunga;
            $angsuranPerBulan = $totalBayar / $lamaCicilan;

            $pinjaman = Peminjaman::create([
                'anggota_id' => $anggota->id,
                'no_pinjaman' => 'PNJ' . Carbon::now()->subMonths($i)->format('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'tanggal_pengajuan' => Carbon::now()->subMonths($i + 2),
                'nominal' => $nominal,
                'lama_cicilan' => $lamaCicilan,
                'bunga_persen' => $bunga,
                'total_bunga' => $totalBunga,
                'total_bayar' => $totalBayar,
                'angsuran_per_bulan' => $angsuranPerBulan,
                'tujuan_pinjaman' => ['Modal usaha', 'Renovasi rumah', 'Biaya pendidikan', 'Modal usaha toko kelontong', 'Kebutuhan mendesak', 'Investasi'][rand(0, 5)],
                'keterangan' => 'Pengajuan pinjaman',
                'status' => $status,
                'verified_by' => in_array($status, ['menunggu_ketua', 'disetujui', 'lunas']) ? $bendahara->id : null,
                'approved_by' => in_array($status, ['disetujui', 'lunas']) ? $ketua->id : null,
                'verified_at' => in_array($status, ['menunggu_ketua', 'disetujui', 'lunas']) ? Carbon::now()->subMonths($i + 1) : null,
                'approved_at' => in_array($status, ['disetujui', 'lunas']) ? Carbon::now()->subMonths($i) : null,
                'tanggal_pencairan' => in_array($status, ['disetujui', 'lunas']) ? Carbon::now()->subMonths($i)->addDays(3) : null,
            ]);

            // Create angsuran for approved/lunas loans
            if (in_array($status, ['disetujui', 'lunas'])) {
                $jumlahAngsuran = $status === 'lunas' ? $lamaCicilan : rand(1, min(5, $lamaCicilan));
                for ($a = 1; $a <= $jumlahAngsuran; $a++) {
                    Angsuran::create([
                        'peminjaman_id' => $pinjaman->id,
                        'no_referensi' => 'TRK' . Carbon::now()->subMonths($jumlahAngsuran - $a)->format('Ymd') . str_pad(($i * 10) + $a, 4, '0', STR_PAD_LEFT),
                        'angsuran_ke' => $a,
                        'nominal' => $angsuranPerBulan,
                        'tanggal_bayar' => Carbon::now()->subMonths($jumlahAngsuran - $a),
                        'metode_pembayaran' => ['tunai', 'transfer', 'qris'][rand(0, 2)],
                        'keterangan' => 'Pembayaran angsuran ke-' . $a,
                        'created_by' => $bendahara->id,
                    ]);
                }
            }
        }

        // ========================================
        // 4. KATEGORI BARANG & BARANG
        // ========================================
        $kategoris = [
            'Sembako' => [
                ['nama' => 'Beras Premium 5kg', 'harga_beli' => 62000, 'harga_jual' => 78000, 'satuan' => 'karung', 'stok' => 45],
                ['nama' => 'Minyak Goreng 1L', 'harga_beli' => 14000, 'harga_jual' => 18000, 'satuan' => 'botol', 'stok' => 80],
                ['nama' => 'Gula Pasir 1kg', 'harga_beli' => 12000, 'harga_jual' => 15000, 'satuan' => 'kg', 'stok' => 40],
                ['nama' => 'Tepung Terigu 1kg', 'harga_beli' => 8000, 'harga_jual' => 11000, 'satuan' => 'kg', 'stok' => 35],
                ['nama' => 'Telur Ayam 1kg', 'harga_beli' => 24000, 'harga_jual' => 28000, 'satuan' => 'kg', 'stok' => 25],
            ],
            'Minuman' => [
                ['nama' => 'Teh Celup (Box)', 'harga_beli' => 5000, 'harga_jual' => 8000, 'satuan' => 'box', 'stok' => 60],
                ['nama' => 'Kopi Sachet (renceng)', 'harga_beli' => 10000, 'harga_jual' => 13000, 'satuan' => 'renceng', 'stok' => 50],
                ['nama' => 'Air Mineral 600ml', 'harga_beli' => 2500, 'harga_jual' => 4000, 'satuan' => 'botol', 'stok' => 100],
                ['nama' => 'Susu UHT 1L', 'harga_beli' => 15000, 'harga_jual' => 19000, 'satuan' => 'kotak', 'stok' => 30],
            ],
            'Kebutuhan Rumah Tangga' => [
                ['nama' => 'Sabun Cuci 1kg', 'harga_beli' => 8000, 'harga_jual' => 12000, 'satuan' => 'kg', 'stok' => 40],
                ['nama' => 'Sabun Mandi', 'harga_beli' => 3000, 'harga_jual' => 5000, 'satuan' => 'pcs', 'stok' => 70],
                ['nama' => 'Shampo Sachet (renceng)', 'harga_beli' => 10000, 'harga_jual' => 13000, 'satuan' => 'renceng', 'stok' => 55],
                ['nama' => 'Deterjen 1kg', 'harga_beli' => 12000, 'harga_jual' => 16000, 'satuan' => 'kg', 'stok' => 30],
            ],
            'Makanan Ringan' => [
                ['nama' => 'Mie Instan (karton)', 'harga_beli' => 85000, 'harga_jual' => 105000, 'satuan' => 'karton', 'stok' => 20],
                ['nama' => 'Biskuit Kaleng', 'harga_beli' => 25000, 'harga_jual' => 35000, 'satuan' => 'kaleng', 'stok' => 15],
                ['nama' => 'Keripik Singkong', 'harga_beli' => 5000, 'harga_jual' => 8000, 'satuan' => 'pcs', 'stok' => 50],
            ],
        ];

        $barangIndex = 0;
        foreach ($kategoris as $namaKategori => $items) {
            $kategori = KategoriBarang::create([
                'nama' => $namaKategori,
                'deskripsi' => 'Kategori ' . $namaKategori,
            ]);

            foreach ($items as $item) {
                $barangIndex++;
                Barang::create([
                    'kategori_id' => $kategori->id,
                    'kode_barang' => 'BRG' . str_pad($barangIndex, 5, '0', STR_PAD_LEFT),
                    'nama' => $item['nama'],
                    'satuan' => $item['satuan'],
                    'harga_beli' => $item['harga_beli'],
                    'harga_jual' => $item['harga_jual'],
                    'stok' => $item['stok'],
                    'stok_minimal' => 20,
                    'is_active' => true,
                ]);
            }
        }

        // ========================================
        // 5. SUPPLIER
        // ========================================
        $suppliers = [
            Supplier::create(['nama' => 'CV. Sumber Makmur', 'alamat' => 'Jl. Industri No. 10, Jakarta', 'no_telepon' => '021-5551234', 'email' => 'sumber.makmur@email.com']),
            Supplier::create(['nama' => 'PT. Berkah Abadi', 'alamat' => 'Jl. Perdagangan No. 5, Bandung', 'no_telepon' => '022-5552345', 'email' => 'berkah.abadi@email.com']),
            Supplier::create(['nama' => 'UD. Sejahtera', 'alamat' => 'Jl. Merdeka No. 20, Surabaya', 'no_telepon' => '031-5553456', 'email' => 'sejahtera@email.com']),
            Supplier::create(['nama' => 'Toko Sumber Rejeki', 'alamat' => 'Jl. Pasar No. 15, Semarang', 'no_telepon' => '024-5554567', 'email' => 'sumber.rejeki@email.com']),
            Supplier::create(['nama' => 'CV. Makmur Jaya', 'alamat' => 'Jl. Raya No. 8, Yogyakarta', 'no_telepon' => '0274-5555678', 'email' => 'makmur.jaya@email.com']),
        ];

        // ========================================
        // 6. PEMBELIAN - Sample purchases
        // ========================================
        $allBarang = Barang::all();
        for ($p = 0; $p < 8; $p++) {
            $pembelian = Pembelian::create([
                'no_nota' => 'NB-' . Carbon::now()->subDays($p * 3)->format('ymd') . '-' . str_pad($p + 1, 3, '0', STR_PAD_LEFT),
                'supplier_id' => $suppliers[rand(0, 4)]->id,
                'tanggal' => Carbon::now()->subDays($p * 3),
                'total' => 0,
                'status' => $p === 0 ? 'proses' : 'selesai',
                'created_by' => $admin->id,
            ]);

            $total = 0;
            $itemCount = rand(2, 5);
            $selectedBarang = $allBarang->random($itemCount);

            foreach ($selectedBarang as $brg) {
                $jumlah = rand(10, 50);
                $subtotal = $brg->harga_beli * $jumlah;
                $total += $subtotal;

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $brg->id,
                    'jumlah' => $jumlah,
                    'harga_beli' => $brg->harga_beli,
                    'subtotal' => $subtotal,
                ]);
            }

            $pembelian->update(['total' => $total]);
        }

        // ========================================
        // 7. PENJUALAN - Sample sales
        // ========================================
        for ($s = 0; $s < 15; $s++) {
            $penjualan = Penjualan::create([
                'no_nota' => 'PJ-' . Carbon::now()->subDays($s)->format('ymd') . '-' . str_pad($s + 1, 3, '0', STR_PAD_LEFT),
                'tanggal' => Carbon::now()->subDays($s),
                'anggota_id' => rand(0, 1) ? $anggotaUsers[rand(0, count($anggotaUsers) - 1)]['anggota']->id : null,
                'total' => 0,
                'bayar' => 0,
                'kembalian' => 0,
                'metode_pembayaran' => ['tunai', 'transfer', 'qris'][rand(0, 2)],
                'status' => 'selesai',
                'created_by' => $admin->id,
            ]);

            $total = 0;
            $itemCount = rand(1, 4);
            $selectedBarang = $allBarang->random($itemCount);

            foreach ($selectedBarang as $brg) {
                $jumlah = rand(1, 5);
                $subtotal = $brg->harga_jual * $jumlah;
                $total += $subtotal;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $brg->id,
                    'jumlah' => $jumlah,
                    'harga_jual' => $brg->harga_jual,
                    'subtotal' => $subtotal,
                ]);
            }

            $bayar = ceil($total / 10000) * 10000; // Pembulatan ke atas
            $penjualan->update([
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $bayar - $total,
            ]);
        }

        // ========================================
        // 8. PENDING MEMBERS - For approval testing
        // ========================================
        $pendingData = [
            ['name' => 'Dian Permata', 'email' => 'dian@koperasi.com', 'nik' => '3201010101900011', 'status' => 'menunggu_bendahara'],
            ['name' => 'Budi Raharjo', 'email' => 'budiraharjo@koperasi.com', 'nik' => '3201010101900012', 'status' => 'menunggu_ketua'],
        ];

        foreach ($pendingData as $i => $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'anggota',
                'is_active' => true,
            ]);

            Anggota::create([
                'user_id' => $user->id,
                'no_anggota' => 'ANG' . date('Y') . str_pad(11 + $i, 4, '0', STR_PAD_LEFT),
                'nik' => $data['nik'],
                'nama_lengkap' => $data['name'],
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => Carbon::now()->subYears(rand(25, 40))->format('Y-m-d'),
                'jenis_kelamin' => $i === 0 ? 'P' : 'L',
                'alamat' => 'Jl. Kenanga No. ' . ($i + 20) . ', Jakarta',
                'no_telepon' => '0813-9876-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'pekerjaan' => 'Karyawan Swasta',
                'status' => $data['status'],
                'verified_by' => $data['status'] === 'menunggu_ketua' ? $bendahara->id : null,
                'verified_at' => $data['status'] === 'menunggu_ketua' ? Carbon::now()->subDays(2) : null,
            ]);
        }

        echo "✅ Seeder berhasil! Akun login:\n";
        echo "   Ketua: ketua@koperasi.com / password\n";
        echo "   Bendahara: bendahara@koperasi.com / password\n";
        echo "   Admin: admin@koperasi.com / password\n";
        echo "   Anggota: andi@koperasi.com / password\n";
    }
}
