<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

use App\Services\NotificationService;

class PembayaranController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $anggota = auth()->user()->anggota;
        $pinjamanList = Peminjaman::where('anggota_id', $anggota->id)
            ->where('status', 'disetujui')
            ->get();
            
        return view('anggota.pembayaran.index', compact('pinjamanList'));
    }

    public function data(Request $request)
    {
        $anggota = auth()->user()->anggota;
        $query = Angsuran::whereHas('peminjaman', function ($q) use ($anggota) {
            $q->where('anggota_id', $anggota->id);
        })->with('peminjaman');

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('tanggal_bayar', fn($row) => $row->tanggal_bayar->format('d/m/Y'))
            ->addColumn('status', function ($row) {
                return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Selesai</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'nominal' => 'required|numeric|min:1000',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'keterangan' => 'nullable|string',
        ]);

        $anggota = auth()->user()->anggota;
        
        // Pastikan pinjaman milik anggota ini
        $peminjaman = Peminjaman::where('id', $request->peminjaman_id)
            ->where('anggota_id', $anggota->id)
            ->firstOrFail();

        $angsuranKe = $peminjaman->jumlah_angsuran_dibayar + 1;

        $angsuran = Angsuran::create([
            'peminjaman_id' => $request->peminjaman_id,
            'no_referensi' => Angsuran::generateNoReferensi(),
            'angsuran_ke' => $angsuranKe,
            'nominal' => $request->nominal,
            'tanggal_bayar' => $request->tanggal_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan' => $request->keterangan ?? 'Pembayaran mandiri oleh anggota.',
            'created_by' => auth()->id(),
        ]);

        // Cek apakah lunas
        if ($peminjaman->fresh()->isLunas()) {
            $peminjaman->update([
                'status' => 'lunas',
                'tanggal_lunas' => now(),
            ]);
        }

        // Notifikasi ke Bendahara
        $this->notificationService->notifyRole(
            'bendahara',
            'Angsuran Diterima',
            $anggota->nama_lengkap . ' telah menyetor angsuran sebesar Rp ' . number_format($request->nominal, 0, ',', '.'),
            'success',
            route('bendahara.angsuran.index')
        );

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran angsuran berhasil disubmit.',
            'data' => $angsuran,
        ]);
    }
}
