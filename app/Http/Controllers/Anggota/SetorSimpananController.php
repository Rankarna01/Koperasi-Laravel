<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\SetorSimpanan;
use App\Models\Anggota;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetorSimpananController extends Controller
{
    public function index()
    {
        $anggota = Auth::user()->anggota;
        $totalSimpanan = $anggota ? $anggota->total_simpanan : 0;

        return view('anggota.setor_simpanan.index', compact('totalSimpanan'));
    }

    public function data(Request $request)
    {
        $anggota = Auth::user()->anggota;
        if (!$anggota) {
            return datatables()->of(collect())->make(true);
        }

        $query = SetorSimpanan::where('anggota_id', $anggota->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i'))
            ->editColumn('status', fn($row) => '<span class="inline-flex items-center gap-1 px-2.5 py-1 bg-' . $row->badge_status . '-100 text-' . $row->badge_status . '-700 text-xs font-semibold rounded-lg"><i class="fas fa-circle text-[6px]"></i> ' . $row->label_status . '</span>')
            ->rawColumns(['status'])
            ->orderColumn('created_at', 'desc')
            ->make(true);
    }

    public function store(Request $request)
    {
        $anggota = Auth::user()->anggota;
        if (!$anggota) {
            return response()->json(['success' => false, 'message' => 'Data anggota tidak ditemukan.'], 404);
        }

        $rules = [
            'jenis_simpanan' => 'required|in:pokok,wajib,sukarela,deposito',
            'nominal' => 'required|numeric|min:10000',
            'metode_pembayaran' => 'required|in:transfer,cash',
            'keterangan' => 'nullable|string|max:255',
        ];

        if ($request->metode_pembayaran === 'transfer') {
            $rules['bukti_transfer'] = 'required|image|max:2048';
        }

        $request->validate($rules);

        $data = [
            'anggota_id' => $anggota->id,
            'no_setor' => (new SetorSimpanan)->generateNoSetor(),
            'jenis_simpanan' => $request->jenis_simpanan,
            'nominal' => $request->nominal,
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan' => $request->keterangan,
            'status' => 'menunggu_bendahara',
        ];

        if ($request->metode_pembayaran === 'transfer' && $request->hasFile('bukti_transfer')) {
            $data['bukti_transfer'] = $request->file('bukti_transfer')->store('bukti_setor', 'public');
        }

        SetorSimpanan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Setor simpanan berhasil diajukan. Menunggu verifikasi bendahara.',
        ]);
    }
}
