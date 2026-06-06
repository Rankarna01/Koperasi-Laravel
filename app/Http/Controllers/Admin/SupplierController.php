<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('admin.supplier.index');
    }

    public function data(Request $request)
    {
        $query = Supplier::query();

        return datatables()->of($query)
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
            'nama' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return response()->json(['success' => true, 'message' => 'Supplier berhasil ditambahkan.']);
    }

    public function show(Supplier $supplier)
    {
        return response()->json([
            'success' => true,
            'data' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
        ]);

        $supplier->update($request->all());

        return response()->json(['success' => true, 'message' => 'Supplier berhasil diperbarui.']);
    }

    public function destroy(Supplier $supplier)
    {
        // Cek jika supplier digunakan di tabel pembelian
        if ($supplier->pembelian()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus supplier yang sudah memiliki riwayat pembelian.'
            ], 422);
        }

        $supplier->delete();
        return response()->json(['success' => true, 'message' => 'Supplier berhasil dihapus.']);
    }
}
