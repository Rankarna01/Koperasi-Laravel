<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $kategoriList = KategoriBarang::all();
        return view('bendahara.barang.index', compact('kategoriList'));
    }

    public function data(Request $request)
    {
        $query = Barang::with('kategori');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        return datatables()->of($query)
            ->editColumn('harga_beli', fn($row) => 'Rp ' . number_format($row->harga_beli, 0, ',', '.'))
            ->editColumn('harga_jual', fn($row) => 'Rp ' . number_format($row->harga_jual, 0, ',', '.'))
            ->addColumn('stok_status', function ($row) {
                if ($row->stok <= 0) {
                    return '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Habis</span>';
                } elseif ($row->isStokMenipis()) {
                    return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menipis (' . $row->stok . ')</span>';
                }
                return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">' . $row->stok . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn-edit bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-yellow-600 transition" data-id="' . $row->id . '"><i class="fas fa-edit"></i></button>
                        <button class="btn-stock bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition" data-id="' . $row->id . '">+ Stok</button>';
            })
            ->rawColumns(['action', 'stok_status'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_barang,id',
            'nama' => 'required|string|max:255',
            'satuan' => 'required|string|max:20',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
        ]);

        $barang = Barang::create([
            'kategori_id' => $request->kategori_id,
            'kode_barang' => Barang::generateKodeBarang(),
            'nama' => $request->nama,
            'satuan' => $request->satuan,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'stok_minimal' => $request->stok_minimal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan.',
            'data' => $barang,
        ]);
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok_minimal' => 'required|integer|min:0',
        ]);

        $barang->update($request->only(['nama', 'kategori_id', 'satuan', 'harga_beli', 'harga_jual', 'stok_minimal']));

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil diperbarui.',
        ]);
    }

    public function addStock(Request $request, Barang $barang)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        // Record barang masuk
        BarangMasuk::create([
            'barang_id' => $barang->id,
            'jumlah' => $request->jumlah,
            'harga_beli' => $barang->harga_beli,
            'tanggal' => now(),
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        // Update stok
        $barang->increment('stok', $request->jumlah);

        return response()->json([
            'success' => true,
            'message' => 'Stok barang berhasil ditambah.',
        ]);
    }
}
