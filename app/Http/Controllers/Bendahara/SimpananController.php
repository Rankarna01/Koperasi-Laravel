<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        $anggotaList = Anggota::aktif()->orderBy('nama_lengkap')->get();
        return view('bendahara.simpanan.index', compact('anggotaList'));
    }

    public function data(Request $request)
    {
        $query = Simpanan::with('anggota', 'creator');

        if ($request->filled('anggota_id')) {
            $query->where('anggota_id', $request->anggota_id);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('tanggal', fn($row) => $row->tanggal->format('d/m/Y'))
            ->addColumn('jenis_label', fn($row) => $row->label_jenis)
            ->addColumn('action', function ($row) {
                return '<button class="btn-print bg-gray-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-gray-600 transition" data-id="' . $row->id . '"><i class="fas fa-print"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'jenis' => 'required|in:pokok,wajib,sukarela,deposito',
            'nominal' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $simpanan = Simpanan::create([
            'anggota_id' => $request->anggota_id,
            'no_transaksi' => Simpanan::generateNoTransaksi(),
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Simpanan berhasil ditambahkan.',
            'data' => $simpanan,
        ]);
    }
}
