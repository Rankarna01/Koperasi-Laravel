<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Barang;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianBarangController extends Controller
{
    public function index()
    {
        $supplierList = Supplier::all();
        $barangList = Barang::active()->get();
        return view('admin.pembelian.index', compact('supplierList', 'barangList'));
    }

    public function data(Request $request)
    {
        $query = Pembelian::with('supplier', 'creator');

        return datatables()->of($query)
            ->editColumn('total', fn($row) => 'Rp ' . number_format($row->total, 0, ',', '.'))
            ->editColumn('tanggal', fn($row) => $row->tanggal->format('d/m/Y'))
            ->addColumn('status_badge', function ($row) {
                $color = $row->status === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn-detail bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition" data-id="' . $row->id . '">Detail</button>';
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $total = 0;

            $pembelian = Pembelian::create([
                'no_nota' => Pembelian::generateNoNota(),
                'supplier_id' => $request->supplier_id,
                'tanggal' => $request->tanggal,
                'total' => 0,
                'status' => 'selesai',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['harga_beli'] * $item['jumlah'];
                $total += $subtotal;

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ]);

                // Tambah stok barang
                Barang::find($item['barang_id'])->increment('stok', $item['jumlah']);
            }

            $pembelian->update(['total' => $total]);

            return response()->json([
                'success' => true,
                'message' => 'Pembelian berhasil dicatat.',
                'data' => $pembelian,
            ]);
        });
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load('detail.barang', 'supplier', 'creator');
        return response()->json([
            'success' => true,
            'data' => $pembelian,
        ]);
    }
}
