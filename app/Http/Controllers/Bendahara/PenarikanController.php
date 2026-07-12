<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use App\Models\Simpanan;
use App\Models\Anggota;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenarikanController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $anggotaList = Anggota::aktif()->get();
        return view('bendahara.penarikan.index', compact('anggotaList'));
    }

    public function data(Request $request)
    {
        $query = PenarikanDana::with('anggota');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('anggota_id')) {
            $query->where('anggota_id', $request->anggota_id);
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
                // Verifikasi (menunggu_bendahara)
                if ($row->status === 'menunggu_bendahara') {
                    $btn .= '<button onclick="showVerifyModal(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition font-semibold text-xs"><i class="fas fa-check"></i> Verifikasi</button>';
                    $btn .= '<button onclick="showRejectModal(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition font-semibold text-xs"><i class="fas fa-times"></i> Tolak</button>';
                }
                // Proses transfer
                if ($row->status === 'diproses' && $row->metode_pembayaran === 'transfer') {
                    $btn .= '<button onclick="showProcessModal(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition font-semibold text-xs"><i class="fas fa-money-bill-transfer"></i> Proses</button>';
                }
                // Proses cash
                if ($row->status === 'diproses' && $row->metode_pembayaran === 'cash') {
                    $btn .= '<button onclick="showProcessCashModal(' . $row->id . ')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition font-semibold text-xs"><i class="fas fa-money-bill"></i> Serahkan</button>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function verify(Request $request, PenarikanDana $penarikan)
    {
        $request->validate([
            'catatan_bendahara' => 'nullable|string',
        ]);

        $penarikan->update([
            'status' => 'disetujui_ketua',
            'verified_by' => auth()->id(),
            'catatan_bendahara' => $request->catatan_bendahara,
        ]);

        $this->notificationService->create(
            $penarikan->anggota->user_id,
            'Penarikan Dana Diverifikasi',
            'Penarikan dana Anda sebesar Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ' telah diverifikasi dan menunggu persetujuan ketua.',
            'info',
            route('ketua.penarikan.index')
        );

        $this->notificationService->notifyRole(
            'ketua',
            'Penarikan Dana Menunggu Persetujuan',
            'Penarikan dana dari ' . $penarikan->anggota->nama_lengkap . ' sebesar Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ' menunggu persetujuan Anda.',
            'warning',
            route('ketua.penarikan.index')
        );

        return response()->json(['success' => true, 'message' => 'Berhasil diverifikasi.']);
    }

    public function reject(Request $request, PenarikanDana $penarikan)
    {
        $request->validate([
            'catatan_bendahara' => 'required|string',
        ]);

        $penarikan->update([
            'status' => 'ditolak',
            'verified_by' => auth()->id(),
            'catatan_bendahara' => $request->catatan_bendahara,
        ]);

        $this->notificationService->create(
            $penarikan->anggota->user_id,
            'Penarikan Dana Ditolak',
            'Penarikan dana Anda sebesar Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ' ditolak. Alasan: ' . $request->catatan_bendahara,
            'danger',
            route('anggota.penarikan.index')
        );

        return response()->json(['success' => true, 'message' => 'Penarikan ditolak.']);
    }

    public function processTransfer(Request $request, PenarikanDana $penarikan)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|max:2048',
        ]);

        $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');

        DB::transaction(function () use ($penarikan, $path) {
            $penarikan->update([
                'status' => 'selesai',
                'processed_by' => auth()->id(),
                'bukti_transfer' => $path,
                'tanggal_proses' => now(),
            ]);

            Simpanan::create([
                'anggota_id' => $penarikan->anggota_id,
                'no_transaksi' => (new Simpanan)->generateNoTransaksi(),
                'jenis' => 'sukarela',
                'nominal' => -$penarikan->nominal,
                'tanggal' => now()->toDateString(),
                'keterangan' => 'Penarikan dana ' . $penarikan->no_penarikan,
                'created_by' => auth()->id(),
            ]);
        });

        $this->notificationService->create(
            $penarikan->anggota->user_id,
            'Penarikan Dana Selesai',
            'Penarikan dana Anda sebesar Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ' telah berhasil ditransfer.',
            'success',
            route('anggota.penarikan.index')
        );

        return response()->json(['success' => true, 'message' => 'Transfer berhasil diproses.']);
    }

    public function processCash(Request $request, PenarikanDana $penarikan)
    {
        DB::transaction(function () use ($penarikan) {
            $penarikan->update([
                'status' => 'selesai',
                'processed_by' => auth()->id(),
                'tanggal_proses' => now(),
            ]);

            Simpanan::create([
                'anggota_id' => $penarikan->anggota_id,
                'no_transaksi' => (new Simpanan)->generateNoTransaksi(),
                'jenis' => 'sukarela',
                'nominal' => -$penarikan->nominal,
                'tanggal' => now()->toDateString(),
                'keterangan' => 'Penarikan dana cash ' . $penarikan->no_penarikan,
                'created_by' => auth()->id(),
            ]);
        });

        $this->notificationService->create(
            $penarikan->anggota->user_id,
            'Penarikan Dana Selesai',
            'Penarikan dana Anda sebesar Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ' telah selesai diserahkan.',
            'success',
            route('anggota.penarikan.index')
        );

        return response()->json(['success' => true, 'message' => 'Penarikan cash berhasil diproses.']);
    }
}
