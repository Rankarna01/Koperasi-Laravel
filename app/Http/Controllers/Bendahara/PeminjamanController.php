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
                    'menunggu_bendahara' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'menunggu_ketua' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'ditolak' => 'bg-red-100 text-red-700 border-red-200',
                    'lunas' => 'bg-purple-100 text-purple-700 border-purple-200',
                ];
                $color = $colors[$row->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                return '<div class="flex items-center justify-center"><span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold rounded-full border ' . $color . '">' . $row->label_status . '</span></div>';
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="flex items-center justify-center gap-1.5">';
                $btn .= '<button class="btn-detail w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition" data-id="' . $row->id . '" title="Detail"><i class="fas fa-eye text-xs"></i></button>';
                if ($row->status === 'menunggu_bendahara') {
                    $btn .= '<button class="btn-verify w-8 h-8 inline-flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200 transition" data-id="' . $row->id . '" title="Verifikasi"><i class="fas fa-clipboard-check text-xs"></i></button>';
                }
                $btn .= '</div>';
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
