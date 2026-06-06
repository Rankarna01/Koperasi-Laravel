<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
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
                if ($row->status === 'berhasil') {
                    return '<span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">Selesai</span>';
                } elseif ($row->status === 'pending' && $row->metode_pembayaran === 'midtrans') {
                    $btn = '<div class="flex flex-col items-center gap-1.5">';
                    $btn .= '<span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-amber-100 text-amber-700 border border-amber-200">Menunggu Pembayaran</span>';
                    if ($row->snap_token) {
                        $btn .= '<button onclick="lanjutkanBayar(\'' . $row->snap_token . '\')" class="text-[10px] bg-primary-600 text-white px-2 py-1 rounded shadow hover:bg-primary-700">Bayar Sekarang</button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                }
                return '<span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">Gagal</span>';
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
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,midtrans',
            'keterangan' => 'nullable|string',
        ]);

        $anggota = auth()->user()->anggota;
        
        // Pastikan pinjaman milik anggota ini
        $peminjaman = Peminjaman::where('id', $request->peminjaman_id)
            ->where('anggota_id', $anggota->id)
            ->firstOrFail();

        $angsuranKe = $peminjaman->jumlah_angsuran_dibayar + 1;
        $noRef = Angsuran::generateNoReferensi();
        $isMidtrans = $request->metode_pembayaran === 'midtrans';

        $angsuran = Angsuran::create([
            'peminjaman_id' => $request->peminjaman_id,
            'no_referensi' => $noRef,
            'angsuran_ke' => $angsuranKe,
            'nominal' => $request->nominal,
            'tanggal_bayar' => $request->tanggal_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan' => $request->keterangan ?? 'Pembayaran mandiri oleh anggota.',
            'status' => $isMidtrans ? 'pending' : 'berhasil',
            'created_by' => auth()->id(),
        ]);

        if ($isMidtrans) {
            // Setup Midtrans Config
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id' => $noRef,
                    'gross_amount' => (int) $request->nominal,
                ],
                'customer_details' => [
                    'first_name' => $anggota->nama_lengkap,
                    'email' => auth()->user()->email,
                    'phone' => $anggota->no_hp ?? '',
                ],
                'item_details' => [
                    [
                        'id' => 'ANGSURAN-' . $angsuranKe,
                        'price' => (int) $request->nominal,
                        'quantity' => 1,
                        'name' => 'Angsuran Pinjaman ke-' . $angsuranKe,
                    ]
                ]
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
                $angsuran->update(['snap_token' => $snapToken]);

                return response()->json([
                    'success' => true,
                    'is_midtrans' => true,
                    'snap_token' => $snapToken,
                    'message' => 'Silakan selesaikan pembayaran Anda.'
                ]);
            } catch (\Exception $e) {
                $angsuran->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat transaksi pembayaran online: ' . $e->getMessage()
                ], 500);
            }
        }

        // Jika bukan midtrans (berhasil langsung)
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
            'is_midtrans' => false,
            'message' => 'Pembayaran angsuran berhasil disubmit.',
            'data' => $angsuran,
        ]);
    }
}
