@extends('layouts.anggota')

@section('title', 'Laporan Transparansi')

@section('header-left')
    <a href="{{ route('anggota.dashboard') }}" class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-chevron-left text-lg"></i>
    </a>
    <span class="ml-4 font-bold text-slate-800 text-lg">Transparansi Keuntungan</span>
@endsection

@section('content')

<div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/30 mb-6 relative overflow-hidden">
    <div class="relative z-10">
        <p class="text-emerald-100 text-sm font-medium mb-1">Estimasi Bunga Simpanan {{ $tahun }}</p>
        <h2 class="text-3xl font-bold tracking-tight">Rp {{ number_format($bungaSimpanan, 0, ',', '.') }}</h2>
        <p class="text-emerald-200 text-xs mt-1">Bunga {{ $bungaPersen }}% per tahun dari total simpanan</p>
    </div>
    <i class="fas fa-chart-line text-white/10 text-6xl absolute right-0 bottom-0 -mb-4 -mr-2"></i>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
    <div class="p-4 border-b border-slate-50">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rincian Simpanan</p>
    </div>
    <div class="p-4 border-b border-slate-50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-shield-alt text-yellow-500 w-5 text-center"></i>
            <span class="text-sm font-medium text-slate-700">Simpanan Pokok</span>
        </div>
        <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($simpananPokok, 0, ',', '.') }}</span>
    </div>
    <div class="p-4 border-b border-slate-50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-lock text-green-500 w-5 text-center"></i>
            <span class="text-sm font-medium text-slate-700">Simpanan Wajib</span>
        </div>
        <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($simpananWajib, 0, ',', '.') }}</span>
    </div>
    <div class="p-4 border-b border-slate-50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-heart text-purple-500 w-5 text-center"></i>
            <span class="text-sm font-medium text-slate-700">Simpanan Sukarela</span>
        </div>
        <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($simpananSukarela, 0, ',', '.') }}</span>
    </div>
    <div class="p-4 flex justify-between items-center bg-emerald-50">
        <div class="flex items-center gap-3">
            <i class="fas fa-percent text-emerald-500 w-5 text-center"></i>
            <span class="text-sm font-bold text-slate-800">Bunga Simpanan ({{ $bungaPersen }}%/thn)</span>
        </div>
        <span class="font-bold text-emerald-600 text-sm">+Rp {{ number_format($bungaSimpanan, 0, ',', '.') }}</span>
    </div>
</div>

@if(count($rekapBulanan) > 0)
<div class="mb-6">
    <h3 class="font-bold text-slate-800 text-sm mb-3">Rekap Bulanan {{ $tahun }}</h3>
    <div class="space-y-3">
        @foreach($rekapBulanan as $rekap)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-bold text-slate-800">{{ $rekap['bulan'] }}</p>
                    <p class="text-xs text-slate-400">Saldo: <span class="font-medium text-slate-600">Rp {{ number_format($rekap['saldo'], 0, ',', '.') }}</span></p>
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <p class="text-[10px] text-slate-400">Masuk</p>
                        <p class="text-xs font-bold text-green-600">+Rp {{ number_format($rekap['masuk'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] text-slate-400">Keluar</p>
                        <p class="text-xs font-bold text-red-500">-Rp {{ number_format($rekap['keluar'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
    <h3 class="font-bold text-slate-800 text-sm mb-3">Cara Perhitungan</h3>
    <div class="text-xs text-slate-600 space-y-2">
        <p>Bunga simpanan dihitung sebesar <strong>{{ $bungaPersen }}% per tahun</strong> dari total saldo simpanan Anda.</p>
        <p>Perhitungan: <strong>Rp {{ number_format($totalSimpanan, 0, ',', '.') }} × {{ $bungaPersen }}% = Rp {{ number_format($bungaSimpanan, 0, ',', '.') }}</strong></p>
        <p>Bunga ini akan dimasukkan ke dalam perhitungan SHU (Sisa Hasil Usaha) dan dibagikan kepada anggota sesuai proporsi kontribusi masing-masing.</p>
    </div>
</div>

@endsection
