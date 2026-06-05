<?php

namespace App\Services;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Peminjaman;
use App\Models\Angsuran;
use App\Models\Penjualan;
use App\Models\Barang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Dashboard data untuk Ketua
     */
    public function getKetuaDashboard(): array
    {
        $totalAnggota = Anggota::aktif()->count();
        $totalSimpanan = Simpanan::sum('nominal');
        $totalPinjamanAktif = Peminjaman::where('status', 'disetujui')->sum('nominal');
        $totalPenjualan = Penjualan::where('status', 'selesai')->sum('total');

        // Hitung estimasi SHU
        $totalBungaPinjaman = Peminjaman::whereIn('status', ['disetujui', 'lunas'])->sum('total_bunga');
        $totalLabaPenjualan = $this->hitungLabaPenjualan();
        $estimasiShu = $totalBungaPinjaman + $totalLabaPenjualan;

        // Perubahan dari bulan lalu
        $bulanLalu = Carbon::now()->subMonth();
        $anggotaBulanLalu = Anggota::aktif()->where('created_at', '<', $bulanLalu)->count();
        $simpananBulanLalu = Simpanan::where('tanggal', '<', $bulanLalu)->sum('nominal');
        $pinjamanBulanLalu = Peminjaman::where('status', 'disetujui')->where('created_at', '<', $bulanLalu)->sum('nominal');
        $penjualanBulanLalu = Penjualan::where('status', 'selesai')->where('tanggal', '<', $bulanLalu)->sum('total');

        // Pending approvals
        $pendingAnggota = Anggota::menungguApproval()->with('user')->latest()->get();
        $pendingPinjaman = Peminjaman::where('status', 'menunggu_ketua')->with('anggota')->latest()->get();

        // Chart data - 6 bulan terakhir
        $chartData = $this->getChartSimpanPinjam6Bulan();

        return [
            'stats' => [
                'total_anggota' => $totalAnggota,
                'total_simpanan' => $totalSimpanan,
                'total_pinjaman_aktif' => $totalPinjamanAktif,
                'total_penjualan' => $totalPenjualan,
                'estimasi_shu' => $estimasiShu,
                'perubahan' => [
                    'anggota' => $totalAnggota - $anggotaBulanLalu,
                    'simpanan' => $simpananBulanLalu > 0 ? round(($totalSimpanan - $simpananBulanLalu) / $simpananBulanLalu * 100, 1) : 0,
                    'pinjaman' => $pinjamanBulanLalu > 0 ? round(($totalPinjamanAktif - $pinjamanBulanLalu) / $pinjamanBulanLalu * 100, 1) : 0,
                    'penjualan' => $penjualanBulanLalu > 0 ? round(($totalPenjualan - $penjualanBulanLalu) / $penjualanBulanLalu * 100, 1) : 0,
                ],
            ],
            'pending_anggota' => $pendingAnggota,
            'pending_pinjaman' => $pendingPinjaman,
            'chart' => $chartData,
        ];
    }

    /**
     * Dashboard data untuk Bendahara
     */
    public function getBendaharaDashboard(): array
    {
        $totalAnggota = Anggota::aktif()->count();
        $totalSimpanan = Simpanan::sum('nominal');
        $totalPinjamanAktif = Peminjaman::where('status', 'disetujui')->sum('nominal');
        $angsuranBulanIni = Angsuran::whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)->sum('nominal');

        // Tunggakan - pinjaman yang seharusnya sudah bayar tapi belum
        $tunggakan = DB::table('peminjaman')
            ->where('status', 'disetujui')
            ->whereRaw('(SELECT COUNT(*) FROM angsuran WHERE angsuran.peminjaman_id = peminjaman.id) < peminjaman.lama_cicilan')
            ->sum('angsuran_per_bulan');

        // Pengajuan pinjaman terbaru
        $pengajuanPinjaman = Peminjaman::where('status', 'menunggu_bendahara')
            ->with('anggota')
            ->latest()
            ->take(5)
            ->get();

        // Pending verifikasi anggota
        $pendingAnggota = Anggota::menungguVerifikasi()->with('user')->latest()->take(5)->get();

        // Transaksi terakhir
        $transaksiTerakhir = $this->getTransaksiTerakhir();

        // Stok menipis
        $stokMenipis = Barang::active()->stokMenipis()->take(5)->get();

        // Chart data
        $chartData = $this->getChartSimpanPinjam6Bulan();

        // Angsuran jatuh tempo
        $angsuranJatuhTempo = $this->getAngsuranJatuhTempo();

        return [
            'stats' => [
                'total_anggota' => $totalAnggota,
                'total_simpanan' => $totalSimpanan,
                'total_pinjaman_aktif' => $totalPinjamanAktif,
                'angsuran_bulan_ini' => $angsuranBulanIni,
                'tunggakan' => $tunggakan,
            ],
            'pengajuan_pinjaman' => $pengajuanPinjaman,
            'pending_anggota' => $pendingAnggota,
            'transaksi_terakhir' => $transaksiTerakhir,
            'stok_menipis' => $stokMenipis,
            'chart' => $chartData,
            'angsuran_jatuh_tempo' => $angsuranJatuhTempo,
        ];
    }

    /**
     * Dashboard data untuk Admin
     */
    public function getAdminDashboard(): array
    {
        $totalPembelian = DB::table('pembelian')->where('status', 'selesai')->sum('total');
        $totalPenjualan = Penjualan::where('status', 'selesai')->sum('total');
        $labaKotor = $this->hitungLabaPenjualan();
        $totalAnggota = Anggota::aktif()->count();

        $totalBungaPinjaman = Peminjaman::whereIn('status', ['disetujui', 'lunas'])->sum('total_bunga');
        $estimasiShu = $totalBungaPinjaman + $labaKotor;

        // Penjualan terlaris
        $topSelling = DB::table('penjualan_detail')
            ->join('barang', 'penjualan_detail.barang_id', '=', 'barang.id')
            ->select('barang.nama', DB::raw('SUM(penjualan_detail.jumlah) as total_terjual'), DB::raw('SUM(penjualan_detail.subtotal) as total_pendapatan'))
            ->groupBy('barang.id', 'barang.nama')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        // Ringkasan stok
        $stokAman = Barang::active()->whereColumn('stok', '>', 'stok_minimal')->count();
        $stokMenipis = Barang::active()->whereColumn('stok', '<=', 'stok_minimal')->where('stok', '>', 0)->count();
        $stokHabis = Barang::active()->where('stok', 0)->count();
        $totalBarang = Barang::active()->count();

        // Transaksi terbaru
        $pembelianTerbaru = DB::table('pembelian')
            ->join('supplier', 'pembelian.supplier_id', '=', 'supplier.id')
            ->select('pembelian.*', 'supplier.nama as supplier_nama')
            ->orderByDesc('pembelian.tanggal')
            ->take(5)
            ->get();

        $penjualanTerbaru = Penjualan::with('creator')
            ->orderByDesc('tanggal')
            ->take(5)
            ->get();

        // Chart penjualan 6 bulan
        $chartPenjualan = $this->getChartPenjualan6Bulan();

        return [
            'stats' => [
                'total_pembelian' => $totalPembelian,
                'total_penjualan' => $totalPenjualan,
                'laba_kotor' => $labaKotor,
                'total_anggota' => $totalAnggota,
                'estimasi_shu' => $estimasiShu,
            ],
            'top_selling' => $topSelling,
            'stok' => [
                'aman' => $stokAman,
                'menipis' => $stokMenipis,
                'habis' => $stokHabis,
                'total' => $totalBarang,
            ],
            'pembelian_terbaru' => $pembelianTerbaru,
            'penjualan_terbaru' => $penjualanTerbaru,
            'chart' => $chartPenjualan,
        ];
    }

    /**
     * Dashboard data untuk Anggota
     */
    public function getAnggotaDashboard(Anggota $anggota): array
    {
        $totalSimpanan = $anggota->simpanan()->sum('nominal');
        $simpananPokok = $anggota->getSimpananByJenis('pokok');
        $simpananWajib = $anggota->getSimpananByJenis('wajib');
        $simpananSukarela = $anggota->getSimpananByJenis('sukarela');

        // Pinjaman aktif
        $pinjamanAktif = $anggota->peminjaman()
            ->where('status', 'disetujui')
            ->first();

        $sisaPinjaman = $pinjamanAktif ? $pinjamanAktif->sisa_pinjaman : 0;
        $angsuranBulanIni = $pinjamanAktif ? $pinjamanAktif->angsuran_per_bulan : 0;

        // Status pengajuan terbaru
        $pengajuanTerbaru = $anggota->peminjaman()
            ->latest()
            ->first();

        return [
            'total_simpanan' => $totalSimpanan,
            'simpanan_pokok' => $simpananPokok,
            'simpanan_wajib' => $simpananWajib,
            'simpanan_sukarela' => $simpananSukarela,
            'sisa_pinjaman' => $sisaPinjaman,
            'angsuran_bulan_ini' => $angsuranBulanIni,
            'pinjaman_aktif' => $pinjamanAktif,
            'pengajuan_terbaru' => $pengajuanTerbaru,
        ];
    }

    /**
     * Chart simpan pinjam 6 bulan terakhir
     */
    private function getChartSimpanPinjam6Bulan(): array
    {
        $labels = [];
        $simpananData = [];
        $pinjamanData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $simpananData[] = Simpanan::whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->sum('nominal');

            $pinjamanData[] = Peminjaman::whereIn('status', ['disetujui', 'lunas'])
                ->whereMonth('tanggal_pengajuan', $date->month)
                ->whereYear('tanggal_pengajuan', $date->year)
                ->sum('nominal');
        }

        return [
            'labels' => $labels,
            'simpanan' => $simpananData,
            'pinjaman' => $pinjamanData,
        ];
    }

    /**
     * Chart penjualan 6 bulan terakhir
     */
    private function getChartPenjualan6Bulan(): array
    {
        $labels = [];
        $penjualanData = [];
        $labaData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');

            $penjualanData[] = Penjualan::where('status', 'selesai')
                ->whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->sum('total');

            // Simplified laba calculation
            $labaData[] = Penjualan::where('status', 'selesai')
                ->whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->sum('total') * 0.25; // Estimated 25% margin
        }

        return [
            'labels' => $labels,
            'penjualan' => $penjualanData,
            'laba' => $labaData,
        ];
    }

    /**
     * Transaksi terakhir (gabungan simpanan & angsuran)
     */
    private function getTransaksiTerakhir(): array
    {
        $simpanan = Simpanan::with('anggota')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($s) {
                return [
                    'tanggal' => $s->tanggal->format('d M Y'),
                    'jenis' => $s->label_jenis,
                    'no_referensi' => $s->no_transaksi,
                    'anggota' => $s->anggota->nama_lengkap,
                    'jumlah' => $s->nominal,
                    'status' => 'Berhasil',
                ];
            })->toArray();

        $angsuran = Angsuran::with('peminjaman.anggota')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($a) {
                return [
                    'tanggal' => $a->tanggal_bayar->format('d M Y'),
                    'jenis' => 'Pembayaran Angsuran',
                    'no_referensi' => $a->no_referensi,
                    'anggota' => $a->peminjaman->anggota->nama_lengkap,
                    'jumlah' => $a->nominal,
                    'status' => 'Berhasil',
                ];
            })->toArray();

        $merged = array_merge($simpanan, $angsuran);
        usort($merged, function ($a, $b) {
            return strcmp($b['tanggal'], $a['tanggal']);
        });

        return array_slice($merged, 0, 5);
    }

    /**
     * Angsuran jatuh tempo
     */
    private function getAngsuranJatuhTempo(): array
    {
        return Peminjaman::where('status', 'disetujui')
            ->with('anggota')
            ->get()
            ->map(function ($p) {
                $angsuranKe = $p->jumlah_angsuran_dibayar + 1;
                if ($angsuranKe > $p->lama_cicilan) return null;

                $jatuhTempo = Carbon::parse($p->tanggal_pencairan)->addMonths($angsuranKe);
                $sisaHari = now()->diffInDays($jatuhTempo, false);

                return [
                    'anggota' => $p->anggota->nama_lengkap,
                    'no_pinjaman' => $p->no_pinjaman,
                    'nominal' => $p->angsuran_per_bulan,
                    'jatuh_tempo' => $jatuhTempo->format('d M Y'),
                    'sisa_hari' => $sisaHari,
                    'status' => $sisaHari < 0 ? 'Telat' : ($sisaHari <= 7 ? 'Segera' : 'Normal'),
                ];
            })
            ->filter()
            ->sortBy('sisa_hari')
            ->take(5)
            ->values()
            ->toArray();
    }

    /**
     * Hitung total laba penjualan
     */
    private function hitungLabaPenjualan(): float
    {
        return DB::table('penjualan_detail')
            ->join('barang', 'penjualan_detail.barang_id', '=', 'barang.id')
            ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
            ->where('penjualan.status', 'selesai')
            ->selectRaw('SUM((penjualan_detail.harga_jual - barang.harga_beli) * penjualan_detail.jumlah) as laba')
            ->value('laba') ?? 0;
    }
}
