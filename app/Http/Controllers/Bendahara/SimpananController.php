<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Simpanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        $anggotaList = Anggota::aktif()->orderBy('nama_lengkap')->get();
        return view('bendahara.simpanan.index', compact('anggotaList'));
    }

    public function data(Request $request)
    {
        $query = Simpanan::with('anggota', 'creator');

        if ($request->filled('anggota_id')) {
            $query->where('anggota_id', $request->anggota_id);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('tanggal', fn($row) => $row->tanggal->format('d/m/Y'))
            ->addColumn('jenis_label', function ($row) {
                $colors = [
                    'pokok' => 'bg-purple-100 text-purple-700 border-purple-200',
                    'wajib' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'sukarela' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'deposito' => 'bg-amber-100 text-amber-700 border-amber-200',
                ];
                $icons = [
                    'pokok' => 'fa-gem',
                    'wajib' => 'fa-calendar-check',
                    'sukarela' => 'fa-heart',
                    'deposito' => 'fa-vault',
                ];
                $color = $colors[$row->jenis] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                $icon = $icons[$row->jenis] ?? 'fa-coins';
                return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-full border ' . $color . '"><i class="fas ' . $icon . ' text-[10px]"></i> ' . $row->label_jenis . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="flex items-center justify-center">
                    <button class="btn-print w-8 h-8 inline-flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 border border-slate-200 transition" data-id="' . $row->id . '" title="Cetak Kwitansi"><i class="fas fa-print text-xs"></i></button>
                </div>';
            })
            ->rawColumns(['action', 'jenis_label'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'jenis' => 'required|in:pokok,wajib,sukarela,deposito',
            'nominal' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $simpanan = Simpanan::create([
            'anggota_id' => $request->anggota_id,
            'no_transaksi' => Simpanan::generateNoTransaksi(),
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Simpanan berhasil ditambahkan.',
            'data' => $simpanan,
        ]);
    }

    public function print(Simpanan $simpanan)
    {
        $simpanan->load('anggota', 'creator');
        $pdf = Pdf::loadView('bendahara.simpanan.print', compact('simpanan'));
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm width receipt
        return $pdf->stream('kwitansi-' . $simpanan->no_transaksi . '.pdf');
    }
}
