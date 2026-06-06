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
            ->addColumn('status', function ($row) {
                if ($row->status === 'berhasil') {
                    return '<span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">Selesai</span>';
                } elseif ($row->status === 'pending') {
                    return '<span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-amber-100 text-amber-700 border border-amber-200">Menunggu (Midtrans)</span>';
                }
                return '<span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">Gagal</span>';
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="flex items-center justify-center gap-1.5">';
                $btn .= '<button class="btn-detail w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition" data-id="' . $row->id . '" title="Detail"><i class="fas fa-eye text-xs"></i></button>';
                if ($row->status === 'berhasil') {
                    $btn .= '<button class="btn-print w-8 h-8 inline-flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 border border-slate-200 transition" data-id="' . $row->id . '" title="Cetak Kwitansi"><i class="fas fa-print text-xs"></i></button>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'nominal' => 'required|numeric|min:1000',
            'tanggal_bayar' => 'required|date',
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
            'metode_pembayaran' => 'tunai', // Fixed tunai for Bendahara
            'keterangan' => $request->keterangan,
            'status' => 'berhasil',
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

    public function show($id)
    {
        $angsuran = Angsuran::with('peminjaman.anggota', 'creator')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $angsuran
        ]);
    }
}
