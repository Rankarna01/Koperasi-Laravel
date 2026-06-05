<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ApprovalPinjamanController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('ketua.approval-pinjaman.index');
    }

    public function data(Request $request)
    {
        $query = Peminjaman::with('anggota');
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['menunggu_ketua', 'disetujui', 'ditolak', 'lunas']);
        }

        return datatables()->of($query)
            ->addColumn('action', function ($row) {
                if ($row->status === 'menunggu_ketua') {
                    return '<button class="btn-approve bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600 transition" data-id="' . $row->id . '">Review</button>';
                }
                return '<button class="btn-detail bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition" data-id="' . $row->id . '">Detail</button>';
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status === 'menunggu_ketua') return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menunggu ACC</span>';
                if ($row->status === 'disetujui' || $row->status === 'lunas') return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Disetujui</span>';
                return '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Ditolak</span>';
            })
            ->editColumn('nominal', function ($row) {
                return 'Rp ' . number_format($row->nominal, 0, ',', '.');
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load('anggota');
        return response()->json([
            'success' => true,
            'data' => $peminjaman
        ]);
    }

    public function approve(Request $request, Peminjaman $peminjaman)
    {
        $peminjaman->update([
            'status' => 'disetujui',
            'catatan_ketua' => $request->catatan,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'tanggal_pencairan' => now(),
        ]);

        $this->notificationService->create(
            $peminjaman->anggota->user_id,
            'Pinjaman Disetujui',
            'Pinjaman Anda sebesar Rp ' . number_format($peminjaman->nominal, 0, ',', '.') . ' telah disetujui.',
            'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'Pinjaman berhasil disetujui.',
        ]);
    }

    public function reject(Request $request, Peminjaman $peminjaman)
    {
        $request->validate(['catatan' => 'nullable|string']);

        $peminjaman->update([
            'status' => 'ditolak',
            'catatan_ketua' => $request->catatan,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->notificationService->create(
            $peminjaman->anggota->user_id,
            'Pinjaman Ditolak',
            'Pinjaman Anda ditolak. ' . ($request->catatan ?? ''),
            'danger'
        );

        return response()->json([
            'success' => true,
            'message' => 'Pinjaman telah ditolak.',
        ]);
    }
}
