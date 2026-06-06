<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header { margin-bottom: 30px; text-align: center; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0 0 0; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Koperasi Sejahtera Bersama</h2>
        <p>Laporan Rekapitulasi Penjualan Bulanan</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Periode (Bulan/Tahun)</th>
                <th class="text-right">Total Omset</th>
                <th class="text-right">Total Laba Bersih</th>
                <th>Catatan</th>
                <th>Admin Input</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sum_omset = 0; 
                $sum_laba = 0; 
            @endphp
            @foreach($penjualan as $idx => $p)
                @php
                    $sum_omset += $p->total_omset;
                    $sum_laba += $p->total_laba;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $bulanIndo[$p->bulan] }} {{ $p->tahun }}</td>
                    <td class="text-right">Rp {{ number_format($p->total_omset, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($p->total_laba, 0, ',', '.') }}</td>
                    <td>{{ $p->keterangan ?? '-' }}</td>
                    <td>{{ $p->creator ? $p->creator->name : 'Sistem' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">TOTAL KESELURUHAN</th>
                <th class="text-right">Rp {{ number_format($sum_omset, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($sum_laba, 0, ',', '.') }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
