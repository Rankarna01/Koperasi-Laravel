<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class AngsuranController extends Controller
{
    public function index()
    {
        $pinjamanList = Peminjaman::where('status', 'disetujui')->with('anggota')->get();
        return view('bendahara.angsuran.index', compact('pinjamanList'));
    }

    public function data(Request $request)
    {
        $query = Angsuran::with('peminjaman.anggota', 'creator');

        if ($request->filled('peminjaman_id')) {
            $query->where('peminjaman_id', $request->peminjaman_id);
        }

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('tanggal_bayar', fn($row) => $row->tanggal_bayar->format('d/m/Y'))
            ->addColumn('anggota_nama', fn($row) => $row->peminjaman->anggota->nama_lengkap)
            ->addColumn('action', function ($row) {
                return '<button class="btn-print bg-gray-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-gray-600 transition" data-id="' . $row->id . '"><i class="fas fa-print"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'nominal' => 'required|numeric|min:1000',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'keterangan' => 'nullable|string',
        ]);

        $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);
        $angsuranKe = $peminjaman->jumlah_angsuran_dibayar + 1;

        $angsuran = Angsuran::create([
            'peminjaman_id' => $request->peminjaman_id,
            'no_referensi' => Angsuran::generateNoReferensi(),
            'angsuran_ke' => $angsuranKe,
            'nominal' => $request->nominal,
            'tanggal_bayar' => $request->tanggal_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        // Cek apakah sudah lunas
        if ($peminjaman->fresh()->isLunas()) {
            $peminjaman->update([
                'status' => 'lunas',
                'tanggal_lunas' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Angsuran ke-' . $angsuranKe . ' berhasil dicatat.',
            'data' => $angsuran,
        ]);
    }
}
