<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\Angsuran;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class MidtransController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function handling(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;

        $angsuran = Angsuran::where('no_referensi', $orderId)->with('peminjaman.anggota')->first();

        if (!$angsuran) {
            return response()->json(['message' => 'Angsuran not found'], 404);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($angsuran->status != 'berhasil') {
                $angsuran->update(['status' => 'berhasil']);

                // Cek pelunasan
                $peminjaman = $angsuran->peminjaman;
                if ($peminjaman->fresh()->isLunas()) {
                    $peminjaman->update([
                        'status' => 'lunas',
                        'tanggal_lunas' => now(),
                    ]);
                }

                // Notifikasi ke Bendahara
                $this->notificationService->notifyRole(
                    'bendahara',
                    'Angsuran Online Diterima',
                    $peminjaman->anggota->nama_lengkap . ' telah melunasi angsuran sebesar Rp ' . number_format($angsuran->nominal, 0, ',', '.') . ' via Midtrans.',
                    'success',
                    route('bendahara.angsuran.index')
                );
            }
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $angsuran->update(['status' => 'gagal']);
        } else if ($transactionStatus == 'pending') {
            $angsuran->update(['status' => 'pending']);
        }

        return response()->json(['message' => 'Notification processed successfully']);
    }
}
