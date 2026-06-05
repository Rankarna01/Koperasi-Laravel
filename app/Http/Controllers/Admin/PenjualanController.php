<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('admin.penjualan.index');
    }

    public function data(Request $request)
    {
        $query = Penjualan::with('creator', 'anggota');

        return datatables()->of($query)
            ->editColumn('total', fn($row) => 'Rp ' . number_format($row->total, 0, ',', '.'))
            ->editColumn('tanggal', fn($row) => $row->tanggal->format('d/m/Y'))
            ->addColumn('action', function ($row) {
                return '<button class="btn-detail bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition" data-id="' . $row->id . '">Detail</button>
                        <button class="btn-print bg-gray-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-gray-600 transition" data-id="' . $row->id . '"><i class="fas fa-print"></i></button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Halaman kasir POS
     */
    public function kasir()
    {
        $barangList = Barang::active()->where('stok', '>', 0)->get();
        return view('admin.penjualan.kasir', compact('barangList'));
    }

    /**
     * Search barang untuk kasir
     */
    public function searchBarang(Request $request)
    {
        $search = $request->get('q', '');
        $barang = Barang::active()
            ->where('stok', '>', 0)
            ->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            })
            ->take(10)
            ->get();

        return response()->json($barang);
    }

    /**
     * Proses penjualan dari kasir
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
        ]);

        return DB::transaction(function () use ($request) {
            $total = 0;
            $details = [];

            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['barang_id']);

                if ($barang->stok < $item['jumlah']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok ' . $barang->nama . ' tidak mencukupi.',
                    ], 422);
                }

                $subtotal = $barang->harga_jual * $item['jumlah'];
                $total += $subtotal;

                $details[] = [
                    'barang_id' => $barang->id,
                    'jumlah' => $item['jumlah'],
                    'harga_jual' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                ];

                // Kurangi stok
                $barang->decrement('stok', $item['jumlah']);
            }

            $kembalian = $request->bayar - $total;

            $penjualan = Penjualan::create([
                'no_nota' => Penjualan::generateNoNota(),
                'tanggal' => now(),
                'anggota_id' => $request->anggota_id,
                'total' => $total,
                'bayar' => $request->bayar,
                'kembalian' => max(0, $kembalian),
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => 'selesai',
                'created_by' => auth()->id(),
            ]);

            foreach ($details as $detail) {
                PenjualanDetail::create(array_merge($detail, [
                    'penjualan_id' => $penjualan->id,
                ]));
            }

            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil!',
                'data' => $penjualan->load('detail.barang'),
                'kembalian' => max(0, $kembalian),
            ]);
        });
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load('detail.barang', 'creator', 'anggota');
        return response()->json([
            'success' => true,
            'data' => $penjualan,
        ]);
    }
}
