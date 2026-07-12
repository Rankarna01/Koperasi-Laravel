<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Simpanan;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function transparansi()
    {
        $anggota = auth()->user()->anggota;
        $tahun = Carbon::now()->year;

        $simpananPokok = $anggota->getSimpananByJenis('pokok');
        $simpananWajib = $anggota->getSimpananByJenis('wajib');
        $simpananSukarela = $anggota->getSimpananByJenis('sukarela');
        $totalSimpanan = $anggota->total_simpanan;

        $bungaPersen = (float) Setting::get('bunga_simpanan_persen', 0.2);
        $bungaSimpanan = $totalSimpanan * ($bungaPersen / 100);

        $riwayatSimpanan = Simpanan::where('anggota_id', $anggota->id)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal')
            ->get()
            ->groupBy(fn($item) => $item->tanggal->format('F Y'));

        $rekapBulanan = [];
        $saldoBerkurang = 0;
        foreach ($riwayatSimpanan as $bulan => $items) {
            $totalMasuk = $items->where('nominal', '>', 0)->sum('nominal');
            $totalKeluar = abs($items->where('nominal', '<', 0)->sum('nominal'));
            $saldoBerkurang += $totalMasuk - $totalKeluar;
            $rekapBulanan[] = [
                'bulan' => $bulan,
                'masuk' => $totalMasuk,
                'keluar' => $totalKeluar,
                'saldo' => $saldoBerkurang,
            ];
        }

        return view('anggota.laporan.transparansi', compact(
            'anggota', 'simpananPokok', 'simpananWajib', 'simpananSukarela',
            'totalSimpanan', 'bungaPersen', 'bungaSimpanan', 'rekapBulanan', 'tahun'
        ));
    }
}
