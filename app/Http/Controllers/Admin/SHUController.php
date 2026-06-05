<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShuPeriode;
use App\Models\ShuAnggota;
use App\Models\Anggota;
use App\Models\Peminjaman;
use App\Models\Simpanan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SHUController extends Controller
{
    public function index()
    {
        $periodeList = ShuPeriode::orderByDesc('tahun')->get();
        return view('admin.shu.index', compact('periodeList'));
    }

    /**
     * Hitung SHU untuk tahun tertentu
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:' . (date('Y') + 1),
        ]);

        $tahun = $request->tahun;

        return DB::transaction(function () use ($tahun) {
            // Hapus kalkulasi sebelumnya jika ada (draft)
            ShuPeriode::where('tahun', $tahun)->where('status', 'draft')->delete();

            // 1. Hitung total pendapatan dari bunga pinjaman
            $totalBungaPinjaman = Peminjaman::whereIn('status', ['disetujui', 'lunas'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->sum('total_bunga');

            // 2. Hitung laba penjualan
            $labaPenjualan = DB::table('penjualan_detail')
                ->join('barang', 'penjualan_detail.barang_id', '=', 'barang.id')
                ->join('penjualan', 'penjualan_detail.penjualan_id', '=', 'penjualan.id')
                ->where('penjualan.status', 'selesai')
                ->whereYear('penjualan.tanggal', $tahun)
                ->selectRaw('SUM((penjualan_detail.harga_jual - barang.harga_beli) * penjualan_detail.jumlah) as laba')
                ->value('laba') ?? 0;

            $totalPendapatan = $totalBungaPinjaman + $labaPenjualan;
            $totalBiaya = $totalPendapatan * 0.2; // Estimasi biaya operasional 20%
            $shuBersih = $totalPendapatan - $totalBiaya;

            // Buat periode SHU
            $periode = ShuPeriode::create([
                'tahun' => $tahun,
                'total_pendapatan' => $totalPendapatan,
                'total_biaya' => $totalBiaya,
                'shu_bersih' => $shuBersih,
                'dana_cadangan_persen' => 20,
                'dana_pengurus_persen' => 10,
                'dana_pendidikan_persen' => 10,
                'dana_sosial_persen' => 10,
                'dana_anggota_persen' => 50,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // 3. Hitung SHU per anggota (proporsional)
            $danaAnggota = $shuBersih * 0.5;
            $anggotaAktif = Anggota::aktif()->get();

            // Total kontribusi semua anggota
            $totalKontribusiSimpanan = Simpanan::whereYear('tanggal', $tahun)->sum('nominal');
            $totalKontribusiPinjaman = $totalBungaPinjaman;
            $totalKontribusiPenjualan = Penjualan::where('status', 'selesai')
                ->whereYear('tanggal', $tahun)
                ->whereNotNull('anggota_id')
                ->sum('total');

            foreach ($anggotaAktif as $anggota) {
                // Kontribusi simpanan per anggota
                $simpananAnggota = Simpanan::where('anggota_id', $anggota->id)
                    ->whereYear('tanggal', $tahun)
                    ->sum('nominal');

                // Kontribusi pinjaman per anggota (bunga yang dibayar)
                $bungaAnggota = Peminjaman::where('anggota_id', $anggota->id)
                    ->whereIn('status', ['disetujui', 'lunas'])
                    ->whereYear('tanggal_pengajuan', $tahun)
                    ->sum('total_bunga');

                // Kontribusi pembelian di toko
                $penjualanAnggota = Penjualan::where('anggota_id', $anggota->id)
                    ->where('status', 'selesai')
                    ->whereYear('tanggal', $tahun)
                    ->sum('total');

                // Hitung proporsi (40% simpanan, 30% pinjaman, 30% penjualan)
                $proporsiSimpanan = $totalKontribusiSimpanan > 0 ? ($simpananAnggota / $totalKontribusiSimpanan) : 0;
                $proporsiPinjaman = $totalKontribusiPinjaman > 0 ? ($bungaAnggota / $totalKontribusiPinjaman) : 0;
                $proporsiPenjualan = $totalKontribusiPenjualan > 0 ? ($penjualanAnggota / $totalKontribusiPenjualan) : 0;

                $shuAnggota = $danaAnggota * (
                    ($proporsiSimpanan * 0.4) +
                    ($proporsiPinjaman * 0.3) +
                    ($proporsiPenjualan * 0.3)
                );

                ShuAnggota::create([
                    'shu_periode_id' => $periode->id,
                    'anggota_id' => $anggota->id,
                    'kontribusi_simpanan' => $simpananAnggota,
                    'kontribusi_pinjaman' => $bungaAnggota,
                    'kontribusi_penjualan' => $penjualanAnggota,
                    'total_shu' => $shuAnggota,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Perhitungan SHU tahun ' . $tahun . ' berhasil.',
                'data' => $periode->load('shuAnggota.anggota'),
            ]);
        });
    }

    public function show(ShuPeriode $shuPeriode)
    {
        $shuPeriode->load('shuAnggota.anggota');
        return response()->json([
            'success' => true,
            'data' => $shuPeriode,
        ]);
    }

    public function finalize(ShuPeriode $shuPeriode)
    {
        $shuPeriode->update(['status' => 'final']);
        return response()->json([
            'success' => true,
            'message' => 'SHU telah difinalkan.',
        ]);
    }
}
