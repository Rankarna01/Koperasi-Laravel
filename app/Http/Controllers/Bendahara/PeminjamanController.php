<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('bendahara.pinjaman.index');
    }

    public function data(Request $request)
    {
        $query = Peminjaman::with('anggota');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('angsuran_per_bulan', fn($row) => 'Rp ' . number_format($row->angsuran_per_bulan, 0, ',', '.'))
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'menunggu_bendahara' => 'bg-yellow-100 text-yellow-800',
                    'menunggu_ketua' => 'bg-blue-100 text-blue-800',
                    'disetujui' => 'bg-green-100 text-green-800',
                    'ditolak' => 'bg-red-100 text-red-800',
                    'lunas' => 'bg-purple-100 text-purple-800',
                ];
                $color = $colors[$row->status] ?? 'bg-gray-100 text-gray-800';
                return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . $row->label_status . '</span>';
            })
            ->addColumn('action', function ($row) {
                $btn = '<button class="btn-detail bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition" data-id="' . $row->id . '">Detail</button>';
                if ($row->status === 'menunggu_bendahara') {
                    $btn .= ' <button class="btn-verify bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600 transition" data-id="' . $row->id . '">Proses</button>';
                }
                return $btn;
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load('anggota', 'angsuran');
        return response()->json([
            'success' => true,
            'data' => $peminjaman,
            'total_dibayar' => $peminjaman->total_dibayar,
            'sisa_pinjaman' => $peminjaman->sisa_pinjaman,
        ]);
    }

    public function verify(Request $request, Peminjaman $peminjaman)
    {
        $action = $request->input('action'); // 'approve' or 'reject'

        if ($action === 'approve') {
            $peminjaman->update([
                'status' => 'menunggu_ketua',
                'catatan_bendahara' => $request->catatan,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $this->notificationService->notifyRole(
                'ketua',
                'Persetujuan Pinjaman',
                'Pinjaman ' . $peminjaman->no_pinjaman . ' menunggu persetujuan Anda.',
                'info',
                route('ketua.approval-pinjaman.index')
            );

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman telah diverifikasi dan dikirim ke Ketua.',
            ]);
        } else {
            $peminjaman->update([
                'status' => 'ditolak',
                'catatan_bendahara' => $request->catatan,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $this->notificationService->create(
                $peminjaman->anggota->user_id,
                'Pinjaman Ditolak',
                'Pinjaman Anda ditolak oleh Bendahara. ' . ($request->catatan ?? ''),
                'danger'
            );

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman telah ditolak.',
            ]);
        }
    }
}
