<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('bendahara.anggota.index');
    }

    public function data(Request $request)
    {
        $query = Anggota::with('user');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        return datatables()->of($query)
            ->addColumn('action', function ($row) {
                $btn = '<button class="btn-detail bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition" data-id="' . $row->id . '"><i class="fas fa-eye"></i></button>';
                if ($row->status === 'menunggu_bendahara') {
                    $btn .= ' <button class="btn-verify bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600 transition" data-id="' . $row->id . '">Verifikasi</button>';
                }
                return $btn;
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'menunggu_bendahara' => 'bg-yellow-100 text-yellow-800',
                    'menunggu_ketua' => 'bg-blue-100 text-blue-800',
                    'aktif' => 'bg-green-100 text-green-800',
                    'ditolak' => 'bg-red-100 text-red-800',
                ];
                $labels = [
                    'menunggu_bendahara' => 'Menunggu Verifikasi',
                    'menunggu_ketua' => 'Menunggu ACC Ketua',
                    'aktif' => 'Aktif',
                    'ditolak' => 'Ditolak',
                ];
                $color = $colors[$row->status] ?? 'bg-gray-100 text-gray-800';
                $label = $labels[$row->status] ?? $row->status;
                return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . $label . '</span>';
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }

    public function show(Anggota $anggota)
    {
        $anggota->load('user', 'simpanan', 'peminjaman');
        return response()->json([
            'success' => true,
            'data' => $anggota,
        ]);
    }

    public function verify(Request $request, Anggota $anggota)
    {
        $anggota->update([
            'status' => 'menunggu_ketua',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $request->catatan,
        ]);

        // Notify ketua
        $this->notificationService->notifyRole(
            'ketua',
            'Persetujuan Anggota Baru',
            'Anggota baru ' . $anggota->nama_lengkap . ' menunggu persetujuan Anda.',
            'info',
            route('ketua.approval-anggota.index')
        );

        return response()->json([
            'success' => true,
            'message' => 'Data anggota berhasil diverifikasi dan dikirim ke Ketua.',
        ]);
    }
}
