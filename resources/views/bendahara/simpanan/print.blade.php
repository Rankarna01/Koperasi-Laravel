<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $simpanan->no_transaksi }}</title>
    <style>
        @page { margin: 0; }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', sans-serif;
        }
        body {
            font-size: 11px;
            color: #1e293b;
            width: 100%;
        }

        /* Header */
        .header {
            background: #1e40af;
            color: white;
            padding: 18px 16px;
            text-align: center;
        }
        .header h1 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .header p {
            font-size: 9px;
            opacity: 0.85;
        }
        .header .jenis-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 3px 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }

        /* Body */
        .body {
            padding: 16px;
        }

        /* No Transaksi */
        .no-transaksi {
            text-align: center;
            padding: 10px;
            border: 1px dashed #94a3b8;
            border-radius: 8px;
            margin-bottom: 14px;
        }
        .no-transaksi .label {
            font-size: 8px;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .no-transaksi .value {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Detail Table */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .detail-table td {
            padding: 7px 0;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .detail-table .label-col {
            color: #64748b;
            font-size: 10px;
            width: 40%;
        }
        .detail-table .value-col {
            font-weight: 600;
            text-align: right;
            font-size: 10px;
        }

        /* Nominal Box */
        .nominal-box {
            text-align: center;
            padding: 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            margin-bottom: 16px;
        }
        .nominal-box .label {
            font-size: 8px;
            color: #3b82f6;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nominal-box .amount {
            font-size: 20px;
            font-weight: 800;
            color: #1e40af;
            margin-top: 3px;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px dashed #cbd5e1;
            margin: 0;
        }

        /* Footer */
        .footer {
            padding: 14px 16px 20px;
            text-align: center;
        }
        .stamp {
            display: inline-block;
            padding: 4px 16px;
            border: 2px solid #22c55e;
            color: #16a34a;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .created-by {
            margin-top: 10px;
            font-size: 8px;
            color: #94a3b8;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Koperasi Sejahtera Bersama</h1>
        <p>Bukti Penerimaan Simpanan</p>
        <div class="jenis-badge">{{ $simpanan->label_jenis }}</div>
    </div>

    <div class="body">
        <div class="no-transaksi">
            <div class="label">No. Transaksi</div>
            <div class="value">{{ $simpanan->no_transaksi }}</div>
        </div>

        <table class="detail-table">
            <tr>
                <td class="label-col">Tanggal</td>
                <td class="value-col">{{ $simpanan->tanggal->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label-col">No. Anggota</td>
                <td class="value-col">{{ $simpanan->anggota->no_anggota }}</td>
            </tr>
            <tr>
                <td class="label-col">Nama Anggota</td>
                <td class="value-col">{{ $simpanan->anggota->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label-col">Jenis Simpanan</td>
                <td class="value-col">{{ $simpanan->label_jenis }}</td>
            </tr>
            @if($simpanan->keterangan)
            <tr>
                <td class="label-col">Keterangan</td>
                <td class="value-col">{{ $simpanan->keterangan }}</td>
            </tr>
            @endif
        </table>

        <div class="nominal-box">
            <div class="label">Nominal Diterima</div>
            <div class="amount">Rp {{ number_format($simpanan->nominal, 0, ',', '.') }}</div>
        </div>
    </div>

    <hr class="divider">

    <div class="footer">
        <div class="stamp">&#10003; LUNAS / DITERIMA</div>
        <div class="created-by">
            Dicatat oleh: {{ $simpanan->creator->name ?? 'Sistem' }}<br>
            Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </div>
    </div>
</body>
</html>
