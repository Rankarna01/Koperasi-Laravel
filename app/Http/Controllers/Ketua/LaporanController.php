<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Penjualan;
use App\Models\Simpanan;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // --- Laporan Simpan Pinjam ---
    public function simpanPinjam()
    {
        return view('ketua.laporan.simpan-pinjam');
    }

    public function simpanPinjamData(Request $request)
    {
        $query = Anggota::query();

        return datatables()->of($query)
            ->addColumn('nama', fn($row) => $row->nama_lengkap)
            ->addColumn('total_simpanan', function($row) {
                $total = Simpanan::where('anggota_id', $row->id)->sum('nominal');
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->addColumn('total_pinjaman', function($row) {
                $total = Peminjaman::where('anggota_id', $row->id)->whereIn('status', ['disetujui', 'lunas'])->sum('nominal');
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->addColumn('sisa_pinjaman', function($row) {
                $total = Peminjaman::where('anggota_id', $row->id)->where('status', 'disetujui')->get()->sum(fn($p) => $p->sisa_pinjaman);
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->filterColumn('nama_lengkap', function($query, $keyword) {
                $query->where('nama_lengkap', 'like', "%{$keyword}%");
            })
            ->make(true);
    }

    public function exportSimpanPinjam($type)
    {
        $anggota = Anggota::all();
        foreach($anggota as $a) {
            $a->total_simpanan = Simpanan::where('anggota_id', $a->id)->sum('nominal');
            $a->total_pinjaman = Peminjaman::where('anggota_id', $a->id)->whereIn('status', ['disetujui', 'lunas'])->sum('nominal');
            $a->sisa_pinjaman = Peminjaman::where('anggota_id', $a->id)->where('status', 'disetujui')->get()->sum(fn($p) => $p->sisa_pinjaman);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('ketua.laporan.export.simpan-pinjam', compact('anggota'));
            return $pdf->download('Laporan_Simpan_Pinjam.pdf');
        } else {
            return response(view('ketua.laporan.export.simpan-pinjam', compact('anggota')))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Simpan_Pinjam.xls"');
        }
    }

    // --- Laporan Penjualan ---
    public function penjualan()
    {
        return view('ketua.laporan.penjualan');
    }

    public function penjualanData(Request $request)
    {
        $query = Penjualan::with('creator')->orderByDesc('tahun')->orderByDesc('bulan');

        return datatables()->of($query)
            ->addColumn('periode', function($row) {
                $bulanIndo = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                return $bulanIndo[$row->bulan] . ' ' . $row->tahun;
            })
            ->editColumn('total_omset', fn($row) => 'Rp ' . number_format($row->total_omset, 0, ',', '.'))
            ->editColumn('total_laba', fn($row) => 'Rp ' . number_format($row->total_laba, 0, ',', '.'))
            ->addColumn('admin', fn($row) => $row->creator ? $row->creator->name : 'Sistem')
            ->make(true);
    }

    public function exportPenjualan($type)
    {
        $penjualan = Penjualan::with('creator')->orderByDesc('tahun')->orderByDesc('bulan')->get();
        $bulanIndo = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('ketua.laporan.export.penjualan', compact('penjualan', 'bulanIndo'));
            return $pdf->download('Laporan_Penjualan.pdf');
        } else {
            return response(view('ketua.laporan.export.penjualan', compact('penjualan', 'bulanIndo')))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="Laporan_Penjualan.xls"');
        }
    }
}
