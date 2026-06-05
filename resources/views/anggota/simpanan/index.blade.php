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
        <h2 class="text-3xl font-bold tracking-tight">Rp {{ number_format(3250000, 0, ',', '.') }}</h2>
    </div>
    <i class="fas fa-wallet text-white/10 text-6xl absolute right-0 bottom-0 -mb-4 -mr-2"></i>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
    <div class="p-4 border-b border-slate-50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-lock text-green-500 w-5 text-center"></i>
            <span class="text-sm font-medium text-slate-700">Simpanan Wajib</span>
        </div>
        <span class="font-bold text-slate-800 text-sm">Rp 1.200.000</span>
    </div>
    <div class="p-4 border-b border-slate-50 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-shield-alt text-yellow-500 w-5 text-center"></i>
            <span class="text-sm font-medium text-slate-700">Simpanan Pokok</span>
        </div>
        <span class="font-bold text-slate-800 text-sm">Rp 500.000</span>
    </div>
    <div class="p-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fas fa-piggy-bank text-purple-500 w-5 text-center"></i>
            <span class="text-sm font-medium text-slate-700">Simpanan Sukarela</span>
        </div>
        <span class="font-bold text-slate-800 text-sm">Rp 1.550.000</span>
    </div>
</div>

<a href="#" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex justify-between items-center hover:bg-slate-50 transition">
    <span class="font-bold text-slate-800 text-sm">Riwayat Simpanan</span>
    <i class="fas fa-chevron-right text-slate-400"></i>
</a>

@endsection
