<?php

namespace App\Http\Controllers\Anggota;

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
        return view('anggota.pinjaman.index');
    }

    public function data(Request $request)
    {
        $anggota = auth()->user()->anggota;
        $query = Peminjaman::where('anggota_id', $anggota->id);

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('tanggal_pengajuan', fn($row) => $row->tanggal_pengajuan->format('d/m/Y'))
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
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1000000',
            'lama_cicilan' => 'required|integer|in:6,12,18,24,36',
            'tujuan_pinjaman' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $anggota = auth()->user()->anggota;

        $peminjaman = new Peminjaman([
            'anggota_id' => $anggota->id,
            'no_pinjaman' => Peminjaman::generateNoPinjaman(),
            'tanggal_pengajuan' => now(),
            'nominal' => $request->nominal,
            'lama_cicilan' => $request->lama_cicilan,
            'bunga_persen' => 1.00, // 1% flat per bulan
            'tujuan_pinjaman' => $request->tujuan_pinjaman,
            'keterangan' => $request->keterangan,
            'status' => 'menunggu_bendahara',
        ]);

        $peminjaman->hitungBunga();
        $peminjaman->save();

        // Notify bendahara
        $this->notificationService->notifyRole(
            'bendahara',
            'Pengajuan Pinjaman Baru',
            $anggota->nama_lengkap . ' mengajukan pinjaman Rp ' . number_format($request->nominal, 0, ',', '.'),
            'info',
            route('bendahara.pinjaman.index')
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan pinjaman berhasil dikirim!',
            'data' => $peminjaman,
        ]);
    }
}
