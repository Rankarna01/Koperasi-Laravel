<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PenarikanController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('ketua.penarikan.index');
    }

    public function data(Request $request)
    {
        $query = PenarikanDana::with('anggota');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y'))
            ->editColumn('metode_pembayaran', fn($row) => ucfirst($row->metode_pembayaran))
            ->editColumn('status', fn($row) => '<span class="badge bg-' . $row->badge_status . '">' . $row->label_status . '</span>')
            ->addColumn('action', function ($row) {
                $btn = '<div class="flex items-center justify-center gap-1.5">';
                // Detail (always shown)
                $btn .= '<button onclick="showDetail(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition font-semibold text-xs"><i class="fas fa-eye"></i></button>';
                // Approve (disetujui_ketua)
                if ($row->status === 'disetujui_ketua') {
                    $btn .= '<button onclick="showApproveModal(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition font-semibold text-xs"><i class="fas fa-check"></i> ACC</button>';
                    $btn .= '<button onclick="showRejectModal(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition font-semibold text-xs"><i class="fas fa-times"></i> Tolak</button>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function approve(Request $request, PenarikanDana $penarikan)
    {
        $request->validate([
            'catatan_ketua' => 'nullable|string',
        ]);

        $penarikan->update([
            'status' => 'diproses',
            'approved_by' => auth()->id(),
            'catatan_ketua' => $request->catatan_ketua,
        ]);

        $this->notificationService->create(
            $penarikan->anggota->user_id,
            'Penarikan Dana Disetujui',
            'Penarikan dana Anda sebesar Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ' telah disetujui ketua. Menunggu proses transfer.',
            'success',
            route('anggota.penarikan.index')
        );

        $this->notificationService->notifyRole(
            'bendahara',
            'Penarikan Dana Perlu Diproses',
            'Penarikan dana dari ' . $penarikan->anggota->nama_lengkap . ' telah disetujui. Silakan proses transfer.',
            'info',
            route('bendahara.penarikan.index')
        );

        return response()->json(['success' => true, 'message' => 'Penarikan disetujui.']);
    }

    public function reject(Request $request, PenarikanDana $penarikan)
    {
        $request->validate([
            'catatan_ketua' => 'required|string',
        ]);

        $penarikan->update([
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
            'catatan_ketua' => $request->catatan_ketua,
        ]);

        $this->notificationService->create(
            $penarikan->anggota->user_id,
            'Penarikan Dana Ditolak Ketua',
            'Penarikan dana Anda sebesar Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ' ditolak oleh ketua. Alasan: ' . $request->catatan_ketua,
            'danger',
            route('anggota.penarikan.index')
        );

        return response()->json(['success' => true, 'message' => 'Penarikan ditolak.']);
    }
}
