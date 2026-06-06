<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\KategoriBarang;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BarangController extends Controller
{
    public function index()
    {
        $kategoriList = KategoriBarang::all();
        $supplierList = Supplier::all();
        return view('admin.barang.index', compact('kategoriList', 'supplierList'));
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
                    return '<div class="flex items-center justify-center"><span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 border border-red-200"><i class="fas fa-times-circle text-[10px]"></i> Habis</span></div>';
                } elseif ($row->isStokMenipis()) {
                    return '<div class="flex items-center justify-center"><span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-700 border border-amber-200"><i class="fas fa-exclamation-triangle text-[10px]"></i> ' . $row->stok . '</span></div>';
                }
                return '<div class="flex items-center justify-center"><span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200"><i class="fas fa-check-circle text-[10px]"></i> ' . $row->stok . '</span></div>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="flex items-center justify-center gap-1.5">
                    <button class="btn-edit w-8 h-8 inline-flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border border-amber-200 transition" data-id="' . $row->id . '" title="Edit"><i class="fas fa-pen text-xs"></i></button>
                    <button class="btn-stock w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition" data-id="' . $row->id . '" title="Tambah Stok"><i class="fas fa-plus text-xs"></i></button>
                </div>';
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
