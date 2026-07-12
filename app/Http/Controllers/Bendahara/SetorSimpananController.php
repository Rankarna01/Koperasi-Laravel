<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\SetorSimpanan;
use App\Models\Simpanan;
use App\Models\Anggota;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SetorSimpananController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('bendahara.setor_simpanan.index');
    }

    public function data(Request $request)
    {
        $query = SetorSimpanan::with('anggota');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('anggota_id')) {
            $query->where('anggota_id', $request->anggota_id);
        }

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i'))
            ->editColumn('metode_pembayaran', fn($row) => ucfirst($row->metode_pembayaran))
            ->editColumn('status', fn($row) => '<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-' . $row->badge_status . '-100 text-' . $row->badge_status . '-700 text-xs font-semibold rounded-lg"><i class="fas fa-circle text-[6px]"></i> ' . $row->label_status . '</span>')
            ->addColumn('action', function ($row) {
                $btn = '<div class="flex items-center justify-center gap-1.5">';
                // Detail (always shown)
                $btn .= '<button onclick="showDetail(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition font-semibold text-xs"><i class="fas fa-eye"></i></button>';
                // Verifikasi (menunggu_bendahara)
                if ($row->status === 'menunggu_bendahara') {
                    $btn .= '<button onclick="showVerifyModal(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition font-semibold text-xs"><i class="fas fa-check"></i> Verifikasi</button>';
                    $btn .= '<button onclick="showRejectModal(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition font-semibold text-xs"><i class="fas fa-times"></i> Tolak</button>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function verify(Request $request, SetorSimpanan $setorSimpanan)
    {
        $request->validate([
            'catatan_bendahara' => 'nullable|string',
        ]);

        $setorSimpanan->update([
            'status' => 'selesai',
            'verified_by' => auth()->id(),
            'catatan_bendahara' => $request->catatan_bendahara,
            'tanggal_verifikasi' => now(),
        ]);

        // Create simpanan record
        Simpanan::create([
            'anggota_id' => $setorSimpanan->anggota_id,
            'no_transaksi' => Simpanan::generateNoTransaksi(),
            'jenis' => $setorSimpanan->jenis_simpanan,
            'nominal' => $setorSimpanan->nominal,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Setor simpanan ' . $setorSimpanan->no_setor,
            'created_by' => auth()->id(),
        ]);

        $this->notificationService->create(
            $setorSimpanan->anggota->user_id,
            'Setor Simpanan Diverifikasi',
            'Setor simpanan Anda sebesar Rp ' . number_format($setorSimpanan->nominal, 0, ',', '.') . ' telah diverifikasi dan masuk ke saldo simpanan.',
            'success',
            route('anggota.setor_simpanan.index')
        );

        return response()->json(['success' => true, 'message' => 'Setor simpanan berhasil diverifikasi.']);
    }

    public function reject(Request $request, SetorSimpanan $setorSimpanan)
    {
        $request->validate([
            'catatan_bendahara' => 'required|string',
        ]);

        $setorSimpanan->update([
            'status' => 'ditolak',
            'verified_by' => auth()->id(),
            'catatan_bendahara' => $request->catatan_bendahara,
            'tanggal_verifikasi' => now(),
        ]);

        $this->notificationService->create(
            $setorSimpanan->anggota->user_id,
            'Setor Simpanan Ditolak',
            'Setor simpanan Anda sebesar Rp ' . number_format($setorSimpanan->nominal, 0, ',', '.') . ' ditolak. Alasan: ' . $request->catatan_bendahara,
            'danger',
            route('anggota.setor_simpanan.index')
        );

        return response()->json(['success' => true, 'message' => 'Setor simpanan ditolak.']);
    }
}
