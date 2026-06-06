<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    private $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    public function index()
    {
        return view('admin.penjualan.index', ['bulanIndo' => $this->bulanIndo]);
    }

    public function data(Request $request)
    {
        $query = Penjualan::with('creator');

        return datatables()->of($query)
            ->editColumn('periode', function($row) {
                return $this->bulanIndo[$row->bulan] . ' ' . $row->tahun;
            })
            ->editColumn('total_omset', fn($row) => 'Rp ' . number_format($row->total_omset, 0, ',', '.'))
            ->editColumn('total_laba', fn($row) => 'Rp ' . number_format($row->total_laba, 0, ',', '.'))
            ->addColumn('action', function ($row) {
                return '<div class="flex items-center justify-center gap-2">
                            <button class="btn-edit bg-amber-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-amber-600 transition shadow-sm" data-id="' . $row->id . '"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn-delete bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-600 transition shadow-sm" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Hapus</button>
                        </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2099',
            'total_omset' => 'required|numeric|min:0',
            'total_laba' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        // Cek duplicate
        if (Penjualan::where('bulan', $request->bulan)->where('tahun', $request->tahun)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Rekap penjualan untuk bulan dan tahun tersebut sudah ada.'
            ], 422);
        }

        Penjualan::create([
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'total_omset' => $request->total_omset,
            'total_laba' => $request->total_laba,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id()
        ]);

        return response()->json(['success' => true, 'message' => 'Data rekap berhasil ditambahkan.']);
    }

    public function show(Penjualan $penjualan)
    {
        return response()->json([
            'success' => true,
            'data' => $penjualan,
        ]);
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2099',
            'total_omset' => 'required|numeric|min:0',
            'total_laba' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        // Cek duplicate selain diri sendiri
        if (Penjualan::where('bulan', $request->bulan)
                     ->where('tahun', $request->tahun)
                     ->where('id', '!=', $penjualan->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Rekap penjualan untuk bulan dan tahun tersebut sudah ada.'
            ], 422);
        }

        $penjualan->update([
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'total_omset' => $request->total_omset,
            'total_laba' => $request->total_laba,
            'keterangan' => $request->keterangan
        ]);

        return response()->json(['success' => true, 'message' => 'Data rekap berhasil diperbarui.']);
    }

    public function destroy(Penjualan $penjualan)
    {
        $penjualan->delete();
        return response()->json(['success' => true, 'message' => 'Data rekap berhasil dihapus.']);
    }
}
