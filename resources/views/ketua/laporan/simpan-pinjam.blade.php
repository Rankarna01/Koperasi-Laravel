@extends('layouts.admin')

@section('title', 'Laporan Simpan Pinjam')

@section('breadcrumb')
    <a href="{{ route('ketua.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Laporan Simpan Pinjam</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Laporan Simpan Pinjam</h2>
        <p class="text-slate-500 text-sm mt-1">Laporan rekapitulasi transaksi simpanan dan pinjaman anggota Koperasi.</p>
    </div>
    
    <div class="flex gap-2">
        <button class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-primary-700 transition flex items-center gap-2">
            <i class="fas fa-download"></i> Cetak Laporan
        </button>
    </div>
</div>

<div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm text-center">
    <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
        <i class="fas fa-cogs"></i>
    </div>
    <h3 class="font-bold text-slate-800 text-lg mb-2">Modul Laporan Dalam Pengembangan</h3>
    <p class="text-slate-500 text-sm max-w-md mx-auto">Fitur penarikan data mutasi detail dan rekap Laporan Simpan Pinjam untuk Ketua sedang dalam proses pengerjaan. Anda dapat memantau aktivitas keseluruhan melalui Dashboard utama.</p>
</div>
@endsection
