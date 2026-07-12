<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index()
    {
        $anggota = auth()->user()->anggota;

        $data = [
            'total_simpanan' => $anggota->total_simpanan,
            'simpanan_pokok' => $anggota->getSimpananByJenis('pokok'),
            'simpanan_wajib' => $anggota->getSimpananByJenis('wajib'),
            'simpanan_sukarela' => $anggota->getSimpananByJenis('sukarela'),
            'riwayat' => $anggota->simpanan()->latest('tanggal')->take(10)->get(),
        ];

        return view('anggota.simpanan.index', $data);
    }

    public function data(Request $request)
    {
        $anggota = auth()->user()->anggota;
        $query = Simpanan::where('anggota_id', $anggota->id);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        return datatables()->of($query)
            ->editColumn('nominal', fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'))
            ->editColumn('tanggal', fn($row) => $row->tanggal->format('d/m/Y'))
            ->addColumn('jenis_label', fn($row) => $row->label_jenis)
            ->make(true);
    }

    public function summary()
    {
        $anggota = auth()->user()->anggota;
        return response()->json([
            'total' => $anggota->total_simpanan,
            'pokok' => $anggota->getSimpananByJenis('pokok'),
            'wajib' => $anggota->getSimpananByJenis('wajib'),
            'sukarela' => $anggota->getSimpananByJenis('sukarela'),
            'deposito' => $anggota->getSimpananByJenis('deposito'),
        ]);
    }
}
