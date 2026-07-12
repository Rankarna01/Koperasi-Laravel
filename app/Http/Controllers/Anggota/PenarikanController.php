<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\PenarikanDana;
use App\Models\Setting;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PenarikanController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $anggota = auth()->user()->anggota;
        $saldoSimpanan = $anggota->total_simpanan;
        $minimalPokok = (float) Setting::get('minimal_saldo_pokok', 500000);
        $simpananPokok = $anggota->getSimpananByJenis('pokok');
        $riwayat = PenarikanDana::where('anggota_id', $anggota->id)
            ->latest()
            ->take(10)
            ->get();

        return view('anggota.penarikan.index', compact(
            'saldoSimpanan', 'minimalPokok', 'simpananPokok', 'riwayat'
        ));
    }

    public function store(Request $request)
    {
        $anggota = auth()->user()->anggota;

        $rules = [
            'nominal' => 'required|numeric|min:10000',
            'metode_pembayaran' => 'required|in:cash,transfer',
            'keterangan' => 'nullable|string|max:255',
        ];

        if ($request->metode_pembayaran === 'transfer') {
            $rules['rekening_bank.nama_bank'] = 'required|string|max:100';
            $rules['rekening_bank.no_rekening'] = 'required|string|max:50';
            $rules['rekening_bank.nama_rekening'] = 'required|string|max:100';
        }

        $validated = $request->validate($rules);

        $nominal = $validated['nominal'];
        $sisaSaldo = $anggota->total_simpanan - $nominal;
        $minimalPokok = (float) Setting::get('minimal_saldo_pokok', 500000);

        if ($sisaSaldo < 0) {
            return back()->withErrors(['nominal' => 'Saldo simpanan tidak mencukupi.'])->withInput();
        }

        if ($anggota->getSimpananByJenis('pokok') > 0 && $sisaSaldo < $minimalPokok) {
            return back()->withErrors([
                'nominal' => 'Saldo simpanan tidak boleh kurang dari Rp ' . number_format($minimalPokok, 0, ',', '.') . ' (minimal simpanan pokok).',
            ])->withInput();
        }

        $penarikan = PenarikanDana::create([
            'anggota_id' => $anggota->id,
            'no_penarikan' => (new PenarikanDana)->generateNoPenarikan(),
            'nominal' => $nominal,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'rekening_bank' => $validated['rekening_bank'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => 'menunggu_bendahara',
        ]);

        $this->notificationService->notifyRole(
            'bendahara',
            'Penarikan Dana Baru',
            'Anggota ' . $anggota->nama_lengkap . ' mengajukan penarikan dana sebesar Rp ' . number_format($nominal, 0, ',', '.'),
            'warning',
            route('bendahara.penarikan.index')
        );

        return redirect()->route('anggota.penarikan.index')
            ->with('success', 'Pengajuan penarikan dana berhasil dikirim.');
    }

    public function data(Request $request)
    {
        $anggota = auth()->user()->anggota;
        $query = PenarikanDana::where('anggota_id', $anggota->id);

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('tanggal', fn($row) => $row->created_at->format('d/m/Y'))
            ->editColumn('status', fn($row) => '<span class="badge bg-' . $row->badge_status . '">' . $row->label_status . '</span>')
            ->rawColumns(['status'])
            ->make(true);
    }
}
