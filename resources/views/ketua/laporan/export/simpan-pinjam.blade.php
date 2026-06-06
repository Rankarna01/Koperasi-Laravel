<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Simpan Pinjam</title>
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
        <p>Laporan Rekapitulasi Simpan Pinjam Anggota</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Anggota</th>
                <th>Nama Anggota</th>
                <th class="text-right">Total Simpanan</th>
                <th class="text-right">Total Pinjaman</th>
                <th class="text-right">Sisa Pinjaman</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sum_simpanan = 0; 
                $sum_pinjaman = 0; 
                $sum_sisa = 0; 
            @endphp
            @foreach($anggota as $idx => $a)
                @php
                    $sum_simpanan += $a->total_simpanan;
                    $sum_pinjaman += $a->total_pinjaman;
                    $sum_sisa += $a->sisa_pinjaman;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $a->no_anggota }}</td>
                    <td>{{ $a->nama_lengkap }}</td>
                    <td class="text-right">Rp {{ number_format($a->total_simpanan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($a->total_pinjaman, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($a->sisa_pinjaman, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">TOTAL KESELURUHAN</th>
                <th class="text-right">Rp {{ number_format($sum_simpanan, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($sum_pinjaman, 0, ',', '.') }}</th>
                <th class="text-right">Rp {{ number_format($sum_sisa, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
