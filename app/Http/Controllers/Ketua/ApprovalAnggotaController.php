<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ApprovalAnggotaController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('ketua.approval-anggota.index');
    }

    /**
     * DataTables AJAX data
     */
    public function data(Request $request)
    {
        $query = Anggota::with('user', 'verifier');
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['menunggu_ketua', 'aktif', 'ditolak']);
        }

        return datatables()->of($query)
            ->addColumn('action', function ($row) {
                if ($row->status === 'menunggu_ketua') {
                    return '<button class="btn-approve bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600 transition" data-id="' . $row->id . '">Setujui</button>
                            <button class="btn-reject bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600 transition" data-id="' . $row->id . '">Tolak</button>';
                }
                return '-';
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->status === 'menunggu_ketua') return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menunggu ACC</span>';
                if ($row->status === 'aktif') return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Disetujui</span>';
                return '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Ditolak</span>';
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }

    public function show(Anggota $anggota)
    {
        return response()->json([
            'success' => true,
            'data' => $anggota
        ]);
    }

    /**
     * Approve anggota
     */
    public function approve(Request $request, Anggota $anggota)
    {
        $anggota->update([
            'status' => 'aktif',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Notify anggota
        $this->notificationService->create(
            $anggota->user_id,
            'Pendaftaran Disetujui',
            'Selamat! Pendaftaran keanggotaan Anda telah disetujui oleh Ketua.',
            'success',
            route('anggota.dashboard')
        );

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil disetujui.',
        ]);
    }

    /**
     * Reject anggota
     */
    public function reject(Request $request, Anggota $anggota)
    {
        $request->validate(['catatan' => 'nullable|string']);

        $anggota->update([
            'status' => 'ditolak',
            'catatan_verifikasi' => $request->catatan,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->notificationService->create(
            $anggota->user_id,
            'Pendaftaran Ditolak',
            'Mohon maaf, pendaftaran keanggotaan Anda ditolak. ' . ($request->catatan ?? ''),
            'danger'
        );

        return response()->json([
            'success' => true,
            'message' => 'Anggota telah ditolak.',
        ]);
    }
}
