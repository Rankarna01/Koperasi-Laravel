@extends('layouts.anggota')

@section('title', 'Simpanan Saya')

@section('header-left')
    <a href="{{ route('anggota.dashboard') }}" class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-chevron-left text-lg"></i>
    </a>
    <span class="ml-4 font-bold text-slate-800 text-lg">Simpanan Saya</span>
@endsection

@section('content')

<div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl p-5 text-white shadow-lg shadow-primary-500/30 mb-6 relative overflow-hidden">
    <div class="relative z-10">
        <p class="text-primary-100 text-sm font-medium mb-1">Total Simpanan</p>
        <h2 class="text-3xl font-bold tracking-tight">Rp {{ number_format($total_simpanan, 0, ',', '.') }}</h2>
    </div>
    <i class="fas fa-wallet text-white/10 text-6xl absolute right-0 bottom-0 -mb-4 -mr-2"></i>
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
        <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($simpanan_pokok, 0, ',', '.') }}</span>
    </div>
    <div class="p-4 border-b border-slate-50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-lock text-green-500 w-5 text-center"></i>
            <span class="text-sm font-medium text-slate-700">Simpanan Wajib</span>
        </div>
        <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($simpanan_wajib, 0, ',', '.') }}</span>
    </div>
    <div class="p-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-heart text-purple-500 w-5 text-center"></i>
            <span class="text-sm font-medium text-slate-700">Simpanan Sukarela</span>
        </div>
        <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($simpanan_sukarela, 0, ',', '.') }}</span>
    </div>
</div>

<div class="mb-6">
    <div class="flex justify-between items-end mb-3">
        <h3 class="font-bold text-slate-800 text-sm">Riwayat Simpanan</h3>
    </div>

    @if($riwayat->isEmpty())
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center">
            <i class="fas fa-inbox text-slate-300 text-3xl mb-2"></i>
            <p class="text-xs text-slate-400">Belum ada riwayat simpanan.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($riwayat as $item)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                            @if($item->jenis === 'pokok') bg-yellow-50 text-yellow-500
                            @elseif($item->jenis === 'wajib') bg-green-50 text-green-500
                            @elseif($item->jenis === 'sukarela') bg-purple-50 text-purple-500
                            @else bg-slate-50 text-slate-500 @endif">
                            @if($item->jenis === 'pokok')
                                <i class="fas fa-shield-alt"></i>
                            @elseif($item->jenis === 'wajib')
                                <i class="fas fa-lock"></i>
                            @elseif($item->jenis === 'sukarela')
                                <i class="fas fa-heart"></i>
                            @else
                                <i class="fas fa-vault"></i>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $item->label_jenis }}</p>
                            <p class="text-[10px] text-slate-400">{{ $item->tanggal->format('d M Y') }}</p>
                        </div>
                    </div>
                    <span class="font-bold text-sm text-green-600">+Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
